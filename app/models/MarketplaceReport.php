<?php

class MarketplaceReport extends Model
{
    protected $table = 'marketplace_reports';

    public function createForProduct(int $productId, int $reporterId, string $category, string $reason): array
    {
        try {
            $allowed = ['inappropriate', 'spam', 'fraud', 'copyright', 'other'];
            $normalizedCategory = in_array($category, $allowed, true) ? $category : 'other';
            $trimmedReason = trim($reason);

            if ($productId <= 0 || $reporterId <= 0 || $trimmedReason === '') {
                return ['success' => false, 'message' => 'Invalid report data'];
            }

            $productStmt = $this->db->prepare('SELECT id, seller_id FROM products WHERE id = ? LIMIT 1');
            $productStmt->execute([$productId]);
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }

            if ((int)$product['seller_id'] === $reporterId) {
                return ['success' => false, 'message' => 'You cannot report your own product'];
            }

            $insert = $this->db->prepare(
                "INSERT INTO {$this->table}
                (order_id, product_id, reporter_id, seller_id, category, reason, status, created_at, updated_at)
                VALUES (NULL, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
            );

            $ok = $insert->execute([
                (int)$product['id'],
                $reporterId,
                (int)$product['seller_id'],
                $normalizedCategory,
                $trimmedReason,
            ]);

            if (!$ok) {
                return ['success' => false, 'message' => 'Failed to submit report'];
            }

            return [
                'success' => true,
                'id' => (int)$this->db->lastInsertId(),
            ];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport createForProduct error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function createForOrder(int $orderId, int $reporterId, string $category, string $reason): array
    {
        try {
            $allowed = ['inappropriate', 'spam', 'fraud', 'copyright', 'other'];
            $normalizedCategory = in_array($category, $allowed, true) ? $category : 'other';
            $trimmedReason = trim($reason);

            if ($orderId <= 0 || $reporterId <= 0 || $trimmedReason === '') {
                return ['success' => false, 'message' => 'Invalid report data'];
            }

            $orderStmt = $this->db->prepare('SELECT id, buyer_id, seller_id, product_id FROM orders WHERE id = ? LIMIT 1');
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return ['success' => false, 'message' => 'Order not found'];
            }

            if ((int)$order['buyer_id'] !== $reporterId) {
                return ['success' => false, 'message' => 'Only the buyer can submit this report'];
            }

            $insert = $this->db->prepare(
                "INSERT INTO {$this->table}
                (order_id, product_id, reporter_id, seller_id, category, reason, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())"
            );

            $ok = $insert->execute([
                (int)$order['id'],
                (int)$order['product_id'],
                $reporterId,
                (int)$order['seller_id'],
                $normalizedCategory,
                $trimmedReason,
            ]);

            if (!$ok) {
                return ['success' => false, 'message' => 'Failed to submit report'];
            }

            return [
                'success' => true,
                'id' => (int)$this->db->lastInsertId(),
            ];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport createForOrder error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function getAdminReports(array $filters = []): array
    {
        try {
            $where = ['1=1'];
            $params = [];

            $status = $filters['status'] ?? null;
            if ($status && $status !== 'all') {
                $where[] = 'r.status = ?';
                $params[] = $status;
            }

            $category = $filters['category'] ?? null;
            if ($category) {
                $where[] = 'r.category = ?';
                $params[] = $category;
            }

            $search = trim((string)($filters['search'] ?? ''));
            if ($search !== '') {
                $where[] = "(
                    p.title LIKE ?
                    OR reporter.first_name LIKE ?
                    OR reporter.last_name LIKE ?
                    OR seller.first_name LIKE ?
                    OR seller.last_name LIKE ?
                    OR r.reason LIKE ?
                )";
                $like = '%' . $search . '%';
                $params = array_merge($params, [$like, $like, $like, $like, $like, $like]);
            }

            $sql = "SELECT
                        r.id,
                        r.order_id,
                        r.product_id,
                        r.reporter_id,
                        r.seller_id,
                        r.category,
                        r.reason,
                        r.status,
                        r.admin_notes,
                        r.reviewed_by_admin_id,
                        r.reviewed_at,
                        r.created_at,
                        r.updated_at,
                        p.title AS product_title,
                        p.images AS product_images,
                        p.price AS product_price,
                        COALESCE(p.is_hidden_by_admin, 0) AS is_hidden_by_admin,
                        p.hidden_by_admin_reason,
                        p.hidden_by_admin_at,
                        reporter.first_name AS reporter_first_name,
                        reporter.last_name AS reporter_last_name,
                        reporter.email AS reporter_email,
                        seller.first_name AS seller_first_name,
                        seller.last_name AS seller_last_name,
                        seller.email AS seller_email,
                        COALESCE((
                            SELECT COUNT(*)
                            FROM marketplace_seller_actions msa_warn
                            WHERE msa_warn.seller_id = r.seller_id
                              AND msa_warn.action_type = 'warning'
                        ), 0) AS warning_count,
                        (
                            SELECT msa_ban.action_type
                            FROM marketplace_seller_actions msa_ban
                            WHERE msa_ban.seller_id = r.seller_id
                              AND msa_ban.action_type IN ('ban', 'unban')
                            ORDER BY msa_ban.created_at DESC, msa_ban.id DESC
                            LIMIT 1
                        ) AS latest_ban_action
                    FROM {$this->table} r
                    INNER JOIN products p ON p.id = r.product_id
                    INNER JOIN users reporter ON reporter.id = r.reporter_id
                    INNER JOIN users seller ON seller.id = r.seller_id
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY r.created_at DESC, r.id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getAdminReports error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByIdWithDetails(int $reportId): ?array
    {
        try {
            $sql = "SELECT
                        r.*,
                        o.status AS order_status,
                        o.quantity AS order_quantity,
                        o.unit_price AS order_unit_price,
                        o.created_at AS order_created_at,
                        p.title AS product_title,
                        p.images AS product_images,
                        reporter.first_name AS reporter_first_name,
                        reporter.last_name AS reporter_last_name,
                        reporter.email AS reporter_email,
                        seller.first_name AS seller_first_name,
                        seller.last_name AS seller_last_name,
                        seller.email AS seller_email
                    FROM {$this->table} r
                    LEFT JOIN orders o ON o.id = r.order_id
                    INNER JOIN products p ON p.id = r.product_id
                    INNER JOIN users reporter ON reporter.id = r.reporter_id
                    INNER JOIN users seller ON seller.id = r.seller_id
                    WHERE r.id = ?
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$reportId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getByIdWithDetails error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateStatus(int $reportId, string $status, int $adminId, ?string $adminNotes = null): bool
    {
        try {
            $allowed = ['pending', 'under-review', 'resolved', 'archived'];
            if (!in_array($status, $allowed, true) || $reportId <= 0 || $adminId <= 0) {
                return false;
            }

            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET status = ?, admin_notes = ?, reviewed_by_admin_id = ?, reviewed_at = NOW(), updated_at = NOW()
                 WHERE id = ?
                 LIMIT 1"
            );

            $stmt->execute([$status, $adminNotes, $adminId, $reportId]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport updateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    public function getSellerOrderReportSummaries(int $sellerId, array $orderIds): array
    {
        try {
            if ($sellerId <= 0 || empty($orderIds)) {
                return [];
            }

            $normalizedOrderIds = array_values(array_filter(array_map('intval', $orderIds), fn($id) => $id > 0));
            if (empty($normalizedOrderIds)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($normalizedOrderIds), '?'));

            $sql = "SELECT
                        r.id AS report_id,
                        r.order_id,
                        r.status,
                        r.category,
                        r.reason,
                        r.created_at,
                        COALESCE(wa.warning_count, 0) AS warning_count,
                        CASE WHEN lb.action_type = 'ban' THEN 1 ELSE 0 END AS is_banned
                    FROM {$this->table} r
                    LEFT JOIN (
                        SELECT report_id, COUNT(*) AS warning_count
                        FROM marketplace_seller_actions
                        WHERE seller_id = ?
                          AND action_type = 'warning'
                        GROUP BY report_id
                    ) wa ON wa.report_id = r.id
                    LEFT JOIN marketplace_seller_actions lb ON lb.id = (
                        SELECT msa.id
                        FROM marketplace_seller_actions msa
                        WHERE msa.report_id = r.id
                          AND msa.seller_id = r.seller_id
                          AND msa.action_type IN ('ban', 'unban')
                        ORDER BY msa.created_at DESC, msa.id DESC
                        LIMIT 1
                    )
                    WHERE r.seller_id = ?
                      AND r.order_id IN ({$placeholders})
                      AND r.id = (
                        SELECT MAX(r2.id)
                        FROM {$this->table} r2
                        WHERE r2.order_id = r.order_id
                          AND r2.seller_id = r.seller_id
                    )";

            $params = array_merge([$sellerId, $sellerId], $normalizedOrderIds);

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $byOrder = [];
            foreach ($rows as $row) {
                $orderId = (int)$row['order_id'];
                $byOrder[$orderId] = [
                    'report_id' => (int)$row['report_id'],
                    'status' => (string)($row['status'] ?? 'pending'),
                    'category' => (string)($row['category'] ?? 'other'),
                    'reason' => (string)($row['reason'] ?? ''),
                    'created_at' => $row['created_at'] ?? null,
                    'warning_count' => (int)($row['warning_count'] ?? 0),
                    'is_banned' => (int)($row['is_banned'] ?? 0) === 1,
                ];
            }

            return $byOrder;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getSellerOrderReportSummaries error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAdminSellerSummaries(array $filters = []): array
    {
        try {
            $where = ['1=1'];
            $params = [];

            $search = trim((string)($filters['search'] ?? ''));
            if ($search !== '') {
                $where[] = "(
                    seller.first_name LIKE ?
                    OR seller.last_name LIKE ?
                    OR seller.email LIKE ?
                )";
                $like = '%' . $search . '%';
                $params = array_merge($params, [$like, $like, $like]);
            }

            $sql = "SELECT
                        r.seller_id,
                        seller.first_name AS seller_first_name,
                        seller.last_name AS seller_last_name,
                        seller.email AS seller_email,
                        COUNT(r.id) AS total_reports,
                        SUM(CASE WHEN r.status = 'pending' THEN 1 ELSE 0 END) AS pending_reports,
                        SUM(CASE WHEN r.status = 'under-review' THEN 1 ELSE 0 END) AS under_review_reports,
                        SUM(CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END) AS resolved_reports,
                        SUM(CASE WHEN r.status = 'archived' THEN 1 ELSE 0 END) AS archived_reports,
                        MAX(r.created_at) AS last_reported_at,
                        COALESCE(w.warning_count, 0) AS warning_count,
                        CASE WHEN b.latest_ban_action = 'ban' THEN 1 ELSE 0 END AS is_banned
                    FROM {$this->table} r
                    INNER JOIN users seller ON seller.id = r.seller_id
                    LEFT JOIN (
                        SELECT seller_id, COUNT(*) AS warning_count
                        FROM marketplace_seller_actions
                        WHERE action_type = 'warning'
                        GROUP BY seller_id
                    ) w ON w.seller_id = r.seller_id
                    LEFT JOIN (
                        SELECT m_latest.seller_id, m_latest.action_type AS latest_ban_action
                        FROM marketplace_seller_actions m_latest
                        INNER JOIN (
                            SELECT seller_id, MAX(id) AS max_id
                            FROM marketplace_seller_actions
                            WHERE action_type IN ('ban', 'unban')
                            GROUP BY seller_id
                        ) mx ON mx.max_id = m_latest.id
                    ) b ON b.seller_id = r.seller_id
                    WHERE " . implode(' AND ', $where) . "
                    GROUP BY r.seller_id, seller.first_name, seller.last_name, seller.email, w.warning_count, b.latest_ban_action
                    ORDER BY total_reports DESC, last_reported_at DESC, r.seller_id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $state = trim((string)($filters['state'] ?? 'all'));
            if ($state === 'banned') {
                return array_values(array_filter($rows, fn($row) => (int)($row['is_banned'] ?? 0) === 1));
            }
            if ($state === 'active') {
                return array_values(array_filter($rows, fn($row) => (int)($row['is_banned'] ?? 0) !== 1));
            }

            return $rows;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getAdminSellerSummaries error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAdminSellerSummaryById(int $sellerId): ?array
    {
        try {
            if ($sellerId <= 0) {
                return null;
            }

            $rows = $this->getAdminSellerSummaries();
            foreach ($rows as $row) {
                if ((int)($row['seller_id'] ?? 0) === $sellerId) {
                    return $row;
                }
            }

            return null;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getAdminSellerSummaryById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getAdminSellerRelatedReports(int $sellerId, array $filters = []): array
    {
        try {
            if ($sellerId <= 0) {
                return [];
            }

            $where = ['r.seller_id = ?'];
            $params = [$sellerId];

            $status = trim((string)($filters['status'] ?? 'all'));
            if ($status !== '' && $status !== 'all') {
                $where[] = 'r.status = ?';
                $params[] = $status;
            }

            $search = trim((string)($filters['search'] ?? ''));
            if ($search !== '') {
                $where[] = "(
                    p.title LIKE ?
                    OR reporter.first_name LIKE ?
                    OR reporter.last_name LIKE ?
                    OR r.reason LIKE ?
                )";
                $like = '%' . $search . '%';
                $params = array_merge($params, [$like, $like, $like, $like]);
            }

            $sql = "SELECT
                        r.id,
                        r.order_id,
                        r.product_id,
                        r.reporter_id,
                        r.seller_id,
                        r.category,
                        r.reason,
                        r.status,
                        r.created_at,
                        p.title AS product_title,
                        p.images AS product_images,
                        p.price AS product_price,
                        reporter.first_name AS reporter_first_name,
                        reporter.last_name AS reporter_last_name,
                        reporter.email AS reporter_email
                    FROM {$this->table} r
                    INNER JOIN products p ON p.id = r.product_id
                    INNER JOIN users reporter ON reporter.id = r.reporter_id
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY r.created_at DESC, r.id DESC
                    LIMIT 200";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getAdminSellerRelatedReports error: ' . $e->getMessage());
            return [];
        }
    }

    public function getSellerReportsCenterItems(int $sellerId, array $filters = []): array
    {
        try {
            if ($sellerId <= 0) {
                return [];
            }

            $where = ['r.seller_id = ?'];
            $params = [$sellerId];

            $status = trim((string)($filters['status'] ?? 'all'));
            if ($status !== '' && $status !== 'all') {
                $where[] = 'r.status = ?';
                $params[] = $status;
            }

            $search = trim((string)($filters['search'] ?? ''));
            if ($search !== '') {
                $where[] = "(
                    p.title LIKE ?
                    OR reporter.first_name LIKE ?
                    OR reporter.last_name LIKE ?
                    OR r.reason LIKE ?
                )";
                $like = '%' . $search . '%';
                $params = array_merge($params, [$like, $like, $like, $like]);
            }

            $sql = "SELECT
                        r.id,
                        r.order_id,
                        r.product_id,
                        r.category,
                        r.reason,
                        r.status,
                        r.created_at,
                        p.title AS product_title,
                        p.images AS product_images,
                        p.price AS product_price,
                        COALESCE(p.is_hidden_by_admin, 0) AS is_hidden_by_admin,
                        p.hidden_by_admin_reason,
                        p.hidden_by_admin_at,
                        reporter.first_name AS reporter_first_name,
                        reporter.last_name AS reporter_last_name,
                        reporter.email AS reporter_email
                    FROM {$this->table} r
                    INNER JOIN products p ON p.id = r.product_id
                    INNER JOIN users reporter ON reporter.id = r.reporter_id
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY r.created_at DESC, r.id DESC
                    LIMIT 300";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReport getSellerReportsCenterItems error: ' . $e->getMessage());
            return [];
        }
    }
}
