<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../../config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $stats = [];

    $stats['users_total'] = (int)$conn->query('SELECT COUNT(*) AS c FROM users')->fetch()['c'];
    $stats['users_banned'] = (int)$conn->query("SELECT COUNT(*) AS c FROM users WHERE status = 'banned'")->fetch()['c'];

    $stats['articles_total'] = (int)$conn->query('SELECT COUNT(*) AS c FROM articles')->fetch()['c'];
    $stats['articles_published'] = (int)$conn->query("SELECT COUNT(*) AS c FROM articles WHERE status = 'approved' AND is_published = 1")->fetch()['c'];
    $stats['articles_pending'] = (int)$conn->query("SELECT COUNT(*) AS c FROM articles WHERE status = 'pending'")->fetch()['c'];
    $stats['articles_rejected'] = (int)$conn->query("SELECT COUNT(*) AS c FROM articles WHERE status = 'rejected'")->fetch()['c'];

    $stats['comments_total'] = (int)$conn->query('SELECT COUNT(*) AS c FROM comments')->fetch()['c'];
    $stats['likes_total'] = (int)$conn->query('SELECT COUNT(*) AS c FROM likes')->fetch()['c'];

    $stats['views_total'] = (int)($conn->query('SELECT SUM(views) AS s FROM articles')->fetch()['s'] ?? 0);

    echo json_encode(['data' => $stats]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
