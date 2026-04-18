<?php
class FAQ extends Model
{
    /**
     * Get all active FAQs ordered by display_order
     */
    public function getAllActive()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, question, answer, display_order 
                FROM faqs 
                WHERE is_active = 1 
                ORDER BY display_order ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting active FAQs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get FAQ by ID
     */
    public function getFAQById($faqId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, question, answer, display_order 
                FROM faqs 
                WHERE id = ? AND is_active = 1
            ");
            $stmt->execute([$faqId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting FAQ: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get total count of active FAQs
     */
    public function getTotalCount()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM faqs WHERE is_active = 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error getting FAQ count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Search FAQs by keyword
     */
    public function search($keyword)
    {
        try {
            $searchTerm = '%' . $keyword . '%';
            $stmt = $this->db->prepare("
                SELECT id, question, answer, display_order 
                FROM faqs 
                WHERE is_active = 1 
                AND (question LIKE ? OR answer LIKE ?)
                ORDER BY display_order ASC
            ");
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error searching FAQs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create new FAQ
     */
    public function create($question, $answer, $displayOrder = 0)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO faqs (question, answer, display_order, is_active)
                VALUES (?, ?, ?, 1)
            ");
            return $stmt->execute([$question, $answer, $displayOrder]);
        } catch (Exception $e) {
            Logger::error("Error creating FAQ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update FAQ
     */
    public function update($faqId, $question, $answer, $displayOrder = 0)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE faqs 
                SET question = ?, answer = ?, display_order = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            return $stmt->execute([$question, $answer, $displayOrder, $faqId]);
        } catch (Exception $e) {
            Logger::error("Error updating FAQ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete FAQ (set is_active to 0)
     */
    public function softDelete($faqId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE faqs 
                SET is_active = 0, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            return $stmt->execute([$faqId]);
        } catch (Exception $e) {
            Logger::error("Error deleting FAQ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all FAQs (including inactive) for admin
     */
    public function getAllForAdmin()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, question, answer, display_order, is_active, created_at, updated_at
                FROM faqs 
                ORDER BY display_order ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting all FAQs for admin: " . $e->getMessage());
            return [];
        }
    }
}
?>
