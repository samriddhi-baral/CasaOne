<?php
$pageTitle = 'FAQ';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$faqs = [];
try {
    $stmt = $pdo->query("SELECT * FROM faq ORDER BY sort_order, id");
    $faqs = $stmt->fetchAll();
} catch (Throwable $e) {
    $faqs = [
        ['question' => 'How do I book a room?', 'answer' => 'Go to the Booking page to select a room and check-in date. You can also apply from the Rooms page.'],
        ['question' => 'What types of rooms are available?', 'answer' => 'See the Rooms page for room types, capacity, and availability.'],
        ['question' => 'What is the fee structure?', 'answer' => 'See the Fee page for monthly and annual rates by room type.'],
    ];
}
$faqLinks = ['booking.php', 'rooms.php', 'fees.php'];
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Frequently Asked Questions</h1>
        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): 
                $link = isset($faqLinks[$i]) ? $faqLinks[$i] : null;
            ?>
            <details class="faq-item">
                <summary>
                    <?php if ($link): ?><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($faq['question']) ?></a><?php else: ?><?= htmlspecialchars($faq['question']) ?><?php endif; ?>
                </summary>
                <div class="faq-answer"><?= nl2br(htmlspecialchars($faq['answer'] ?? '')) ?></div>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
 