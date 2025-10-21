<?php

class Order extends Model
{
    protected $table = 'orders';

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table}
                (transaction_id, buyer_id, seller_id, product_id, quantity, unit_price,
                 payment_method, status, cancel_reason, slip_path,
                 bank_name, bank_branch, account_name, account_number,
                 created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'yet_to_ship', NULL, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                (int)$data['transaction_id'],
                (int)$data['buyer_id'],
                (int)$data['seller_id'],
                (int)$data['product_id'],
                (int)$data['quantity'],
                number_format((float)$data['unit_price'], 2, '.', ''),
                $data['payment_method'],              // 'cash_on_delivery' | 'preorder'
                $data['slip_path'],                   // or null
                $data['bank_name'],                   // snapshot for preorder or null
                $data['bank_branch'],
                $data['account_name'],
                $data['account_number'],
            ]);
        } catch (Throwable $e) {
            Logger::error('Order create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get orders for a buyer with product info.
     */
    public function getOrdersForBuyer(int $buyerId): array
    {
        try {
            $sql = "SELECT 
                        o.id, o.product_id, o.quantity, o.unit_price, o.status, o.payment_method, o.created_at,
                        p.title, p.images
                    FROM {$this->table} o
                    INNER JOIN products p ON p.id = o.product_id
                    WHERE o.buyer_id = ?
                    ORDER BY o.created_at DESC, o.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$buyerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('getOrdersForBuyer error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get orders for a seller with product and buyer info.
     */
    public function getOrdersForSeller(int $sellerId): array
    {
        try {
            $sql = "SELECT 
                        o.id, o.product_id, o.quantity, o.unit_price, o.status, o.payment_method, 
                        o.created_at, o.slip_path, o.cancel_reason, o.buyer_id,
                        p.title,
                        u.first_name, u.last_name
                    FROM {$this->table} o
                    INNER JOIN products p ON p.id = o.product_id
                    INNER JOIN users u ON u.id = o.buyer_id
                    WHERE o.seller_id = ?
                    ORDER BY o.created_at DESC, o.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('getOrdersForSeller error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark an order as delivered (seller-owned).
     */
    public function markDelivered(int $sellerId, int $orderId): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table}
                SET status = 'delivered', updated_at = NOW()
                WHERE id = ? AND seller_id = ? AND status <> 'cancelled' AND status <> 'delivered' LIMIT 1");
            $stmt->execute([$orderId, $sellerId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('markDelivered error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel an order with a reason (seller-owned).
     */
    public function cancel(int $sellerId, int $orderId, string $reason): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table}
                SET status = 'cancelled', cancel_reason = ?, updated_at = NOW()
                WHERE id = ? AND seller_id = ? AND status <> 'cancelled' AND status <> 'delivered' LIMIT 1");
            $stmt->execute([$reason, $orderId, $sellerId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('cancel order error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aggregate seller analytics between dates.
     */
    public function getSellerAnalytics(int $sellerId, string $from, string $to, int $topN = 5): array
    {
        try {
            // KPIs (exclude cancelled from revenue/units) + distinct customers
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN quantity * unit_price END),0) AS revenue,
                    COUNT(*) AS orders,
                    COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN quantity END),0) AS units,
                    COUNT(DISTINCT buyer_id) AS customers
                FROM {$this->table}
                WHERE seller_id = ? AND created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$sellerId, $from, $to]);
            $k = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['revenue'=>0,'orders'=>0,'units'=>0,'customers'=>0];
            $revenue = (float)$k['revenue'];
            $orders = (int)$k['orders'];
            $units = (int)$k['units'];
            $customers = (int)$k['customers'];
            $aov = $orders > 0 ? ($revenue / $orders) : 0.0;

            // Time series (daily): revenue, units, orders
            $stmt = $this->db->prepare("
                SELECT DATE(created_at) as d,
                       COUNT(*) AS orders,
                       COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN quantity * unit_price END),0) AS revenue,
                       COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN quantity END),0) AS units
                FROM {$this->table}
                WHERE seller_id = ? AND created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            $stmt->execute([$sellerId, $from, $to]);
            $seriesRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $series = [
                'labels' => array_map(fn($r) => $r['d'], $seriesRows),
                'orders' => array_map(fn($r) => (int)$r['orders'], $seriesRows),
                'revenue' => array_map(fn($r) => (float)$r['revenue'], $seriesRows),
                'units' => array_map(fn($r) => (int)$r['units'], $seriesRows),
            ];

            // Status counts
            $stmt = $this->db->prepare("
                SELECT status, COUNT(*) as c
                FROM {$this->table}
                WHERE seller_id = ? AND created_at BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$sellerId, $from, $to]);
            $statusRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $statusCounts = [
                'pending' => 0,
                'yet_to_ship' => 0,
                'processing' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0,
            ];
            foreach ($statusRows as $r) {
                $key = strtolower($r['status']);
                if (!isset($statusCounts[$key])) $statusCounts[$key] = 0;
                $statusCounts[$key] = (int)$r['c'];
            }

            // Top products (by revenue)
            $stmt = $this->db->prepare("
                SELECT o.product_id, p.title,
                       SUM(o.quantity) AS units,
                       SUM(o.quantity * o.unit_price) AS revenue
                FROM {$this->table} o
                INNER JOIN products p ON p.id = o.product_id
                WHERE o.seller_id = ? AND o.created_at BETWEEN ? AND ?
                GROUP BY o.product_id, p.title
                ORDER BY revenue DESC
                LIMIT {$topN}
            ");
            $stmt->execute([$sellerId, $from, $to]);
            $topRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $topProducts = [
                'labels' => array_map(fn($r) => $r['title'], $topRows),
                'units' => array_map(fn($r) => (int)$r['units'], $topRows),
                'revenue' => array_map(fn($r) => (float)$r['revenue'], $topRows),
            ];

            // Payment method split (by revenue)
            $stmt = $this->db->prepare("
                SELECT payment_method, COALESCE(SUM(quantity * unit_price),0) AS revenue
                FROM {$this->table}
                WHERE seller_id = ? AND created_at BETWEEN ? AND ?
                GROUP BY payment_method
            ");
            $stmt->execute([$sellerId, $from, $to]);
            $pmRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $paymentSplit = [
                'labels' => array_map(fn($r) => $r['payment_method'] ?: 'unknown', $pmRows),
                'revenue' => array_map(fn($r) => (float)$r['revenue'], $pmRows),
            ];

            return [
                'kpis' => [
                    'revenue' => $revenue,
                    'orders' => $orders,
                    'units' => $units,
                    'customers' => $customers,
                    'aov' => $aov,
                ],
                'series' => $series,
                'status' => $statusCounts,
                'topProducts' => $topProducts,
                'paymentSplit' => $paymentSplit,
            ];
        } catch (Throwable $e) {
            Logger::error('getSellerAnalytics error: ' . $e->getMessage());
            return [
                'kpis' => ['revenue'=>0,'orders'=>0,'units'=>0,'customers'=>0,'aov'=>0],
                'series' => ['labels'=>[], 'orders'=>[], 'revenue'=>[], 'units'=>[]],
                'status' => ['pending'=>0,'yet_to_ship'=>0,'processing'=>0,'shipped'=>0,'delivered'=>0,'cancelled'=>0],
                'topProducts' => ['labels'=>[], 'units'=>[], 'revenue'=>[]],
                'paymentSplit' => ['labels'=>[], 'revenue'=>[]],
            ];
        }
    }
}