<?php
/**
 * NotificationService - Handles notification creation and business logic
 * Implements Service Pattern to separate notification logic from controllers
 */
require_once __DIR__ . '/../../config/db.php';

class NotificationService {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Create a notification for a user
     * 
     * @param int $recipientId User ID who will receive the notification
     * @param int|null $actorId User ID who performed the action (null for system notifications)
     * @param string $type Notification type (like, comment, follow, save, etc.)
     * @param int|null $entityId ID of the related entity (article_id, comment_id, etc.)
     * @param string $message Notification message text
     * @return array ['success' => bool, 'notification_id' => int|null]
     */
    public function createNotification(int $recipientId, ?int $actorId, string $type, ?int $entityId, string $message): array {
        // Do not notify self
        if ($actorId && $actorId === $recipientId) {
            return ['success' => false, 'notification_id' => null];
        }

        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO notifications (user_id, actor_id, type, entity_id, message) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$recipientId, $actorId, $type, $entityId, $message]);
            
            $notificationId = (int)$this->conn->lastInsertId();
            return ['success' => true, 'notification_id' => $notificationId];
        } catch (PDOException $e) {
            error_log("Notification creation failed: " . $e->getMessage());
            return ['success' => false, 'notification_id' => null];
        }
    }
}

