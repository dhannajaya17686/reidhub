<?php
class ClubAdmin extends Model
{
    /**
     * Get request by ID
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT ar.*, u.first_name, u.last_name, u.email 
                FROM admin_requests ar
                INNER JOIN users u ON ar.user_id = u.id
                WHERE ar.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Check if user has an approved club admin request
     */
    public function isApprovedClubAdmin(int $userId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM admin_requests 
                WHERE user_id = :user_id AND request_type = 'club_admin' AND status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && (int)$result['count'] > 0;
    }
}
