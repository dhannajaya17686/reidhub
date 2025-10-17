<?php
class Dashboard_AdminDashboardController extends Controller {
    public function showAdminDashboard() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $sessionAdmin = $_SESSION['admin'] ?? null;
        $adminId = $_SESSION['admin_id'] ?? ($sessionAdmin['id'] ?? null);

        if (!$adminId) {
            header('Location: /login', true, 302);
            exit;
        }

        $admin = $sessionAdmin;
        if (!$admin || (int)($admin['id'] ?? 0) !== (int)$adminId) {
            $admin = (new User())->findById((int)$adminId); // or use Admin model if you have one
            if (!$admin) {
                $_SESSION = [];
                header('Location: /login', true, 302);
                exit;
            }
            $_SESSION['admin'] = $admin;
        }

        $this->viewApp('/Admin/admin-dashboard-view', ['admin' => $admin], 'Admin Dashboard - ReidHub');
    }
}
