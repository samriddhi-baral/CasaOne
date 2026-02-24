<?php
$pageTitle = 'Login';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isAdmin()) {
    header('Location: admin/');
    exit;
}
if (isLoggedInAsUser()) {
    header('Location: ' . ($_GET['redirect'] ?? 'index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_GET['redirect'] ?? 'index.php';
    $login_as = $_POST['login_as'] ?? 'user'; // 'user' | 'admin'

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        $pdo = getDB();

        if ($login_as === 'admin') {
            $stmt = $pdo->prepare("SELECT a_id, a_name, a_password FROM admin WHERE a_email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            if ($admin) {
                $valid = verifyPassword($password, $admin['a_password']);
                if (!$valid && $admin['a_password'] === $password) {
                    $valid = true;
                    $hash = hashPassword($password);
                    $pdo->prepare("UPDATE admin SET a_password = ? WHERE a_id = ?")->execute([$hash, $admin['a_id']]);
                }
                if ($valid) {
                    loginAdmin((int)$admin['a_id'], $admin['a_name']);
                    header('Location: admin/');
                    exit;
                }
            }
            $error = 'Invalid admin email or password.';
        } else {
            $stmt = $pdo->prepare("SELECT u_id, u_name, u_password FROM users WHERE u_email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && verifyPassword($password, $user['u_password'])) {
                loginUser((int)$user['u_id'], $user['u_name']);
                header('Location: ' . (strpos($redirect, '/') === 0 ? $redirect : $redirect));
                exit;
            }
            $error = 'Invalid email or password.';
        }
    }
}

$redirect = $_GET['redirect'] ?? 'index.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">Login</h1>
        <div class="form-card">
            <h2>Sign In</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post" action="login.php?<?= htmlspecialchars(http_build_query(['redirect' => $redirect])) ?>">
                <div class="form-group">
                    <label>Login as</label>
                    <select name="login_as">
                        <option value="user">User</option>
                        <option value="admin" <?= ($_POST['login_as'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="admission.php" class="btn btn-secondary">Register</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
