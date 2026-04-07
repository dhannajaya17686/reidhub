<?php

class EduResourceModel extends Model {

    // Fetch approved resources with filters
    public function getAllResources($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $limit = 100, $offset = 0) {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM edu_resources r
                JOIN users u ON r.user_id = u.id
                WHERE r.status = 'approved'
                  AND (r.is_hidden = 0 OR r.is_hidden IS NULL)";
        
        $params = [];

        if ($type && $type !== 'all') {
            $sql .= " AND r.type = :type";
            $params[':type'] = $type;
        }
        if ($subject) {
            $sql .= " AND r.subject = :subject";
            $params[':subject'] = $subject;
        }
        if ($year) {
            $sql .= " AND r.year_level = :year";
            $params[':year'] = $year;
        }
        if ($search) {
            $sql .= " AND (r.title LIKE :search OR r.description LIKE :search OR r.tags LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if ($tag) {
            $sql .= " AND r.tags LIKE :tag";
            $params[':tag'] = "%$tag%";
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminResources($status = 'all', $type = 'all', $subject = null, $year = null, $search = null, $tag = null, $hidden = null, $limit = 200, $offset = 0) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM edu_resources r
                JOIN users u ON r.user_id = u.id
                WHERE 1=1";
        $params = [];

        if ($status && $status !== 'all') {
            $sql .= " AND r.status = :status";
            $params[':status'] = $status;
        }
        if ($type && $type !== 'all') {
            $sql .= " AND r.type = :type";
            $params[':type'] = $type;
        }
        if ($subject) {
            $sql .= " AND r.subject = :subject";
            $params[':subject'] = $subject;
        }
        if ($year) {
            $sql .= " AND r.year_level = :year";
            $params[':year'] = $year;
        }
        if ($search) {
            $sql .= " AND (r.title LIKE :search OR r.description LIKE :search OR r.tags LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if ($tag) {
            $sql .= " AND r.tags LIKE :tag";
            $params[':tag'] = "%$tag%";
        }
        if ($hidden !== null && $hidden !== '') {
            $sql .= " AND r.is_hidden = :hidden";
            $params[':hidden'] = ((string)$hidden === '1') ? 1 : 0;
        }

        $sql .= " ORDER BY
                    CASE r.status
                        WHEN 'pending' THEN 0
                        WHEN 'approved' THEN 1
                        WHEN 'rejected' THEN 2
                        ELSE 3
                    END,
                    r.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminCounts() {
        $sql = "SELECT
                    COUNT(*) AS total_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    SUM(CASE WHEN status = 'approved' AND is_hidden = 1 THEN 1 ELSE 0 END) AS hidden_count
                FROM edu_resources";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Upload a new resource
    public function createResource($data) {
        $sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, video_link, file_path, status)
                VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :link, :file, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $data['user_id'],
            ':title' => $data['title'],
            ':desc' => $data['description'],
            ':subject' => $data['subject'],
            ':tags' => $data['tags'],
            ':type' => $data['type'],
            ':year' => $data['year_level'],
            ':link' => $data['video_link'] ?? null,
            ':file' => $data['file_path'] ?? null
        ]);
    }

    // Get user's own submissions (to track status)
    public function getMySubmissions($userId) {
        $sql = "SELECT * FROM edu_resources WHERE user_id = :uid ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get Single Resource (For Editing - user scoped)
    public function getResourceById($id, $userId = null) {
        $sql = "SELECT * FROM edu_resources WHERE id = :id";
        $params = [':id' => $id];

        if ($userId !== null) {
            $sql .= " AND user_id = :uid";
            $params[':uid'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateResourceMetadataByAdmin($id, $data) {
        $sql = "UPDATE edu_resources
                SET title = :title,
                    description = :description,
                    subject = :subject,
                    tags = :tags,
                    year_level = :year_level
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':subject' => $data['subject'],
            ':tags' => $data['tags'],
            ':year_level' => $data['year_level'],
            ':id' => $id
        ]);
    }

    public function approveResource($id) {
        $stmt = $this->db->prepare("UPDATE edu_resources SET status = 'approved', admin_feedback = NULL, is_hidden = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function rejectResource($id, $feedback) {
        $stmt = $this->db->prepare("UPDATE edu_resources SET status = 'rejected', admin_feedback = :feedback WHERE id = :id");
        $stmt->execute([':feedback' => $feedback, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function setResourceHidden($id, $hidden) {
        $stmt = $this->db->prepare("UPDATE edu_resources SET is_hidden = :hidden WHERE id = :id AND status = 'approved'");
        $stmt->execute([':hidden' => (int)$hidden, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getFilterTags() {
        $stmt = $this->db->prepare("SELECT id, name, slug FROM edu_filter_tags ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createFilterTag($name) {
        $slug = $this->toTagSlug($name);
        $stmt = $this->db->prepare("INSERT INTO edu_filter_tags (name, slug) VALUES (:name, :slug)");
        return $stmt->execute([':name' => $name, ':slug' => $slug]);
    }

    public function updateFilterTag($id, $name) {
        $slug = $this->toTagSlug($name);
        $stmt = $this->db->prepare("UPDATE edu_filter_tags SET name = :name, slug = :slug WHERE id = :id");
        return $stmt->execute([':name' => $name, ':slug' => $slug, ':id' => $id]);
    }

    public function deleteFilterTag($id) {
        $stmt = $this->db->prepare("DELETE FROM edu_filter_tags WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    private function toTagSlug($name) {
        $slug = strtolower(trim((string)$name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug ?: ('tag-' . uniqid());
    }

    // Update Resource (only pending + owner)
    public function updateResource($id, $userId, $data) {
        $sql = "UPDATE edu_resources 
                SET title = :title,
                    description = :desc,
                    subject = :subject,
                    tags = :tags,
                    year_level = :year,
                    type = :type,
                    video_link = :link,
                    file_path = :file,
                    status = 'pending'
                WHERE id = :id AND user_id = :uid AND status = 'pending'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':desc' => $data['description'],
            ':subject' => $data['subject'],
            ':tags' => $data['tags'],
            ':year' => $data['year_level'],
            ':type' => $data['type'],
            ':link' => $data['video_link'] ?? null,
            ':file' => $data['file_path'] ?? null,
            ':id' => $id,
            ':uid' => $userId
        ]);
        return $stmt->rowCount() > 0;
    }

    // Delete submission (only pending + owner)
    public function deleteResource($id, $userId) {
        $sql = "DELETE FROM edu_resources WHERE id = :id AND user_id = :uid AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }

    // Toggle Bookmark
    public function toggleBookmark($userId, $resourceId) {
        $check = "SELECT id FROM edu_bookmarks WHERE user_id = :uid AND resource_id = :rid";
        $stmt = $this->db->prepare($check);
        $stmt->execute([':uid' => $userId, ':rid' => $resourceId]);

        if ($stmt->rowCount() > 0) {
            $del = "DELETE FROM edu_bookmarks WHERE user_id = :uid AND resource_id = :rid";
            $this->db->prepare($del)->execute([':uid' => $userId, ':rid' => $resourceId]);
            return 'removed';
        } else {
            $ins = "INSERT INTO edu_bookmarks (user_id, resource_id) VALUES (:uid, :rid)";
            $this->db->prepare($ins)->execute([':uid' => $userId, ':rid' => $resourceId]);
            return 'added';
        }
    }

    // Check if a resource is bookmarked by user
    public function isBookmarked($resourceId, $userId) {
        $sql = "SELECT id FROM edu_bookmarks WHERE user_id = :uid AND resource_id = :rid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId, ':rid' => $resourceId]);
        return $stmt->rowCount() > 0;
    }

    // Get user's bookmarked resources
    public function getBookmarkedResources($userId) {
        $sql = "SELECT r.*
                FROM edu_resources r
                JOIN edu_bookmarks b ON r.id = b.resource_id
                WHERE b.user_id = :uid
                  AND r.status = 'approved'
                  AND (r.is_hidden = 0 OR r.is_hidden IS NULL)
                ORDER BY b.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
