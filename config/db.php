<?php
class Database {
    // Adjust these for your local setup
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = '';
    private static $dbname = 'blog_project';

    private static $instance = null;
    private static $conn = null;
    private static $bootstrapped = false;
    private static $testPdo = null;

    // Private constructor to prevent direct instantiation
    private function __construct() {
        if (!self::$bootstrapped) {
            self::bootstrap();
        }
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    // Get the single instance of Database
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getConnection() {
        if (self::$testPdo instanceof PDO) {
            return self::$testPdo;
        }
        if (!self::$bootstrapped) {
            self::bootstrap();
        }
        return self::$conn;
    }

    /**
     * Allow tests to inject an in-memory PDO (e.g., SQLite) without touching MySQL.
     */
    public static function setTestConnection(PDO $pdo): void {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $pdo->exec('PRAGMA foreign_keys = ON');
        }
        self::$testPdo = $pdo;
        self::$conn = $pdo;
        self::$bootstrapped = true;
        self::createTables($pdo);
    }

    private static function bootstrap(): void {
        try {
            // 1) Admin connection (no DB) to ensure database exists
            $admin = new PDO(
                'mysql:host=' . self::$host . ';charset=utf8mb4',
                self::$user,
                self::$pass,
                [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
            );
            $admin->exec('CREATE DATABASE IF NOT EXISTS `' . self::$dbname . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $admin = null;

            // 2) Connect directly to the target DB with charset
            self::$conn = new PDO(
                'mysql:host=' . self::$host . ';dbname=' . self::$dbname . ';charset=utf8mb4',
                self::$user,
                self::$pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // 3) Create/upgrade required tables
            self::createTables(self::$conn);

            self::$bootstrapped = true;

        } catch (PDOException $e) {
            die('Database bootstrap failed: ' . $e->getMessage());
        }
    }

    private static function createTables(PDO $pdo): void {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

            // For SQLite use simplified, compatible DDL
            if ($driver === 'sqlite') {
                $pdo->exec('CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL UNIQUE,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    full_name TEXT NULL,
                    bio TEXT NULL,
                    profile_image TEXT NOT NULL DEFAULT "default-avatar.jpg",
                    is_admin INTEGER NOT NULL DEFAULT 0,
                    verification_code TEXT NULL,
                    is_verified INTEGER NOT NULL DEFAULT 0,
                    reset_token TEXT NULL,
                    reset_token_expiry DATETIME NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS articles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    title TEXT NOT NULL,
                    slug TEXT NOT NULL UNIQUE,
                    content TEXT NULL,
                    excerpt TEXT NULL,
                    featured_image TEXT NULL,
                    category TEXT NULL,
                    tags TEXT NULL,
                    is_published INTEGER NOT NULL DEFAULT 0,
                    is_featured INTEGER NOT NULL DEFAULT 0,
                    views INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS likes (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    article_id INTEGER NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(user_id, article_id),
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS bookmarks (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    article_id INTEGER NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(user_id, article_id),
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS comments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    article_id INTEGER NOT NULL,
                    content TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY(article_id) REFERENCES articles(id) ON DELETE CASCADE
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS follows (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    follower_id INTEGER NOT NULL,
                    followee_id INTEGER NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(follower_id, followee_id),
                    FOREIGN KEY(follower_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY(followee_id) REFERENCES users(id) ON DELETE CASCADE
                );');

                $pdo->exec('CREATE TABLE IF NOT EXISTS notifications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    actor_id INTEGER NULL,
                    type TEXT NOT NULL,
                    entity_id INTEGER NULL,
                    message TEXT NOT NULL,
                    is_read INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
                );');

                return;
            }

            // MySQL / MariaDB path remains unchanged
            $execute = function(string $sql) use ($pdo) {
                $pdo->exec($sql);
            };

            $execute(
                "CREATE TABLE IF NOT EXISTS `users` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `username` VARCHAR(50) NOT NULL UNIQUE,
                    `email` VARCHAR(100) NOT NULL UNIQUE,
                    `password` VARCHAR(255) NOT NULL,
                    `full_name` VARCHAR(100) NULL,
                    `bio` TEXT NULL,
                    `profile_image` VARCHAR(255) NOT NULL DEFAULT 'default-avatar.jpg',
                    `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
                    `verification_code` VARCHAR(6) NULL,
                    `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
                    `reset_token` VARCHAR(100) NULL,
                    `reset_token_expiry` DATETIME NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (`email`),
                    INDEX (`username`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `articles` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `title` VARCHAR(255) NOT NULL,
                    `slug` VARCHAR(255) NOT NULL UNIQUE,
                    `content` LONGTEXT NULL,
                    `excerpt` TEXT NULL,
                    `featured_image` VARCHAR(255) NULL,
                    `category` VARCHAR(50) NULL,
                    `tags` VARCHAR(255) NULL,
                    `is_published` TINYINT(1) NOT NULL DEFAULT 0,
                    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
                    `views` INT NOT NULL DEFAULT 0,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (`user_id`),
                    INDEX (`slug`),
                    CONSTRAINT `fk_articles_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `likes` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `article_id` INT NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY `uniq_user_article_like` (`user_id`, `article_id`),
                    INDEX (`article_id`),
                    CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_likes_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `bookmarks` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `article_id` INT NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY `uniq_user_article_bookmark` (`user_id`, `article_id`),
                    INDEX (`article_id`),
                    CONSTRAINT `fk_bookmarks_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_bookmarks_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `comments` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `article_id` INT NOT NULL,
                    `content` TEXT NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX (`user_id`),
                    INDEX (`article_id`),
                    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_comments_article` FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `follows` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `follower_id` INT NOT NULL,
                    `followee_id` INT NOT NULL,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY `uniq_follow` (`follower_id`, `followee_id`),
                    INDEX (`followee_id`),
                    CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_follows_followee` FOREIGN KEY (`followee_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );

            $execute(
                "CREATE TABLE IF NOT EXISTS `notifications` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `actor_id` INT NULL,
                    `type` VARCHAR(50) NOT NULL,
                    `entity_id` INT NULL,
                    `message` VARCHAR(255) NOT NULL,
                    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX (`user_id`),
                    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            );
        } catch (PDOException $e) {
            die('Table creation failed: ' . $e->getMessage());
        }
    }

    public static function closeConnection(): void {
        self::$conn = null;
        self::$bootstrapped = false;
    }
}

?>