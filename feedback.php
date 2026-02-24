<?php
$pageTitle = 'Feedback';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$error = '';
$success = '';
$pdo = getDB();
$userId = isLoggedInAsUser() ? getCurrentUserId() : null;
$userName = isLoggedInAsUser() ? getCurrentUserName() : '';
$userEmail = '';
if (isLoggedInAsUser() && $userId) {
    $stmt = $pdo->prepare("SELECT u_email FROM users WHERE u_id = ?");
    $stmt->execute([$userId]);
    $userEmail = $stmt->fetchColumn() ?: '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '') ?: $userName;
    $email = trim($_POST['email'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Rating must be 1–5.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (u_id, name, email, rating, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $email, $rating, $message ?: null]);
            header('Location: feedback.php?success=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Feedback failed. Ensure database/schema_complete.sql is imported.';
        }
    }
}

$success = isset($_GET['success']) && $_GET['success'] == '1';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Feedback</h1>
        <div class="form-card">
            <h2>Send Feedback</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success">Thank you for your feedback!</div><?php endif; ?>
            <form method="post" action="feedback.php">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $userName ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $userEmail ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Rating (1–5) *</label>
                    <select name="rating">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>" <?= ($_POST['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= $i ?> ★</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
