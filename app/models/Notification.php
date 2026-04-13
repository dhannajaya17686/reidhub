<?php

class Notification extends Model
{
    protected $table = 'notifications';

    public function createNotification(array $data): int|false
    {
        try {
            $sql = "INSERT INTO {$this->table}
                (recipient_id, recipient_role, content, `from`, topic, `timestamp`, isRead, type)
                VALUES (:recipient_id, :recipient_role, :content, :from, :topic, NOW(), 0, :type)";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':recipient_id' => $data['recipient_id'] ?? null,
                ':recipient_role' => $data['recipient_role'] ?? null,
                ':content' => $data['content'],
                ':from' => $data['from'],
                ':topic' => $data['topic'] ?? null,
                ':type' => $data['type'],
            ]);

            if (!$ok) {
                Logger::error('createNotification failed: ' . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) {
            Logger::error('createNotification error: ' . $e->getMessage());
            return false;
        }
    }

    public function getNotifications(array $filters = []): array
    {
        try {
            $where = [];
            $params = [];

            if (isset($filters['recipient_id']) && isset($filters['recipient_role'])) {
                $where[] = 'recipient_id = :recipient_id AND recipient_role = :recipient_role';
                $params[':recipient_id'] = (int)$filters['recipient_id'];
                $params[':recipient_role'] = (string)$filters['recipient_role'];
            }

            if (!empty($filters['type'])) {
                $where[] = 'type = :type';
                $params[':type'] = (string)$filters['type'];
            }

            if (!empty($filters['from'])) {
                $where[] = '`from` = :from';
                $params[':from'] = (string)$filters['from'];
            }

            if (array_key_exists('isRead', $filters) && $filters['isRead'] !== null) {
                $where[] = 'isRead = :isRead';
                $params[':isRead'] = (int)$filters['isRead'];
            }

            if (!empty($filters['topic'])) {
                $where[] = 'topic = :topic';
                $params[':topic'] = (string)$filters['topic'];
            }

            if (!empty($filters['topicPrefix'])) {
                $where[] = 'topic LIKE :topicPrefix';
                $params[':topicPrefix'] = (string)$filters['topicPrefix'] . '%';
            }

            $sql = "SELECT id, content, `from`, topic, `timestamp`, isRead, type, recipient_role, recipient_id
                    FROM {$this->table}";

            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $limit = isset($filters['limit']) ? (int)$filters['limit'] : 50;
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;

            $limit = max(1, min(200, $limit));
            $offset = max(0, $offset);

            $sql .= ' ORDER BY `timestamp` DESC LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($rows as &$row) {
                $row['id'] = (int)$row['id'];
                $row['isRead'] = (bool)$row['isRead'];
                $row['recipient_id'] = $row['recipient_id'] !== null ? (int)$row['recipient_id'] : null;
            }

            return $rows;
        } catch (Throwable $e) {
            Logger::error('getNotifications error: ' . $e->getMessage());
            return [];
        }
    }

    public function markAsRead(int $notificationId, ?int $recipientId = null, ?string $recipientRole = null): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET isRead = 1 WHERE id = :id";
            $params = [':id' => $notificationId];

            if ($recipientId !== null && $recipientRole !== null) {
                $sql .= ' AND recipient_id = :recipient_id AND recipient_role = :recipient_role';
                $params[':recipient_id'] = $recipientId;
                $params[':recipient_role'] = $recipientRole;
            }

            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute($params)) {
                Logger::error('markAsRead failed: ' . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('markAsRead error: ' . $e->getMessage());
            return false;
        }
    }
}
