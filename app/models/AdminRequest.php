<?php
class AdminRequest extends Model
{
    protected $table = 'admin_requests';

    /**
     * Create a new admin request
     */
    public function createRequest(int $userId, string $requestType = 'club_admin', ?string $reason = null): int
    {
        // Check if there's already a pending request
        $existingRequest = $this->getPendingRequest($userId, $requestType);
        if ($existingRequest) {
            throw new Exception('You already have a pending admin request');
        }

        $sql = "INSERT INTO admin_requests (user_id, request_type, reason_request) 
                VALUES (:user_id, :request_type, :reason)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':request_type', $requestType, PDO::PARAM_STR);
        $stmt->bindValue(':reason', $reason, PDO::PARAM_STR);
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }

    /**
     * Get pending request for a user
     */
    public function getPendingRequest(int $userId, string $requestType = 'club_admin'): ?array
    {
        $sql = "SELECT * FROM admin_requests 
                WHERE user_id = :user_id AND request_type = :request_type AND status = 'pending'
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':request_type', $requestType, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all pending requests
     */
    public function getPendingRequests(): array
    {
        $sql = "SELECT ar.*, u.first_name, u.last_name, u.email 
                FROM admin_requests ar
                INNER JOIN users u ON ar.user_id = u.id
                WHERE ar.status = 'pending'
                ORDER BY ar.requested_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Approve a request
     */
    public function approveRequest(int $requestId, int $reviewedBy): bool
    {
        $request = $this->getById($requestId);
        if (!$request) {
            return false;
        }

        // Update request status
        $sql = "UPDATE admin_requests 
                SET status = 'approved', reviewed_by = :reviewed_by, reviewed_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $requestId, PDO::PARAM_INT);
        $stmt->bindValue(':reviewed_by', $reviewedBy, PDO::PARAM_INT);
        $stmt->execute();

        // Add to community_admins
        $clubModel = new Club();
        $clubModel->makeUserClubAdmin($request['user_id'], $request['request_type']);

        return true;
    }

    /**
     * Reject a request
     */
    public function rejectRequest(int $requestId, int $reviewedBy, ?string $reason = null): bool
    {
        $sql = "UPDATE admin_requests 
                SET status = 'rejected', reviewed_by = :reviewed_by, reason_rejection = :reason, reviewed_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $requestId, PDO::PARAM_INT);
        $stmt->bindValue(':reviewed_by', $reviewedBy, PDO::PARAM_INT);
        $stmt->bindValue(':reason', $reason, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

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
