<?php
class ForumQuestion extends Model
{
    protected $table = 'forum_questions';

    /**
     * Create a new forum question.
     */
    public function create(int $userId, string $title, string $body): int|false
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (user_id, title, body, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())"
            );
            if (!$stmt->execute([$userId, $title, $body])) {
                return false;
            }
            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) {
            Logger::error('ForumQuestion create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the single most recent open question with author and answer count.
     * Used on the user dashboard "Answer Question" card.
     */
    public function getLatestUnanswered(): ?array
    {
        try {
            $sql = "SELECT
                        q.id,
                        q.title,
                        q.body,
                        q.votes,
                        q.status,
                        q.created_at,
                        u.first_name,
                        u.last_name,
                        COUNT(a.id) AS answer_count
                    FROM {$this->table} q
                    LEFT JOIN users          u ON u.id = q.user_id
                    LEFT JOIN forum_answers  a ON a.question_id = q.id
                    WHERE q.status = 'open'
                    GROUP BY q.id, q.title, q.body, q.votes, q.status, q.created_at,
                             u.first_name, u.last_name
                    ORDER BY q.created_at DESC
                    LIMIT 1";
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            Logger::error('getLatestUnanswered error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Count questions posted by a specific user.
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
            Logger::error('ForumQuestion countByUser error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all questions (paginated) with author and answer counts.
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        try {
            $sql = "SELECT
                        q.id, q.title, q.body, q.votes, q.views, q.status, q.created_at,
                        u.first_name, u.last_name,
                        COUNT(a.id) AS answer_count
                    FROM {$this->table} q
                    LEFT JOIN users         u ON u.id = q.user_id
                    LEFT JOIN forum_answers a ON a.question_id = q.id
                    GROUP BY q.id, q.title, q.body, q.votes, q.views, q.status, q.created_at,
                             u.first_name, u.last_name
                    ORDER BY q.created_at DESC
                    LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('ForumQuestion findAll error: ' . $e->getMessage());
            return [];
        }
    }
}
