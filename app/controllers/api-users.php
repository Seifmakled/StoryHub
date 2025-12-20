<?php
// Simple Users API for admin dashboard
// Endpoints via index.php?url=api-users

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (!isset($conn)) {
	require_once __DIR__ . '/../../config/db.php';
	$database = new Database();
	$conn = $database->getConnection();
}

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
	if ($method === 'GET') {
		// Public profile fetch: index.php?url=api-users&id=123
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id > 0) {
			// Basic user info
			$stmt = $conn->prepare('SELECT id, username, full_name, bio, profile_image, created_at FROM users WHERE id = ?');
			$stmt->execute([$id]);
			$user = $stmt->fetch();
			if (!$user) {
				http_response_code(404);
				echo json_encode(['error' => 'User not found']);
				exit;
			}

			$stats = [
				'articles' => 0,
				'followers' => 0,
				'following' => 0,
				'likes' => 0,
			];

			// Articles count (published only for public profile)
			$stmt = $conn->prepare('SELECT COUNT(*) AS c FROM articles WHERE user_id = ? AND is_published = 1');
			$stmt->execute([$id]);
			$stats['articles'] = (int)$stmt->fetch()['c'];

			// Followers count (follows schema: follower_id -> followee_id)
			$stmt = $conn->prepare('SELECT COUNT(*) AS c FROM follows WHERE followee_id = ?');
			$stmt->execute([$id]);
			$stats['followers'] = (int)$stmt->fetch()['c'];

			// Following count
			$stmt = $conn->prepare('SELECT COUNT(*) AS c FROM follows WHERE follower_id = ?');
			$stmt->execute([$id]);
			$stats['following'] = (int)$stmt->fetch()['c'];

			// Likes received across this user's articles
			$stmt = $conn->prepare('SELECT COUNT(*) AS c FROM likes l JOIN articles a ON a.id = l.article_id WHERE a.user_id = ?');
			$stmt->execute([$id]);
			$stats['likes'] = (int)$stmt->fetch()['c'];

			// Viewer context (optional)
			$viewerId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
			$isFollowing = false;
			if ($viewerId > 0 && $viewerId !== $id) {
				$stmt = $conn->prepare('SELECT 1 FROM follows WHERE follower_id = ? AND followee_id = ? LIMIT 1');
				$stmt->execute([$viewerId, $id]);
				$isFollowing = (bool)$stmt->fetchColumn();
			}

			// Recent published articles for the profile
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

		// List users (try to get common fields, handle different table structures)
		try {
			// First, get table structure to see what columns exist
			$stmt = $conn->query("DESCRIBE users");
			$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
			
			// Build SELECT query based on available columns
			$selectFields = [];
			$commonFields = ['id', 'username', 'email', 'created_at', 'is_admin', 'full_name', 'name', 'user_name'];
			
			foreach ($commonFields as $field) {
				if (in_array($field, $columns)) {
					$selectFields[] = $field;
				}
			}
			
			if (empty($selectFields)) {
				$selectFields = ['*']; // fallback to all columns
			}
			
			$query = 'SELECT ' . implode(', ', $selectFields) . ' FROM users ORDER BY id DESC LIMIT 100';
			$stmt = $conn->query($query);
			$users = $stmt->fetchAll();
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
			// Check what columns exist in the users table
			$stmt = $conn->query("DESCRIBE users");
			$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
			
			// Build INSERT query based on available columns
			$insertFields = [];
			$insertValues = [];
			
			// Try common field names
			if (in_array('username', $columns)) {
				$insertFields[] = 'username';
				$insertValues[] = $username;
			} elseif (in_array('user_name', $columns)) {
				$insertFields[] = 'user_name';
				$insertValues[] = $username;
			} elseif (in_array('name', $columns)) {
				$insertFields[] = 'name';
				$insertValues[] = $username;
			}
			
			if (in_array('email', $columns)) {
				$insertFields[] = 'email';
				$insertValues[] = $email;
			}
			
			if (in_array('password', $columns)) {
				$insertFields[] = 'password';
				$insertValues[] = $hash;
			}
			
			if (in_array('is_admin', $columns)) {
				$insertFields[] = 'is_admin';
				$insertValues[] = 0;
			}
			
			if (empty($insertFields)) {
				http_response_code(500);
				echo json_encode(['error' => 'No compatible fields found in users table']);
				exit;
			}
			
			$placeholders = str_repeat('?,', count($insertValues) - 1) . '?';
			$query = 'INSERT INTO users (' . implode(', ', $insertFields) . ') VALUES (' . $placeholders . ')';
			$stmt = $conn->prepare($query);
			$stmt->execute($insertValues);
		} catch (PDOException $e) {
			$code = $e->getCode();
			if ($code === '23000') { // integrity constraint violation (e.g., duplicate)
				http_response_code(409);
				echo json_encode(['error' => 'Username or email already exists']);
				exit;
			}
			throw $e;
		}
		$id = (int)$conn->lastInsertId();

		echo json_encode(['message' => 'User created', 'id' => $id]);
		exit;
	}

	if ($method === 'DELETE') {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id <= 0) {
			http_response_code(400);
			echo json_encode(['error' => 'id is required']);
			exit;
		}

		$stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
		$stmt->execute([$id]);
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