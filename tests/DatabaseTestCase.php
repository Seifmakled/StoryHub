<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/db.php';

abstract class DatabaseTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = new PDO('sqlite::memory:');
        Database::setTestConnection($this->pdo);
    }

    protected function seedUser(array $overrides = []): int
    {
        $data = array_merge([
            'username' => 'user_' . bin2hex(random_bytes(3)),
            'email' => 'user' . rand(1000, 9999) . '@example.com',
            'password' => password_hash('secret', PASSWORD_BCRYPT),
            'full_name' => 'Test User',
            'bio' => null,
            'profile_image' => 'default-avatar.jpg',
            'is_admin' => 0,
            'is_verified' => 1
        ], $overrides);

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password, full_name, bio, profile_image, is_admin, is_verified) VALUES (:u, :e, :p, :f, :b, :img, :a, :v)'
        );
        $stmt->execute([
            ':u' => $data['username'],
            ':e' => $data['email'],
            ':p' => $data['password'],
            ':f' => $data['full_name'],
            ':b' => $data['bio'],
            ':img' => $data['profile_image'],
            ':a' => $data['is_admin'],
            ':v' => $data['is_verified'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
