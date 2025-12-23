<?php
// API for authenticated user's own data
// Endpoints via index.php?url=api-me

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../repositories/UserRepository.php';

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

function isMultipartRequest(): bool {
    if (!empty($_FILES)) {
        return true;
    }
    $ct = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    return stripos($ct, 'multipart/form-data') !== false;
}

/**
 * Save a user-uploaded image to public/images/{subdir} with basic validation.
 * Returns ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function saveUserImage(?array $file, int $userId, string $subdir, string $prefix): array {
    if (!$file || !isset($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'path' => null, 'error' => 'No file uploaded'];
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'Upload failed'];
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = $file['type'] ?? '';
    if (!isset($allowed[$mime])) {
        return ['success' => false, 'path' => null, 'error' => 'Invalid image type'];
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['success' => false, 'path' => null, 'error' => 'Image exceeds 5MB'];
    }

    $ext = $allowed[$mime];
    $dir = __DIR__ . '/../../public/images/' . trim($subdir, '/');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $name = $prefix . '_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => false, 'path' => null, 'error' => 'Failed to save image'];
    }

    return ['success' => true, 'path' => trim($subdir, '/') . '/' . $name, 'error' => null];
}

try {
    $userRepository = new UserRepository();
    
    if ($method === 'GET') {
        $section = isset($_GET['section']) ? $_GET['section'] : 'overview';

        if ($section === 'overview') {
            // user details - Using Repository
            $user = $userRepository->findById($userId, ['id', 'username', 'email', 'full_name', 'bio', 'profile_image', 'cover_image', 'created_at']);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                exit;
            }

            // counts - Using Repository for user-related stats
            $counts = [
                'articles' => $userRepository->countUserArticles($userId),
                'followers' => $userRepository->countFollowers($userId),
                'following' => $userRepository->countFollowing($userId),
                'likes' => 0,
                'saved' => 0,
                'comments' => 0,
            ];

            // Still need direct queries for likes, saved, comments (not user table)
            require_once __DIR__ . '/../../config/db.php';
            $conn = Database::getInstance()->getConnection();
            $counts['likes'] = (int)$conn->query("SELECT COUNT(*) AS c FROM likes WHERE user_id = $userId")->fetch()['c'];
            // bookmarks table is our saved
            $counts['saved'] = (int)$conn->query("SELECT COUNT(*) AS c FROM bookmarks WHERE user_id = $userId")->fetch()['c'];
            $counts['comments'] = (int)$conn->query("SELECT COUNT(*) AS c FROM comments WHERE user_id = $userId")->fetch()['c'];

            echo json_encode(['user' => $user, 'counts' => $counts]);
            exit;
        }

        // Articles, saved, liked, comments sections - Still need direct queries (not user table)
        require_once __DIR__ . '/../../config/db.php';
        $conn = Database::getInstance()->getConnection();
        
        if ($section === 'articles') {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.is_published, a.created_at,
                        (SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
                        (SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
                 FROM articles a
                 WHERE a.user_id = ? AND a.is_published = 1
                 ORDER BY a.created_at DESC'
            );
            $stmt->execute([$userId]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            exit;
        }

        if ($section === 'drafts') {
            $stmt = $conn->prepare(
                'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.is_published, a.created_at, a.updated_at
                 FROM articles a
                 WHERE a.user_id = ? AND a.is_published = 0
                 ORDER BY a.updated_at DESC'
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
                'SELECT c.id, c.content, c.created_at, c.updated_at,
                        a.id AS article_id, a.title AS article_title, a.slug AS article_slug, a.excerpt AS article_excerpt
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
        // Update profile details: full_name, bio, email, and optional avatar/cover uploads
        $data = isMultipartRequest() ? $_POST : readJsonBodyAssoc();
        $updateData = [];

        if (isset($data['full_name'])) {
            $updateData['full_name'] = trim((string)$data['full_name']);
        }
        if (isset($data['bio'])) {
            $updateData['bio'] = trim((string)$data['bio']);
        }
        if (isset($data['email'])) {
            $email = trim((string)$data['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid email address']);
                exit;
            }
            // ensure not used by another user - Using Repository
            if ($userRepository->emailExists($email, $userId)) {
                http_response_code(409);
                echo json_encode(['error' => 'Email already in use']);
                exit;
            }
            $updateData['email'] = $email;
        }

        // Handle file uploads (multipart only)
        if (isMultipartRequest()) {
            if (!empty($_FILES['avatar'])) {
                $r = saveUserImage($_FILES['avatar'], $userId, 'authors', 'avatar');
                if (!$r['success']) {
                    http_response_code(422);
                    echo json_encode(['error' => $r['error']]);
                    exit;
                }
                $updateData['profile_image'] = $r['path'];
            }
            if (!empty($_FILES['cover'])) {
                $r = saveUserImage($_FILES['cover'], $userId, 'covers', 'cover');
                if (!$r['success']) {
                    http_response_code(422);
                    echo json_encode(['error' => $r['error']]);
                    exit;
                }
                $updateData['cover_image'] = $r['path'];
            }
        }

        if (empty($updateData)) {
            http_response_code(400);
            echo json_encode(['error' => 'No updatable fields provided']);
            exit;
        }

        // Update profile - Using Repository
        $userRepository->updateProfile($userId, $updateData);

        // Keep session in sync for navbar/avatar usage
        if (isset($updateData['full_name'])) {
            $_SESSION['full_name'] = $updateData['full_name'];
        }
        if (isset($updateData['profile_image'])) {
            $_SESSION['profile_image'] = $updateData['profile_image'];
        }
        if (isset($updateData['cover_image'])) {
            $_SESSION['cover_image'] = $updateData['cover_image'];
        }

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
