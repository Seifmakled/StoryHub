<?php
// API for authenticated user's own data
// Endpoints via index.php?url=api-me

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../../config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
}

header('Content-Type: application/json');

// Require authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// Helpers
function readJsonBodyAssoc(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

try {
    if ($method === 'GET') {
        $section = isset($_GET['section']) ? $_GET['section'] : 'overview';

        if ($section === 'overview') {
            // user details
            $stmt = $conn->prepare('SELECT id, username, email, full_name, bio, profile_image, created_at FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                exit;
            }

            // counts
            $counts = [
                'articles' => 0,
                'likes' => 0,
                'saved' => 0,
                'comments' => 0,
            ];

            $counts['articles'] = (int)$conn->query("SELECT COUNT(*) AS c FROM articles WHERE user_id = $userId")->fetch()['c'];
            $counts['likes'] = (int)$conn->query("SELECT COUNT(*) AS c FROM likes WHERE user_id = $userId")->fetch()['c'];
            // bookmarks table is our saved
            $counts['saved'] = (int)$conn->query("SELECT COUNT(*) AS c FROM bookmarks WHERE user_id = $userId")->fetch()['c'];
            $counts['comments'] = (int)$conn->query("SELECT COUNT(*) AS c FROM comments WHERE user_id = $userId")->fetch()['c'];

            echo json_encode(['user' => $user, 'counts' => $counts]);
            exit;
        }

        if ($section === 'articles') {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.is_published, a.created_at,
                        (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                        (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
                 FROM articles a
                 WHERE a.user_id = ?
                 ORDER BY a.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        if ($section === 'saved') {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.created_at, b.created_at AS saved_at
                 FROM bookmarks b
                 JOIN articles a ON a.id = b.article_id
                 WHERE b.user_id = ?
                 ORDER BY b.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        if ($section === 'liked') {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.created_at, l.created_at AS liked_at
                 FROM likes l
                 JOIN articles a ON a.id = l.article_id
                 WHERE l.user_id = ?
                 ORDER BY l.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        if ($section === 'comments') {
            $stmt = $conn->prepare(
                'SELECT c.id, c.content, c.created_at, c.updated_at, a.id AS article_id, a.title AS article_title
                 FROM comments c
                 JOIN articles a ON a.id = c.article_id
                 WHERE c.user_id = ?
                 ORDER BY c.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Invalid section']);
        exit;
    }

    if ($method === 'POST') {
        // Update profile details: full_name, bio, email
        $data = readJsonBodyAssoc();
        $fields = [];
        $values = [];

        if (isset($data['full_name'])) {
            $fields[] = 'full_name = ?';
            $values[] = trim((string)$data['full_name']);
        }
        if (isset($data['bio'])) {
            $fields[] = 'bio = ?';
            $values[] = trim((string)$data['bio']);
        }
        if (isset($data['email'])) {
            $email = trim((string)$data['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email address']);
                exit;
            }
            // ensure not used by another user
            $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(['error' => 'Email already in use']);
                exit;
            }
            $fields[] = 'email = ?';
            $values[] = $email;
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'No updatable fields provided']);
            exit;
        }

        $values[] = $userId;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->execute($values);

        echo json_encode(['message' => 'Profile updated']);
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
