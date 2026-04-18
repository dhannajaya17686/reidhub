<?php

class MarketplaceAdmin extends Model
{
    protected $table = 'orders';

    /**
     * Get platform-wide marketplace analytics for admin dashboard.
     * 
     * @param string $from Start date (YYYY-MM-DD HH:MM:SS format or date only)
     * @param string $to End date (YYYY-MM-DD HH:MM:SS format or date only)
     * @param int $topN Number of top items to fetch
     * @return array Analytics data with kpis, series, status, topProducts, topCategories, recentOrders
     */
    public function getMarketplaceAnalytics(string $from, string $to, int $topN = 5): array
    {
        try {
            // ==================== KPIs ====================
            // Revenue: ONLY from delivered orders
            // Orders: ALL orders (total count, including pending, cancelled, etc)
            // Units: ONLY from delivered orders
            // Customers: COUNT DISTINCT buyer_id from all orders
            // Active sellers: COUNT DISTINCT seller_id from all orders
            
            $stmt = $this->db->prepare("
                SELECT 
                    COALESCE(SUM(CASE WHEN status = 'delivered' THEN quantity * unit_price ELSE 0 END), 0) AS revenue,
                    COUNT(*) AS total_orders,
                    COALESCE(SUM(CASE WHEN status = 'delivered' THEN quantity ELSE 0 END), 0) AS units,
                    COUNT(DISTINCT buyer_id) AS customers,
                    COUNT(DISTINCT seller_id) AS active_sellers
                FROM {$this->table}
                WHERE created_at BETWEEN ? AND ?
            ");
            $stmt->execute([$from, $to]);
            $kpiRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'revenue' => 0, 
                'total_orders' => 0, 
                'units' => 0, 
                'customers' => 0,
                'active_sellers' => 0
            ];
            
            $revenue = (float)$kpiRow['revenue'];
            $totalOrders = (int)$kpiRow['total_orders'];
            $units = (int)$kpiRow['units'];
            $customers = (int)$kpiRow['customers'];
            $activeSellers = (int)$kpiRow['active_sellers'];

            // ==================== TIME SERIES (Daily) ====================
            // Orders: ALL orders (regardless of status)
            // Revenue: ONLY from delivered orders
            $stmt = $this->db->prepare("
                SELECT DATE(created_at) as date,
                       COUNT(*) AS orders,
                       COALESCE(SUM(CASE WHEN status = 'delivered' THEN quantity * unit_price ELSE 0 END), 0) AS revenue,
                       COALESCE(SUM(CASE WHEN status = 'delivered' THEN quantity ELSE 0 END), 0) AS units
                FROM {$this->table}
                WHERE created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            $stmt->execute([$from, $to]);
            $seriesRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $series = [
                'labels' => array_map(fn($r) => $r['date'], $seriesRows),
                'orders' => array_map(fn($r) => (int)$r['orders'], $seriesRows),
                'revenue' => array_map(fn($r) => (float)$r['revenue'], $seriesRows),
                'units' => array_map(fn($r) => (int)$r['units'], $seriesRows),
            ];

            // ==================== STATUS BREAKDOWN ====================
            $stmt = $this->db->prepare("
                SELECT status, COUNT(*) as count
                FROM {$this->table}
                WHERE created_at BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$from, $to]);
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
                if (isset($statusCounts[$key])) {
                    $statusCounts[$key] = (int)$r['count'];
                }
            }

            // ==================== TOP PRODUCTS (by revenue) ====================
            $stmt = $this->db->prepare("
                SELECT o.product_id, p.title,
                       SUM(CASE WHEN o.status = 'delivered' THEN o.quantity ELSE 0 END) AS units,
                       SUM(CASE WHEN o.status = 'delivered' THEN o.quantity * o.unit_price ELSE 0 END) AS revenue
                FROM {$this->table} o
                INNER JOIN products p ON p.id = o.product_id
                WHERE o.created_at BETWEEN ? AND ?
                GROUP BY o.product_id, p.title
                ORDER BY revenue DESC
                LIMIT {$topN}
            ");
            $stmt->execute([$from, $to]);
            $topProductRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $topProducts = [
                'labels' => array_map(fn($r) => $r['title'], $topProductRows),
                'units' => array_map(fn($r) => (int)$r['units'], $topProductRows),
                'revenue' => array_map(fn($r) => (float)$r['revenue'], $topProductRows),
            ];

            // ==================== TOP CATEGORIES (Item Type: New vs Used - Count of items added) ====================
            $stmt = $this->db->prepare("
                SELECT p.product_type,
                       COUNT(DISTINCT p.id) AS item_count
                FROM products p
                WHERE p.created_at BETWEEN ? AND ?
                GROUP BY p.product_type
                ORDER BY item_count DESC
            ");
            $stmt->execute([$from, $to]);
            $categoryRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $topCategories = [
                'labels' => array_map(fn($r) => ucfirst($r['product_type'] ?: 'Other'), $categoryRows),
                'itemCount' => array_map(fn($r) => (int)$r['item_count'], $categoryRows),
            ];

            // ==================== RECENT ORDERS ====================
            $stmt = $this->db->prepare("
                SELECT o.id, o.transaction_id, 
                       u.first_name, u.last_name,
                       o.quantity * o.unit_price AS amount,
                       o.status,
                       o.created_at
                FROM {$this->table} o
                INNER JOIN users u ON u.id = o.buyer_id
                WHERE o.created_at BETWEEN ? AND ?
                ORDER BY o.created_at DESC
                LIMIT {$topN}
            ");
            $stmt->execute([$from, $to]);
            $recentOrderRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $recentOrders = array_map(function($r) {
                return [
                    'id' => (int)$r['id'],
                    'transactionId' => (int)$r['transaction_id'],
                    'customerName' => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Unknown',
                    'amount' => (float)$r['amount'],
                    'status' => $r['status'],
                    'date' => $r['created_at'],
                ];
            }, $recentOrderRows);

            Logger::info("Admin marketplace analytics fetched: from={$from}, to={$to}, revenue={$revenue}, orders={$totalOrders}, sellers={$activeSellers}");

            return [
                'success' => true,
                'kpis' => [
                    'revenue' => $revenue,
                    'orders' => $totalOrders,
                    'units' => $units,
                    'customers' => $customers,
                    'activeSellers' => $activeSellers,
                ],
                'series' => $series,
                'status' => $statusCounts,
                'topProducts' => $topProducts,
                'topCategories' => $topCategories,
                'recentOrders' => $recentOrders,
            ];
        } catch (Throwable $e) {
            Logger::error('getMarketplaceAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'kpis' => ['revenue' => 0, 'orders' => 0, 'units' => 0, 'customers' => 0, 'activeSellers' => 0],
                'series' => ['labels' => [], 'orders' => [], 'revenue' => [], 'units' => []],
                'status' => ['pending' => 0, 'yet_to_ship' => 0, 'processing' => 0, 'shipped' => 0, 'delivered' => 0, 'cancelled' => 0],
                'topProducts' => ['labels' => [], 'units' => [], 'revenue' => []],
                'topCategories' => ['labels' => [], 'itemCount' => []],
                'recentOrders' => [],
            ];
        }
    }
}
