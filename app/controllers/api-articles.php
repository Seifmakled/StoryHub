<?php
// API for articles (minimal: list own, delete)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../../config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

function readJson(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

try {
    if ($method === 'GET') {
        // If mine=1 return only current user's articles
        $mine = isset($_GET['mine']) ? (int)$_GET['mine'] : 0;
        if ($mine === 1) {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.is_published, a.created_at,
                        (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                        (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
                 FROM articles a WHERE a.user_id = ? ORDER BY a.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Unsupported query']);
        exit;
    }

    if ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            exit;
        }

        // Delete only if owned by user
        $stmt = $conn->prepare('DELETE FROM articles WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found or not owned by user']);
            exit;
        }
        echo json_encode(['message' => 'Article deleted']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
