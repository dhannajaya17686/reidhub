<?php
class Dashboard_UserDashboardController extends Controller {
    public function showUserDashboard() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Prefer full user cached in session; else load by user_id
        $sessionUser = $_SESSION['user'] ?? null;
        $userId = $_SESSION['user_id'] ?? ($sessionUser['id'] ?? null);

        if (!$userId) {
            header('Location: /login', true, 302);
            exit;
        }

        $user = $sessionUser;
        if (!$user || (int)($user['id'] ?? 0) !== (int)$userId) {
            $user = (new User())->findById((int)$userId);
            if (!$user) {
                // Bad session -> force re-login
                $_SESSION = [];
                header('Location: /login', true, 302);
                exit;
            }
            // Cache for later requests (optional)
            $_SESSION['user'] = $user;
        }

        $this->viewApp('/User/user-dashboard-view', ['user' => $user], 'User Dashboard - ReidHub');
    }
}