<?php

class Transaction extends Model
{
    protected $table = 'transactions';

    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO {$this->table} (buyer_id, item_count, total_amount, created_at)
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                (int)$data['buyer_id'],
                (int)$data['item_count'],
                number_format((float)$data['total_amount'], 2, '.', ''),
            ]);
            return $ok ? (int)$this->db->lastInsertId() : null;
        } catch (Throwable $e) {
            Logger::error('Transaction create error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Load one transaction (owned by buyer) and its orders with product + seller info.
     */
    public function getBuyerTransactionFull(int $buyerId, int $transactionId): ?array
    {
        try {
            // Transaction header
            $st = $this->db->prepare("SELECT id, buyer_id, item_count, total_amount, created_at
                                      FROM {$this->table}
                                      WHERE id = ? AND buyer_id = ?
                                      LIMIT 1");
            $st->execute([$transactionId, $buyerId]);
            $tx = $st->fetch(PDO::FETCH_ASSOC);
            if (!$tx) return null;

            // Orders in this transaction
            $os = $this->db->prepare("
                SELECT 
                    o.id, o.transaction_id, o.product_id, o.quantity, o.unit_price,
                    o.payment_method, o.status, o.created_at, o.slip_path,
                    u.first_name AS seller_first, u.last_name AS seller_last,
                    p.title AS product_title, p.images
                FROM orders o
                INNER JOIN users u ON u.id = o.seller_id
                INNER JOIN products p ON p.id = o.product_id
                WHERE o.transaction_id = ? AND o.buyer_id = ?
                ORDER BY o.id ASC
            ");
            $os->execute([$transactionId, $buyerId]);
            $rows = $os->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $orders = array_map(function ($r) {
                $sellerName = trim(($r['seller_first'] ?? '') . ' ' . ($r['seller_last'] ?? ''));
                // first product image if available
                $img = '/images/placeholders/product.png';
                if (!empty($r['images'])) {
                    $decoded = json_decode($r['images'], true);
                    if (is_array($decoded) && !empty($decoded[0])) $img = $decoded[0];
                }
                return [
                    'id' => (int)$r['id'],
                    'product_id' => (int)$r['product_id'],
                    'product_title' => $r['product_title'],
                    'image' => $img,
                    'quantity' => (int)$r['quantity'],
                    'unit_price' => (float)$r['unit_price'],
                    'payment_method' => $r['payment_method'],
                    'status' => $r['status'],
                    'slip_path' => $r['slip_path'],
                    'created_at' => $r['created_at'],
                    'seller_name' => $sellerName ?: 'Seller',
                ];
            }, $rows);

            return [
                'id' => (int)$tx['id'],
                'buyer_id' => (int)$tx['buyer_id'],
                'item_count' => (int)$tx['item_count'],
                'total_amount' => (float)$tx['total_amount'],
                'created_at' => $tx['created_at'],
                'orders' => $orders,
            ];
        } catch (Throwable $e) {
            Logger::error('getBuyerTransactionFull error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all transactions for a buyer with nested order rows.
     * Optional filters: ['status'=>..., 'payment'=>..., 'from'=>Y-m-d, 'to'=>Y-m-d, 'search'=>string]
     */
    public function getBuyerTransactionsWithOrders(int $buyerId, array $filters = []): array
    {
        try {
            // Base transactions for the buyer
            $params = [$buyerId];
            $txWhere = "WHERE t.buyer_id = ?";
            // Date range
            if (!empty($filters['from'])) {
                $txWhere .= " AND t.created_at >= ?";
                $params[] = $filters['from'] . ' 00:00:00';
            }
            if (!empty($filters['to'])) {
                $txWhere .= " AND t.created_at <= ?";
                $params[] = $filters['to'] . ' 23:59:59';
            }

            $txSql = "SELECT t.id, t.buyer_id, t.item_count, t.total_amount, t.created_at
                      FROM {$this->table} t
                      {$txWhere}
                      ORDER BY t.created_at DESC, t.id DESC";
            $stmt = $this->db->prepare($txSql);
            $stmt->execute($params);
            $txRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            if (!$txRows) return [];

            $txIds = array_map(fn($r) => (int)$r['id'], $txRows);
            $in = implode(',', array_fill(0, count($txIds), '?'));

            // Orders for these transactions, joined with product and seller (for names)
            $oParams = $txIds;
            $oWhere = "WHERE o.transaction_id IN ($in) AND o.buyer_id = ?";
            $oParams[] = $buyerId;

            // Filter by status
            if (!empty($filters['status'])) {
                $oWhere .= " AND o.status = ?";
                $oParams[] = $filters['status']; // yet_to_ship | delivered | cancelled
            }
            // Filter by payment method
            if (!empty($filters['payment'])) {
                $oWhere .= " AND o.payment_method = ?";
                $oParams[] = $filters['payment']; // cash_on_delivery | preorder
            }

            $orderSql = "SELECT 
                            o.id, o.transaction_id, o.product_id, o.quantity, o.unit_price,
                            o.payment_method, o.status, o.created_at,
                            p.title AS product_title,
                            u.first_name, u.last_name
                         FROM orders o
                         INNER JOIN products p ON p.id = o.product_id
                         INNER JOIN users u ON u.id = o.seller_id
                         {$oWhere}
                         ORDER BY o.created_at DESC, o.id DESC";
            $os = $this->db->prepare($orderSql);
            $os->execute($oParams);
            $orderRows = $os->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Optional "search" filter across product title or seller name (client does too, but safe here)
            $search = strtolower(trim((string)($filters['search'] ?? '')));
            if ($search !== '') {
                $orderRows = array_values(array_filter($orderRows, function ($r) use ($search) {
                    $seller = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                    return (strpos(strtolower((string)$r['product_title']), $search) !== false) ||
                           (strpos(strtolower($seller), $search) !== false);
                }));
            }

            // Group orders by transaction_id
            $byTx = [];
            foreach ($orderRows as $o) {
                $tid = (int)$o['transaction_id'];
                $sellerName = trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? ''));
                $byTx[$tid][] = [
                    'id' => 'ORD' . (int)$o['id'],
                    'product_title' => $o['product_title'],
                    'seller_name' => $sellerName !== '' ? $sellerName : 'Seller #' . ($o['transaction_id'] ?? ''),
                    'quantity' => (int)$o['quantity'],
                    'unit_price' => (float)$o['unit_price'],
                    'payment_method' => $o['payment_method'],
                    'status' => $o['status'],
                ];
            }

            // Build final payload in the same shape as your mock
            $out = [];
            foreach ($txRows as $t) {
                $tid = (int)$t['id'];
                $orders = $byTx[$tid] ?? [];

                // If filters remove all orders for a tx, skip tx
                if (!empty($filters) && empty($orders)) continue;

                $out[] = [
                    'id' => 'TX' . $tid,
                    'transaction_id' => $tid,
                    'buyer_id' => (int)$t['buyer_id'],
                    'item_count' => (int)$t['item_count'],
                    'total_amount' => (float)$t['total_amount'],
                    'created_at' => $t['created_at'],
                    'orders' => $orders,
                ];
            }

            return $out;
        } catch (Throwable $e) {
            Logger::error('getBuyerTransactionsWithOrders error: ' . $e->getMessage());
            return [];
        }
    }
}