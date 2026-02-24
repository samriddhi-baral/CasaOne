<?php
require_once __DIR__ . '/includes/auth.php';
logoutUser();
if (isset($_GET['admin']) && $_GET['admin'] === '1') {
    header('Location: admin/login.php');
} else {
    header('Location: index.php');
}
exit;
 