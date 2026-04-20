<?php
class FoundItem extends Model
{
    protected $table = 'found_items';

    /**
     * Create a new found item report.
     * Returns inserted ID on success, false on failure.
     *
     * @param array $data Found item data
     * @return int|false
     */
    public function create(array $data)
    {
        try {
            Logger::info("Creating found item report for user_id={$data['user_id']} item={$data['item_name']}");

            // Combine date and time
            $dateTimeFound = $data['date_found'];
            if (!empty($data['time_found'])) {
                $dateTimeFound .= ' ' . $data['time_found'];
            } else {
                $dateTimeFound .= ' 00:00:00';
            }

            // Prepare contact details
            $contactDetails = $data['mobile'];
            if (!empty($data['email'])) {
                $contactDetails .= ' | ' . $data['email'];
            }

            // Call stored procedure
            $sql = "CALL sp_submit_found_item(
                :user_id, :item_name, :category, :description,
                :found_location, :specific_area, :date_time_found,
                :mobile, :email, :alt_contact, :contact_details,
                :item_condition, :current_location, :special_instructions,
                @found_item_id
            )";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':user_id' => $data['user_id'],
                ':item_name' => $data['item_name'],
                ':category' => $data['category'] ?? null,
                ':description' => $data['description'],
                ':found_location' => $data['location'],
                ':specific_area' => $data['specific_area'] ?? null,
                ':date_time_found' => $dateTimeFound,
                ':mobile' => $data['mobile'] ?? null,
                ':email' => $data['email'] ?? null,
                ':alt_contact' => $data['alt_contact'] ?? null,
                ':contact_details' => $contactDetails,
                ':item_condition' => $data['condition'] ?? 'Good',
                ':current_location' => $data['current_location'] ?? null,
                ':special_instructions' => $data['special_instructions'] ?? null
            ]);

            if (!$ok) {
                Logger::error("Failed to create found item: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            // Get the output parameter
            $result = $this->db->query("SELECT @found_item_id as id")->fetch(PDO::FETCH_ASSOC);
            $id = (int)$result['id'];

            Logger::info("Found item created successfully with id={$id}");
            return $id;
        } catch (Throwable $e) {
            Logger::error("DB error creating found item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all found items.
     * @return array
     */
    public function findAll(): array
    {
        try {
            $sql = "SELECT 
                fi.id,
                fi.user_id,
                fi.item_name,
                fi.category,
                fi.description,
                fi.found_location,
                fi.specific_area,
                fi.date_time_found,
                fi.mobile,
                fi.email,
                fi.alt_contact,
                fi.contact_details,
                fi.current_custody,
                fi.condition_status,
                fi.distinguishing_features,
                fi.special_instructions,
                fi.status,
                fi.created_at,
                fi.updated_at,
                u.email as user_email
            FROM found_items fi
            INNER JOIN users u ON fi.user_id = u.id
            ORDER BY fi.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findAll found items error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get found items by user ID.
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        try {
            $sql = "CALL sp_get_user_found_items(?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findByUserId found items error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get found item by ID with images.
     * @param int $itemId
     * @return array|null
     */
    public function findByIdWithImages(int $itemId): ?array
    {
        try {
            $sql = "CALL sp_get_found_item_with_images(?)";
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
     * Update found item status.
     * @param int $itemId
     * @param int $userId
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $itemId, int $userId, string $status): bool
    {
        try {
            // Use direct UPDATE since stored procedure doesn't exist yet
            $sql = "UPDATE found_items 
                    SET status = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$status, $itemId, $userId]);

            if (!$ok) {
                Logger::error("Failed to update found item status");
                return false;
            }

            Logger::info("Found item status updated: item_id={$itemId}, status={$status}");
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('updateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete found item.
     * @param int $itemId
     * @param int $userId
     * @return bool
     */
    public function delete(int $itemId, int $userId): bool
    {
        try {
            $sql = "CALL sp_delete_found_item(?, ?)";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$itemId, $userId]);

            if (!$ok) {
                Logger::error("Failed to delete found item");
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('delete found item error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Filter found items.
     * @param array $filters
     * @return array
     */
    public function filter(array $filters): array
    {
        try {
            $sql = "CALL sp_filter_found_items(?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $filters['status'] ?? null,
                $filters['location'] ?? null,
                $filters['date_from'] ?? null,
                $filters['date_to'] ?? null
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('filter found items error: ' . $e->getMessage());
            return [];
        }
    }
}
