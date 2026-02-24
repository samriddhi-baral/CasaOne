<?php

$pageTitle = 'Booking';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$query = isset($_GET['room']) ? '?room=' . (int)$_GET['room'] : '';
header('Location: my-bookings.php' . $query);
exit;
$error = '';
$success = '';

$pdo = getDB();
$roomId = isset($_GET['room']) ? (int)$_GET['room'] : 0;
$stmt = $pdo->query("
    SELECT r.room_id, r.room_no, r.price, r.h_id, h.h_name
    FROM room r
    LEFT JOIN hostel h ON h.h_id = r.h_id
    WHERE (r.availability IS NULL OR LOWER(r.availability) != 'occupied')
    ORDER BY r.room_no
");
$rooms = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = (int)($_POST['room_id'] ?? 0);
    $check_in = trim($_POST['check_in_date'] ?? '');
    $userId = getCurrentUserId();

    if (!$room_id || !$check_in) {
        $error = 'Please select room and check-in date.';
    } else {
        $stmt = $pdo->prepare("SELECT room_id, price, h_id FROM room WHERE room_id = ? AND (availability IS NULL OR LOWER(availability) != 'occupied')");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch();
        if (!$room) {
            $error = 'Invalid or unavailable room.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO booking (u_id, room_id, h_id, status, amount, book_date, check_in) VALUES (?, ?, ?, 'pending', ?, CURDATE(), ?)");
                $stmt->execute([$userId, $room_id, $room['h_id'], $room['price'], $check_in]);
                $success = 'Booking request submitted. Pay to confirm. <a href="my-bookings.php">View My Bookings</a>';
            } catch (PDOException $e) {
                $error = 'Booking failed. Ensure database/schema_complete.sql is imported.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Book a Room</h1>
        <div class="form-card" style="max-width: 560px;">
            <h2>New Booking</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if (empty($rooms)): ?>
            <p class="alert alert-info">No rooms available for booking.</p>
            <?php else: ?>
            <form method="post" action="booking.php">
                <div class="form-group">
                    <label>Room *</label>
                    <select name="room_id" required>
                        <option value="">Select room</option>
                        <?php foreach ($rooms as $r): ?>
                        <option value="<?= (int)$r['room_id'] ?>" <?= ($roomId && $roomId == $r['room_id']) ? 'selected' : '' ?>>
                            Room <?= htmlspecialchars($r['room_no']) ?> — <?= htmlspecialchars($r['h_name'] ?? '') ?> — Rs.<?= number_format($r['price']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" name="check_in_date" value="<?= htmlspecialchars($_POST['check_in_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                    <a href="rooms.php" class="btn btn-secondary">View Rooms</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
