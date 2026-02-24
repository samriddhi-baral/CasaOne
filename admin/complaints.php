<?php
$pageTitle = 'Complaints';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$aId = getCurrentAdminId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['c_id'])) {
    $c_id = (int)$_POST['c_id'];
    if ($_POST['action'] === 'update_status') {
        $status = trim($_POST['status'] ?? '');
        if (in_array($status, ['open', 'in_progress', 'resolved'], true)) {
            $pdo->prepare("UPDATE complaint SET status = ?, a_id = ? WHERE c_id = ?")->execute([$status, $aId, $c_id]);
        }
    }
}

$complaints = $pdo->query("
    SELECT c.*, u.u_name as user_name, u.u_email as user_email
    FROM complaint c
    LEFT JOIN users u ON u.u_id = c.u_id
    ORDER BY c.c_date DESC, c.c_id DESC
")->fetchAll();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Complaints</h1>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $c): 
                        $name = !empty($c['c_name']) ? $c['c_name'] : (!empty($c['user_name']) ? $c['user_name'] . ' (' . ($c['user_email'] ?? '') . ')' : '—');
                    ?>
                    <tr>
                        <td><?= (int)$c['c_id'] ?></td>
                        <td><?= htmlspecialchars($name) ?></td>
                        <td><?= nl2br(htmlspecialchars($c['description'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars($c['status'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($c['c_date'] ?? '—') ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="c_id" value="<?= (int)$c['c_id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="open" <?= ($c['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="in_progress" <?= ($c['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="resolved" <?= ($c['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($complaints)): ?>
        <p style="color: var(--color-text-muted);">No complaints yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
 