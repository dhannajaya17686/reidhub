<?php
class Blog extends Model
{
    protected $table = 'blogs';

    /**
     * Get all published blogs with author information
     */
    public function getAllBlogs(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT 
                    b.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM blogs b
                INNER JOIN users u ON b.author_id = u.id
                WHERE b.status = 'published'
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get blogs by specific author
     */
    public function getBlogsByAuthor(int $authorId): array
    {
        $sql = "SELECT 
                    b.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM blogs b
                INNER JOIN users u ON b.author_id = u.id
                WHERE b.author_id = ?
                ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific blog by ID with author information
     */
    public function getBlogById(int $blogId)
    {
        $sql = "SELECT 
                    b.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM blogs b
                INNER JOIN users u ON b.author_id = u.id
                WHERE b.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$blogId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Search blogs by title or content
     */
    public function searchBlogs(string $query, string $category = 'all'): array
    {
        $sql = "SELECT 
                    b.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM blogs b
                INNER JOIN users u ON b.author_id = u.id
                WHERE b.status = 'published'
                AND (b.title LIKE ? OR b.content LIKE ?)";
        
        $params = ["%$query%", "%$query%"];
        
        if ($category !== 'all') {
            $sql .= " AND b.category = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get blogs by category
     */
    public function getBlogsByCategory(string $category): array
    {
        $sql = "SELECT 
                    b.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM blogs b
                INNER JOIN users u ON b.author_id = u.id
                WHERE b.status = 'published' AND b.category = ?
                ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new blog post
     */
    public function createBlog(array $data)
    {
        try {
            error_log("📝 Blog Model: Starting createBlog");
            error_log("  Author ID: " . $data['author_id']);
            error_log("  Title: " . $data['title']);
            error_log("  Category: " . ($data['category'] ?? 'campus-life'));
            error_log("  Has image: " . ($data['image_path'] ? 'Yes - ' . $data['image_path'] : 'No'));
            
            $sql = "INSERT INTO blogs 
                    (author_id, title, content, image_path, category, tags, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log("❌ Failed to prepare SQL statement");
                error_log("  Error: " . $this->db->errorInfo()[2]);
                return false;
            }
            
            error_log("  ✓ SQL prepared");
            
            $tags = isset($data['tags']) && is_array($data['tags']) ? json_encode($data['tags']) : null;
            error_log("  Tags JSON: " . ($tags ?? 'null'));
            
            $bindParams = [
                $data['author_id'],
                $data['title'],
                $data['content'],
                $data['image_path'] ?? null,
                $data['category'] ?? 'campus-life',
                $tags,
                $data['status'] ?? 'published'
            ];
            
            error_log("  Executing SQL with " . count($bindParams) . " parameters");
            
            $success = $stmt->execute($bindParams);
            
            if (!$success) {
                error_log("❌ SQL execute failed");
                error_log("  Error Info: " . json_encode($stmt->errorInfo()));
                return false;
            }
            
            error_log("  ✓ SQL executed successfully");
            
            $lastId = $this->db->lastInsertId();
            error_log("✓✓ Blog created successfully with ID: $lastId");
            
            return $lastId ?: false;
            
        } catch (Exception $e) {
            error_log("❌ Exception in createBlog: " . $e->getMessage());
            error_log("  Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Update a blog post
     */
    public function updateBlog(int $blogId, array $data): bool
    {
        $sql = "UPDATE blogs 
                SET title = ?, content = ?, image_path = ?, category = ?, tags = ?, status = ?, updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $tags = isset($data['tags']) ? json_encode($data['tags']) : null;
        
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['image_path'] ?? null,
            $data['category'] ?? 'campus-life',
            $tags,
            $data['status'] ?? 'published',
            $blogId
        ]);
    }

    /**
     * Delete a blog post
     */
    public function deleteBlog(int $blogId): bool
    {
        $sql = "DELETE FROM blogs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$blogId]);
    }

    /**
     * Check if user is the author of the blog
     */
    public function isAuthor(int $blogId, int $userId): bool
    {
        $sql = "SELECT author_id FROM blogs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$blogId]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $blog && (int)$blog['author_id'] === $userId;
    }

    /**
     * Increment blog views
     */
    public function incrementViews(int $blogId): bool
    {
        $sql = "UPDATE blogs SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$blogId]);
    }
}
