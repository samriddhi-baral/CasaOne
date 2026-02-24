<?php
if (!defined('SKIP_CONFIG')) require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        .admin-nav { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
        .admin-nav a { color: rgba(255, 255, 255, 0.85); text-decoration: none; padding: 0.5rem 0.75rem; border-radius: 8px; font-weight: 500; }
        .admin-nav a:hover, .admin-nav a.active { color: #fff; background: rgba(255, 255, 255, 0.2); }
        .admin-nav .btn-nav { background: #fff !important; color: var(--color-primary) !important; font-weight: 600 !important; }
        .admin-nav .btn-nav:hover { background: #ffd1f5 !important; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <img src="../assets/images/casaone-logo.png" alt="Logo">
            </a>
            <nav class="main-nav admin-nav">
                <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Dashboard</a>
                <a href="hostels.php" class="<?= $currentPage === 'hostels' ? 'active' : '' ?>">Hostels</a>
                <a href="rooms.php" class="<?= $currentPage === 'rooms' ? 'active' : '' ?>">Rooms</a>
                <a href="bookings.php" class="<?= $currentPage === 'bookings' ? 'active' : '' ?>">Bookings</a>
                <a href="complaints.php" class="<?= $currentPage === 'complaints' ? 'active' : '' ?>">Complaints</a>
                <a href="feedback.php" class="<?= $currentPage === 'feedback' ? 'active' : '' ?>">Feedback</a>
                <a href="payments.php" class="<?= $currentPage === 'payments' ? 'active' : '' ?>">Payments</a>
                <a href="users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">Users</a>
                <a href="../logout.php?admin=1" class="btn-nav">Logout</a>
            </nav>
        </div>
    </header>
    <main class="main-content">
