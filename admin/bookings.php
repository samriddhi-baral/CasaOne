<?php
$pageTitle = 'Bookings';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
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
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
