<?php

require_once __DIR__ . '/../app/services/NotificationService.php';
require_once __DIR__ . '/DatabaseTestCase.php';

class NotificationServiceTest extends DatabaseTestCase
{
    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService();
    }

    public function testDoesNotNotifySelf(): void
    {
        $userId = $this->seedUser();
        $result = $this->service->createNotification($userId, $userId, 'like', 1, 'liked your post');
        $this->assertFalse($result['success']);
        $count = (int)$this->pdo->query('SELECT COUNT(*) FROM notifications')->fetchColumn();
        $this->assertSame(0, $count);
    }

    public function testCreatesNotificationRecord(): void
    {
        $recipientId = $this->seedUser(['username' => 'recipient']);
        $actorId = $this->seedUser(['username' => 'actor']);

        $result = $this->service->createNotification($recipientId, $actorId, 'comment', 42, 'commented on your story');
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['notification_id']);

        $row = $this->pdo->query('SELECT user_id, actor_id, type, entity_id, message FROM notifications')->fetch();
        $this->assertSame($recipientId, (int)$row['user_id']);
        $this->assertSame($actorId, (int)$row['actor_id']);
        $this->assertSame('comment', $row['type']);
        $this->assertSame(42, (int)$row['entity_id']);
        $this->assertSame('commented on your story', $row['message']);
    }
}
