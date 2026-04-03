<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Community_CommunityAdminController extends Controller
{
    // ============ ADMIN DASHBOARD ============
    public function showCommunityAdminDashboard()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        
        // Debug logging
        Logger::info("CommunityAdminController::showCommunityAdminDashboard called for admin: " . ($admin['email'] ?? 'unknown'));
        
        $data = ['admin' => $admin];
        $this->viewApp('/Admin/community-and-social/manage-community-view', $data, 'Community Management - ReidHub');
    }

    // Admin dashboard handles all community management
    // Specific management done through manage-community-view.php
}