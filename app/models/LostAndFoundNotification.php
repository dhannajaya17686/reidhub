<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Logger.php';

/**
 * LostAndFoundNotification Model
 * Handles in-app notifications for Lost and Found system
 */
class LostAndFoundNotification extends Model
{
    protected $table = 'lostandfound_notifications';

    /**
     * Create a notification record
     */
    public function createNotification($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (
                    item_type, item_id, notification_type, 
                    recipient_type, recipient_id, message
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $data['item_type'],
                $data['item_id'],
                $data['notification_type'],
                $data['recipient_type'],
                $data['recipient_id'] ?? null,
                $data['message']
            ]);

            if ($success) {
                Logger::info("Notification created: type={$data['notification_type']} item_type={$data['item_type']} item_id={$data['item_id']}");
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            Logger::error("Failed to create notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast notification to all users
     */
    public function broadcastToAllUsers($itemType, $itemId, $message)
    {
        return $this->createNotification([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'notification_type' => 'user_broadcast',
            'recipient_type' => 'all_users',
            'message' => $message
        ]);
    }

    /**
     * Send NOC alert (for Critical lost items)
     */
    public function sendNOCAlert($itemId, $message)
    {
        return $this->createNotification([
            'item_type' => 'lost',
            'item_id' => $itemId,
            'notification_type' => 'noc_alert',
            'recipient_type' => 'noc',
            'message' => $message
        ]);
    }

    /**
     * Send Students' Union alert (for found items)
     */
    public function sendUnionAlert($itemId, $message)
    {
        return $this->createNotification([
            'item_type' => 'found',
            'item_id' => $itemId,
            'notification_type' => 'union_alert',
            'recipient_type' => 'union',
            'message' => $message
        ]);
    }

    /**
     * Get user's unread notifications
     */
    public function getUserNotifications($userId = null, $limit = 50)
    {
        try {
            $query = "
                SELECT n.*, 
                    CASE 
                        WHEN n.item_type = 'lost' THEN li.item_name
                        WHEN n.item_type = 'found' THEN fi.item_name
                    END as item_name,
                    CASE 
                        WHEN n.item_type = 'lost' THEN li.category
                        WHEN n.item_type = 'found' THEN fi.category
                    END as category
                FROM {$this->table} n
                LEFT JOIN lost_items li ON n.item_type = 'lost' AND n.item_id = li.id
                LEFT JOIN found_items fi ON n.item_type = 'found' AND n.item_id = fi.id
                WHERE n.recipient_type = 'all_users' 
                   OR (n.recipient_type = 'specific_user' AND n.recipient_id = ?)
                ORDER BY n.sent_at DESC
                LIMIT ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("Failed to get user notifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent notifications for items view
     */
    public function getRecentNotifications($limit = 20)
    {
        try {
            $query = "
                SELECT n.*, 
                    CASE 
                        WHEN n.item_type = 'lost' THEN li.item_name
                        WHEN n.item_type = 'found' THEN fi.item_name
                    END as item_name
                FROM {$this->table} n
                LEFT JOIN lost_items li ON n.item_type = 'lost' AND n.item_id = li.id
                LEFT JOIN found_items fi ON n.item_type = 'found' AND n.item_id = fi.id
                WHERE n.recipient_type = 'all_users'
                ORDER BY n.sent_at DESC
                LIMIT ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Logger::error("Failed to get recent notifications: " . $e->getMessage());
            return [];
        }
    }
}
