<?php
$pageTitle = 'Forgot Password';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedInAsUser() || isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $pdo = getDB();
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                u_id INT NOT NULL,
                token_hash VARCHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                KEY (token_hash),
                KEY (expires_at),
                FOREIGN KEY (u_id) REFERENCES users(u_id) ON DELETE CASCADE
            )");
        } catch (Throwable $e) { }

        $stmt = $pdo->prepare("SELECT u_id FROM users WHERE u_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("INSERT INTO password_reset_tokens (u_id, token_hash, expires_at) VALUES (?, ?, ?)")->execute([$user['u_id'], $tokenHash, $expires]);
            $success = 'If this email is registered, a reset link has been generated. <a href="reset-password.php?token=' . urlencode($token) . '">Click here to reset your password</a>. Link expires in 1 hour.';
        } else {
            $success = 'If this email is registered, you will receive a reset link. Check your email or try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Forgot Password</h1>
        <div class="form-card" style="max-width: 420px;">
            <h2>Reset your password</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if (!$success): ?>
            <form method="post" action="forgot-password.php">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Send reset link</button>
                    <a href="login.php" class="btn btn-secondary">Back to Login</a>
                </div>
            </form>
            <?php else: ?>
            <p><a href="login.php" class="btn btn-primary">Back to Login</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>