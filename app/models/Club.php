<?php
class Club extends Model
{
    protected $table = 'clubs';

    /**
     * Get all active clubs
     */
    public function getAllClubs(string $category = null): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    (SELECT COUNT(*) FROM club_memberships WHERE club_id = c.id) as actual_member_count
                FROM clubs c
                INNER JOIN users u ON c.creator_id = u.id
                WHERE c.status = 'active'";
        
        if ($category && $category !== 'all') {
            $sql .= " AND c.category = ?";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($category && $category !== 'all') {
            $stmt->execute([$category]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get clubs created by a specific user
     */
    public function getClubsByCreator(int $userId): array
    {
        $sql = "SELECT 
                    c.*,
                    (SELECT COUNT(*) FROM club_memberships WHERE club_id = c.id) as actual_member_count
                FROM clubs c
                WHERE c.creator_id = ?
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get clubs where user is a member
     */
    public function getClubsByMember(int $userId): array
    {
        $sql = "SELECT 
                    c.*,
                    cm.role as member_role,
                    (SELECT COUNT(*) FROM club_memberships WHERE club_id = c.id) as actual_member_count
                FROM clubs c
                INNER JOIN club_memberships cm ON c.id = cm.club_id
                WHERE cm.user_id = ?
                ORDER BY cm.joined_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get clubs where user joined (not creator)
     */
    public function getJoinedClubs(int $userId): array
    {
        $sql = "SELECT 
                    c.*,
                    cm.role as member_role,
                    (SELECT COUNT(*) FROM club_memberships WHERE club_id = c.id) as actual_member_count
                FROM clubs c
                INNER JOIN club_memberships cm ON c.id = cm.club_id
                WHERE cm.user_id = ? AND c.creator_id != ?
                ORDER BY cm.joined_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single club by ID
     */
    public function getClubById(int $clubId): ?array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name as creator_first_name,
                    u.last_name as creator_last_name,
                    u.email as creator_email,
                    (SELECT COUNT(*) FROM club_memberships WHERE club_id = c.id) as actual_member_count
                FROM clubs c
                INNER JOIN users u ON c.creator_id = u.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clubId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new club
     */
    public function createClub(array $data): int|false
    {
        $sql = "INSERT INTO clubs 
                (name, description, category, creator_id, image_url, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['category'] ?? 'other',
            $data['creator_id'],
            $data['image_url'] ?? null
        ]);
        
        if ($success) {
            $clubId = $this->db->lastInsertId();
            // Auto-add creator as owner
            $this->addMember($clubId, $data['creator_id'], 'owner');
            return $clubId;
        }
        
        return false;
    }

    /**
     * Update club
     */
    public function updateClub(int $clubId, array $data): bool
    {
        $sql = "UPDATE clubs 
                SET name = ?, description = ?, category = ?, image_url = ?, updated_at = NOW()
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['category'] ?? 'other',
            $data['image_url'] ?? null,
            $clubId
        ]);
    }

    /**
     * Delete club
     */
    public function deleteClub(int $clubId): bool
    {
        $sql = "DELETE FROM clubs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$clubId]);
    }

    /**
     * Check if user is club owner/admin
     */
    public function isClubAdmin(int $clubId, int $userId): bool
    {
        $sql = "SELECT role FROM club_memberships 
                WHERE club_id = ? AND user_id = ? AND role IN ('owner', 'admin')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clubId, $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if user is club creator
     */
    public function isClubCreator(int $clubId, int $userId): bool
    {
        $sql = "SELECT creator_id FROM clubs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clubId]);
        $club = $stmt->fetch(PDO::FETCH_ASSOC);
        return $club && (int)$club['creator_id'] === $userId;
    }

    /**
     * Add member to club
     */
    public function addMember(int $clubId, int $userId, string $role = 'member'): bool
    {
        $sql = "INSERT INTO club_memberships (club_id, user_id, role, joined_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE role = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$clubId, $userId, $role, $role]);
    }

    /**
     * Remove member from club
     */
    public function removeMember(int $clubId, int $userId): bool
    {
        $sql = "DELETE FROM club_memberships WHERE club_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$clubId, $userId]);
    }

    /**
     * Check if user is club member
     */
    public function isMember(int $clubId, int $userId): bool
    {
        $sql = "SELECT id FROM club_memberships WHERE club_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clubId, $userId]);
        return $stmt->fetch() !== false;
    }
}
