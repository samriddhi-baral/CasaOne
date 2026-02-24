<?php
$pageTitle = 'Rooms';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$stmt = $pdo->query("
    SELECT r.room_id, r.room_no, r.price, r.availability, r.h_id, r.photo,
           rt.type, rt.capacity, h.h_name
    FROM room r
    LEFT JOIN roomtype rt ON rt.room_id = r.room_id
    LEFT JOIN hostel h ON h.h_id = r.h_id
    ORDER BY h.h_name, r.room_no
");
$rooms = $stmt->fetchAll();

// Room images grouped by capacity (stored in project root)
$singleSeatImages = ['room1.jpeg', 'room2.jpeg']; // 1-seater
$doubleSeatImages = ['room3.jpeg', 'room4.jpeg']; // 2-seater
$tripleSeatImages = ['room5.jpeg', 'room6.jpeg']; // 3+ seater

// Default rooms to display if database is empty
$defaultRooms = [
    ['room_no' => '101', 'h_name' => 'CasaOne Hostel', 'type' => 'Single Room', 'capacity' => 1, 'price' => 15000, 'availability' => 'available'],
    ['room_no' => '102', 'h_name' => 'CasaOne Hostel', 'type' => 'Double Sharing', 'capacity' => 2, 'price' => 12000, 'availability' => 'available'],
    ['room_no' => '103', 'h_name' => 'CasaOne Hostel', 'type' => 'Triple Sharing', 'capacity' => 3, 'price' => 11000, 'availability' => 'available'],
    ['room_no' => '104', 'h_name' => 'CasaOne Hostel', 'type' => 'Four Sharing', 'capacity' => 4, 'price' => 10000, 'availability' => 'available'],
    ['room_no' => '105', 'h_name' => 'CasaOne Hostel', 'type' => 'Single Room', 'capacity' => 1, 'price' => 15000, 'availability' => 'available'],
    ['room_no' => '106', 'h_name' => 'CasaOne Hostel', 'type' => 'Double Sharing', 'capacity' => 2, 'price' => 12000, 'availability' => 'available'],
];

// Use default rooms if database is empty
if (empty($rooms)) {
    $rooms = $defaultRooms;
}

/**
 * Choose a room image based on capacity (1, 2, 3+ seater).
 * Falls back to single-seater images if capacity is missing.
 */
function getRoomImageByCapacity($capacity, $index, $singleImages, $doubleImages, $tripleImages) {
    $capacity = (int)$capacity ?: 1;
    if ($capacity <= 1) {
        $images = $singleImages;
    } elseif ($capacity === 2) {
        $images = $doubleImages;
    } else {
        $images = $tripleImages;
    }
    return $images[$index % count($images)];
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Rooms & Fee</h1>
        <div class="card-grid">
            <?php foreach ($rooms as $index => $r):
                $capacity = isset($r['capacity']) ? (int)$r['capacity'] : 1;
                // Use per-room photo if set; otherwise fall back to capacity-based default image
                if (!empty($r['photo'])) {
                    $img = $r['photo'];
                } else {
                    $img = getRoomImageByCapacity($capacity, $index, $singleSeatImages, $doubleSeatImages, $tripleSeatImages);
                }
                $available = (strtolower($r['availability'] ?? '') !== 'occupied');
            ?>
            <div class="room-card">
                <img src="<?= htmlspecialchars($img) ?>" alt="Room <?= htmlspecialchars($r['room_no']) ?>" onerror="this.src='assets/images/room-placeholder.svg'">
                <div class="room-body">
                    <h3>Room <?= htmlspecialchars($r['room_no']) ?></h3>
                    <p class="room-meta"><?= htmlspecialchars($r['h_name'] ?? 'Hostel') ?> · <?= htmlspecialchars($r['type'] ?? '—') ?> (<?= (int)($r['capacity'] ?? 0) ?> Seater)</p>
                    <p class="room-fee">Rs. <?= number_format($r['price'] ?? 0) ?>/month</p>
                    <?php if (isset($r['room_id'])): ?>
                        <?php if (isLoggedInAsUser()): ?>
                        <a href="my-bookings.php?room=<?= (int)$r['room_id'] ?>" class="btn btn-primary" style="margin-top:0.75rem;">Book Now</a>
                        <?php else: ?>
                        <a href="login.php?redirect=<?= urlencode('my-bookings.php') ?>" class="btn btn-secondary" style="margin-top:0.75rem;">Login to Book</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (isLoggedInAsUser()): ?>
                        <a href="my-bookings.php" class="btn btn-primary" style="margin-top:0.75rem;">Apply</a>
                        <?php else: ?>
                        <a href="login.php?redirect=<?= urlencode('my-bookings.php') ?>" class="btn btn-secondary" style="margin-top:0.75rem;">Login to Apply</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($rooms)): ?>
        <p style="text-align:center; color: var(--color-text-muted);">No rooms added yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
