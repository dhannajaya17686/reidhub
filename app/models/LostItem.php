<?php
class LostItem extends Model
{
    protected $table = 'lost_items';

    /**
     * Create a new lost item report.
     * Returns inserted ID on success, false on failure.
     *
     * @param array $data Lost item data
     * @return int|false
     */
    public function create(array $data)
    {
        try {
            Logger::info("Creating lost item report for user_id={$data['user_id']} item={$data['item_name']}");

            // Map priority to severity level
            $severityMap = [
                'low' => 'General',
                'medium' => 'Important',
                'high' => 'Critical'
            ];
            $severity = $severityMap[$data['priority'] ?? 'low'] ?? 'General';

            // Combine date and time
            $dateTimeLost = $data['date_lost'];
            if (!empty($data['time_lost'])) {
                $dateTimeLost .= ' ' . $data['time_lost'];
            } else {
                $dateTimeLost .= ' 00:00:00';
            }

            // Prepare contact details (combined for backward compatibility)
            $contactDetails = $data['mobile'];
            if (!empty($data['email'])) {
                $contactDetails .= ' | ' . $data['email'];
            }

            // Call stored procedure
            $sql = "CALL sp_submit_lost_item(
                :user_id, :item_name, :category, :description, 
                :last_known_location, :specific_area, :date_time_lost,
                :mobile, :email, :alt_contact, :contact_details,
                :severity_level, :reward_offered, :reward_amount,
                :reward_details, :special_instructions, @lost_item_id
            )";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':item_name' => $data['item_name'],
                ':category' => $data['category'] ?? null,
                ':description' => $data['description'],
                ':last_known_location' => $data['location'],
                ':specific_area' => $data['specific_area'] ?? null,
                ':date_time_lost' => $dateTimeLost,
                ':mobile' => $data['mobile'] ?? null,
                ':email' => $data['email'] ?? null,
                ':alt_contact' => $data['alt_contact'] ?? null,
                ':contact_details' => $contactDetails,
                ':severity_level' => $severity,
                ':reward_offered' => !empty($data['reward_offered']) ? 1 : 0,
                ':reward_amount' => $data['reward_amount'] ?? null,
                ':reward_details' => $data['reward_details'] ?? null,
                ':special_instructions' => $data['special_instructions'] ?? null
            ]);

            if (!$ok) {
                Logger::error("Failed to create lost item: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            // Get the output parameter
            $result = $this->db->query("SELECT @lost_item_id as id")->fetch(PDO::FETCH_ASSOC);
            $id = (int)$result['id'];

            Logger::info("Lost item created successfully with id={$id}");
            return $id;
        } catch (Throwable $e) {
            Logger::error("DB error creating lost item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all lost items.
     * @return array
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT 
                li.id,
                li.user_id,
                li.item_name,
                li.category,
                li.description,
                li.last_known_location,
                li.specific_area,
                li.date_time_lost,
                li.mobile,
                li.email,
                li.alt_contact,
                li.contact_details,
                li.severity_level,
                li.reward_offered,
                li.reward_amount,
                li.reward_details,
                li.special_instructions,
                li.status,
                li.noc_notified,
                li.created_at,
                li.updated_at,
                u.email as user_email,
                u.first_name,
                u.last_name
            FROM lost_items li
            LEFT JOIN users u ON li.user_id = u.id
            ORDER BY li.created_at DESC";
            
            $stmt = $this->db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            Logger::info("LostItem findAll() returned " . count($result) . " items");
            
            return $result;
        } catch (Throwable $e) {
            Logger::error('findAll lost items error: ' . $e->getMessage());
            Logger::error('SQL error details: ' . print_r($this->db->errorInfo(), true));
            return [];
        }
    }

    /**
     * Get lost items by user ID.
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        try {
            // Use direct SQL query instead of stored procedure for better column control
            $sql = "SELECT 
                li.id,
                li.user_id,
                li.item_name,
                li.category,
                li.description,
                li.last_known_location,
                li.specific_area,
                li.date_time_lost,
                li.mobile,
                li.email,
                li.alt_contact,
                li.contact_details,
                li.severity_level,
                li.reward_offered,
                li.reward_amount,
                li.reward_details,
                li.special_instructions,
                li.status,
                li.noc_notified,
                li.created_at,
                li.updated_at,
                u.email as user_email,
                u.first_name,
                u.last_name
            FROM lost_items li
            LEFT JOIN users u ON li.user_id = u.id
            WHERE li.user_id = ?
            ORDER BY li.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            Logger::info("LostItem findByUserId({$userId}) returned " . count($result) . " items");
            
            return $result;
        } catch (Throwable $e) {
            Logger::error('findByUserId lost items error: ' . $e->getMessage());
            Logger::error('SQL error details: ' . print_r($this->db->errorInfo(), true));
            return [];
        }
    }

    /**
     * Get lost item by ID with images.
     * @param int $itemId
     * @return array|null
     */
    public function findByIdWithImages(int $itemId): ?array
    {
        try {
            $sql = "CALL sp_get_lost_item_with_images(?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId]);
            
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$item) {
                return null;
            }

            // Get images from next result set
            $stmt->nextRowset();
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $item['images'] = $images;

            return $item;
        } catch (Throwable $e) {
            Logger::error('findByIdWithImages error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update lost item status.
     * @param int $itemId
     * @param int $userId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $itemId, int $userId, string $status): bool
    {
        try {
            $sql = "CALL sp_update_lost_item_status(?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$itemId, $userId, $status]);

            if (!$ok) {
                Logger::error("Failed to update lost item status");
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('updateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete lost item.
     * @param int $itemId
     * @param int $userId
     * @return bool
     */
    public function delete(int $itemId, int $userId): bool
    {
        try {
            $sql = "CALL sp_delete_lost_item(?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$itemId, $userId]);

            if (!$ok) {
                Logger::error("Failed to delete lost item");
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('delete lost item error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Filter lost items.
     * @param array $filters
     * @return array
     */
    public function filter(array $filters): array
    {
        try {
            $sql = "CALL sp_filter_lost_items(?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $filters['severity_level'] ?? null,
                $filters['status'] ?? null,
                $filters['location'] ?? null,
                $filters['date_from'] ?? null,
                $filters['date_to'] ?? null
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('filter lost items error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get critical lost items (for NOC/Admin).
     * @return array
     */
    public function findCritical(): array
    {
        try {
            $sql = "CALL sp_get_critical_items()";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findCritical items error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get items with rewards offered.
     * @return array
     */
    public function findWithRewards(): array
    {
        try {
            $sql = "CALL sp_get_items_with_rewards()";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findWithRewards error: ' . $e->getMessage());
            return [];
        }
    }
}
