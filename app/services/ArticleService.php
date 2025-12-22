<?php
/**
 * ArticleService - Handles article-related business logic
 * Implements Service Pattern to separate article business logic from controllers
 */
require_once __DIR__ . '/../../config/db.php';

class ArticleService {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Generate URL-friendly slug from text
     * 
     * @param string $text Text to convert to slug
     * @return string URL-friendly slug
     */
    public function generateSlug(string $text): string {
        $text = strtolower(trim($text));
        $text = preg_replace('~[^a-z0-9]+~', '-', $text);
        $text = trim($text, '-');
        return $text ?: 'story';
    }

    /**
     * Generate unique slug, appending number if needed
     * 
     * @param string $base Base slug to make unique
     * @param int|null $excludeId Article ID to exclude from uniqueness check
     * @return string Unique slug
     */
    public function generateUniqueSlug(string $base, ?int $excludeId = null): string {
        $slug = $base;
        $i = 1;
        
        while (true) {
            $query = 'SELECT id FROM articles WHERE slug = ?' . ($excludeId ? ' AND id != ?' : '');
            $stmt = $this->conn->prepare($query);
            $params = [$slug];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            $stmt->execute($params);
            
            if (!$stmt->fetch()) {
                return $slug;
            }
            
            $slug = $base . '-' . $i;
            $i++;
        }
    }

    /**
     * Normalize and limit tags string
     * 
     * @param string $tags Comma-separated tags string
     * @return string Normalized tags (max 5 tags, trimmed, comma-space separated)
     */
    public function normalizeTags(string $tags): string {
        $parts = array_filter(array_map('trim', explode(',', $tags)));
        $parts = array_slice($parts, 0, 5);
        return implode(', ', $parts);
    }

    /**
     * Generate excerpt from subtitle or body content
     * 
     * @param string|null $subtitle Subtitle text
     * @param string|null $body Body text
     * @param int $limit Maximum character length (default 240)
     * @return string Excerpt text with ellipsis if truncated
     */
    public function generateExcerpt(?string $subtitle, ?string $body, int $limit = 240): string {
        $src = $subtitle ?: $body ?: '';
        $plain = trim(strip_tags($src));
        
        if (strlen($plain) <= $limit) {
            return $plain;
        }
        
        return rtrim(mb_substr($plain, 0, $limit), " \t\n\r\0\x0B") . '…';
    }

    /**
     * Save article cover image with validation
     * 
     * @param array|null $file $_FILES array for the uploaded image
     * @param int $userId User ID for file naming
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function saveArticleImage(?array $file, int $userId): array {
        if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'path' => null, 'error' => 'No file uploaded'];
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$file['type']])) {
            return ['success' => false, 'path' => null, 'error' => 'Invalid image type'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'path' => null, 'error' => 'Image exceeds 5MB'];
        }

        $ext = $allowed[$file['type']];
        $dir = realpath(__DIR__ . '/../../public/images/articles');
        if (!$dir) {
            $dir = __DIR__ . '/../../public/images/articles';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $name = 'article_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['success' => false, 'path' => null, 'error' => 'Failed to save image'];
        }

        // Return path relative to public/images/
        return ['success' => true, 'path' => 'articles/' . $name, 'error' => null];
    }
}

