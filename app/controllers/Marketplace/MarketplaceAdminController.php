<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
class Marketplace_MarketplaceAdminController extends Controller
{
    public function showAdminMarketplaceAnalytics()
    {
        $admin = Auth_LoginController::getSessionAdmin(true); // protect route
        $this->viewApp('/Admin/marketplace/admin-analytics-view', ['admin' => $admin], 'Marketplace Admin Analytics - ReidHub');
    }
    public function showAdminMarketplaceReportedItems()
    {
        $admin = Auth_LoginController::getSessionAdmin(true); // protect route
        $this->viewApp('/Admin/marketplace/admin-reported-view', ['admin' => $admin], 'Marketplace Admin Reported Items - ReidHub');
    }
    public function showAdminMarketplaceSellers()
    {
        $admin = Auth_LoginController::getSessionAdmin(true); // protect route
        $this->viewApp('/Admin/marketplace/admin-report-moderation-view', ['admin' => $admin], 'Marketplace Admin Sellers - ReidHub');
    }
    public function showAdminMarketplaceSellerDetail()
    {
        $admin = Auth_LoginController::getSessionAdmin(true); // protect route
        $sellerId = (int)($_GET['id'] ?? 0);

        if ($sellerId <= 0) {
            header('Location: /dashboard/marketplace/admin/sellers');
            exit;
        }

        $reportModel = new MarketplaceReport();
        $seller = $reportModel->getAdminSellerSummaryById($sellerId);
        if (!$seller) {
            http_response_code(404);
            $this->viewApp('/Admin/marketplace/admin-seller-detail-view', [
                'admin' => $admin,
                'sellerId' => $sellerId,
                'seller' => null,
            ], 'Seller Not Found - ReidHub');
            return;
        }

        $this->viewApp('/Admin/marketplace/admin-seller-detail-view', [
            'admin' => $admin,
            'sellerId' => $sellerId,
            'seller' => $seller,
        ], 'Marketplace Seller Detail - ReidHub');
    }
    public function showAdminMarketplaceArchivedItems()
    {
        $admin = Auth_LoginController::getSessionAdmin(true); // protect route
        $this->viewApp('/Admin/marketplace/admin-archived-view', ['admin' => $admin], 'Marketplace Admin Archived Items - ReidHub');
    }

    /**
     * GET /dashboard/marketplace/admin/analytics/data
     * Returns platform-wide marketplace analytics data.
     * Query params:
     *   range: 7d | 30d | 90d | 365d (default 30d)
     */
    public function adminAnalyticsData()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Parse range parameter (default 30d)
            $range = $_GET['range'] ?? '30d';
            $days = match ($range) {
                '7d' => 7,
                '30d' => 30,
                '90d' => 90,
                '365d' => 365,
                default => 30,
            };

            $to = date('Y-m-d 23:59:59');
            $from = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

            $adminModel = new MarketplaceAdmin();
            $analytics = $adminModel->getMarketplaceAnalytics($from, $to, topN: 5);

            if (!$analytics['success']) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to fetch analytics']);
                return;
            }

            // Remove internal success flag and return full analytics
            unset($analytics['success']);
            echo json_encode([
                'success' => true,
                'rangeDays' => $days,
                ...$analytics,
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            Logger::error('adminAnalyticsData error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * GET /dashboard/marketplace/admin/reported/data
     */
    public function getReportedItemsData()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $rows = $reportModel->getAdminReports([
                'status' => $_GET['status'] ?? 'all',
                'category' => $_GET['category'] ?? '',
                'search' => $_GET['search'] ?? '',
            ]);

            $items = array_map(function (array $row) {
                $images = json_decode((string)($row['product_images'] ?? '[]'), true);
                $firstImage = (is_array($images) && !empty($images))
                    ? $images[0]
                    : '/images/placeholders/product.png';

                $reporterName = trim((string)($row['reporter_first_name'] ?? '') . ' ' . (string)($row['reporter_last_name'] ?? ''));
                $sellerName = trim((string)($row['seller_first_name'] ?? '') . ' ' . (string)($row['seller_last_name'] ?? ''));

                return [
                    'id' => (int)$row['id'],
                    'order_id' => isset($row['order_id']) && $row['order_id'] !== null ? (int)$row['order_id'] : null,
                    'product_id' => (int)$row['product_id'],
                    'product_title' => $row['product_title'] ?? 'Unknown Product',
                    'product_price' => (float)($row['product_price'] ?? 0),
                    'product_image' => $firstImage,
                    'is_hidden_by_admin' => (int)($row['is_hidden_by_admin'] ?? 0) === 1,
                    'hidden_by_admin_reason' => (string)($row['hidden_by_admin_reason'] ?? ''),
                    'hidden_by_admin_at' => $row['hidden_by_admin_at'] ?? null,
                    'reporter_name' => $reporterName !== '' ? $reporterName : 'Unknown Reporter',
                    'reporter_email' => $row['reporter_email'] ?? '',
                    'seller_id' => (int)$row['seller_id'],
                    'seller_name' => $sellerName !== '' ? $sellerName : 'Unknown Seller',
                    'seller_email' => $row['seller_email'] ?? '',
                    'category' => $row['category'] ?? 'other',
                    'reason' => $row['reason'] ?? '',
                    'status' => $row['status'] ?? 'pending',
                    'admin_notes' => $row['admin_notes'] ?? '',
                    'warning_count' => (int)($row['warning_count'] ?? 0),
                    'is_banned' => (($row['latest_ban_action'] ?? '') === 'ban'),
                    'created_at' => $row['created_at'] ?? null,
                ];
            }, $rows);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'count' => count($items),
            ]);
        } catch (Throwable $e) {
            Logger::error('getReportedItemsData error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/reported/hide-product
     */
    public function hideReportedProduct()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));
            if ($reportId <= 0 || $reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Hide reason is required']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $report = $reportModel->getByIdWithDetails($reportId);
            if (!$report) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Report not found']);
                return;
            }

            $marketplaceModel = new MarketPlace();
            $ok = $marketplaceModel->setAdminHiddenStatus((int)$report['product_id'], true, $reason);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to hide product']);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            Logger::error('hideReportedProduct error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/reported/unhide-product
     */
    public function unhideReportedProduct()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            if ($reportId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $report = $reportModel->getByIdWithDetails($reportId);
            if (!$report) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Report not found']);
                return;
            }

            $marketplaceModel = new MarketPlace();
            $ok = $marketplaceModel->setAdminHiddenStatus((int)$report['product_id'], false, null);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to unhide product']);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            Logger::error('unhideReportedProduct error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * GET /dashboard/marketplace/admin/sellers/data
     */
    public function getAdminSellerModerationData()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $rows = $reportModel->getAdminSellerSummaries([
                'search' => $_GET['search'] ?? '',
                'state' => $_GET['state'] ?? 'all',
            ]);

            $items = array_map(function (array $row) {
                $sellerName = trim((string)($row['seller_first_name'] ?? '') . ' ' . (string)($row['seller_last_name'] ?? ''));

                return [
                    'seller_id' => (int)($row['seller_id'] ?? 0),
                    'seller_name' => $sellerName !== '' ? $sellerName : 'Unknown Seller',
                    'seller_email' => (string)($row['seller_email'] ?? ''),
                    'total_reports' => (int)($row['total_reports'] ?? 0),
                    'pending_reports' => (int)($row['pending_reports'] ?? 0),
                    'under_review_reports' => (int)($row['under_review_reports'] ?? 0),
                    'resolved_reports' => (int)($row['resolved_reports'] ?? 0),
                    'archived_reports' => (int)($row['archived_reports'] ?? 0),
                    'warning_count' => (int)($row['warning_count'] ?? 0),
                    'is_banned' => (int)($row['is_banned'] ?? 0) === 1,
                    'last_reported_at' => $row['last_reported_at'] ?? null,
                ];
            }, $rows);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'count' => count($items),
            ]);
        } catch (Throwable $e) {
            Logger::error('getAdminSellerModerationData error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * GET /dashboard/marketplace/admin/sellers/{id}/data
     */
    public function getAdminSellerModerationDetailData()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $sellerId = (int)($_GET['id'] ?? 0);
            if ($sellerId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid seller']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $summary = $reportModel->getAdminSellerSummaryById($sellerId);
            if (!$summary) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Seller moderation profile not found']);
                return;
            }

            $reports = $reportModel->getAdminSellerRelatedReports($sellerId, [
                'status' => $_GET['status'] ?? 'all',
                'search' => $_GET['search'] ?? '',
            ]);

            $actionModel = new MarketplaceSellerAction();
            $historyRows = $actionModel->getSellerModerationHistory($sellerId, 200);

            $sellerName = trim((string)($summary['seller_first_name'] ?? '') . ' ' . (string)($summary['seller_last_name'] ?? ''));
            $seller = [
                'seller_id' => (int)($summary['seller_id'] ?? 0),
                'seller_name' => $sellerName !== '' ? $sellerName : 'Unknown Seller',
                'seller_email' => (string)($summary['seller_email'] ?? ''),
                'total_reports' => (int)($summary['total_reports'] ?? 0),
                'pending_reports' => (int)($summary['pending_reports'] ?? 0),
                'under_review_reports' => (int)($summary['under_review_reports'] ?? 0),
                'resolved_reports' => (int)($summary['resolved_reports'] ?? 0),
                'archived_reports' => (int)($summary['archived_reports'] ?? 0),
                'warning_count' => (int)($summary['warning_count'] ?? 0),
                'is_banned' => (int)($summary['is_banned'] ?? 0) === 1,
                'last_reported_at' => $summary['last_reported_at'] ?? null,
            ];

            $reportItems = array_map(function (array $row) {
                $images = json_decode((string)($row['product_images'] ?? '[]'), true);
                $firstImage = (is_array($images) && !empty($images)) ? $images[0] : '/images/placeholders/product.png';
                $reporterName = trim((string)($row['reporter_first_name'] ?? '') . ' ' . (string)($row['reporter_last_name'] ?? ''));

                return [
                    'id' => (int)$row['id'],
                    'order_id' => isset($row['order_id']) && $row['order_id'] !== null ? (int)$row['order_id'] : null,
                    'product_id' => (int)$row['product_id'],
                    'product_title' => $row['product_title'] ?? 'Unknown Product',
                    'product_price' => (float)($row['product_price'] ?? 0),
                    'product_image' => $firstImage,
                    'reporter_name' => $reporterName !== '' ? $reporterName : 'Unknown Reporter',
                    'reporter_email' => $row['reporter_email'] ?? '',
                    'category' => $row['category'] ?? 'other',
                    'reason' => $row['reason'] ?? '',
                    'status' => $row['status'] ?? 'pending',
                    'created_at' => $row['created_at'] ?? null,
                ];
            }, $reports);

            $history = array_map(function (array $row) {
                $adminName = trim((string)($row['admin_first_name'] ?? '') . ' ' . (string)($row['admin_last_name'] ?? ''));
                if ($adminName === '') {
                    $adminName = (string)($row['admin_email'] ?? 'Admin');
                }
                return [
                    'id' => (int)($row['id'] ?? 0),
                    'report_id' => isset($row['report_id']) && $row['report_id'] !== null ? (int)$row['report_id'] : null,
                    'action_type' => (string)($row['action_type'] ?? ''),
                    'reason' => (string)($row['reason'] ?? ''),
                    'admin_name' => $adminName !== '' ? $adminName : 'Admin',
                    'created_at' => $row['created_at'] ?? null,
                ];
            }, $historyRows);

            echo json_encode([
                'success' => true,
                'seller' => $seller,
                'reports' => $reportItems,
                'history' => $history,
            ]);
        } catch (Throwable $e) {
            Logger::error('getAdminSellerModerationDetailData error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/reported/update-status
     */
    public function updateReportedItemStatus()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            $status = trim((string)($_POST['status'] ?? ''));
            $adminNotes = trim((string)($_POST['admin_notes'] ?? ''));
            if ($reportId <= 0 || $status === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $ok = $reportModel->updateStatus($reportId, $status, (int)$admin['id'], $adminNotes !== '' ? $adminNotes : null);

            if (!$ok) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Unable to update report status']);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            Logger::error('updateReportedItemStatus error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/sellers/{id}/warn
     */
    public function warnMarketplaceSeller()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $sellerId = (int)($_GET['id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));
            $reportId = isset($_POST['report_id']) && (int)$_POST['report_id'] > 0
                ? (int)$_POST['report_id']
                : null;

            if ($sellerId <= 0 || $reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Reason is required']);
                return;
            }

            if ($reportId !== null) {
                $reportModel = new MarketplaceReport();
                $report = $reportModel->getByIdWithDetails($reportId);
                if (!$report || (int)($report['seller_id'] ?? 0) !== $sellerId) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Invalid report reference']);
                    return;
                }
            }

            $actionModel = new MarketplaceSellerAction();
            $ok = $actionModel->addWarning($sellerId, $reportId, (int)$admin['id'], $reason);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to issue warning']);
                return;
            }

            $warningCount = $actionModel->getWarningCount($sellerId);
            echo json_encode(['success' => true, 'warning_count' => $warningCount]);
        } catch (Throwable $e) {
            Logger::error('warnMarketplaceSeller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/sellers/{id}/toggle-ban
     */
    public function toggleMarketplaceSellerBan()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $sellerId = (int)($_GET['id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));
            $reportId = isset($_POST['report_id']) && (int)$_POST['report_id'] > 0
                ? (int)$_POST['report_id']
                : null;

            if ($sellerId <= 0 || $reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Ban toggle reason is required']);
                return;
            }

            if ($reportId !== null) {
                $reportModel = new MarketplaceReport();
                $report = $reportModel->getByIdWithDetails($reportId);
                if (!$report || (int)($report['seller_id'] ?? 0) !== $sellerId) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Invalid report reference']);
                    return;
                }
            }

            $actionModel = new MarketplaceSellerAction();
            $result = $actionModel->toggleBan($sellerId, $reportId, (int)$admin['id'], $reason);
            if (!$result['success']) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            echo json_encode([
                'success' => true,
                'is_banned' => (bool)$result['isBanned'],
                'action_type' => $result['actionType'],
            ]);
        } catch (Throwable $e) {
            Logger::error('toggleMarketplaceSellerBan error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/reported/warn-seller
     */
    public function warnReportedSeller()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));

            if ($reportId <= 0 || $reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Reason is required']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $report = $reportModel->getByIdWithDetails($reportId);
            if (!$report) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Report not found']);
                return;
            }

            $actionModel = new MarketplaceSellerAction();
            $ok = $actionModel->addWarning((int)$report['seller_id'], $reportId, (int)$admin['id'], $reason);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to issue warning']);
                return;
            }

            $warningCount = $actionModel->getWarningCount((int)$report['seller_id']);
            echo json_encode(['success' => true, 'warning_count' => $warningCount]);
        } catch (Throwable $e) {
            Logger::error('warnReportedSeller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/admin/reported/toggle-seller-ban
     */
    public function toggleReportedSellerBan()
    {
        header('Content-Type: application/json');
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $reportId = (int)($_POST['report_id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));

            if ($reportId <= 0 || $reason === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Ban toggle reason is required']);
                return;
            }

            $reportModel = new MarketplaceReport();
            $report = $reportModel->getByIdWithDetails($reportId);
            if (!$report) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Report not found']);
                return;
            }

            $actionModel = new MarketplaceSellerAction();
            $result = $actionModel->toggleBan((int)$report['seller_id'], $reportId, (int)$admin['id'], $reason);
            if (!$result['success']) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            echo json_encode([
                'success' => true,
                'is_banned' => (bool)$result['isBanned'],
                'action_type' => $result['actionType'],
            ]);
        } catch (Throwable $e) {
            Logger::error('toggleReportedSellerBan error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }
}