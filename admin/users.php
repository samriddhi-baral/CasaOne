<?php
$pageTitle = 'Users';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['u_id'])) {
    $u_id = (int)$_POST['u_id'];
    if ($_POST['action'] === 'delete' && $u_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT u_id FROM users WHERE u_id = ?");
            $stmt->execute([$u_id]);
            if ($stmt->fetch()) {
                $pdo->prepare("DELETE FROM payment WHERE u_id = ?")->execute([$u_id]);
                try { $pdo->prepare("DELETE FROM booking WHERE u_id = ?")->execute([$u_id]); } catch (Throwable $e) { }
                try { $pdo->prepare("UPDATE complaint SET u_id = NULL WHERE u_id = ?")->execute([$u_id]); } catch (Throwable $e) { }
                $pdo->prepare("UPDATE hostel SET u_id = NULL WHERE u_id = ?")->execute([$u_id]);
                try { $pdo->prepare("UPDATE feedback SET u_id = NULL WHERE u_id = ?")->execute([$u_id]); } catch (Throwable $e) { }
                $pdo->prepare("DELETE FROM users WHERE u_id = ?")->execute([$u_id]);
                $success = 'User deleted.';
            } else {
                $error = 'User not found.';
            }
        } catch (PDOException $e) {
            $error = 'Could not delete user. ' . $e->getMessage();
        }
    }
}

$users = $pdo->query("
    SELECT u.*, h.h_name
    FROM users u
    LEFT JOIN hostel h ON h.h_id = u.h_id
    ORDER BY u.u_id DESC
")->fetchAll();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Registered Users</h1>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Reg Date</th>
                        <th>Gender</th>
                        <th>Hostel</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['u_id'] ?></td>
                        <td><?= htmlspecialchars($u['u_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['u_email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['u_phone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars(mb_substr($u['u_address'] ?? '', 0, 40)) ?><?= mb_strlen($u['u_address'] ?? '') > 40 ? '…' : '' ?></td>
                        <td><?= htmlspecialchars($u['reg_date'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['gender'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['h_name'] ?? '—') ?></td>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="u_id" value="<?= (int)$u['u_id'] ?>">
                                <button type="submit" class="btn btn-secondary" style="padding:0.3rem 0.6rem; font-size:0.85rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($users)): ?>
        <p style="color: var(--color-text-muted);">No registered users yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?> 
