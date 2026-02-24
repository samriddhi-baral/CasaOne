<?php
$pageTitle = 'Hostels';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

$hostels = $pdo->query("SELECT h.*, (SELECT COUNT(*) FROM room r WHERE r.h_id = h.h_id) as room_count FROM hostel h ORDER BY h.h_id")->fetchAll();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Hostels</h1>
        

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
