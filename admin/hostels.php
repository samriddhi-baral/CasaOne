<?php
$pageTitle = 'Hostels';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['h_id'])) {
    $h_id = (int)$_POST['h_id'];
    try {
        $pdo->prepare("DELETE FROM hostel WHERE h_id = ?")->execute([$h_id]);
        $success = 'Hostel deleted.';
    } catch (PDOException $e) {
        $error = 'Cannot delete: hostel has rooms or references.';
    }
}

$hostels = $pdo->query("SELECT h.*, (SELECT COUNT(*) FROM room r WHERE r.h_id = h.h_id) as room_count FROM hostel h ORDER BY h.h_id")->fetchAll();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Hostels</h1>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Rooms</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hostels as $h): ?>
                    <tr>
                        <td><?= (int)$h['h_id'] ?></td>
                        <td><?= htmlspecialchars($h['h_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['location'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['h_email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($h['h_phone'] ?? '—') ?></td>
                        <td><?= (int)($h['room_count'] ?? 0) ?></td>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this hostel?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="h_id" value="<?= (int)$h['h_id'] ?>">
                                <button type="submit" class="btn btn-secondary" style="padding:0.3rem 0.6rem; font-size:0.85rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($hostels)): ?>
        <p style="color: var(--color-text-muted);">No hostels yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
