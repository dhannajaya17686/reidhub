<?php

class EduResourceModel extends Model {
    private $moderationColumnsChecked = false;
    private $moderationColumnsReady = false;

    private function bindParams($stmt, array $params) {
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }

    private function hasColumn($table, $column) {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM {$table} LIKE :column");
        $stmt->execute([':column' => $column]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    private function ensureArchiveModerationColumns() {
        if ($this->moderationColumnsChecked) {
            return $this->moderationColumnsReady;
        }

        $this->moderationColumnsChecked = true;

        try {
            if (!$this->hasColumn('edu_resources', 'is_hidden')) {
                $this->db->exec("ALTER TABLE edu_resources ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER admin_feedback");
            }
            if (!$this->hasColumn('edu_resources', 'removal_requested')) {
                $this->db->exec("ALTER TABLE edu_resources ADD COLUMN removal_requested TINYINT(1) NOT NULL DEFAULT 0 AFTER is_hidden");
            }
            if (!$this->hasColumn('edu_resources', 'removal_reason')) {
                $this->db->exec("ALTER TABLE edu_resources ADD COLUMN removal_reason TEXT NULL AFTER removal_requested");
            }
            if (!$this->hasColumn('edu_resources', 'removal_requested_at')) {
                $this->db->exec("ALTER TABLE edu_resources ADD COLUMN removal_requested_at TIMESTAMP NULL AFTER removal_reason");
            }

            $this->moderationColumnsReady =
                $this->hasColumn('edu_resources', 'is_hidden') &&
                $this->hasColumn('edu_resources', 'removal_requested') &&
                $this->hasColumn('edu_resources', 'removal_reason') &&
                $this->hasColumn('edu_resources', 'removal_requested_at');
        } catch (Exception $e) {
            $this->moderationColumnsReady = false;
        }

        return $this->moderationColumnsReady;
    }

    private function appendPublicResourceFilters(&$sql, &$params, $type, $subject, $year, $search, $tag) {
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
    }

    private function appendAdminResourceFilters(&$sql, &$params, $status, $type, $subject, $year, $search, $tag, $hidden, $removal = null) {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();

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
        if ($moderationColumnsReady && $hidden !== null && $hidden !== '') {
            $sql .= " AND r.is_hidden = :hidden";
            $params[':hidden'] = ((string)$hidden === '1') ? 1 : 0;
        }
        if ($moderationColumnsReady && $removal !== null && $removal !== '') {
            $sql .= " AND r.removal_requested = :removal_requested";
            $params[':removal_requested'] = ((string)$removal === '1') ? 1 : 0;
        }
    }

    // Fetch approved resources with filters
    public function getAllResources($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $limit = 100, $offset = 0) {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM edu_resources r
                JOIN users u ON r.user_id = u.id
                WHERE r.status = 'approved'";
        if ($moderationColumnsReady) {
            $sql .= " AND (r.is_hidden = 0 OR r.is_hidden IS NULL)";
        }

        $params = [];
        $this->appendPublicResourceFilters($sql, $params, $type, $subject, $year, $search, $tag);

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllResourcesCount($type = 'all', $subject = null, $year = null, $search = null, $tag = null) {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $sql = "SELECT COUNT(*)
                FROM edu_resources r
                WHERE r.status = 'approved'";
        if ($moderationColumnsReady) {
            $sql .= " AND (r.is_hidden = 0 OR r.is_hidden IS NULL)";
        }
        $params = [];

        $this->appendPublicResourceFilters($sql, $params, $type, $subject, $year, $search, $tag);

        $stmt = $this->db->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getAdminResources($status = 'all', $type = 'all', $subject = null, $year = null, $search = null, $tag = null, $hidden = null, $limit = 200, $offset = 0, $removal = null) {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM edu_resources r
                JOIN users u ON r.user_id = u.id
                WHERE 1=1";
        $params = [];
        $this->appendAdminResourceFilters($sql, $params, $status, $type, $subject, $year, $search, $tag, $hidden, $removal);

        $sql .= " ORDER BY ";
        if ($moderationColumnsReady) {
            $sql .= "CASE WHEN r.removal_requested = 1 THEN 0 ELSE 1 END, ";
        }
        $sql .= "CASE r.status
                        WHEN 'pending' THEN 0
                        WHEN 'approved' THEN 1
                        WHEN 'rejected' THEN 2
                        ELSE 3
                    END,
                    r.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminResourcesCount($status = 'all', $type = 'all', $subject = null, $year = null, $search = null, $tag = null, $hidden = null, $removal = null) {
        $sql = "SELECT COUNT(*)
                FROM edu_resources r
                WHERE 1=1";
        $params = [];

        $this->appendAdminResourceFilters($sql, $params, $status, $type, $subject, $year, $search, $tag, $hidden, $removal);

        $stmt = $this->db->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getAdminCounts() {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $hiddenCountSql = $moderationColumnsReady ? "SUM(CASE WHEN status = 'approved' AND is_hidden = 1 THEN 1 ELSE 0 END)" : "0";
        $removalCountSql = $moderationColumnsReady ? "SUM(CASE WHEN removal_requested = 1 THEN 1 ELSE 0 END)" : "0";

        $sql = "SELECT
                    COUNT(*) AS total_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    {$hiddenCountSql} AS hidden_count,
                    {$removalCountSql} AS removal_request_count
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
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $sql = $moderationColumnsReady
            ? "UPDATE edu_resources SET status = 'approved', admin_feedback = NULL, is_hidden = 0 WHERE id = :id"
            : "UPDATE edu_resources SET status = 'approved', admin_feedback = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function rejectResource($id, $feedback) {
        $stmt = $this->db->prepare("UPDATE edu_resources SET status = 'rejected', admin_feedback = :feedback WHERE id = :id");
        $stmt->execute([':feedback' => $feedback, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function setResourceHidden($id, $hidden) {
        if (!$this->ensureArchiveModerationColumns()) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE edu_resources SET is_hidden = :hidden WHERE id = :id AND status = 'approved'");
        $stmt->execute([':hidden' => (int)$hidden, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function requestRemoval($id, $userId, $reason) {
        if (!$this->ensureArchiveModerationColumns()) {
            return false;
        }
        $sql = "UPDATE edu_resources
                SET removal_requested = 1,
                    removal_reason = :reason,
                    removal_requested_at = NOW()
                WHERE id = :id
                  AND user_id = :uid
                  AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reason' => $reason,
            ':id' => $id,
            ':uid' => $userId
        ]);
        return $stmt->rowCount() > 0;
    }

    public function clearRemovalRequest($id) {
        if (!$this->ensureArchiveModerationColumns()) {
            return false;
        }
        $sql = "UPDATE edu_resources
                SET removal_requested = 0,
                    removal_reason = NULL,
                    removal_requested_at = NULL
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
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

    public function isResourceVisibleForBookmark($resourceId) {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $sql = "SELECT id
                FROM edu_resources
                WHERE id = :id
                  AND status = 'approved'";
        if ($moderationColumnsReady) {
            $sql .= " AND (is_hidden = 0 OR is_hidden IS NULL)";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $resourceId]);
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
