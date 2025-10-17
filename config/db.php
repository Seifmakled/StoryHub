<?php
class Database {
    // Adjust these for your local setup
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = '';
    private static $dbname = 'blog_project';

    private static $conn = null;
    private static $bootstrapped = false;

    public function __construct() {
        if (!self::$bootstrapped) {
            self::bootstrap();
        }
    }

    public static function getConnection() {
        if (!self::$bootstrapped) {
            self::bootstrap();
        }
        return self::$conn;
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
            self::createTables();

            self::$bootstrapped = true;

        } catch (PDOException $e) {
            die('Database bootstrap failed: ' . $e->getMessage());
        }
    }

    private static function createTables(): void {
        try {
            // Users table (idempotent)
            self::$conn->exec(
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