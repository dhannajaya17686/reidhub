<?php
class UserQuestion extends Model
{
    /**
     * Create a new question
     */
    public function create($userId, $category, $subject, $message, $imagePath = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_questions (user_id, category, subject, message, image_path, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            
            if ($stmt->execute([$userId, $category, $subject, $message, $imagePath])) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            Logger::error("Error creating question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find question by ID
     */
    public function findById($questionId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_questions WHERE id = ?");
            $stmt->execute([$questionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error finding question: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all questions by user
     */
    public function getByUserId($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_questions 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting user questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all pending questions (for admin)
     */
    public function getAllPending()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_questions 
                WHERE status = 'pending' 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting pending questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get filtered questions with pagination
     */
    public function getFiltered($status = null, $category = null, $limit = 10, $offset = 0)
    {
        try {
            $query = "SELECT * FROM user_questions WHERE 1=1";
            $params = [];

            if ($status) {
                $query .= " AND status = ?";
                $params[] = $status;
            }

            if ($category) {
                $query .= " AND category = ?";
                $params[] = $category;
            }

            // Convert limit and offset to integers and add to query string
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " ORDER BY created_at DESC LIMIT " . $limit . " OFFSET " . $offset;

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting filtered questions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pending count
     */
    public function getPendingCount()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_questions WHERE status = 'pending'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error getting pending count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get unread reply count for user
     */
    public function getUnreadReplyCount($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM user_question_replies 
                WHERE question_id IN (
                    SELECT id FROM user_questions WHERE user_id = ?
                )
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error getting unread reply count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update question status
     */
    public function updateStatus($questionId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE user_questions 
                SET status = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            return $stmt->execute([$status, $questionId]);
        } catch (Exception $e) {
            Logger::error("Error updating question status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update question details
     */
    public function update($questionId, $userId, $category, $subject, $message, $imagePath = null)
    {
        try {
            if ($imagePath !== null) {
                $stmt = $this->db->prepare("
                    UPDATE user_questions 
                    SET category = ?, subject = ?, message = ?, image_path = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?
                ");
                return $stmt->execute([$category, $subject, $message, $imagePath, $questionId, $userId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE user_questions 
                    SET category = ?, subject = ?, message = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?
                ");
                return $stmt->execute([$category, $subject, $message, $questionId, $userId]);
            }
        } catch (Exception $e) {
            Logger::error("Error updating question: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add reply to question
     */
    public function addReply($questionId, $adminId, $replyMessage)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_question_replies (question_id, admin_id, reply_message)
                VALUES (?, ?, ?)
            ");
            
            if ($stmt->execute([$questionId, $adminId, $replyMessage])) {
                // Update question status to 'replied'
                $this->updateStatus($questionId, 'replied');
                return true;
            }
            return false;
        } catch (Exception $e) {
            Logger::error("Error adding reply: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get replies for a question
     */
    public function getReplies($questionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, a.email as admin_email 
                FROM user_question_replies r
                JOIN admins a ON r.admin_id = a.id
                WHERE r.question_id = ?
                ORDER BY r.created_at ASC
            ");
            $stmt->execute([$questionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error("Error getting replies: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total question count
     */
    public function getTotalCount()
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_questions");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error getting total count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count by category
     */
    public function getCountByCategory($category)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM user_questions 
                WHERE category = ?
            ");
            $stmt->execute([$category]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error getting count by category: " . $e->getMessage());
            return 0;
        }
    }
}
?>
