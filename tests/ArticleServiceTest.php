<?php

require_once __DIR__ . '/../app/services/ArticleService.php';
require_once __DIR__ . '/DatabaseTestCase.php';

class ArticleServiceTest extends DatabaseTestCase
{
    private ArticleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ArticleService();
    }

    public function testGenerateSlugNormalizesText(): void
    {
        $slug = $this->service->generateSlug('  Hello World!  ');
        $this->assertSame('hello-world', $slug);
    }

    public function testGenerateUniqueSlugAppendsCounterWhenExists(): void
    {
        $authorId = $this->seedUser();
        $stmt = $this->pdo->prepare('INSERT INTO articles (user_id, title, slug, content) VALUES (?, ?, ?, ?)');
        $stmt->execute([$authorId, 'Hello World', 'hello-world', 'Body']);

        $unique = $this->service->generateUniqueSlug('hello-world');
        $this->assertSame('hello-world-1', $unique);
    }

    public function testNormalizeTagsTrimsAndLimitsToFive(): void
    {
        $tags = $this->service->normalizeTags('  apple, banana , cherry, date, egg, fig  ');
        $this->assertSame('apple, banana, cherry, date, egg', $tags);
    }

    public function testGenerateExcerptAddsEllipsisWhenTooLong(): void
    {
        $body = str_repeat('word ', 80);
        $excerpt = $this->service->generateExcerpt(null, $body, 60);
        $this->assertStringEndsWith('…', $excerpt);
        $this->assertLessThanOrEqual(65, strlen($excerpt));
    }
}
