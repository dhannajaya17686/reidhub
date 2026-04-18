<?php
class ForumAnswer extends Model
{
    protected $table = 'forum_answers';

    /**
     * Post an answer to a question.
     */
    public function create(int $questionId, int $userId, string $body): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (question_id, user_id, body, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())"
            );
            if (!$stmt->execute([$questionId, $userId, $body])) {
                return false;
            }
            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) {
            Logger::error('ForumAnswer create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count answers given by a specific user.
     */
    public function countByUser(int $userId): int
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            Logger::error('ForumAnswer countByUser error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all answers for a question with author info.
     */
    public function findByQuestion(int $questionId): array
    {
        try {
            $sql = "SELECT
                        a.id, a.body, a.votes, a.is_accepted, a.created_at,
                        u.first_name, u.last_name
                    FROM {$this->table} a
                    LEFT JOIN users u ON u.id = a.user_id
                    WHERE a.question_id = ?
                    ORDER BY a.is_accepted DESC, a.votes DESC, a.created_at ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$questionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('ForumAnswer findByQuestion error: ' . $e->getMessage());
            return [];
        }
    }
}
