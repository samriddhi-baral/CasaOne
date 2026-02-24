<?php
$pageTitle = 'Admin Login';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        $pdo = getDB();
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
                header('Location: index.php');
                exit;
            }
        }
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Hostel Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="section-title">Admin Login</h1>
            <div class="form-card" style="max-width: 400px; margin: 0 auto;">
                <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="post" action="login.php">
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
                    </div>
                </form>
                <p style="text-align:center; margin-top:1rem;"><a href="../login.php">Login as User</a></p>
            </div>
        </div>
    </section>
</body>
</html>
 