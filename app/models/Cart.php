<?php
class Cart extends Model
{
    protected $table = 'cart_items';

    /**
     * Insert or increment a cart line. Also sets/updates payment_method.
     */
    public function addOrIncrement(int $userId, int $productId, int $qty, float $unitPrice, string $paymentMethod = 'cash_on_delivery'): bool
    {
        try {
            $qty = max(1, min($qty, 999));
            $paymentMethod = $this->sanitizePaymentMethod($paymentMethod);

            $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity, unit_price, payment_method, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                        quantity = LEAST(quantity + VALUES(quantity), 999),
                        unit_price = VALUES(unit_price),
                        payment_method = VALUES(payment_method),
                        updated_at = NOW()";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $productId, $qty, $unitPrice, $paymentMethod]);
        } catch (Throwable $e) {
            Logger::error('Cart addOrIncrement error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart items joined with product info for a user.
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
     * Remove a single item from cart for a user.
     */
    public function removeItem(int $userId, int $productId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$userId, $productId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('Cart removeItem error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update quantity for an item in cart (clamped 1..999).
     */
    public function updateQuantity(int $userId, int $productId, int $qty): bool
    {
        try {
            $qty = max(1, min($qty, 999));
            $stmt = $this->db->prepare("UPDATE {$this->table} SET quantity = ?, updated_at = NOW()
                                        WHERE user_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$qty, $userId, $productId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('Cart updateQuantity error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment method for an existing cart item.
     */
    public function updatePaymentMethod(int $userId, int $productId, string $paymentMethod): bool
    {
        Logger::info("Updating payment method for user_id={$userId}, product_id={$productId} to {$paymentMethod}");
        try {
            $paymentMethod = $this->sanitizePaymentMethod($paymentMethod);
            $stmt = $this->db->prepare("UPDATE {$this->table} SET payment_method = ?, updated_at = NOW()
                                        WHERE user_id = ? AND product_id = ? LIMIT 1");
            $stmt->execute([$paymentMethod, $userId, $productId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('Cart updatePaymentMethod error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate payment method against enum.
     */
    private function sanitizePaymentMethod(string $method): string
    {
        $method = strtolower(trim($method));
        return in_array($method, ['cash_on_delivery', 'preorder'], true) ? $method : 'cash_on_delivery';
    }
}