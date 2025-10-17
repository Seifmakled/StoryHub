<?php
$pageTitle = '404 - Page Not Found';
$pageDescription = 'Page not found';
$pageCSS = 'landing.css';

include '../partials/header.php';
include '../partials/navbar.php';
?>

<div style="text-align: center; padding: 5rem 2rem; min-height: 60vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
    <h1 style="font-size: 6rem; color: #6366f1; margin: 0;">404</h1>
    <h2 style="font-size: 2rem; color: #1e293b; margin: 1rem 0;">Page Not Found</h2>
    <p style="color: #64748b; margin-bottom: 2rem;">The page you're looking for doesn't exist or has been moved.</p>
    <a href="index.php?page=home" style="padding: 1rem 2rem; background: #6366f1; color: white; text-decoration: none; border-radius: 12px; font-weight: 600;">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>

<?php include '../partials/footer.php'; ?>
