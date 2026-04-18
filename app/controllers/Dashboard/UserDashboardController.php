<?php
class Dashboard_UserDashboardController extends Controller {
    public function showUserDashboard() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Get authenticated user
        $sessionUser = Auth_LoginController::getSessionUser(true);
        if (!$sessionUser) {
            header('Location: /login');
            exit;
        }

        $userId = $sessionUser['id'];
        
        // Initialize models
        $userModel = new User();
        $orderModel = new Order();
        $lostItemModel = new LostItem();
        $foundItemModel = new FoundItem();
        $imageModel = new LostAndFoundImage();
        $forumQuestionModel = new ForumQuestion();
        $forumAnswerModel = new ForumAnswer();
        $communityPostModel = new CommunityPost();

        // Get full user details
        $user = $userModel->findById($userId) ?? $sessionUser;

        // Get recent orders (last 2)
        $allOrders = $orderModel->getOrdersForBuyer($userId);
        $recentOrders = array_slice($allOrders, 0, 2);

        // Get recent lost & found items (last 3, mixed)
        $recentLostItems = $lostItemModel->findAll();
        $recentFoundItems = $foundItemModel->findAll();
        
        // Combine and mark type
        $allLFItems = [];
        foreach (array_slice($recentLostItems, 0, 2) as $item) {
            $item['_type'] = 'lost';
            $item['_location'] = $item['last_known_location'] ?? 'Unknown';
            $item['_date'] = $item['date_time_lost'] ?? $item['created_at'];
            $item['_badge'] = $item['severity_level'] ?? 'General';
            $item['images'] = $imageModel->getImages('lost', $item['id']);
            $allLFItems[] = $item;
        }
        foreach (array_slice($recentFoundItems, 0, 1) as $item) {
            $item['_type'] = 'found';
            $item['_location'] = $item['found_location'] ?? 'Unknown';
            $item['_date'] = $item['date_time_found'] ?? $item['created_at'];
            $item['_badge'] = $item['condition_status'] ?? 'Good';
            $item['images'] = $imageModel->getImages('found', $item['id']);
            $allLFItems[] = $item;
        }
        // Sort by created_at descending
        usort($allLFItems, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        $recentLFItems = array_slice($allLFItems, 0, 3);

        // Get featured question (latest unanswered)
        $featuredQuestion = $forumQuestionModel->getLatestUnanswered();

        // Get featured blog
        $featuredBlog = $communityPostModel->getFeaturedBlog();

        // Get featured posts (last 2)
        $featuredPosts = $communityPostModel->getFeaturedPosts(2);

        // Get recent blogs (last 3)
        $recentBlogs = $communityPostModel->getRecentBlogs(3);

        // Get upcoming events (next 4)
        $upcomingEvents = $communityPostModel->getUpcomingEvents(4);

        // Calculate user stats
        $stats = [
            'questions_asked' => $forumQuestionModel->countByUser($userId),
            'answers_given'   => $forumAnswerModel->countByUser($userId),
            'orders_placed'   => count($allOrders),
            'blogs_written'   => $communityPostModel->countBlogsByUser($userId),
        ];

        $this->viewApp('/User/user-dashboard-view', [
            'user'             => $user,
            'recentOrders'     => $recentOrders,
            'recentLFItems'    => $recentLFItems,
            'featuredQuestion' => $featuredQuestion,
            'featuredBlog'     => $featuredBlog,
            'featuredPosts'    => $featuredPosts,
            'recentBlogs'      => $recentBlogs,
            'upcomingEvents'   => $upcomingEvents,
            'stats'            => $stats,
        ], 'User Dashboard - ReidHub');
    }
}