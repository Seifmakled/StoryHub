<?php
// Social interactions API: likes, bookmarks, comments, follows, notifications
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../../config/db.php';
    $conn = Database::getInstance()->getConnection();
}

require_once __DIR__ . '/../services/NotificationService.php';

header('Content-Type: application/json');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$method = $_SERVER['REQUEST_METHOD'];
$notificationService = new NotificationService();

function requireAuth(?int $userId): void {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

try {
    if ($method === 'GET') {
        // Following list for current user
        if (isset($_GET['following_list'])) {
            requireAuth($userId);
            $stmt = $conn->prepare(
                'SELECT u.id, u.username, u.full_name, u.profile_image, u.bio, u.created_at
                 FROM follows f
                 JOIN users u ON u.id = f.followee_id
                 WHERE f.follower_id = ?
                 ORDER BY u.username ASC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        // Followers list for current user
        if (isset($_GET['followers_list'])) {
            requireAuth($userId);
            $stmt = $conn->prepare(
                'SELECT u.id, u.username, u.full_name, u.profile_image, u.bio, u.created_at
                 FROM follows f
                 JOIN users u ON u.id = f.follower_id
                 WHERE f.followee_id = ?
                 ORDER BY u.username ASC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        // Public comments listing for an article
        if (isset($_GET['article_id'])) {
            $articleId = (int)$_GET['article_id'];
            if ($articleId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'article_id required']);
                exit;
            }
            $stmt = $conn->prepare(
                'SELECT c.id, c.content, c.created_at, c.user_id,
                        u.username, u.full_name, u.profile_image
                 FROM comments c
                 JOIN users u ON u.id = c.user_id
                 WHERE c.article_id = ?
                 ORDER BY c.created_at DESC
                 LIMIT 100'
            );
            $stmt->execute([$articleId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        // Notifications for logged-in user
        if (isset($_GET['notifications'])) {
            requireAuth($userId);
            $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 20;
            $stmt = $conn->prepare(
                'SELECT n.id, n.type, n.entity_id, n.message, n.is_read, n.created_at,
                        a.username AS actor_username, a.full_name AS actor_full_name, a.profile_image AS actor_image
                 FROM notifications n
                 LEFT JOIN users a ON a.id = n.actor_id
                 WHERE n.user_id = ?
                 ORDER BY n.created_at DESC
                 LIMIT ?'
            );
            $stmt->execute([$userId, $limit]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Unsupported query']);
        exit;
    }

    if ($method === 'POST') {
        requireAuth($userId);
        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'like') {
            $articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
            if ($articleId <= 0) { http_response_code(400); echo json_encode(['error' => 'article_id required']); exit; }
            // toggle
            $stmt = $conn->prepare('SELECT id, user_id FROM articles WHERE id = ? LIMIT 1');
            $stmt->execute([$articleId]);
            $article = $stmt->fetch();
            if (!$article) { http_response_code(404); echo json_encode(['error' => 'Article not found']); exit; }

            $stmt = $conn->prepare('SELECT id FROM likes WHERE user_id = ? AND article_id = ?');
            $stmt->execute([$userId, $articleId]);
            if ($stmt->fetch()) {
                $conn->prepare('DELETE FROM likes WHERE user_id = ? AND article_id = ?')->execute([$userId, $articleId]);
                echo json_encode(['status' => 'unliked']);
                exit;
            }
            $conn->prepare('INSERT INTO likes (user_id, article_id) VALUES (?, ?)')->execute([$userId, $articleId]);
            $notificationService->createNotification((int)$article['user_id'], $userId, 'like', $articleId, 'liked your story');
            echo json_encode(['status' => 'liked']);
            exit;
        }

        if ($action === 'save') {
            $articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
            if ($articleId <= 0) { http_response_code(400); echo json_encode(['error' => 'article_id required']); exit; }
            $stmt = $conn->prepare('SELECT id, user_id FROM articles WHERE id = ? LIMIT 1');
            $stmt->execute([$articleId]);
            $article = $stmt->fetch();
            if (!$article) { http_response_code(404); echo json_encode(['error' => 'Article not found']); exit; }

            $stmt = $conn->prepare('SELECT id FROM bookmarks WHERE user_id = ? AND article_id = ?');
            $stmt->execute([$userId, $articleId]);
            if ($stmt->fetch()) {
                $conn->prepare('DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?')->execute([$userId, $articleId]);
                echo json_encode(['status' => 'unsaved']);
                exit;
            }
            $conn->prepare('INSERT INTO bookmarks (user_id, article_id) VALUES (?, ?)')->execute([$userId, $articleId]);
            $notificationService->createNotification((int)$article['user_id'], $userId, 'save', $articleId, 'saved your story');
            echo json_encode(['status' => 'saved']);
            exit;
        }

        if ($action === 'comment') {
            $articleId = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            if ($articleId <= 0 || strlen($content) < 2) { http_response_code(400); echo json_encode(['error' => 'article_id and content required']); exit; }
            $stmt = $conn->prepare('SELECT id, user_id FROM articles WHERE id = ? LIMIT 1');
            $stmt->execute([$articleId]);
            $article = $stmt->fetch();
            if (!$article) { http_response_code(404); echo json_encode(['error' => 'Article not found']); exit; }

            $stmt = $conn->prepare('INSERT INTO comments (user_id, article_id, content) VALUES (?, ?, ?)');
            $stmt->execute([$userId, $articleId, $content]);
            $commentId = (int)$conn->lastInsertId();

            $notificationService->createNotification((int)$article['user_id'], $userId, 'comment', $articleId, 'commented on your story');

            $stmt = $conn->prepare('SELECT c.id, c.content, c.created_at, c.user_id, u.username, u.full_name, u.profile_image FROM comments c JOIN users u ON u.id = c.user_id WHERE c.id = ?');
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch();
            echo json_encode(['status' => 'commented', 'data' => $comment]);
            exit;
        }

        if ($action === 'follow') {
            $targetId = isset($_POST['target_id']) ? (int)$_POST['target_id'] : 0;
            if ($targetId <= 0 || $targetId === $userId) { http_response_code(400); echo json_encode(['error' => 'Invalid target']); exit; }
            $stmt = $conn->prepare('SELECT id FROM users WHERE id = ?');
            $stmt->execute([$targetId]);
            if (!$stmt->fetch()) { http_response_code(404); echo json_encode(['error' => 'User not found']); exit; }

            $stmt = $conn->prepare('SELECT id FROM follows WHERE follower_id = ? AND followee_id = ?');
            $stmt->execute([$userId, $targetId]);
            if ($stmt->fetch()) {
                $conn->prepare('DELETE FROM follows WHERE follower_id = ? AND followee_id = ?')->execute([$userId, $targetId]);
                echo json_encode(['status' => 'unfollowed']);
                exit;
            }
            $conn->prepare('INSERT INTO follows (follower_id, followee_id) VALUES (?, ?)')->execute([$userId, $targetId]);
            $notificationService->createNotification($targetId, $userId, 'follow', null, 'started following you');
            echo json_encode(['status' => 'followed']);
            exit;
        }

        if ($action === 'read_notifications') {
            $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')->execute([$userId]);
            echo json_encode(['status' => 'read']);
            exit;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Unsupported action']);
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
