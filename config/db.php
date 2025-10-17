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
            // Connect directly to the existing blog_project database
            self::$conn = new PDO(
                'mysql:host=' . self::$host . ';dbname=' . self::$dbname . ';charset=utf8mb4',
                self::$user,
                self::$pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // No need to create tables - using existing blog_project database

            self::$bootstrapped = true;

        } catch (PDOException $e) {
            die('Database bootstrap failed: ' . $e->getMessage());
        }
    }


    public static function closeConnection(): void {
        self::$conn = null;
        self::$bootstrapped = false;
    }
}

?>