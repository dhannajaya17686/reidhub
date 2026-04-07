<?php

class ForumAdminModel extends Model
{
    public function getQuestionStats(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_questions,
                    SUM(CASE WHEN moderation_status = 'active' THEN 1 ELSE 0 END) AS active_questions,
                    SUM(CASE WHEN moderation_status = 'hidden' THEN 1 ELSE 0 END) AS hidden_questions,
                    SUM(CASE WHEN moderation_status = 'deleted' THEN 1 ELSE 0 END) AS deleted_questions
                FROM forum_questions";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return $stats ?: [
            'total_questions' => 0,
            'active_questions' => 0,
            'hidden_questions' => 0,
            'deleted_questions' => 0
        ];
    }

    public function getReportStats(): array
    {
        $sql = "SELECT
                    COUNT(*) AS total_reports,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_reports,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_reports,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) AS dismissed_reports
                FROM forum_reports";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return $stats ?: [
            'total_reports' => 0,
            'pending_reports' => 0,
            'resolved_reports' => 0,
            'dismissed_reports' => 0
        ];
    }

    public function getQuestionsForModeration(
        string $status = 'all',
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $limit = 25
    ): array {
        $sql = "SELECT q.id, q.user_id, q.title, q.content, q.category, q.tags, q.moderation_status, q.moderation_note, q.created_at,
                       u.first_name, u.last_name,
                       (SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id AND a.moderation_status <> 'deleted') AS answer_count
                FROM forum_questions q
                JOIN users u ON u.id = q.user_id
                WHERE 1=1";

        $params = [];

        if ($status !== 'all') {
            $sql .= " AND q.moderation_status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (q.title LIKE :search OR q.content LIKE :search OR q.category LIKE :search OR q.tags LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($dateFrom) {
            $sql .= " AND DATE(q.created_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(q.created_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " ORDER BY q.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnswersForModeration(
        string $status = 'all',
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $limit = 25
    ): array {
        $sql = "SELECT a.id, a.question_id, a.user_id, a.content, a.is_accepted, a.moderation_status, a.moderation_note, a.created_at,
                       u.first_name, u.last_name,
                       q.title AS question_title
                FROM forum_answers a
                JOIN users u ON u.id = a.user_id
                JOIN forum_questions q ON q.id = a.question_id
                WHERE 1=1";

        $params = [];

        if ($status !== 'all') {
            $sql .= " AND a.moderation_status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (a.content LIKE :search OR q.title LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($dateFrom) {
            $sql .= " AND DATE(a.created_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(a.created_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommentsForModeration(
        string $status = 'all',
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $limit = 25
    ): array {
        $sql = "SELECT c.id, c.parent_type, c.parent_id, c.user_id, c.content, c.moderation_status, c.moderation_note, c.created_at,
                       u.first_name, u.last_name,
                       a.question_id AS answer_question_id
                FROM forum_comments c
                JOIN users u ON u.id = c.user_id
                LEFT JOIN forum_answers a ON c.parent_type = 'answer' AND a.id = c.parent_id
                WHERE 1=1";

        $params = [];

        if ($status !== 'all') {
            $sql .= " AND c.moderation_status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND c.content LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if ($dateFrom) {
            $sql .= " AND DATE(c.created_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(c.created_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function moderateQuestion(int $questionId, int $adminId, string $action, ?string $note = null): bool
    {
        $question = $this->getQuestionModerationTarget($questionId);
        if (!$question) {
            return false;
        }

        try {
            if ($this->db->inTransaction() === false) {
                $this->db->beginTransaction();
            }

            $ok = $this->moderateContentByTable('forum_questions', $questionId, $adminId, $action, $note);
            if (!$ok) {
                $this->db->rollBack();
                return false;
            }

            $cascadeOk = $this->cascadeQuestionModeration($questionId, $adminId, $action);
            if (!$cascadeOk) {
                $this->db->rollBack();
                return false;
            }

            if ($action === 'delete') {
                $reason = trim((string)$note);
                if ($reason === '') {
                    $reason = 'Your question was removed by forum moderation.';
                }

                $shortTitle = trim((string)($question['title'] ?? ''));
                if ($shortTitle === '') {
                    $shortTitle = 'your question';
                }

                $notificationMessage = 'Your question was removed: ' . $reason;
                if (strlen($notificationMessage) > 255) {
                    $notificationMessage = substr($notificationMessage, 0, 252) . '...';
                }

                $this->createUserNotification(
                    (int)$question['user_id'],
                    'forum_moderation',
                    $notificationMessage,
                    '/dashboard/forum/all'
                );

                $subject = 'Your forum question was removed';
                $body = 'Question: ' . $shortTitle . "\n\nReason: " . $reason;
                $this->sendAdminMessage($adminId, (int)$question['user_id'], 'warning', $subject, $body);
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            Logger::error('ForumAdminModel moderateQuestion cascade exception: ' . $e->getMessage());
            return false;
        }
    }

    public function moderateAnswer(int $answerId, int $adminId, string $action, ?string $note = null): bool
    {
        return $this->moderateContentByTable('forum_answers', $answerId, $adminId, $action, $note);
    }

    public function moderateComment(int $commentId, int $adminId, string $action, ?string $note = null): bool
    {
        return $this->moderateContentByTable('forum_comments', $commentId, $adminId, $action, $note);
    }

    public function updateQuestionClassification(int $questionId, int $adminId, string $category, string $tags): bool
    {
        if (!$this->contentExists('forum_questions', $questionId)) {
            return false;
        }

        $sql = "UPDATE forum_questions
                SET category = :category,
                    tags = :tags,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE id = :question_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':category' => $category,
            ':tags' => $tags,
            ':admin_id' => $adminId,
            ':question_id' => $questionId
        ]);

        return true;
    }

    public function updateQuestionContentByAdmin(int $questionId, int $adminId, string $title, string $content): bool
    {
        if (!$this->contentExists('forum_questions', $questionId)) {
            return false;
        }

        $sql = "UPDATE forum_questions
                SET title = :title,
                    content = :content,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE id = :question_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':admin_id' => $adminId,
            ':question_id' => $questionId
        ]);
        return true;
    }

    public function updateAnswerContentByAdmin(int $answerId, int $adminId, string $content): bool
    {
        if (!$this->contentExists('forum_answers', $answerId)) {
            return false;
        }

        $sql = "UPDATE forum_answers
                SET content = :content,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE id = :answer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':content' => $content,
            ':admin_id' => $adminId,
            ':answer_id' => $answerId
        ]);
        return true;
    }

    public function updateCommentContentByAdmin(int $commentId, int $adminId, string $content): bool
    {
        if (!$this->contentExists('forum_comments', $commentId)) {
            return false;
        }

        $sql = "UPDATE forum_comments
                SET content = :content,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE id = :comment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':content' => $content,
            ':admin_id' => $adminId,
            ':comment_id' => $commentId
        ]);
        return true;
    }

    public function getPendingReports(int $limit = 30): array
    {
        $sql = "SELECT r.id, r.user_id, r.target_type, r.target_id, r.reason, r.status, r.created_at,
                       ru.first_name AS reporter_first_name, ru.last_name AS reporter_last_name
                FROM forum_reports r
                JOIN users ru ON ru.id = r.user_id
                WHERE r.status = 'pending'
                ORDER BY r.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($reports as &$report) {
            $report['target_preview'] = $this->getReportTargetPreview($report['target_type'], (int)$report['target_id']);
        }
        unset($report);

        return $reports;
    }

    public function reviewReport(int $reportId, int $adminId, string $action, string $reviewMessage): bool
    {
        if (!in_array($action, ['resolved', 'dismissed'], true)) {
            return false;
        }

        $sql = "UPDATE forum_reports
                SET status = :status,
                    reviewed_by_admin_id = :admin_id,
                    review_message = :review_message,
                    reviewed_at = NOW()
                WHERE id = :report_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':status' => $action,
            ':admin_id' => $adminId,
            ':review_message' => $reviewMessage,
            ':report_id' => $reportId
        ]);

        return $stmt->rowCount() > 0;
    }

    public function moderateReportedTarget(int $adminId, string $targetType, int $targetId, string $action, ?string $note = null): bool
    {
        $table = $this->resolveModerationTable($targetType);
        if ($table === null) {
            return false;
        }
        return $this->moderateContentByTable($table, $targetId, $adminId, $action, $note);
    }

    public function createUserSuspension(int $adminId, int $userId, string $reason, ?string $endsAt, bool $isPermanent): bool
    {
        if (!$this->userExists($userId)) {
            return false;
        }

        $disableCurrentSql = "UPDATE forum_user_suspensions
                              SET is_active = 0
                              WHERE user_id = :user_id AND is_active = 1";
        $this->db->prepare($disableCurrentSql)->execute([':user_id' => $userId]);

        $sql = "INSERT INTO forum_user_suspensions (user_id, admin_id, reason, ends_at, is_permanent, is_active)
                VALUES (:user_id, :admin_id, :reason, :ends_at, :is_permanent, 1)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':admin_id' => $adminId,
            ':reason' => $reason,
            ':ends_at' => $isPermanent ? null : $endsAt,
            ':is_permanent' => $isPermanent ? 1 : 0
        ]);
    }

    public function liftUserSuspension(int $userId): bool
    {
        $sql = "UPDATE forum_user_suspensions
                SET is_active = 0
                WHERE user_id = :user_id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    public function getActiveSuspensions(int $limit = 20): array
    {
        $sql = "SELECT s.*, u.first_name, u.last_name
                FROM forum_user_suspensions s
                JOIN users u ON u.id = s.user_id
                WHERE s.is_active = 1
                  AND (s.is_permanent = 1 OR s.ends_at IS NULL OR s.ends_at > NOW())
                ORDER BY s.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendAdminMessage(int $adminId, int $userId, string $type, string $subject, string $body): bool
    {
        if (!in_array($type, ['warning', 'message'], true)) {
            return false;
        }
        if (!$this->userExists($userId)) {
            return false;
        }

        $sql = "INSERT INTO forum_admin_messages (user_id, admin_id, message_type, subject, body)
                VALUES (:user_id, :admin_id, :message_type, :subject, :body)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':admin_id' => $adminId,
            ':message_type' => $type,
            ':subject' => $subject,
            ':body' => $body
        ]);
    }

    public function getRecentAdminMessages(int $limit = 20): array
    {
        $sql = "SELECT m.id, m.user_id, m.message_type, m.subject, m.body, m.created_at,
                       u.first_name, u.last_name
                FROM forum_admin_messages m
                JOIN users u ON u.id = m.user_id
                ORDER BY m.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserSuspended(int $userId): bool
    {
        $sql = "SELECT id
                FROM forum_user_suspensions
                WHERE user_id = :user_id
                  AND is_active = 1
                  AND (is_permanent = 1 OR ends_at IS NULL OR ends_at > NOW())
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getQuestionModerationTarget(int $questionId): ?array
    {
        $stmt = $this->db->prepare("SELECT id, user_id, title FROM forum_questions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $questionId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function cascadeQuestionModeration(int $questionId, int $adminId, string $action): bool
    {
        $status = $this->mapQuestionActionToStatus($action);
        if ($status === null) {
            return false;
        }

        $answerIds = $this->getAnswerIdsByQuestion($questionId);
        $cascadeNote = $this->getCascadeModerationNote($action);
        $restoreNotes = [
            $this->getCascadeModerationNote('hide'),
            $this->getCascadeModerationNote('delete')
        ];

        if (in_array($action, ['hide', 'delete'], true)) {
            $answerOk = $this->updateAnswersByQuestion($questionId, $adminId, $status, $cascadeNote);
            if (!$answerOk) {
                return false;
            }

            $questionCommentsOk = $this->updateCommentsByParent('question', [$questionId], $adminId, $status, $cascadeNote);
            if (!$questionCommentsOk) {
                return false;
            }

            if (!empty($answerIds)) {
                $answerCommentsOk = $this->updateCommentsByParent('answer', $answerIds, $adminId, $status, $cascadeNote);
                if (!$answerCommentsOk) {
                    return false;
                }
            }

            return true;
        }

        if ($action === 'restore' || $action === 'activate') {
            $answerOk = $this->updateAnswersByQuestion($questionId, $adminId, 'active', null, $restoreNotes);
            if (!$answerOk) {
                return false;
            }

            $questionCommentsOk = $this->updateCommentsByParent('question', [$questionId], $adminId, 'active', null, $restoreNotes);
            if (!$questionCommentsOk) {
                return false;
            }

            if (!empty($answerIds)) {
                $answerCommentsOk = $this->updateCommentsByParent('answer', $answerIds, $adminId, 'active', null, $restoreNotes);
                if (!$answerCommentsOk) {
                    return false;
                }
            }

            return true;
        }

        return true;
    }

    private function getCascadeModerationNote(string $action): string
    {
        if ($action === 'hide') {
            return 'Hidden automatically because the parent question was hidden.';
        }
        if ($action === 'delete') {
            return 'Deleted automatically because the parent question was deleted.';
        }
        return 'Updated automatically because the parent question was moderated.';
    }

    private function getAnswerIdsByQuestion(int $questionId): array
    {
        $stmt = $this->db->prepare("SELECT id FROM forum_answers WHERE question_id = :question_id");
        $stmt->execute([':question_id' => $questionId]);
        return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id'));
    }

    private function updateAnswersByQuestion(
        int $questionId,
        int $adminId,
        string $status,
        ?string $note,
        array $onlyIfNotes = []
    ): bool {
        $sql = "UPDATE forum_answers
                SET moderation_status = :status,
                    moderation_note = :note,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE question_id = :question_id";

        $params = [
            ':status' => $status,
            ':note' => $note,
            ':admin_id' => $adminId,
            ':question_id' => $questionId
        ];

        if (!empty($onlyIfNotes)) {
            $placeholders = [];
            foreach ($onlyIfNotes as $index => $restoreNote) {
                $ph = ':restore_note_' . $index;
                $placeholders[] = $ph;
                $params[$ph] = $restoreNote;
            }
            $sql .= " AND moderation_note IN (" . implode(', ', $placeholders) . ")";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function updateCommentsByParent(
        string $parentType,
        array $parentIds,
        int $adminId,
        string $status,
        ?string $note,
        array $onlyIfNotes = []
    ): bool {
        if (empty($parentIds)) {
            return true;
        }

        $params = [
            ':parent_type' => $parentType,
            ':status' => $status,
            ':note' => $note,
            ':admin_id' => $adminId
        ];

        $idPlaceholders = [];
        foreach (array_values($parentIds) as $index => $parentId) {
            $ph = ':parent_id_' . $index;
            $idPlaceholders[] = $ph;
            $params[$ph] = (int)$parentId;
        }

        $sql = "UPDATE forum_comments
                SET moderation_status = :status,
                    moderation_note = :note,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE parent_type = :parent_type
                  AND parent_id IN (" . implode(', ', $idPlaceholders) . ")";

        if (!empty($onlyIfNotes)) {
            $notePlaceholders = [];
            foreach ($onlyIfNotes as $index => $restoreNote) {
                $ph = ':comment_restore_note_' . $index;
                $notePlaceholders[] = $ph;
                $params[$ph] = $restoreNote;
            }
            $sql .= " AND moderation_note IN (" . implode(', ', $notePlaceholders) . ")";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    private function createUserNotification(int $userId, string $type, string $message, ?string $link = null): bool
    {
        $sql = "INSERT INTO notifications (user_id, type, message, link)
                VALUES (:user_id, :type, :message, :link)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':message' => $message,
            ':link' => $link
        ]);
    }

    private function mapQuestionActionToStatus(string $action): ?string
    {
        if ($action === 'hide') {
            return 'hidden';
        }
        if ($action === 'delete') {
            return 'deleted';
        }
        if ($action === 'activate' || $action === 'restore') {
            return 'active';
        }
        return null;
    }

    private function getReportTargetPreview(string $type, int $targetId): string
    {
        if ($type === 'question') {
            $stmt = $this->db->prepare("SELECT title FROM forum_questions WHERE id = :id");
            $stmt->execute([':id' => $targetId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? ('Question: ' . $row['title']) : 'Question was removed';
        }

        if ($type === 'answer') {
            $stmt = $this->db->prepare("SELECT content FROM forum_answers WHERE id = :id");
            $stmt->execute([':id' => $targetId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? ('Answer: ' . substr($row['content'], 0, 120)) : 'Answer was removed';
        }

        if ($type === 'comment') {
            $stmt = $this->db->prepare("SELECT content FROM forum_comments WHERE id = :id");
            $stmt->execute([':id' => $targetId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? ('Comment: ' . substr($row['content'], 0, 120)) : 'Comment was removed';
        }

        return 'Unknown target';
    }

    private function resolveModerationTable(string $targetType): ?string
    {
        if ($targetType === 'question') return 'forum_questions';
        if ($targetType === 'answer') return 'forum_answers';
        if ($targetType === 'comment') return 'forum_comments';
        return null;
    }

    private function moderateContentByTable(string $table, int $targetId, int $adminId, string $action, ?string $note = null): bool
    {
        $status = $this->mapQuestionActionToStatus($action);
        Logger::debug('ForumAdminModel moderateContentByTable start: ' . json_encode([
            'table' => $table,
            'target_id' => $targetId,
            'admin_id' => $adminId,
            'action' => $action,
            'mapped_status' => $status,
            'note' => $note
        ]));

        if ($status === null) {
            Logger::warning('ForumAdminModel moderateContentByTable aborted: invalid action');
            return false;
        }

        $allowedTables = ['forum_questions', 'forum_answers', 'forum_comments'];
        if (!in_array($table, $allowedTables, true)) {
            Logger::warning('ForumAdminModel moderateContentByTable aborted: invalid table ' . $table);
            return false;
        }
        if (!$this->contentExists($table, $targetId)) {
            Logger::warning('ForumAdminModel moderateContentByTable aborted: target row missing');
            return false;
        }

        $sql = "UPDATE {$table}
                SET moderation_status = :status,
                    moderation_note = :note,
                    moderated_by_admin_id = :admin_id,
                    moderated_at = NOW()
                WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':status' => $status,
                ':note' => $note,
                ':admin_id' => $adminId,
                ':id' => $targetId
            ]);
        } catch (Throwable $e) {
            Logger::error('ForumAdminModel moderateContentByTable exception: ' . $e->getMessage());
            return false;
        }

        Logger::debug('ForumAdminModel moderateContentByTable success: ' . json_encode([
            'table' => $table,
            'target_id' => $targetId,
            'status' => $status
        ]));

        return true;
    }

    private function contentExists(string $table, int $id): bool
    {
        $allowedTables = ['forum_questions', 'forum_answers', 'forum_comments'];
        if (!in_array($table, $allowedTables, true)) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT id FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $exists = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        Logger::debug('ForumAdminModel contentExists: ' . json_encode([
            'table' => $table,
            'id' => $id,
            'exists' => $exists
        ]));
        return $exists;
    }

    private function userExists(int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }
}
