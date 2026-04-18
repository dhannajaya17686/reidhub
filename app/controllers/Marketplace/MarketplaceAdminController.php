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
}