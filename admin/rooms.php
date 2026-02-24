<?php
$pageTitle = 'Rooms';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $h_id = (int)($_POST['h_id'] ?? 0);
        $room_no = trim($_POST['room_no'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $availability = trim($_POST['availability'] ?? 'available') ?: 'available';

        // Handle optional room photo upload
        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/rooms/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = 'room_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $fullPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath)) {
                // store relative path from project root
                $photoPath = 'uploads/rooms/' . $fileName;
            }
        }

        if ($h_id && $room_no !== '') {
            // Insert room with optional photo path
            $stmt = $pdo->prepare("INSERT INTO room (h_id, room_no, price, availability, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$h_id, $room_no, $price, $availability, $photoPath]);
            $r_id = $pdo->lastInsertId();

            $type = trim($_POST['type'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            if ($type !== '' || $capacity > 0) {
                $pdo->prepare("INSERT INTO roomtype (room_id, type, capacity) VALUES (?, ?, ?)")->execute([$r_id, $type ?: null, $capacity ?: null]);
            }
            $success = 'Room added.';
        } else {
            $error = 'Hostel and room number required.';
        }
    } elseif ($_POST['action'] === 'update_avail' && isset($_POST['room_id'], $_POST['availability'])) {
        $pdo->prepare("UPDATE room SET availability = ? WHERE room_id = ?")->execute([$_POST['availability'], (int)$_POST['room_id']]);
        $success = 'Availability updated.';
    } elseif ($_POST['action'] === 'delete' && isset($_POST['room_id'])) {
        $roomId = (int)$_POST['room_id'];
        try {
            // Only allow delete when there are no active bookings for this room
            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS cnt
                FROM booking
                WHERE room_id = ?
                  AND status IN ('pending','paid','confirmed','checked_in')
            ");
            $stmt->execute([$roomId]);
            $active = (int)$stmt->fetchColumn();

            if ($active > 0) {
                $error = 'Cannot delete: room has active bookings.';
            } else {
                // Remove checked-out / historical bookings for this room, then delete room
                $pdo->prepare("DELETE FROM booking WHERE room_id = ?")->execute([$roomId]);
                $pdo->prepare("DELETE FROM roomtype WHERE room_id = ?")->execute([$roomId]);
                $pdo->prepare("DELETE FROM room WHERE room_id = ?")->execute([$roomId]);
                $success = 'Room deleted.';
            }
        } catch (PDOException $e) {
            $error = 'Cannot delete room.';
        }
    }
}

try {
    $rooms = $pdo->query("
        SELECT r.room_id, r.room_no, r.price, r.availability, r.h_id, r.photo,
               rt.type, rt.capacity, h.h_name
        FROM room r
        LEFT JOIN roomtype rt ON rt.room_id = r.room_id
        LEFT JOIN hostel h ON h.h_id = r.h_id
        ORDER BY h.h_name, r.room_no
    ")->fetchAll();
    if ($rooms && count($rooms) > 1) {
        $seen = [];
        $rooms = array_values(array_filter($rooms, function ($row) use (&$seen) {
            $id = (int)$row['room_id'];
            if (isset($seen[$id])) return false;
            $seen[$id] = true;
            return true;
        }));
    }
} catch (PDOException $e) {
    $rooms = [];
    if ($error === '') $error = 'Could not load rooms from database. Please add a room above.';
}
$hostels = $pdo->query("SELECT h_id, h_name FROM hostel ORDER BY h_name")->fetchAll();

/**
 * Choose an admin preview image based on capacity:
 *  - 1 seater  → room1 / room2
 *  - 2 seater  → room3 / room4
 *  - 3+ seater → room5 / room6
 */
function getRoomImageAdminByCapacity($capacity, $index = 0) {
    $capacity = (int)$capacity ?: 1;

    if ($capacity <= 1) {
        $images = ['room1.jpeg', 'room2.jpeg'];
    } elseif ($capacity === 2) {
        $images = ['room3.jpeg', 'room4.jpeg'];
    } else {
        $images = ['room5.jpeg', 'room6.jpeg'];
    }

    return $images[$index % count($images)];
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Rooms</h1>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <h2 style="margin-bottom: 1rem;">Room details</h2>
        <div class="card-grid" style="margin-bottom: 2rem;">
            <?php foreach ($rooms as $index => $r):
                $capacity = isset($r['capacity']) ? (int)$r['capacity'] : 1;
                // Use uploaded photo if available, otherwise capacity-based preview image
                if (!empty($r['photo'])) {
                    $imgPath = '../' . ltrim($r['photo'], '/');
                } else {
                    $img = getRoomImageAdminByCapacity($capacity, $index);
                    $imgPath = '../' . $img;
                }
            ?>
            <div class="room-card">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="Room <?= htmlspecialchars($r['room_no']) ?>" onerror="this.src='../assets/images/room-placeholder.svg'">
                <div class="room-body">
                    <h3>Room <?= htmlspecialchars($r['room_no']) ?></h3>
                    <p class="room-meta"><?= htmlspecialchars($r['h_name'] ?? 'Hostel') ?> · <?= htmlspecialchars($r['type'] ?? '—') ?> (<?= (int)($r['capacity'] ?? 0) ?> Seater)</p>
                    <p class="room-fee">Rs. <?= number_format($r['price'] ?? 0) ?>/month</p>
                    <form method="post" style="margin-top: 0.75rem;">
                        <input type="hidden" name="action" value="update_avail">
                        <input type="hidden" name="room_id" value="<?= (int)$r['room_id'] ?>">
                        <select name="availability" onchange="this.form.submit()" style="margin-bottom: 0.5rem;">
                            <option value="available" <?= ($r['availability'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="occupied" <?= ($r['availability'] ?? '') === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                        </select>
                    </form>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this room?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="room_id" value="<?= (int)$r['room_id'] ?>">
                        <button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($rooms)): ?>
        <p style="color: var(--color-text-muted); margin-bottom: 2rem;">No rooms yet. Add a hostel and room below.</p>
        <?php endif; ?>

        <?php if (!empty($hostels)): ?>
        <div class="form-card" style="max-width: 500px; margin-bottom: 2rem;">
            <h2>Add Room</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Hostel *</label>
                    <select name="h_id" required>
                        <?php foreach ($hostels as $h): ?>
                        <option value="<?= (int)$h['h_id'] ?>"><?= htmlspecialchars($h['h_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Room No *</label>
                    <input type="text" name="room_no" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" value="0">
                </div>
                <div class="form-group">
                    <label>Availability</label>
                    <select name="availability">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type (roomtype)</label>
                    <input type="text" name="type" placeholder="e.g. Single">
                </div>
                <div class="form-group">
                    <label>Capacity</label>
                    <input type="number" name="capacity" min="0">
                </div>
                <div class="form-group">
                    <label>Room Photo</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hostel</th>
                        <th>Room No</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $r): ?>
                    <tr>
                        <td><?= (int)$r['room_id'] ?></td>
                        <td><?= htmlspecialchars($r['h_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['room_no'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($r['type'] ?? '—') ?></td>
                        <td><?= (int)($r['capacity'] ?? 0) ?></td>
                        <td>Rs. <?= number_format($r['price'] ?? 0) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update_avail">
                                <input type="hidden" name="room_id" value="<?= (int)$r['room_id'] ?>">
                                <select name="availability" onchange="this.form.submit()">
                                    <option value="available" <?= ($r['availability'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                                    <option value="occupied" <?= ($r['availability'] ?? '') === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Delete this room?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="room_id" value="<?= (int)$r['room_id'] ?>">
                                <button type="submit" class="btn btn-secondary" style="padding:0.3rem 0.6rem; font-size:0.85rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($rooms)): ?>
        <p style="color: var(--color-text-muted);">No rooms yet. <?= empty($hostels) ? 'Add a hostel first, then ' : '' ?>add a room above.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
