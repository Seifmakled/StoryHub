<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Get the requested page from URL
$page = isset($_GET['url']) ? $_GET['url'] : 'landing';

// Define routes
$routes = [
    '' => __DIR__ . '/app/views/landing.php',
    'landing' => __DIR__ . '/app/views/landing.php',
    'login' => __DIR__ . '/app/views/login.php',
    'register' => __DIR__ . '/app/views/register.php',
    'forgot-password' => __DIR__ . '/app/views/forgot-password.php',
    'verify' => __DIR__ . '/app/views/verify.php',
    'profile' => __DIR__ . '/app/views/profile.php',
    'admin' => __DIR__ . '/app/views/admin-dashboard.php',
    'explore' => __DIR__ . '/app/views/explore.php',
    'article' => __DIR__ . '/app/views/article.php',
    'logout' => __DIR__ . '/app/views/logout.php',
    // API routes
    'api-users' => __DIR__ . '/app/controllers/api-users.php'
];

// Check if route exists
if (array_key_exists($page, $routes)) {
    $file = $routes[$page];
    if (file_exists($file)) {
        include $file;
    } else {
        include __DIR__ . '/app/views/404.php';
    }
} else {
    include __DIR__ . '/app/views/404.php';
}
?>
