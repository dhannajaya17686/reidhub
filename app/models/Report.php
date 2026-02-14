<?php

class Report extends Model {
    protected $table = 'reports';
    public $id;
    public $report_type;
    public $content_id;
    public $user_id;
    public $description;
    public $status;
    public $admin_notes;
    public $created_at;
    public $updated_at;

    /**
     * Create a new report
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO {$this->table} (report_type, content_id, user_id, description, status) 
                  VALUES (?, ?, ?, ?, 'pending')";
        
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            return $stmt->execute([$this->report_type, $this->content_id, $this->user_id, $this->description]);
        }
        return false;
    }

    /**
     * Get all reports (admin)
     * @return array
     */
    public function getAllReports() {
        $query = "SELECT r.*, u.first_name, u.last_name, u.email 
                  FROM {$this->table} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Get reports by status
     * @param string $status
     * @return array
     */
    public function getReportsByStatus($status) {
        $query = "SELECT r.*, u.first_name, u.last_name, u.email 
                  FROM {$this->table} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.status = ? 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute([$status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Get reports by type
     * @param string $type
     * @return array
     */
    public function getReportsByType($type) {
        $query = "SELECT r.*, u.first_name, u.last_name, u.email 
                  FROM {$this->table} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.report_type = ? 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute([$type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Update report status
     * @param int $id
     * @param string $status
     * @param string $admin_notes
     * @return bool
     */
    public function updateStatus($id, $status, $admin_notes = null) {
        if ($admin_notes) {
            $query = "UPDATE {$this->table} SET status = ?, admin_notes = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            if ($stmt) {
                return $stmt->execute([$status, $admin_notes, $id]);
            }
        } else {
            $query = "UPDATE {$this->table} SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            if ($stmt) {
                return $stmt->execute([$status, $id]);
            }
        }
        return false;
    }

    /**
     * Get report by ID
     * @param int $id
     * @return array|null
     */
    public function getReportById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt) {
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }
}
