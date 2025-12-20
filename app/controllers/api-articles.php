<?php
// API for articles (create, list, delete)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/../../config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
}

header('Content-Type: application/json');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$method = $_SERVER['REQUEST_METHOD'];

function readJson(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('~[^a-z0-9]+~', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'story';
}

function uniqueSlug(PDO $conn, string $base, ?int $excludeId = null): string {
    $slug = $base;
    $i = 1;
    while (true) {
        $query = 'SELECT id FROM articles WHERE slug = ?' . ($excludeId ? ' AND id != ?' : '');
        $stmt = $conn->prepare($query);
        $params = [$slug];
        if ($excludeId) {
            $params[] = $excludeId;
        }
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

function normalizeTags(string $tags): string {
    $parts = array_filter(array_map('trim', explode(',', $tags)));
    $parts = array_slice($parts, 0, 5);
    return implode(', ', $parts);
}

function excerptFrom(?string $subtitle, ?string $body, int $limit = 240): string {
    $src = $subtitle ?: $body ?: '';
    $plain = trim(strip_tags($src));
    if (strlen($plain) <= $limit) {
        return $plain;
    }
    return rtrim(mb_substr($plain, 0, $limit), " \t\n\r\0\x0B") . '…';
}

function saveCover(?array $file, int $userId): ?string {
    if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$file['type']])) {
        throw new RuntimeException('Invalid image type');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image exceeds 5MB');
    }

    $ext = $allowed[$file['type']];
    $dir = realpath(__DIR__ . '/../../public/images/articles');
    if (!$dir) {
        $dir = __DIR__ . '/../../public/images/articles';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    $name = 'article_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Failed to save image');
    }

    // Stored relative to public/images/
    return 'articles/' . $name;
}

function getUserStatus(PDO $conn, int $userId): ?array {
    $stmt = $conn->prepare('SELECT id, status, is_admin FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

try {
    if ($method === 'GET') {
        // Public listing of published articles
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
        $mine = isset($_GET['mine']) ? (int)$_GET['mine'] : 0;

        if ($mine === 1) {
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.is_published, a.status, a.created_at,
                        a.category, a.tags, a.reviewed_at, a.reviewed_by,
                        (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                        (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
                 FROM articles a WHERE a.user_id = ? ORDER BY a.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        if ($id || $slug) {
            $stmt = $conn->prepare(
                'SELECT a.id, a.user_id, a.title, a.slug, a.excerpt, a.content, a.featured_image, a.category, a.tags,
                        a.is_published, a.status, a.created_at, a.updated_at, a.views, a.reviewed_at, a.reviewed_by,
                        u.username, u.full_name, u.profile_image,
                        (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                        (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
                 FROM articles a
                 JOIN users u ON u.id = a.user_id
                 WHERE ' . ($id ? 'a.id = ?' : 'a.slug = ?') . ' AND ((a.status = "approved" AND a.is_published = 1) OR a.user_id = ? OR ? = 1)
                 LIMIT 1'
            );
            $param = $id ?: $slug;
            $stmt->execute([$param, $userId ?: 0, $isAdmin ? 1 : 0]);
            $article = $stmt->fetch();
            if (!$article) {
                http_response_code(404);
                echo json_encode(['error' => 'Article not found']);
                exit;
            }
            echo json_encode(['data' => $article]);
            exit;
        }

        $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 12;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
        $featuredOnly = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest'; // latest|likes|views
        $requestedStatus = isset($_GET['status']) ? $_GET['status'] : null;
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        if (!$isAdmin || !in_array($requestedStatus, $allowedStatuses, true)) {
            $requestedStatus = null;
        }

        $where = $requestedStatus ? 'a.status = :status' : "a.status = 'approved' AND a.is_published = 1";
        if ($featuredOnly === 1) {
            $where .= ' AND a.is_featured = 1';
        }

        $order = 'a.created_at DESC';
        if ($sort === 'likes') {
            $order = 'likes_count DESC, a.created_at DESC';
        } elseif ($sort === 'views') {
            $order = 'a.views DESC, a.created_at DESC';
        }

        $countStmt = $conn->prepare('SELECT COUNT(*) AS total FROM articles a WHERE ' . $where);
        if ($requestedStatus) {
            $countStmt->bindValue(':status', $requestedStatus, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)($countStmt->fetch()['total'] ?? 0);

        $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.category, a.tags, a.is_featured, a.created_at, a.views, a.status,
                    u.username, u.full_name, u.profile_image,
                    (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                    (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
             FROM articles a
             JOIN users u ON u.id = a.user_id
               WHERE ' . $where . '
             ORDER BY ' . $order . '
             LIMIT :limit OFFSET :offset'
        );
           if ($requestedStatus) {
              $stmt->bindValue(':status', $requestedStatus, PDO::PARAM_STR);
           }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        echo json_encode([
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ]
        ]);
        exit;
    }

    if ($method === 'POST') {
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $me = getUserStatus($conn, $userId);
        if (!$me || ($me['status'] ?? 'active') === 'banned') {
            http_response_code(403);
            echo json_encode(['error' => 'Account is banned or not found']);
            exit;
        }

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';
        $body = isset($_POST['body']) ? trim($_POST['body']) : '';
        $tags = isset($_POST['tags']) ? normalizeTags($_POST['tags']) : '';
        $visibility = isset($_POST['visibility']) ? $_POST['visibility'] : 'public';

        if (strlen($title) < 3) {
            http_response_code(422);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }
        if (strlen($body) < 20) {
            http_response_code(422);
            echo json_encode(['error' => 'Body is too short']);
            exit;
        }

        $slugBase = slugify($title);
        $slug = uniqueSlug($conn, $slugBase);
        $excerpt = excerptFrom($subtitle, $body);
        $status = $isAdmin ? 'approved' : 'pending';
        $isPublished = ($visibility === 'public' && $isAdmin) ? 1 : 0;
        $reviewedBy = $isPublished ? $userId : null;
        $reviewedAt = $isPublished ? date('Y-m-d H:i:s') : null;
        $featuredImage = null;

        try {
            if (!empty($_FILES['cover'])) {
                $featuredImage = saveCover($_FILES['cover'], $userId);
            }
        } catch (RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        $stmt = $conn->prepare(
            'INSERT INTO articles (user_id, title, slug, content, excerpt, featured_image, category, tags, is_published, is_featured, status, reviewed_by, reviewed_at, rejection_reason)
             VALUES (:user_id, :title, :slug, :content, :excerpt, :featured_image, :category, :tags, :is_published, 0, :status, :reviewed_by, :reviewed_at, NULL)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':slug' => $slug,
            ':content' => $body,
            ':excerpt' => $excerpt,
            ':featured_image' => $featuredImage,
            ':category' => null,
            ':tags' => $tags,
            ':is_published' => $isPublished,
            ':status' => $status,
            ':reviewed_by' => $reviewedBy,
            ':reviewed_at' => $reviewedAt
        ]);

        $id = (int)$conn->lastInsertId();
        http_response_code(201);
        echo json_encode([
            'message' => $isPublished ? 'Published' : 'Submitted for review',
            'data' => [
                'id' => $id,
                'slug' => $slug,
                'title' => $title,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'tags' => $tags,
                'is_published' => $isPublished,
                'status' => $status
            ]
        ]);
        exit;
    }

    if ($method === 'DELETE') {
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $me = getUserStatus($conn, $userId);
        if (!$me || ($me['status'] ?? 'active') === 'banned') {
            http_response_code(403);
            echo json_encode(['error' => 'Account is banned or not found']);
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            exit;
        }

        $stmt = $conn->prepare('DELETE FROM articles WHERE id = ? AND (user_id = ? OR ? = 1)');
        $stmt->execute([$id, $userId, $isAdmin ? 1 : 0]);
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found or not owned by user']);
            exit;
        }
        echo json_encode(['message' => 'Article deleted']);
        exit;
    }

    if ($method === 'PATCH') {
        if (!$userId || !$isAdmin) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin privileges required']);
            exit;
        }

        $payload = readJson();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($payload['id'] ?? 0);
        $newStatus = isset($payload['status']) ? strtolower(trim((string)$payload['status'])) : '';
        $reason = isset($payload['reason']) ? trim((string)$payload['reason']) : null;

        if ($id <= 0 || !in_array($newStatus, ['approved', 'rejected'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'id and valid status are required']);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        if ($newStatus === 'approved') {
            $stmt = $conn->prepare(
                'UPDATE articles SET status = "approved", is_published = 1, reviewed_by = ?, reviewed_at = ?, rejection_reason = NULL WHERE id = ?'
            );
            $stmt->execute([$userId, $now, $id]);
            echo json_encode(['message' => 'Article approved']);
            exit;
        }

        if ($newStatus === 'rejected') {
            $stmt = $conn->prepare(
                'UPDATE articles SET status = "rejected", is_published = 0, reviewed_by = ?, reviewed_at = ?, rejection_reason = ? WHERE id = ?'
            );
            $stmt->execute([$userId, $now, $reason, $id]);
            echo json_encode(['message' => 'Article rejected']);
            exit;
        }
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
