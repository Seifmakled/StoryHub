<?php
// Simple Users API for admin dashboard
// Endpoints via index.php?url=api-users

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/../repositories/UserRepository.php';

header('Content-Type: application/json');

// Optional: simple admin guard (expects $_SESSION['is_admin'] true)
// Uncomment when you have session-based admin auth ready
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
// 	http_response_code(403);
// 	echo json_encode(['error' => 'Forbidden']);
// 	exit;
// }

$method = $_SERVER['REQUEST_METHOD'];

// Utility: read JSON body
function readJsonBody() {
	$raw = file_get_contents('php://input');
	$decoded = json_decode($raw, true);
	return is_array($decoded) ? $decoded : [];
}

try {
	$userRepository = new UserRepository();
	
	if ($method === 'GET') {
		// Public profile fetch: index.php?url=api-users&id=123
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id > 0) {
			// Basic user info - Using Repository
			$user = $userRepository->findById($id, ['id', 'username', 'full_name', 'bio', 'profile_image', 'created_at']);
			if (!$user) {
				http_response_code(404);
				echo json_encode(['error' => 'User not found']);
				exit;
			}

			// Statistics - Using Repository
			$stats = [
				'articles' => $userRepository->countUserArticles($id, true),
				'followers' => $userRepository->countFollowers($id),
				'following' => $userRepository->countFollowing($id),
				'likes' => $userRepository->countTotalLikesReceived($id),
			];

			// Viewer context (optional) - Still need direct query for follows check
			$viewerId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
			$isFollowing = false;
			if ($viewerId > 0 && $viewerId !== $id) {
				require_once __DIR__ . '/../../config/db.php';
				$conn = Database::getInstance()->getConnection();
				$stmt = $conn->prepare('SELECT 1 FROM follows WHERE follower_id = ? AND followee_id = ? LIMIT 1');
				$stmt->execute([$viewerId, $id]);
				$isFollowing = (bool)$stmt->fetchColumn();
			}

			// Recent published articles for the profile - Still need direct query (articles table)
			require_once __DIR__ . '/../../config/db.php';
			$conn = Database::getInstance()->getConnection();
			$stmt = $conn->prepare(
				'SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.views, a.created_at,
						(SELECT COUNT(*) FROM likes l WHERE l.article_id = a.id) AS likes_count,
						(SELECT COUNT(*) FROM comments c WHERE c.article_id = a.id) AS comments_count
				 FROM articles a
				 WHERE a.user_id = ? AND a.is_published = 1
				 ORDER BY a.created_at DESC
				 LIMIT 24'
			);
			$stmt->execute([$id]);
			$articles = $stmt->fetchAll();

			echo json_encode([
				'user' => $user,
				'stats' => $stats,
				'is_following' => $isFollowing,
				'articles' => $articles,
			]);
			exit;
		}

		// List users - Using Repository
		try {
			$users = $userRepository->listAll(100);
			echo json_encode(['data' => $users]);
		} catch (PDOException $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Failed to fetch users', 'details' => $e->getMessage()]);
		}
		exit;
	}

	if ($method === 'POST') {
		$body = readJsonBody();
		$username = isset($body['username']) ? trim($body['username']) : '';
		$email = isset($body['email']) ? trim($body['email']) : '';
		$password = isset($body['password']) ? (string)$body['password'] : '';

		if ($username === '' || $email === '' || $password === '') {
			http_response_code(400);
			echo json_encode(['error' => 'username, email, and password are required']);
			exit;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			http_response_code(400);
			echo json_encode(['error' => 'Invalid email address']);
			exit;
		}

		$hash = password_hash($password, PASSWORD_BCRYPT);
		try {
			// Create user - Using Repository
			$id = $userRepository->createUser([
				'username' => $username,
				'email' => $email,
				'password' => $hash,
				'is_admin' => 0
			]);
			echo json_encode(['message' => 'User created', 'id' => $id]);
			exit;
		} catch (PDOException $e) {
			$code = $e->getCode();
			if ($code === '23000') { // integrity constraint violation (e.g., duplicate)
				http_response_code(409);
				echo json_encode(['error' => 'Username or email already exists']);
				exit;
			}
			throw $e;
		}
	}

	if ($method === 'DELETE') {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id <= 0) {
			http_response_code(400);
			echo json_encode(['error' => 'id is required']);
			exit;
		}

		// Delete user - Using Repository
		$userRepository->delete($id);
		echo json_encode(['message' => 'User deleted']);
		exit;
	}

	// Method not allowed
	http_response_code(405);
	echo json_encode(['error' => 'Method not allowed']);
	exit;

} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
	exit;
}