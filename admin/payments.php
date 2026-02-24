<?php
$pageTitle = 'Payments';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
try {
    $payments = $pdo->query("
        SELECT p.*, u.u_name, u.u_email
        FROM payment p
        LEFT JOIN users u ON u.u_id = p.u_id
        ORDER BY p.pay_date DESC, p.p_id DESC
    ")->fetchAll();
} catch (Throwable $e) {
    $payments = $pdo->query("
        SELECT p.p_id, p.u_id, p.amount, p.pay_date, u.u_name, u.u_email
        FROM payment p
        LEFT JOIN users u ON u.u_id = p.u_id
        ORDER BY p.pay_date DESC, p.p_id DESC
    ")->fetchAll();
}
$total = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payment")->fetchColumn();
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Payments</h1>
        <p style="color: var(--color-text-muted); margin-bottom:1rem;">Total: Rs.<?= number_format($total) ?></p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?= (int)$p['p_id'] ?></td>
                        <td><?= htmlspecialchars($p['u_name'] ?? '—') ?> (<?= htmlspecialchars($p['u_email'] ?? '—') ?>)</td>
                        <td>Rs.<?= number_format($p['amount'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($p['pay_type'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($p['pay_date'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($payments)): ?>
        <p style="color: var(--color-text-muted);">No payments yet.</p>
        <?php endif; ?>
    </div>
</section> 
<?php require_once __DIR__ . '/includes/footer.php'; ?>
