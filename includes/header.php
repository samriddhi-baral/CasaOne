<?php
if (!defined('SKIP_CONFIG')) require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Hostel</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <img src="assets/images/casaone-logo.png" alt="Logo">
            </a>
            <nav class="main-nav">
                <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="facilities.php" class="<?= $currentPage === 'facilities' ? 'active' : '' ?>">Facilities</a>
                <a href="rooms.php" class="<?= $currentPage === 'rooms' ? 'active' : '' ?>">Rooms</a>
                <a href="study-hall.php" class="<?= $currentPage === 'study-hall' ? 'active' : '' ?>">Study Hall</a>
                <a href="fees.php" class="<?= $currentPage === 'fees' ? 'active' : '' ?>">Fees</a>
                <a href="complaint.php" class="<?= $currentPage === 'complaint' ? 'active' : '' ?>">Complaint</a>
                <a href="feedback.php" class="<?= $currentPage === 'feedback' ? 'active' : '' ?>">Feedback</a>
                <a href="<?= isLoggedInAsUser() ? 'my-bookings.php' : 'login.php?redirect=' . urlencode('my-bookings.php') ?>" class="<?= $currentPage === 'my-bookings' ? 'active' : '' ?>">Bookings</a>
                <?php if (isLoggedInAsUser()): ?>
                    
                    <a href="logout.php" class="btn-nav">Logout</a>
                <?php else: ?>
                    <a href="admission.php">Admission</a>
                    <a href="login.php" class="btn-nav">Login</a>
                <?php endif; ?>
            </nav>
            <button class="nav-toggle" aria-label="Menu">☰</button>
        </div>
    </header>
    <main class="main-content">
 