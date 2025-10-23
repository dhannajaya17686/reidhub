<?php
class Cart extends Model
{
    protected $table = 'cart_items';

    private function getCartRow(int $userId, int $productId): ?array
    {
        $stmt = $this->db->prepare("SELECT id, quantity FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 1");
        $stmt->execute([$userId, $productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Add or increment an item and reserve stock.
     */
    public function addOrIncrement(int $userId, int $productId, int $qty, float $unitPrice, string $paymentMethod = 'cash_on_delivery'): bool
    {
        if ($qty <= 0) return false;

        try {
            $this->db->beginTransaction();

            $mp = new MarketPlace();
            if (!$mp->reserveStock($productId, $qty)) {
                $this->db->rollBack();
                return false; // not enough stock to reserve
            }

            $existing = $this->getCartRow($userId, $productId);
            if ($existing) {
                $stmt = $this->db->prepare("
                    UPDATE {$this->table}
                    SET quantity = quantity + ?, unit_price = ?, payment_method = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $ok = $stmt->execute([$qty, number_format($unitPrice, 2, '.', ''), $paymentMethod, (int)$existing['id']]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} (user_id, product_id, quantity, unit_price, payment_method, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $ok = $stmt->execute([$userId, $productId, $qty, number_format($unitPrice, 2, '.', ''), $paymentMethod]);
            }

            if (!$ok) {
                // rollback reservation
                $mp->releaseStock($productId, $qty);
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            Logger::error('addOrIncrement error: ' . $e->getMessage());
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get items for user (unchanged in signature).
     */
    public function getItemsForUser(int $userId): array
    {
        try {
            $sql = "SELECT 
                        ci.product_id, 
                        ci.quantity, 
                        ci.unit_price,
                        ci.payment_method,
                        p.title, 
                        p.images, 
                        p.condition_type, 
                        p.stock_quantity, 
                        p.payment_methods, 
                        p.seller_id, 
                        p.status
                    FROM {$this->table} ci
                    INNER JOIN products p ON p.id = ci.product_id
                    WHERE ci.user_id = ?
                    ORDER BY ci.updated_at DESC, ci.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('Cart getItemsForUser error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Remove an item and release reserved stock.
     */
    public function removeItem(int $userId, int $productId): bool
    {
        try {
            $this->db->beginTransaction();

            $row = $this->getCartRow($userId, $productId);
            if (!$row) {
                $this->db->rollBack();
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? LIMIT 1");
            $ok = $stmt->execute([(int)$row['id']]);
            if (!$ok) { $this->db->rollBack(); return false; }

            // release the reserved qty
            $mp = new MarketPlace();
            $mp->releaseStock($productId, (int)$row['quantity']);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            Logger::error('removeItem error: ' . $e->getMessage());
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /**
     * Remove from cart without restocking (used after successful checkout).
     */
    public function removeItemNoRestock(int $userId, int $productId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 1");
            return $stmt->execute([$userId, $productId]);
        } catch (Throwable $e) {
            Logger::error('removeItemNoRestock error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update quantity and adjust reservation delta.
     */
    public function updateQuantity(int $userId, int $productId, int $qty): bool
    {
        if ($qty <= 0) return $this->removeItem($userId, $productId);

        try {
            $this->db->beginTransaction();

            $row = $this->getCartRow($userId, $productId);
            if (!$row) { $this->db->rollBack(); return false; }

            $current = (int)$row['quantity'];
            $delta = $qty - $current;

            $mp = new MarketPlace();
            if ($delta > 0) {
                // Need more stock reserved
                if (!$mp->reserveStock($productId, $delta)) {
                    $this->db->rollBack();
                    return false;
                }
            } elseif ($delta < 0) {
                // Release extra
                $mp->releaseStock($productId, abs($delta));
            }

            $stmt = $this->db->prepare("UPDATE {$this->table} SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $ok = $stmt->execute([$qty, (int)$row['id']]);

            if (!$ok) {
                // In case of failure, try to revert reservation/release
                if ($delta > 0) $mp->releaseStock($productId, $delta);
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            Logger::error('updateQuantity error: ' . $e->getMessage());
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }

    /**
     * Update payment method only (no stock change).
     */
    public function updatePaymentMethod(int $userId, int $productId, string $paymentMethod): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET payment_method = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ? LIMIT 1");
            return $stmt->execute([$paymentMethod, $userId, $productId]);
        } catch (Throwable $e) {
            Logger::error('updatePaymentMethod error: ' . $e->getMessage());
            return false;
        }
    }
}