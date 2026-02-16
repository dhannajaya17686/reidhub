<?php

class ForumModel extends Model {

    // ==================================================================
    // 1. QUESTION LOGIC (Updated with Filter, Search, Tags, Pagination)
    // ==================================================================

    public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0) {
        // Base Query: Get question data + author info + counts
        // UPDATED: vote_count now calculates (Upvotes - Downvotes)
        $sql = "SELECT q.*, u.first_name, u.last_name, 
                (SELECT COALESCE(SUM(CASE WHEN vote_type = 'up' THEN 1 WHEN vote_type = 'down' THEN -1 ELSE 0 END), 0) 
                 FROM forum_votes v WHERE v.target_type = 'question' AND v.target_id = q.id) as vote_count,
                (SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id) as answer_count
                FROM forum_questions q
                JOIN users u ON q.user_id = u.id";
        
        $params = [];
        $whereClauses = [];

        // 1. Search Logic
        if ($search) {
            // If the search term is short (3 chars or less), use LIKE (Slower but finds "PHP")
            if (strlen($search) <= 3) {
                $whereClauses[] = "(q.title LIKE :search OR q.content LIKE :search)";
                $params[':search'] = "%$search%";
            } 
            // Otherwise use the High-Performance Full-Text Search
            else {
                $whereClauses[] = "MATCH(q.title, q.content) AGAINST(:search IN NATURAL LANGUAGE MODE)";
                $params[':search'] = $search;
            }
        }

        // 2. Tag Filter (High Performance Boolean Search)
        if ($tag) {
            // Boolean Mode allows precise matching. 
            $whereClauses[] = "MATCH(q.tags) AGAINST(:tag IN BOOLEAN MODE)";
            $params[':tag'] = '+"' . $tag . '"'; 
        }

        // 3. Unanswered Filter (Specific Filter Logic)
        if ($filter === 'unanswered') {
            $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id) = 0";
        }

        // Apply WHERE clauses
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        // 4. Sorting Logic
        if ($filter === 'trending') {
            // ADVANCED TRENDING: Weighted Score (Votes + Time)
            // Note: We use GREATEST(vote_count, 1) to prevent errors with negative scores in LOG10
            $sql .= " ORDER BY (LOG10(GREATEST(vote_count, 1)) + (UNIX_TIMESTAMP(q.created_at) / 45000)) DESC";

        } elseif ($filter === 'newest') {
            // NEWEST: Strict creation date
            $sql .= " ORDER BY q.created_at DESC";

        } elseif ($filter === 'unanswered') {
            // UNANSWERED: Show the newest unanswered questions first
            $sql .= " ORDER BY q.created_at DESC";

        } else {
            // Default Fallback
            $sql .= " ORDER BY q.created_at DESC"; 
        }

        // 5. Pagination
        $sql .= " LIMIT :limit OFFSET :offset";
        
        // Prepare & Execute
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper: Get total count for pagination (matches filter logic)
    public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null) {
        $sql = "SELECT COUNT(*) as total FROM forum_questions q";
        $whereClauses = [];
        $params = [];
        
        if ($search) {
             $whereClauses[] = "(title LIKE :search OR content LIKE :search)";
             $params[':search'] = "%$search%";
        }
        if ($tag) {
             $whereClauses[] = "tags LIKE :tag";
             $params[':tag'] = "%$tag%";
        }
        
        // Fix: Ensure the count respects the 'unanswered' filter
        if ($filter === 'unanswered') {
            $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id) = 0";
        }
        
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getQuestionById($id) {
        // UPDATED: vote_count uses Sum of Up/Down
        $sql = "SELECT q.*, u.first_name, u.last_name,
                (SELECT COALESCE(SUM(CASE WHEN vote_type = 'up' THEN 1 WHEN vote_type = 'down' THEN -1 ELSE 0 END), 0) 
                 FROM forum_votes v WHERE v.target_type = 'question' AND v.target_id = q.id) as vote_count
                FROM forum_questions q 
                JOIN users u ON q.user_id = u.id 
                WHERE q.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- NEW METHOD: Find Similar Questions (For Suggestions) ---
    public function findSimilarQuestions($query, $limit = 3) {
        // Matches against title or content using Full-Text Search, or a simple LIKE fallback
        $sql = "SELECT id, title FROM forum_questions 
                WHERE MATCH(title, content) AGAINST(:query IN NATURAL LANGUAGE MODE) 
                OR title LIKE :likeQuery
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', $query);
        $stmt->bindValue(':likeQuery', "%$query%");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createQuestion($userId, $data) {
        $sql = "INSERT INTO forum_questions (user_id, title, content, category, tags) 
                VALUES (:uid, :title, :content, :cat, :tags)";
        
        $stmt = $this->db->prepare($sql);
        
        $tags = isset($data['tags']) ? $data['tags'] : null;

        return $stmt->execute([
            ':uid' => $userId,
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':cat' => $data['category'],
            ':tags' => $tags
        ]);
    }

    // ==================================================================
    // 2. ANSWER LOGIC
    // ==================================================================

    public function getAnswers($questionId) {
        // UPDATED: vote_count uses Sum of Up/Down
        // UPDATED: We sort by 'is_accepted DESC' so the solution appears first!
        $sql = "SELECT a.*, u.first_name, u.last_name, 
                (SELECT COALESCE(SUM(CASE WHEN vote_type = 'up' THEN 1 WHEN vote_type = 'down' THEN -1 ELSE 0 END), 0) 
                 FROM forum_votes v WHERE v.target_type = 'answer' AND v.target_id = a.id) as vote_count
                FROM forum_answers a
                JOIN users u ON a.user_id = u.id
                WHERE a.question_id = :qid
                ORDER BY a.is_accepted DESC, a.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':qid' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAnswer($userId, $questionId, $content) {
        $sql = "INSERT INTO forum_answers (user_id, question_id, content) VALUES (:uid, :qid, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':qid' => $questionId,
            ':content' => $content
        ]);
    }

    // --- NEW METHOD: Mark Answer as Accepted ---
    public function markAnswerAsAccepted($questionId, $answerId) {
        // 1. Reset all answers for this question to 0 (Not Accepted)
        // This ensures there is only ever one Accepted Answer per question.
        $resetSql = "UPDATE forum_answers SET is_accepted = 0 WHERE question_id = :qid";
        $this->db->prepare($resetSql)->execute([':qid' => $questionId]);

        // 2. Set the specific answer to 1 (Accepted)
        $setSql = "UPDATE forum_answers SET is_accepted = 1 WHERE id = :aid AND question_id = :qid";
        return $this->db->prepare($setSql)->execute([
            ':aid' => $answerId,
            ':qid' => $questionId
        ]);
    }

    // ==================================================================
    // 3. VOTING LOGIC (UPDATED for Up/Down Support)
    // ==================================================================

    public function toggleVote($userId, $type, $targetId, $voteValue = 'up') {
        // 1. Check if ANY vote exists for this user on this item
        $sql = "SELECT id, vote_type FROM forum_votes 
                WHERE user_id = :uid AND target_type = :type AND target_id = :tid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId, ':type' => $type, ':tid' => $targetId]);
        $existingVote = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingVote) {
            // A vote exists. Check if it is the SAME as the new vote.
            if ($existingVote['vote_type'] === $voteValue) {
                // Same vote -> Remove it (Toggle Off)
                $del = "DELETE FROM forum_votes WHERE id = :id";
                $this->db->prepare($del)->execute([':id' => $existingVote['id']]);
                return 'removed';
            } else {
                // Different vote -> Update it (Switch Up to Down, or vice versa)
                $upd = "UPDATE forum_votes SET vote_type = :val WHERE id = :id";
                $this->db->prepare($upd)->execute([':val' => $voteValue, ':id' => $existingVote['id']]);
                return 'updated';
            }
        } else {
            // No vote exists -> Create new one
            $ins = "INSERT INTO forum_votes (user_id, target_type, target_id, vote_type) 
                    VALUES (:uid, :type, :tid, :val)";
            $this->db->prepare($ins)->execute([
                ':uid' => $userId, 
                ':type' => $type, 
                ':tid' => $targetId, 
                ':val' => $voteValue
            ]);
            return 'added';
        }
    }

    public function getVoteCount($type, $targetId) {
        // UPDATED: Calculates Score (Up - Down)
        $sql = "SELECT 
                COALESCE(SUM(CASE WHEN vote_type = 'up' THEN 1 WHEN vote_type = 'down' THEN -1 ELSE 0 END), 0) as count 
                FROM forum_votes 
                WHERE target_type = :type AND target_id = :tid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':type' => $type, ':tid' => $targetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['count'] : 0;
    }

    // ==================================================================
    // 4. BOOKMARK LOGIC
    // ==================================================================

    public function toggleBookmark($userId, $questionId) {
        $sql = "SELECT id FROM forum_bookmarks WHERE user_id = :uid AND question_id = :qid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId, ':qid' => $questionId]);

        if ($stmt->rowCount() > 0) {
            $del = "DELETE FROM forum_bookmarks WHERE user_id = :uid AND question_id = :qid";
            $this->db->prepare($del)->execute([':uid' => $userId, ':qid' => $questionId]);
            return 'removed';
        } else {
            $ins = "INSERT INTO forum_bookmarks (user_id, question_id) VALUES (:uid, :qid)";
            $this->db->prepare($ins)->execute([':uid' => $userId, ':qid' => $questionId]);
            return 'added';
        }
    }

    // ==================================================================
    // 5. REPORTING LOGIC
    // ==================================================================

    public function createReport($userId, $type, $targetId, $reason) {
        $sql = "INSERT INTO forum_reports (user_id, target_type, target_id, reason) 
                VALUES (:uid, :type, :tid, :reason)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId, 
            ':type' => $type, 
            ':tid' => $targetId, 
            ':reason' => $reason
        ]);
    }

    // ==================================================================
    // 6. EDIT & DELETE LOGIC (Questions & Answers)
    // ==================================================================

    public function checkOwnership($type, $id, $userId) {
        $table = ($type === 'question') ? 'forum_questions' : 'forum_answers';
        $sql = "SELECT id FROM $table WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }

    public function deleteContent($type, $id, $userId) {
        if (!$this->checkOwnership($type, $id, $userId)) return false;
        
        $table = ($type === 'question') ? 'forum_questions' : 'forum_answers';
        $sql = "DELETE FROM $table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function updateQuestion($id, $userId, $title, $content) {
        if (!$this->checkOwnership('question', $id, $userId)) return false;

        $sql = "UPDATE forum_questions SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);
    }

    public function updateAnswer($id, $userId, $content) {
        if (!$this->checkOwnership('answer', $id, $userId)) return false;

        $sql = "UPDATE forum_answers SET content = :content WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':content' => $content, ':id' => $id]);
    }

    // ==================================================================
    // 7. COMMENT LOGIC
    // ==================================================================

    public function addComment($userId, $parentType, $parentId, $content) {
        $sql = "INSERT INTO forum_comments (user_id, parent_type, parent_id, content) 
                VALUES (:uid, :ptype, :pid, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId, 
            ':ptype' => $parentType, 
            ':pid' => $parentId, 
            ':content' => $content
        ]);
    }

    public function getComments($parentType, $parentId) {
        $sql = "SELECT c.*, u.first_name, u.last_name 
                FROM forum_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.parent_type = :ptype AND c.parent_id = :pid
                ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ptype' => $parentType, ':pid' => $parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================================================================
    // 8. COMMENT DELETION LOGIC
    // ==================================================================

    public function deleteComment($commentId, $userId) {
        // 1. Check Ownership
        $sql = "SELECT id FROM forum_comments WHERE id = :id AND user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $commentId, ':uid' => $userId]);

        if ($stmt->rowCount() > 0) {
            // 2. Delete the comment
            $delSql = "DELETE FROM forum_comments WHERE id = :id";
            $delStmt = $this->db->prepare($delSql);
            return $delStmt->execute([':id' => $commentId]);
        }
        return false; // User didn't own the comment or it doesn't exist
    }

    // ==================================================================
    // 9. NOTIFICATION LOGIC
    // ==================================================================

    public function createNotification($userId, $type, $message, $link) {
        $sql = "INSERT INTO notifications (user_id, type, message, link) 
                VALUES (:uid, :type, :msg, :link)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':type' => $type,
            ':msg' => $message,
            ':link' => $link
        ]);
    }

    // Helper to get answer details (needed to find the author for notifications)
    public function getAnswersByAnswerId($answerId) {
        $sql = "SELECT * FROM forum_answers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $answerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- NEW: Get recent notifications for a user ---
    public function getUserNotifications($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':uid', $userId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NEW: Get count of unread notifications ---
    public function getUnreadNotificationCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :uid AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['count'] : 0;
    }

    // --- NEW: Mark a specific notification as read ---
    public function markNotificationAsRead($id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}