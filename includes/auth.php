<?php
/**
 * Authentication - Session, Cookie, Password Hashing (schema_complete.sql tables)
 * Supports: Admin (admin table) and User (users table)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SESSION_USER_KEY', 'user_id');      // u_id for users
define('SESSION_USER_NAME', 'user_name');
define('SESSION_ADMIN_KEY', 'admin_id');   // a_id for admin
define('SESSION_ADMIN_NAME', 'admin_name');
define('SESSION_ROLE', 'role');            // 'user' | 'admin'

function isAdmin() {
    return isset($_SESSION[SESSION_ROLE]) && $_SESSION[SESSION_ROLE] === 'admin';
}

function isLoggedInAsUser() {
    return isset($_SESSION[SESSION_USER_KEY]);
}

function requireLogin() {
    if (!isLoggedInAsUser()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: admin/login.php');
        exit;
    }
}

function getCurrentUserId() {
    return isset($_SESSION[SESSION_USER_KEY]) ? (int)$_SESSION[SESSION_USER_KEY] : null;
}

function getCurrentUserName() {
    return $_SESSION[SESSION_USER_NAME] ?? null;
}

function getCurrentAdminId() {
    return isset($_SESSION[SESSION_ADMIN_KEY]) ? (int)$_SESSION[SESSION_ADMIN_KEY] : null;
}

function getCurrentAdminName() {
    return $_SESSION[SESSION_ADMIN_NAME] ?? null;
}

function loginUser($uId, $userName) {
    $_SESSION[SESSION_USER_KEY] = (int)$uId;
    $_SESSION[SESSION_USER_NAME] = $userName;
    $_SESSION[SESSION_ROLE] = 'user';
}

function loginAdmin($aId, $adminName) {
    $_SESSION[SESSION_ADMIN_KEY] = (int)$aId;
    $_SESSION[SESSION_ADMIN_NAME] = $adminName;
    $_SESSION[SESSION_ROLE] = 'admin';
}

function logoutUser() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/** Check either user or admin is logged in (for nav) */
function isLoggedIn() {
    return isLoggedInAsUser() || isAdmin();
}
 