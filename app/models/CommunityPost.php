<?php
class CommunityPost extends Model
{
    protected $table = 'community_posts';

    // ─── Dashboard helpers ────────────────────────────────────────────────────

    /**
     * Get the single most-recently featured blog post.
     * Used on the dashboard "Featured Blogs" card.
     */
    public function getFeaturedBlog(): ?array
    {
        try {
            $sql = "SELECT cp.*, u.first_name, u.last_name
                    FROM {$this->table} cp
                    LEFT JOIN users u ON u.id = cp.user_id
                    WHERE cp.type = 'blog' AND cp.is_featured = 1 AND cp.status = 'active'
                    ORDER BY cp.created_at DESC
                    LIMIT 1";
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            Logger::error('getFeaturedBlog error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the most-recently featured community posts.
     * Used on the dashboard "Featured Posts" section.
     */
    public function getFeaturedPosts(int $limit = 2): array
    {
        try {
            $sql = "SELECT cp.*, u.first_name, u.last_name
                    FROM {$this->table} cp
                    LEFT JOIN users u ON u.id = cp.user_id
                    WHERE cp.type = 'post' AND cp.is_featured = 1 AND cp.status = 'active'
                    ORDER BY cp.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('getFeaturedPosts error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the latest blog posts (not necessarily featured).
     * Used on the dashboard "Recent Blogs" section.
     */
    public function getRecentBlogs(int $limit = 3): array
    {
        try {
            $sql = "SELECT cp.*, u.first_name, u.last_name
                    FROM {$this->table} cp
                    LEFT JOIN users u ON u.id = cp.user_id
                    WHERE cp.type = 'blog' AND cp.status = 'active'
                    ORDER BY cp.created_at DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('getRecentBlogs error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get upcoming events ordered by event_date ASC.
     * Used on the dashboard "Upcoming Events" section.
     */
    public function getUpcomingEvents(int $limit = 4): array
    {
        try {
            $sql = "SELECT cp.*, u.first_name, u.last_name
                    FROM {$this->table} cp
                    LEFT JOIN users u ON u.id = cp.user_id
                    WHERE cp.type = 'event'
                      AND cp.status = 'active'
                      AND cp.event_date >= CURDATE()
                    ORDER BY cp.event_date ASC, cp.event_time_start ASC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('getUpcomingEvents error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count blog posts written by a specific user.
     */
    public function countBlogsByUser(int $userId): int
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ? AND type = 'blog'"
            );
            $stmt->execute([$userId]);
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            Logger::error('countBlogsByUser error: ' . $e->getMessage());
            return 0;
        }
    }

    // ─── Generic CRUD ─────────────────────────────────────────────────────────

    /**
     * Create a community post / blog / event.
     */
    public function create(array $data): int|false
    {
        try {
            $sql = "INSERT INTO {$this->table}
                        (user_id, type, title, body, image_url, group_name,
                         is_featured, event_date, event_time_start, event_time_end,
                         event_location, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                (int)$data['user_id'],
                $data['type'],
                $data['title'],
                $data['body'],
                $data['image_url']        ?? null,
                $data['group_name']       ?? null,
                !empty($data['is_featured']) ? 1 : 0,
                $data['event_date']       ?? null,
                $data['event_time_start'] ?? null,
                $data['event_time_end']   ?? null,
                $data['event_location']   ?? null,
            ]);
            return $ok ? (int)$this->db->lastInsertId() : false;
        } catch (Throwable $e) {
            Logger::error('CommunityPost create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find all posts of a given type (paginated).
     */
    public function findByType(string $type, int $limit = 20, int $offset = 0): array
    {
        try {
            $sql = "SELECT cp.*, u.first_name, u.last_name
                    FROM {$this->table} cp
                    LEFT JOIN users u ON u.id = cp.user_id
                    WHERE cp.type = ? AND cp.status = 'active'
                    ORDER BY cp.created_at DESC
                    LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $type,   PDO::PARAM_STR);
            $stmt->bindValue(2, $limit,  PDO::PARAM_INT);
            $stmt->bindValue(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('CommunityPost findByType error: ' . $e->getMessage());
            return [];
        }
    }
}
