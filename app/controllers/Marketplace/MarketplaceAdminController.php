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
}