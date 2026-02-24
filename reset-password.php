<?php
$pageTitle = 'Reset Password';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedInAsUser() || isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$token = trim($_GET['token'] ?? '');

if (!$token) {
    header('Location: forgot-password.php');
    exit;
}

$pdo = getDB();
$tokenHash = hash('sha256', $token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT u_id FROM password_reset_tokens WHERE token_hash = ? AND expires_at > NOW()");
            $stmt->execute([$tokenHash]);
            $row = $stmt->fetch();
            if ($row) {
                $hash = hashPassword($password);
                $pdo->prepare("UPDATE users SET u_password = ? WHERE u_id = ?")->execute([$hash, $row['u_id']]);
                $pdo->prepare("DELETE FROM password_reset_tokens WHERE token_hash = ?")->execute([$tokenHash]);
                $success = 'Password updated. You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Invalid or expired reset link. Please <a href="forgot-password.php">request a new one</a>.';
            }
        } catch (Throwable $e) {
            $error = 'Reset failed. The link may have expired. Please <a href="forgot-password.php">request a new one</a>.';
        }
    }
} else {
    try {
        $stmt = $pdo->prepare("SELECT u_id FROM password_reset_tokens WHERE token_hash = ? AND expires_at > NOW()");
        $stmt->execute([$tokenHash]);
        if (!$stmt->fetch()) {
            $error = 'Invalid or expired reset link. Please <a href="forgot-password.php">request a new one</a>.';
        }
    } catch (Throwable $e) {
        $error = 'Invalid reset link. Please <a href="forgot-password.php">request a new one</a>.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Reset Password</h1>
        <div class="form-card" style="max-width: 420px;">
            <h2>Set new password</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if (!$success && !$error): ?>
            <form method="post" action="reset-password.php?token=<?= htmlspecialchars($token) ?>">
                <div class="form-group">
                    <label>New password *</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm password *</label>
                    <input type="password" name="password2" required minlength="6">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update password</button>
                    <a href="login.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            <?php elseif ($success): ?>
            <p><a href="login.php" class="btn btn-primary">Login</a></p>
            <?php else: ?>
            <p><a href="forgot-password.php" class="btn btn-primary">Request new link</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
