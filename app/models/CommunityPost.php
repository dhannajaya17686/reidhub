<?php
class CommunityPost extends Model
{
    protected $table = 'community_posts';

    /**
     * Get all published community posts with author information
     * @return array
     */
    public function getAllPosts(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT 
                    p.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
                    (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comments_count
                FROM community_posts p
                INNER JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published'
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get posts by specific author
     * @param int $authorId
     * @return array
     */
    public function getPostsByAuthor(int $authorId): array
    {
        $sql = "SELECT 
                    p.*,
                    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as likes_count,
                    (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comments_count
                FROM community_posts p
                WHERE p.author_id = ?
                ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new community post
     * @param array $data
     * @return int|false Post ID or false on failure
     */
    public function createPost(array $data)
    {
        $sql = "INSERT INTO community_posts 
                (author_id, title, content, post_type, images, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $images = isset($data['images']) ? json_encode($data['images']) : null;
        
        $success = $stmt->execute([
            $data['author_id'],
            $data['title'] ?? null,
            $data['content'],
            $data['post_type'] ?? 'general',
            $images,
            $data['status'] ?? 'published'
        ]);
        
        return $success ? $this->db->lastInsertId() : false;
    }

    /**
     * Update a post
     * @param int $postId
     * @param array $data
     * @return bool
     */
    public function updatePost(int $postId, array $data): bool
    {
        $sql = "UPDATE community_posts 
                SET title = ?, content = ?, images = ?, status = ?, updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $images = isset($data['images']) ? json_encode($data['images']) : null;
        
        return $stmt->execute([
            $data['title'] ?? null,
            $data['content'],
            $images,
            $data['status'] ?? 'published',
            $postId
        ]);
    }

    /**
     * Delete a post
     * @param int $postId
     * @return bool
     */
    public function deletePost(int $postId): bool
    {
        $sql = "DELETE FROM community_posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$postId]);
    }

    /**
     * Check if user is the author of the post
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function isAuthor(int $postId, int $userId): bool
    {
        $sql = "SELECT author_id FROM community_posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $post && (int)$post['author_id'] === $userId;
    }
}
