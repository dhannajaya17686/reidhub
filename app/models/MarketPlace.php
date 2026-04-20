<?php
class MarketPlace extends Model
{
    protected $table = 'products';

    /** * Create a marketplace item. * Returns inserted ID on success, false on failure.*/
    public function createItem(array $data)
    {
        try {
            Logger::info("Creating marketplace item for seller_id={$data['seller_id']} title={$data['title']}");

            $sql = "INSERT INTO {$this->table} (
                seller_id, title, description, price, category, product_type, condition_type,
                stock_quantity, status, payment_methods, images,
                bank_name, bank_branch, account_name, account_number,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                $data['seller_id'],
                $data['title'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['product_type'],
                $data['condition_type'],
                $data['stock_quantity'],
                $data['status'] ?? 'active',
                $data['payment_methods'], // JSON string
                $data['images'],          // JSON string
                $data['bank_name'],
                $data['bank_branch'],
                $data['account_name'],
                $data['account_number'],
            ]);

            if (!$ok) {
                Logger::error("Failed to create marketplace item: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            $id = (int)$this->db->lastInsertId();
            Logger::info("Marketplace item created with id={$id}");
            return $id;
        } catch (Throwable $e) {
            Logger::error("DB error creating marketplace item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get minimal data of active items for a seller (for Active Items page).* */
    public function findActiveBySellerMinimal(int $sellerId): array
    {
        try {
            $sql = "SELECT id, title, price, condition_type, images, stock_quantity, updated_at
                    FROM {$this->table}
                WHERE seller_id = ? AND status = 'active' AND COALESCE(is_hidden_by_admin, 0) = 0
                    ORDER BY updated_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findActiveBySellerMinimal error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Public listings by category (merchandise | second-hand)
     * Optionally filter by condition_type (brand_new | used)
     * Returns minimal fields needed for cards.
     */
    public function findPublicByCategory(string $category, ?string $condition = null): array
    {
        try {
            $sql = "SELECT id, title, price, condition_type, images, stock_quantity, product_type, created_at
                    FROM {$this->table}
                    WHERE status = 'active' AND COALESCE(is_hidden_by_admin, 0) = 0 AND category = :category";
            $params = [':category' => $category];

            if ($condition !== null) {
                $sql .= " AND condition_type = :condition";
                $params[':condition'] = $condition;
            }

            $sql .= " ORDER BY created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findPublicByCategory error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Archive an item owned by a specific seller.
     */
    public function archiveItemForSeller(int $itemId, int $sellerId): bool
    {
        try {
            $sql = "UPDATE {$this->table}
                    SET status = 'archived', updated_at = NOW()
                    WHERE id = ? AND seller_id = ? AND status != 'archived'";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([$itemId, $sellerId]);

            if (!$ok) {
                Logger::error("archiveItemForSeller failed: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('archiveItemForSeller error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get minimal data of archived items for a seller.
     */
    public function findArchivedBySellerMinimal(int $sellerId): array
    {
        try {
            $sql = "SELECT id, title, price, condition_type, images, stock_quantity, updated_at,
                           COALESCE(is_hidden_by_admin, 0) AS is_hidden_by_admin,
                           hidden_by_admin_reason,
                           hidden_by_admin_at
                    FROM {$this->table}
                    WHERE seller_id = ? AND (status = 'archived' OR COALESCE(is_hidden_by_admin, 0) = 1)
                    ORDER BY updated_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('findArchivedBySellerMinimal error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Unarchive (set status to active) an item owned by a seller.
     */
    public function unarchiveItemForSeller(int $itemId, int $sellerId): bool
    {
        try {
            $sql = "UPDATE {$this->table}
                    SET status = 'active', updated_at = NOW()
                    WHERE id = ? AND seller_id = ? AND status = 'archived' AND COALESCE(is_hidden_by_admin, 0) = 0";
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute([$itemId, $sellerId])) {
                Logger::error("unarchiveItemForSeller failed: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('unarchiveItemForSeller error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a single item by id for a specific seller.
     */
    public function findItemByIdForSeller(int $itemId, int $sellerId): ?array
    {
        try {
            $sql = "SELECT id, seller_id, title, description, price, category, product_type, condition_type,
                           stock_quantity, status, COALESCE(is_hidden_by_admin, 0) AS is_hidden_by_admin,
                           hidden_by_admin_reason, hidden_by_admin_at, payment_methods, images,
                           bank_name, bank_branch, account_name, account_number, created_at, updated_at
                    FROM {$this->table}
                    WHERE id = ? AND seller_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $sellerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (Throwable $e) {
            Logger::error('findItemByIdForSeller error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an item for a specific seller.
     */
    public function updateItemForSeller(int $itemId, int $sellerId, array $data): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET
                        title = ?, description = ?, price = ?, category = ?, product_type = ?, condition_type = ?,
                        stock_quantity = ?, payment_methods = ?, images = ?, bank_name = ?, bank_branch = ?,
                        account_name = ?, account_number = ?, updated_at = NOW()
                    WHERE id = ? AND seller_id = ?";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                $data['title'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['product_type'],
                $data['condition_type'],
                $data['stock_quantity'],
                $data['payment_methods'], // JSON string
                $data['images'],          // JSON string
                $data['bank_name'],
                $data['bank_branch'],
                $data['account_name'],
                $data['account_number'],
                $itemId,
                $sellerId
            ]);

            if (!$ok) {
                Logger::error("updateItemForSeller failed: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }
            return $stmt->rowCount() >= 0; // treat no-change as success
        } catch (Throwable $e) {
            Logger::error('updateItemForSeller error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a public (active) product by ID for product details page.
     */
    public function findPublicItemById(int $id): ?array
    {
        try {
            $sql = "SELECT id, seller_id, title, description, price, category, product_type, condition_type,
                           stock_quantity, status, COALESCE(is_hidden_by_admin, 0) AS is_hidden_by_admin,
                           hidden_by_admin_reason, hidden_by_admin_at, images, payment_methods, created_at, updated_at
                    FROM {$this->table}
                    WHERE id = ? AND status = 'active' AND COALESCE(is_hidden_by_admin, 0) = 0
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (Throwable $e) {
            Logger::error('findPublicItemById error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Atomically reserve stock (decrease) if available.
     */
    public function reserveStock(int $productId, int $qty): bool
    {
        if ($qty <= 0) return true;
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET stock_quantity = stock_quantity - ?
                WHERE id = ? AND stock_quantity >= ?
                LIMIT 1
            ");
            $stmt->execute([$qty, $productId, $qty]);
            return $stmt->rowCount() === 1;
        } catch (Throwable $e) {
            Logger::error("reserveStock error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Release stock (increase) back.
     */
    public function releaseStock(int $productId, int $qty): bool
    {
        if ($qty <= 0) return true;
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET stock_quantity = stock_quantity + ?
                WHERE id = ?
                LIMIT 1
            ");
            $stmt->execute([$qty, $productId]);
            return $stmt->rowCount() === 1;
        } catch (Throwable $e) {
            Logger::error("releaseStock error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of active marketplace items
     */
    public function getTotalActiveCount(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (Throwable $e) {
            Logger::error("getTotalActiveCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent marketplace items
     */
    public function getRecentItems(int $limit = 5): array
    {
        try {
            $sql = "SELECT 
                        id,
                        title,
                        seller_id,
                        created_at
                    FROM {$this->table}
                    WHERE status = 'active'
                    ORDER BY created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error("getRecentItems error: " . $e->getMessage());
            return [];
        }
    }

    public function setAdminHiddenStatus(int $productId, bool $hidden, ?string $reason = null): bool
    {
        try {
            if ($productId <= 0) {
                return false;
            }

            if ($hidden) {
                $sql = "UPDATE {$this->table}
                        SET is_hidden_by_admin = 1,
                            hidden_by_admin_reason = ?,
                            hidden_by_admin_at = NOW(),
                            updated_at = NOW()
                        WHERE id = ?
                        LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([trim((string)$reason), $productId]);
            } else {
                $sql = "UPDATE {$this->table}
                        SET is_hidden_by_admin = 0,
                            hidden_by_admin_reason = NULL,
                            hidden_by_admin_at = NULL,
                            updated_at = NOW()
                        WHERE id = ?
                        LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$productId]);
            }

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('setAdminHiddenStatus error: ' . $e->getMessage());
            return false;
        }
    }
}

