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
            $admin = (new User())->findById((int)$adminId);
            if (!$admin) {
                $_SESSION = [];
                header('Location: /login', true, 302);
                exit;
            }
            $_SESSION['admin'] = $admin;
        }

        // Get counts
        $totalUsersCount = (new User())->getTotalCount();
        $marketplaceCount = (new MarketPlace())->getTotalActiveCount();
        $forumPostsCount = (new ForumModel())->getTotalActivePostsCount();
        $pendingReportsCount = (new Report())->getPendingReportsCount();

        // Get recent activities
        $recentUsers = (new User())->getRecentUsers(5);
        $recentBlogs = (new Blog())->getRecentBlogs(5);
        $recentItems = (new MarketPlace())->getRecentItems(5);
        $recentReports = (new Report())->getRecentReports(5);
        $recentEvents = (new Event())->getRecentEvents(5);

        // Merge and sort all activities by timestamp
        $allActivities = [];
        
        foreach ($recentUsers as $user) {
            $allActivities[] = [
                'type' => 'user',
                'timestamp' => strtotime($user['created_at']),
                'created_at' => $user['created_at'],
                'data' => $user
            ];
        }
        
        foreach ($recentBlogs as $blog) {
            $allActivities[] = [
                'type' => 'blog',
                'timestamp' => strtotime($blog['created_at']),
                'created_at' => $blog['created_at'],
                'data' => $blog
            ];
        }
        
        foreach ($recentItems as $item) {
            $allActivities[] = [
                'type' => 'marketplace',
                'timestamp' => strtotime($item['created_at']),
                'created_at' => $item['created_at'],
                'data' => $item
            ];
        }
        
        foreach ($recentReports as $report) {
            $allActivities[] = [
                'type' => 'report',
                'timestamp' => strtotime($report['created_at']),
                'created_at' => $report['created_at'],
                'data' => $report
            ];
        }
        
        foreach ($recentEvents as $event) {
            $allActivities[] = [
                'type' => 'event',
                'timestamp' => strtotime($event['created_at']),
                'created_at' => $event['created_at'],
                'data' => $event
            ];
        }

        // Sort by timestamp descending and get top 5
        usort($allActivities, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        $recentActivities = array_slice($allActivities, 0, 5);

        $this->viewApp('/Admin/admin-dashboard-view', [
            'admin' => $admin,
            'totalUsersCount' => $totalUsersCount,
            'marketplaceCount' => $marketplaceCount,
            'forumPostsCount' => $forumPostsCount,
            'pendingReportsCount' => $pendingReportsCount,
            'recentActivities' => $recentActivities,
            'recentUsers' => array_slice($recentUsers, 0, 5)
        ], 'Admin Dashboard - ReidHub');
    }
    
}
