<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container">
        <h1>Welcome to CasaOne</h1>
        <p>Your go-to home platform</p>
        <?php if (!isLoggedInAsUser()): ?>
        <a href="admission.php" class="btn btn-primary">Apply for Admission</a>
        <a href="login.php" class="btn btn-secondary">Login</a>
        <?php else: ?>
        <a href="booking.php" class="btn btn-primary">Book a Room</a>
        <a href="rooms.php" class="btn btn-secondary">View Rooms</a>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="card-grid">
            <div class="card">
                <h3>Safe & Secure</h3>
                <p>24/7 security and CCTV. Peace of mind for you and your family.</p>
            </div>
            <div class="card">
                <h3>Student-Friendly</h3>
                <p>Designed for students with study hall, WiFi, and disciplined routine.</p>
            </div>
            <div class="card">
                <h3>Affordable</h3>
                <p>Fees according to seaters. Monthly and yearly payment options with discounts.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">FAQs</h2>
        <div class="faq-list" style="max-width: 600px; margin: 0 auto;">
            <div class="card" style="margin-bottom: 0.75rem;">
                <a href="booking.php" style="display:block; padding: 0.75rem 1rem; text-decoration: none; color: inherit; font-weight: 500;">How do I book a room?</a>
                <p style="margin: 0 1rem 0.75rem; font-size: 0.9rem; color: var(--color-text-muted);">Go to the Booking page to select a room and check-in date. You can also apply from the Rooms page.</p>
            </div>
            <div class="card" style="margin-bottom: 0.75rem;">
                <a href="rooms.php" style="display:block; padding: 0.75rem 1rem; text-decoration: none; color: inherit; font-weight: 500;">What types of rooms are available?</a>
                <p style="margin: 0 1rem 0.75rem; font-size: 0.9rem; color: var(--color-text-muted);">See the Rooms page for room types, capacity, and availability.</p>
            </div>
            <div class="card" style="margin-bottom: 0.75rem;">
                <a href="fees.php" style="display:block; padding: 0.75rem 1rem; text-decoration: none; color: inherit; font-weight: 500;">What is the fee structure?</a>
                <p style="margin: 0 1rem 0.75rem; font-size: 0.9rem; color: var(--color-text-muted);">See the Fee page for monthly and annual rates by room type.</p>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
