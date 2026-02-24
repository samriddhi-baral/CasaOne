<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$countHostels = $pdo->query("SELECT COUNT(*) FROM hostel")->fetchColumn();
$countRooms = $pdo->query("SELECT COUNT(*) FROM room")->fetchColumn();
$countUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$countBookings = 0;
try {
    $countBookings = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
} catch (Throwable $e) { }
$countComplaints = $pdo->query("SELECT COUNT(*) FROM complaint")->fetchColumn();
$countFeedback = 0;
try {
    $countFeedback = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
} catch (Throwable $e) { }
$countPayments = $pdo->query("SELECT COUNT(*) FROM payment")->fetchColumn();


try {
    $dashboardRooms = $pdo->query("
        SELECT r.room_id, r.room_no, r.price, r.availability, r.h_id,
               rt.type, rt.capacity, h.h_name
        FROM room r
        LEFT JOIN roomtype rt ON rt.room_id = r.room_id
        LEFT JOIN hostel h ON h.h_id = r.h_id
        ORDER BY h.h_name, r.room_no
    ")->fetchAll();
} catch (PDOException $e) {
    $dashboardRooms = [];
}
$roomImages = ['room1.jpeg', 'room2.jpeg', 'room3.jpeg', 'room4.jpeg'];
function getRoomImageDashboard($index, $images) {
    return $images[$index % count($images)];
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Admin Dashboard</h1>
        <p style="color: var(--color-text-muted); margin-bottom:2rem;">Welcome, <?= htmlspecialchars(getCurrentAdminName()) ?>.</p>
        <div class="card-grid">
            <a href="hostels.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Hostels</h3>
                <p><?= (int)$countHostels ?> hostels</p>
            </a>
            <a href="rooms.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Rooms</h3>
                <p><?= (int)$countRooms ?> rooms</p>
            </a>
            <a href="users.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Users</h3>
                <p><?= (int)$countUsers ?> registered users</p>
            </a>
            <a href="bookings.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Bookings</h3>
                <p><?= (int)$countBookings ?> bookings</p>
            </a>
            <a href="complaints.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Complaints</h3>
                <p><?= (int)$countComplaints ?> complaints</p>
            </a>
            <a href="feedback.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Feedback</h3>
                <p><?= (int)$countFeedback ?> feedback</p>
            </a>
            <a href="payments.php" class="card" style="text-decoration:none; color:inherit;">
                <h3>Payments</h3>
                <p><?= (int)$countPayments ?> payments</p>
            </a>
        </div>


        <h2 style="margin: 2rem 0 1rem;">Room details</h2>
        <p style="color: var(--color-text-muted); margin-bottom: 1rem;">Rooms from database — <a href="rooms.php">Manage rooms</a></p>
        <div class="card-grid" style="margin-bottom: 2rem;">
            <?php foreach ($dashboardRooms as $index => $r):
                $img = getRoomImageDashboard($index, $roomImages);
                $imgPath = '../' . $img;
            ?>
            <div class="room-card">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="Room <?= htmlspecialchars($r['room_no']) ?>" onerror="this.src='../assets/images/room-placeholder.svg'">
                <div class="room-body">
                    <h3>Room <?= htmlspecialchars($r['room_no']) ?></h3>
                    <p class="room-meta"><?= htmlspecialchars($r['h_name'] ?? 'Hostel') ?> · <?= htmlspecialchars($r['type'] ?? '—') ?> (<?= (int)($r['capacity'] ?? 0) ?> Seater)</p>
                    <p class="room-fee">Rs. <?= number_format($r['price'] ?? 0) ?>/month</p>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted);"><?= htmlspecialchars($r['availability'] ?? 'available') ?></p>
                    <a href="rooms.php" class="btn btn-secondary" style="margin-top: 0.5rem; padding: 0.4rem 0.8rem; font-size: 0.9rem;">Manage</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($dashboardRooms)): ?>
        <p style="color: var(--color-text-muted); margin-bottom: 2rem;">No rooms yet. <a href="rooms.php">Add rooms</a>.</p>
        <?php endif; ?>
        </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
