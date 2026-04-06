<?php

class CommunityAdmin extends Model
{
    protected $table = 'community_admins';

    public function getAllAdmins(): array
    {
        $sql = "SELECT
                    ca.id,
                    ca.user_id,
                    ca.role_type,
                    ca.created_at,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.reg_no
                FROM community_admins ca
                INNER JOIN users u ON u.id = ca.user_id
                ORDER BY ca.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAdmin(int $userId, string $roleType): bool
    {
        $check = $this->db->prepare("SELECT id FROM community_admins WHERE user_id = ? AND role_type = ? LIMIT 1");
        $check->execute([$userId, $roleType]);
        if ($check->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO community_admins (user_id, role_type, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$userId, $roleType]);
    }

    public function removeAdminById(int $communityAdminId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM community_admins WHERE id = ?");
        return $stmt->execute([$communityAdminId]);
    }

    public function searchUsers(string $query, int $limit = 20): array
    {
        $term = '%' . trim($query) . '%';

        $sql = "SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.reg_no,
                    CASE WHEN ca.user_id IS NULL THEN 0 ELSE 1 END AS is_community_admin
                FROM users u
                LEFT JOIN (
                    SELECT DISTINCT user_id FROM community_admins
                ) ca ON ca.user_id = u.id
                WHERE u.first_name LIKE :term
                   OR u.last_name LIKE :term
                   OR u.email LIKE :term
                   OR u.reg_no LIKE :term
                ORDER BY u.first_name ASC, u.last_name ASC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':term', $term, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReportedBlogs(): array
    {
        $sql = "SELECT
                    b.id,
                    b.title,
                    b.status,
                    b.created_at,
                    u.first_name,
                    u.last_name,
                    COUNT(r.id) AS report_count
                FROM blogs b
                INNER JOIN users u ON u.id = b.author_id
                INNER JOIN reports r ON r.content_id = b.id AND r.report_type = 'blog'
                GROUP BY b.id, b.title, b.status, b.created_at, u.first_name, u.last_name
                ORDER BY report_count DESC, b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBlogById(int $blogId): bool
    {
        $this->db->beginTransaction();

        try {
            $stmtReport = $this->db->prepare("DELETE FROM reports WHERE report_type = 'blog' AND content_id = ?");
            $stmtReport->execute([$blogId]);

            $stmtBlog = $this->db->prepare("DELETE FROM blogs WHERE id = ?");
            $stmtBlog->execute([$blogId]);

            $this->db->commit();
            return $stmtBlog->rowCount() > 0;
        } catch (Throwable $e) {
            $this->db->rollBack();
            Logger::error('Failed to delete blog: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllClubsForAdmin(): array
    {
        $sql = "SELECT
                    c.id,
                    c.name,
                    c.status,
                    c.created_at,
                    u.first_name AS creator_first_name,
                    u.last_name AS creator_last_name,
                    (SELECT COUNT(*) FROM club_memberships cm WHERE cm.club_id = c.id) AS member_count
                FROM clubs c
                INNER JOIN users u ON u.id = c.creator_id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteClubById(int $clubId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clubs WHERE id = ?");
        $stmt->execute([$clubId]);
        return $stmt->rowCount() > 0;
    }

    public function getAllEventsForAdmin(): array
    {
        $sql = "SELECT
                    e.id,
                    e.title,
                    e.status,
                    e.event_date,
                    e.created_at,
                    u.first_name AS creator_first_name,
                    u.last_name AS creator_last_name,
                    c.name AS club_name
                FROM events e
                INNER JOIN users u ON u.id = e.creator_id
                LEFT JOIN clubs c ON c.id = e.club_id
                ORDER BY e.event_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteEventById(int $eventId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        return $stmt->rowCount() > 0;
    }
}
