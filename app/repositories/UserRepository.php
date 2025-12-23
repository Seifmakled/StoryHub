<?php
/**
 * UserRepository - Handles all database operations for users
 * Implements Repository Pattern to separate data access from business logic
 */
class UserRepository {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/db.php';
        $this->pdo = Database::getConnection();
    }

    /**
     * Find a user by email or username
     * 
     * @param string $emailOrUsername Email or username to search for
     * @return array|null User data array (id, full_name, username, email, password, is_admin) or null if not found
     * @throws PDOException If database query fails
     */
    public function findByEmailOrUsername(string $emailOrUsername): ?array {
        $sql = "SELECT id, full_name, username, email, password, is_admin, profile_image, cover_image 
                FROM users 
                WHERE email = ? OR username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$emailOrUsername, $emailOrUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Find a user by ID
     * 
     * @param int $id User ID
     * @param array|null $fields Optional array of fields to select (default: all)
     * @return array|null User data array or null if not found
     * @throws PDOException If database query fails
     */
    public function findById(int $id, ?array $fields = null): ?array {
        if ($fields === null) {
            $sql = "SELECT * FROM users WHERE id = ?";
        } else {
            $fieldList = implode(', ', $fields);
            $sql = "SELECT $fieldList FROM users WHERE id = ?";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Find a user by email
     * 
     * @param string $email Email address
     * @return array|null User data array or null if not found
     * @throws PDOException If database query fails
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Find a user by username
     * 
     * @param string $username Username
     * @return array|null User data array or null if not found
     * @throws PDOException If database query fails
     */
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Find a user by reset token
     * 
     * @param string $token Reset token
     * @return array|null User data array or null if not found
     * @throws PDOException If database query fails
     */
    public function findByResetToken(string $token): ?array {
        $sql = "SELECT * FROM users WHERE reset_token = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }

    /**
     * Check if email exists
     * 
     * @param string $email Email address
     * @param int|null $excludeUserId Optional user ID to exclude from check
     * @return bool True if email exists, false otherwise
     * @throws PDOException If database query fails
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool {
        if ($excludeUserId !== null) {
            $sql = "SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $excludeUserId]);
        } else {
            $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Check if username exists
     * 
     * @param string $username Username
     * @return bool True if username exists, false otherwise
     * @throws PDOException If database query fails
     */
    public function usernameExists(string $username): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Create a new user
     * 
     * @param array $data User data (username, email, password, full_name, is_verified, etc.)
     * @return int New user ID
     * @throws PDOException If database query fails
     */
    public function createUser(array $data): int {
        $fields = [];
        $placeholders = [];
        $values = [];

        $allowedFields = ['username', 'email', 'password', 'full_name', 'is_verified', 'is_admin', 'bio', 'profile_image', 'cover_image'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = $field;
                $placeholders[] = '?';
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            throw new InvalidArgumentException('No valid fields provided for user creation');
        }

        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Fields to update (full_name, bio, email, etc.)
     * @return bool True on success
     * @throws PDOException If database query fails
     */
    public function updateProfile(int $userId, array $data): bool {
        $fields = [];
        $values = [];

        $allowedFields = ['full_name', 'bio', 'email', 'profile_image', 'cover_image'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Update user password
     * 
     * @param int $userId User ID
     * @param string $hashedPassword Hashed password
     * @return bool True on success
     * @throws PDOException If database query fails
     */
    public function updatePassword(int $userId, string $hashedPassword): bool {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Update password and clear reset token (for forgot password flow)
     * 
     * @param string $email User email
     * @param string $hashedPassword Hashed password
     * @return bool True on success
     * @throws PDOException If database query fails
     */
    public function updatePasswordAndClearResetToken(string $email, string $hashedPassword): bool {
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$hashedPassword, $email]);
    }

    /**
     * Save reset token for password reset
     * 
     * @param int $userId User ID
     * @param string $token Reset token
     * @param string $expiresAt Expiry datetime string
     * @return bool True on success
     * @throws PDOException If database query fails
     */
    public function saveResetToken(int $userId, string $token, string $expiresAt): bool {
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$token, $expiresAt, $userId]);
    }

    /**
     * Count user articles
     * 
     * @param int $userId User ID
     * @param bool $publishedOnly If true, only count published articles
     * @return int Article count
     * @throws PDOException If database query fails
     */
    public function countUserArticles(int $userId, bool $publishedOnly = false): int {
        if ($publishedOnly) {
            $sql = "SELECT COUNT(*) FROM articles WHERE user_id = ? AND is_published = 1";
        } else {
            $sql = "SELECT COUNT(*) FROM articles WHERE user_id = ?";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count followers (users following this user)
     * 
     * @param int $userId User ID
     * @return int Follower count
     * @throws PDOException If database query fails
     */
    public function countFollowers(int $userId): int {
        $sql = "SELECT COUNT(*) FROM follows WHERE followee_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count following (users this user follows)
     * 
     * @param int $userId User ID
     * @return int Following count
     * @throws PDOException If database query fails
     */
    public function countFollowing(int $userId): int {
        $sql = "SELECT COUNT(*) FROM follows WHERE follower_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count total likes received on user's articles
     * 
     * @param int $userId User ID
     * @return int Total likes count
     * @throws PDOException If database query fails
     */
    public function countTotalLikesReceived(int $userId): int {
        $sql = "SELECT COUNT(*) FROM likes l JOIN articles a ON a.id = l.article_id WHERE a.user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Delete a user
     * 
     * @param int $userId User ID
     * @return bool True on success
     * @throws PDOException If database query fails
     */
    public function delete(int $userId): bool {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId]);
    }

    /**
     * List all users (for admin)
     * 
     * @param int $limit Maximum number of users to return
     * @return array Array of user data
     * @throws PDOException If database query fails
     */
    public function listAll(int $limit = 100): array {
        $sql = "SELECT id, username, email, created_at, is_admin, full_name FROM users ORDER BY id DESC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
