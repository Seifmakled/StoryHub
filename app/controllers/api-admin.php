<?php
// Aggregated admin dashboard API (read-only)
// Endpoint: index.php?url=api-admin

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../services/NotificationService.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $conn = Database::getInstance()->getConnection();

    if ($method === 'POST') {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        $action = $payload['action'] ?? '';
        $articleId = isset($payload['article_id']) ? (int)$payload['article_id'] : 0;
        $commentId = isset($payload['comment_id']) ? (int)$payload['comment_id'] : 0;

        if (in_array($action, ['take_down','flag'], true)) {
            if (!$articleId) {
                http_response_code(400);
                echo json_encode(['error' => 'article_id is required']);
                exit;
            }

            // Fetch article with author for actions
            $stmt = $conn->prepare('SELECT id, user_id, title, slug, is_published FROM articles WHERE id = ? LIMIT 1');
            $stmt->execute([$articleId]);
            $article = $stmt->fetch();
            if (!$article) {
                http_response_code(404);
                echo json_encode(['error' => 'Article not found']);
                exit;
            }

            if ($action === 'take_down') {
                $update = $conn->prepare('UPDATE articles SET is_published = 0 WHERE id = ?');
                $update->execute([$articleId]);
                echo json_encode(['message' => 'Article taken down']);
                exit;
            }

            if ($action === 'flag') {
                $actorId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
                $service = new NotificationService();
                $title = $article['title'] ?: 'your article';
                $service->createNotification((int)$article['user_id'], $actorId, 'flag', $articleId, "Your article '{$title}' was flagged by an admin.");
                echo json_encode(['message' => 'Author flagged and notified']);
                exit;
            }
        }

        if (in_array($action, ['comment_delete','comment_flag'], true)) {
            if (!$commentId) {
                http_response_code(400);
                echo json_encode(['error' => 'comment_id is required']);
                exit;
            }
            $stmt = $conn->prepare('SELECT c.id, c.user_id, c.article_id, c.content, u.username FROM comments c JOIN users u ON u.id = c.user_id WHERE c.id = ? LIMIT 1');
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch();
            if (!$comment) {
                http_response_code(404);
                echo json_encode(['error' => 'Comment not found']);
                exit;
            }

            if ($action === 'comment_delete') {
                $del = $conn->prepare('DELETE FROM comments WHERE id = ?');
                $del->execute([$commentId]);
                echo json_encode(['message' => 'Comment removed']);
                exit;
            }

            if ($action === 'comment_flag') {
                $actorId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
                $service = new NotificationService();
                $excerpt = mb_substr($comment['content'], 0, 80);
                $service->createNotification((int)$comment['user_id'], $actorId, 'flag', $commentId, "Your comment was flagged by an admin: {$excerpt}");
                echo json_encode(['message' => 'Commenter flagged and notified']);
                exit;
            }
        }

        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
        exit;
    }

    // GET: dashboard aggregates
    $safeScalar = function(string $sql, array $params = [], $fallback = 0) use ($conn) {
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return $fallback;
        }
    };

    $totalUsers = (int)$safeScalar('SELECT COUNT(*) FROM users');
    $totalArticles = (int)$safeScalar('SELECT COUNT(*) FROM articles WHERE is_published = 1');
    $totalViews = (int)$safeScalar('SELECT COALESCE(SUM(views), 0) FROM articles');
    $totalComments = (int)$safeScalar('SELECT COUNT(*) FROM comments', [], 0);

    // Growth: last 7 days new users and new articles
    $labels = [];
    $dailyArticles = [];
    $dailyUsers = [];
    for ($i = 6; $i >= 0; $i--) {
        $labels[] = date('M j', strtotime("-$i days"));
        $day = date('Y-m-d', strtotime("-$i days"));
        $dailyArticles[$day] = 0;
        $dailyUsers[$day] = 0;
    }

    $articleStmt = $conn->prepare('SELECT DATE(created_at) as d, COUNT(*) as c FROM articles WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at)');
    $articleStmt->execute();
    foreach ($articleStmt->fetchAll() as $row) {
        $day = $row['d'];
        if (isset($dailyArticles[$day])) {
            $dailyArticles[$day] = (int)$row['c'];
        }
    }

    $userStmt = $conn->prepare('SELECT DATE(created_at) as d, COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at)');
    $userStmt->execute();
    foreach ($userStmt->fetchAll() as $row) {
        $day = $row['d'];
        if (isset($dailyUsers[$day])) {
            $dailyUsers[$day] = (int)$row['c'];
        }
    }

    // Category distribution of published articles
    $categoryStmt = $conn->prepare('SELECT category, COUNT(*) AS c FROM articles WHERE is_published = 1 GROUP BY category ORDER BY c DESC');
    $categoryStmt->execute();
    $categoryLabels = [];
    $categoryCounts = [];
    foreach ($categoryStmt->fetchAll() as $row) {
        $categoryLabels[] = $row['category'] ?: 'uncategorized';
        $categoryCounts[] = (int)$row['c'];
    }

    // Recent published articles (latest 8) - drafts excluded
    $recentArticlesStmt = $conn->prepare(
        'SELECT a.id, a.title, a.slug, a.category, a.views, a.is_published, a.created_at,
                u.username AS author
         FROM articles a
         JOIN users u ON u.id = a.user_id
         WHERE a.is_published = 1
         ORDER BY a.created_at DESC
         LIMIT 8'
    );
    $recentArticlesStmt->execute();
    $recentArticles = $recentArticlesStmt->fetchAll();

    // Recent users (latest 8)
    $recentUsersStmt = $conn->prepare(
        'SELECT id, username, email, created_at, is_admin
         FROM users
         ORDER BY created_at DESC
         LIMIT 8'
    );
    $recentUsersStmt->execute();
    $recentUsers = $recentUsersStmt->fetchAll();

    // Recent comments (latest 50)
    $recentCommentsStmt = $conn->prepare(
        'SELECT c.id, c.content, c.created_at, c.user_id, c.article_id,
                u.username AS author,
                a.title AS article_title, a.slug AS article_slug
         FROM comments c
         JOIN users u ON u.id = c.user_id
         JOIN articles a ON a.id = c.article_id
         ORDER BY c.created_at DESC
         LIMIT 50'
    );
    $recentCommentsStmt->execute();
    $recentComments = $recentCommentsStmt->fetchAll();

    echo json_encode([
        'stats' => [
            'users' => $totalUsers,
            'articles' => $totalArticles,
            'views' => $totalViews,
            'comments' => $totalComments
        ],
        'charts' => [
            'traffic' => [
                'labels' => $labels,
                'articles' => array_values($dailyArticles),
                'users' => array_values($dailyUsers)
            ],
            'categories' => [
                'labels' => $categoryLabels,
                'counts' => $categoryCounts
            ]
        ],
        'recent' => [
            'articles' => $recentArticles,
            'users' => $recentUsers,
            'comments' => $recentComments
        ]
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
