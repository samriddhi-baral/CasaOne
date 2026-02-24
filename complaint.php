<?php
$pageTitle = 'Complaint';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

$error = ''; 
$success = '';
$userId = isLoggedInAsUser() ? getCurrentUserId() : null;
$userName = isLoggedInAsUser() ? getCurrentUserName() : '';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c_name = trim($_POST['c_name'] ?? '') ?: $userName;
    $description = trim($_POST['description'] ?? '');

    if (!$description) {
        $error = 'Description is required.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO complaint (a_id, u_id, c_name, description, status, c_date) VALUES (NULL, ?, ?, ?, 'open', CURDATE())");
            $stmt->execute([$userId, $c_name ?: null, $description]);
            $success = 'Complaint submitted. We will get back to you soon.';
        } catch (PDOException $e) {
            $error = 'Failed to submit complaint.';
        }
    }
}
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Submit a Complaint</h1>
        <div class="form-card">
            <h2>Complaint Form</h2>
            <?php if ($error && !$success): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <form method="post" action="complaint.php">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="c_name" value="<?= htmlspecialchars($_POST['c_name'] ?? $userName ?? '') ?>" placeholder="Your name">
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
