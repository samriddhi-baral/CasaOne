<?php
$pageTitle = 'Bookings';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// Admin manages booking lifecycle after payment: confirm, check-in, check-out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    $bid = (int)$_POST['booking_id'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("SELECT b_id, status FROM booking WHERE b_id = ?");
    $stmt->execute([$bid]);
    if ($row = $stmt->fetch()) {
        if ($action === 'confirm' && $row['status'] === 'paid') {
            $pdo->prepare("UPDATE booking SET status = 'confirmed' WHERE b_id = ?")->execute([$bid]);
        } elseif ($action === 'check_in' && $row['status'] === 'confirmed') {
            $pdo->prepare("UPDATE booking SET status = 'checked_in' WHERE b_id = ?")->execute([$bid]);
        } elseif ($action === 'check_out' && $row['status'] === 'checked_in') {
            $pdo->prepare("UPDATE booking SET check_out = CURDATE(), status = 'checked_out' WHERE b_id = ?")->execute([$bid]);
        }
    }

    header('Location: bookings.php');
    exit;
}

$bookings = [];
try {
    $bookings = $pdo->query("
        SELECT b.*, r.room_no, h.h_name, u.u_name, u.u_email
        FROM booking b
        JOIN room r ON r.room_id = b.room_id
        LEFT JOIN hostel h ON h.h_id = b.h_id
        JOIN users u ON u.u_id = b.u_id
        ORDER BY b.book_date DESC, b.b_id DESC
    ")->fetchAll();
} catch (Throwable $e) {
    $noBookingTable = true;
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Bookings</h1>
        <?php if (!empty($noBookingTable)): ?>
        <p class="alert alert-info">Import <code>database/schema_complete.sql</code> to use the booking table.</p>
        <?php elseif (empty($bookings)): ?>
        <p style="color: var(--color-text-muted);">No bookings yet.</p>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Room</th>
                        <th>Hostel</th>
                        <th>Amount</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                        <th>Book Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= (int)$b['b_id'] ?></td>
                        <td><?= htmlspecialchars($b['u_name'] ?? '—') ?> (<?= htmlspecialchars($b['u_email'] ?? '') ?>)</td>
                        <td><?= htmlspecialchars($b['room_no'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($b['h_name'] ?? '—') ?></td>
                        <td>Rs.<?= number_format($b['amount'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($b['check_in'] ?? '—') ?></td>
                        <td><?= !empty($b['check_out']) ? htmlspecialchars($b['check_out']) : '—' ?></td>
                        <td><span class="badge badge-<?= $b['status'] ?? 'pending' ?>"><?= htmlspecialchars($b['status'] ?? 'pending') ?></span></td>
                        <td><?= htmlspecialchars($b['book_date'] ?? '—') ?></td>
                        <td>
                            <?php if (($b['status'] ?? '') === 'paid'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$b['b_id'] ?>">
                                <input type="hidden" name="action" value="confirm">
                                <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.7rem; font-size:0.85rem;">Confirm</button>
                            </form>
                            <?php elseif (($b['status'] ?? '') === 'confirmed'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$b['b_id'] ?>">
                                <input type="hidden" name="action" value="check_in">
                                <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.7rem; font-size:0.85rem;">Check In</button>
                            </form>
                            <?php elseif (($b['status'] ?? '') === 'checked_in'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= (int)$b['b_id'] ?>">
                                <input type="hidden" name="action" value="check_out">
                                <button type="submit" class="btn btn-secondary" style="padding:0.35rem 0.7rem; font-size:0.85rem;">Check Out</button>
                            </form>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
