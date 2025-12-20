<?php
$pageTitle = 'Notifications - StoryHub';
$pageDescription = 'Your recent activity';
$pageCSS = 'notifications.css';
$pageJS = 'notifications.js';

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/navbar.php';
?>

<div class="notif-shell">
    <div class="notif-header">
        <div>
            <h1>Notifications</h1>
            <p class="muted">Latest interactions on your stories and profile.</p>
        </div>
        <button class="btn" id="markReadBtn">Mark all as read</button>
    </div>
    <div id="notifList" class="notif-list">
        <div class="empty-state loading">Loading notifications...</div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
