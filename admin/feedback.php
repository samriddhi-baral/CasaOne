<?php
$pageTitle = 'Feedback';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

try {
    $feedbacks = $pdo->query("
        SELECT f.*, u.u_name as user_name, u.u_email as user_email
        FROM feedback f
        LEFT JOIN users u ON u.u_id = f.u_id
        ORDER BY f.created_at DESC, f.f_id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $feedbacks = [];
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">User Feedback</h1>
        <p style="color: var(--color-text-muted); margin-bottom: 1.5rem;">Feedback submitted by users from the Feedback page.</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rating</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $f): ?>
                    <tr>
                        <td><?= (int)$f['f_id'] ?></td>
                        <td><?= htmlspecialchars($f['name'] ?? '—') ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($f['email'] ?? '') ?>"><?= htmlspecialchars($f['email'] ?? '—') ?></a></td>
                        <td>
                            <span class="feedback-rating" title="<?= (int)($f['rating'] ?? 0) ?> out of 5">
                                <?php $rating = (int)($f['rating'] ?? 0); for ($i = 1; $i <= 5; $i++): ?>
                                <span style="color: <?= $i <= $rating ? '#f0c14b' : '#ddd' ?>;">★</span>
                                <?php endfor; ?>
                                (<?= $rating ?>)
                            </span>
                        </td>
                        <td><?= nl2br(htmlspecialchars($f['message'] ?? '—')) ?></td>
                        <td><?= htmlspecialchars($f['created_at'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($feedbacks)): ?>
        <p style="color: var(--color-text-muted);">No feedback yet.</p>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
