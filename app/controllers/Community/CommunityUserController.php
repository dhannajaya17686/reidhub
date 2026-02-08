<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/CommunityPost.php';
require_once __DIR__ . '/../../models/Club.php';
require_once __DIR__ . '/../../models/Event.php';

class Community_CommunityUserController extends Controller
{
    // ============ MAIN COMMUNITY FEED ============
    public function showCommunityDashboard()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Debug logging
        Logger::info("CommunityUserController::showCommunityDashboard called for user: " . ($user['email'] ?? 'unknown'));
        
        // Check if user is a community admin
        $isCommunityAdmin = $this->checkIfCommunityAdmin($user['id']);
        
        // Get community posts from database
        try {
            $postModel = new CommunityPost();
            $posts = $postModel->getAllPosts(50, 0);
        } catch (Exception $e) {
            // If table doesn't exist yet, use empty array
            Logger::warning("Could not fetch posts: " . $e->getMessage());
            $posts = [];
        }
        
        $data = [
            'user' => $user,
            'posts' => $posts,
            'isCommunityAdmin' => $isCommunityAdmin
        ];
        
        Logger::info("Rendering community feed view with " . count($posts) . " posts");
        $this->viewApp('/User/community/community-feed', $data, 'Community - ReidHub');
    }
    
    /**
     * Check if user has community admin role
     */
    private function checkIfCommunityAdmin(int $userId): bool
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM community_admins WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && (int)$result['count'] > 0;
        } catch (Exception $e) {
            // Table might not exist yet, return false
            return false;
        }
    }
    
    /**
     * Check if user has club admin permission (granted by system admin)
     */
    private function checkIfClubAdmin(int $userId): bool
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM community_admins WHERE user_id = ? AND role_type = 'club_admin'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && (int)$result['count'] > 0;
        } catch (Exception $e) {
            // Table might not exist yet, return false
            return false;
        }
    }
    
    /**
     * Check if user is a club creator or admin
     */
    private function isClubAdminOrCreator(int $userId): bool
    {
        try {
            $clubModel = new Club();
            // Check if user created any clubs
            $createdClubs = $clubModel->getClubsByCreator($userId);
            if (!empty($createdClubs)) {
                return true;
            }
            
            // Check if user is admin/owner of any club
            $memberClubs = $clubModel->getClubsByMember($userId);
            foreach ($memberClubs as $club) {
                if (in_array($club['member_role'], ['admin', 'owner'])) {
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // ============ POST MANAGEMENT (For Community Admins) ============
    public function showCreatePost()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        if (!$this->checkIfCommunityAdmin($user['id'])) {
            header('Location: /dashboard/community');
            exit;
        }
        
        $data = ['user' => $user];
        $this->viewApp('/User/community/create-post', $data, 'Create Post - ReidHub');
    }
    
    public function createPost()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        if (!$this->checkIfCommunityAdmin($user['id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $postModel = new CommunityPost();
        $postId = $postModel->createPost([
            'author_id' => $user['id'],
            'title' => $_POST['title'] ?? null,
            'content' => $_POST['content'] ?? '',
            'post_type' => $_POST['post_type'] ?? 'general',
            'images' => $_POST['images'] ?? [],
            'status' => 'published'
        ]);
        
        if ($postId) {
            header('Location: /dashboard/community');
        } else {
            echo "Failed to create post";
        }
    }
    
    public function showMyPosts()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        if (!$this->checkIfCommunityAdmin($user['id'])) {
            header('Location: /dashboard/community');
            exit;
        }
        
        $postModel = new CommunityPost();
        $posts = $postModel->getPostsByAuthor($user['id']);
        
        $data = [
            'user' => $user,
            'posts' => $posts
        ];
        
        $this->viewApp('/User/community/my-posts', $data, 'My Posts - ReidHub');
    }
    
    public function deletePost()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = json_decode(file_get_contents('php://input'), true);
        $postId = $data['post_id'] ?? 0;
        
        $postModel = new CommunityPost();
        
        // Check if user is author or admin
        if ($postModel->isAuthor($postId, $user['id'])) {
            $success = $postModel->deletePost($postId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        }
    }

    // ============ BLOGS ============
    public function showAllBlogs()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/all-blogs', $data, 'Blogs - ReidHub');
    }

    public function showViewBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/view-blog', $data, 'View Blog - ReidHub');
    }

    public function showCreateBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = [
            'user' => $user,
            'categories' => [
                'academics' => 'Academics',
                'campus-life' => 'Campus Life',
                'student-tips' => 'Student Tips',
                'events' => 'Events'
            ]
        ];
        $this->viewApp('/User/community/blogs/create-blog', $data, 'Create Blog - ReidHub');
    }

    public function showEditBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/edit-blog', $data, 'Edit Blog - ReidHub');
    }

    // ============ CLUBS ============
    public function showAllClubs()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        $clubModel = new Club();
        $category = $_GET['category'] ?? 'all';
        
        try {
            $allClubs = $clubModel->getAllClubs($category !== 'all' ? $category : null);
            $myClubs = $clubModel->getClubsByCreator($user['id']);
            $joinedClubs = $clubModel->getJoinedClubs($user['id']);
        } catch (Exception $e) {
            Logger::warning("Could not fetch clubs: " . $e->getMessage());
            $allClubs = [];
            $myClubs = [];
            $joinedClubs = [];
        }
        
        $isCommunityAdmin = $this->checkIfCommunityAdmin($user['id']);
        $isClubAdmin = $this->checkIfClubAdmin($user['id']);
        
        $data = [
            'user' => $user,
            'clubs' => $allClubs,
            'myClubs' => $myClubs,
            'joinedClubs' => $joinedClubs,
            'isCommunityAdmin' => $isCommunityAdmin,
            'isClubAdmin' => $isClubAdmin,
            'categories' => [
                'all' => 'All',
                'academic' => 'Academic',
                'cultural' => 'Cultural',
                'sports' => 'Sports',
                'technology' => 'Technology',
                'arts' => 'Arts',
                'social' => 'Social',
                'other' => 'Other'
            ]
        ];
        
        $this->viewApp('/User/community/clubs/all-clubs', $data, 'Clubs - ReidHub');
    }

    public function showViewClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $clubId = $_GET['id'] ?? 0;
        
        $clubModel = new Club();
        
        try {
            $club = $clubModel->getClubById($clubId);
            if (!$club) {
                header('Location: /dashboard/community/clubs');
                exit;
            }
            
            $isOwner = $clubModel->isClubCreator($clubId, $user['id']);
            $isAdmin = $clubModel->isClubAdmin($clubId, $user['id']);
            $isMember = $clubModel->isMember($clubId, $user['id']);
            $isCommunityAdmin = $this->checkIfCommunityAdmin($user['id']);
            
            $data = [
                'user' => $user,
                'club' => $club,
                'isOwner' => $isOwner,
                'isAdmin' => $isAdmin,
                'isMember' => $isMember,
                'isCommunityAdmin' => $isCommunityAdmin
            ];
        } catch (Exception $e) {
            Logger::warning("Could not fetch club: " . $e->getMessage());
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $this->viewApp('/User/community/clubs/view-club', $data, 'View Club - ReidHub');
    }

    public function showCreateClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Check if user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $data = [
            'user' => $user,
            'categories' => [
                'academic' => 'Academic',
                'cultural' => 'Cultural',
                'sports' => 'Sports',
                'technology' => 'Technology',
                'arts' => 'Arts',
                'social' => 'Social',
                'other' => 'Other'
            ]
        ];
        $this->viewApp('/User/community/clubs/create-club', $data, 'Create Club - ReidHub');
    }
    
    public function createClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $imageUrl = null;
        
        // Handle image upload if provided
        if (isset($_FILES['club_image']) && $_FILES['club_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['club_image'];
            $allowedMimes = ['image/jpeg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedMimes)) {
                header('Location: /dashboard/community/clubs/create?error=invalid_image');
                exit;
            }
            
            // Create storage directory if it doesn't exist
            $storageDir = __DIR__ . '/../../../public/storage/clubs';
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            // Generate unique filename
            $ext = $mimeType === 'image/jpeg' ? 'jpg' : 'png';
            $filename = 'club_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filePath = $storageDir . '/' . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $imageUrl = '/storage/clubs/' . $filename;
            }
        }
        
        $clubModel = new Club();
        $clubId = $clubModel->createClub([
            'name' => $_POST['club_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? 'other',
            'creator_id' => $user['id'],
            'image_url' => $imageUrl
        ]);
        
        if ($clubId) {
            header('Location: /dashboard/community/clubs/view?id=' . $clubId);
        } else {
            header('Location: /dashboard/community/clubs/create?error=1');
        }
    }

    public function showEditClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $clubId = $_GET['id'] ?? 0;
        
        $clubModel = new Club();
        
        try {
            $club = $clubModel->getClubById($clubId);
            if (!$club) {
                header('Location: /dashboard/community/clubs');
                exit;
            }
            
            // Only owner can edit
            if ($club['creator_id'] != $user['id']) {
                header('Location: /dashboard/community/clubs/view?id=' . $clubId);
                exit;
            }
            
            $data = [
                'user' => $user,
                'club' => $club,
                'categories' => [
                    'academic' => 'Academic',
                    'cultural' => 'Cultural',
                    'sports' => 'Sports',
                    'technology' => 'Technology',
                    'arts' => 'Arts',
                    'social' => 'Social',
                    'other' => 'Other'
                ]
            ];
        } catch (Exception $e) {
            Logger::warning("Could not fetch club: " . $e->getMessage());
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $this->viewApp('/User/community/clubs/edit-club', $data, 'Edit Club - ReidHub');
    }
    
    public function editClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $clubId = $_POST['club_id'] ?? $_GET['id'] ?? 0;
        
        $clubModel = new Club();
        
        try {
            $club = $clubModel->getClubById($clubId);
            if (!$club) {
                header('Location: /dashboard/community/clubs');
                exit;
            }
            
            // Only owner can edit
            if ($club['creator_id'] != $user['id']) {
                header('Location: /dashboard/community/clubs/view?id=' . $clubId);
                exit;
            }
        } catch (Exception $e) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $imageUrl = $club['image_url']; // Keep existing image by default
        
        // Handle image upload if provided
        if (isset($_FILES['club_image']) && $_FILES['club_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['club_image'];
            $allowedMimes = ['image/jpeg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mimeType, $allowedMimes)) {
                // Create storage directory if it doesn't exist
                $storageDir = __DIR__ . '/../../../public/storage/clubs';
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0755, true);
                }
                
                // Delete old image if exists
                if ($club['image_url']) {
                    $oldPath = __DIR__ . '/../../../public' . $club['image_url'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                // Generate unique filename
                $ext = $mimeType === 'image/jpeg' ? 'jpg' : 'png';
                $filename = 'club_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filePath = $storageDir . '/' . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $imageUrl = '/storage/clubs/' . $filename;
                }
            }
        }
        
        $success = $clubModel->updateClub($clubId, [
            'name' => $_POST['club_name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? 'other',
            'image_url' => $imageUrl
        ]);
        
        if ($success) {
            header('Location: /dashboard/community/clubs/view?id=' . $clubId);
        } else {
            header('Location: /dashboard/community/clubs/edit?id=' . $clubId . '&error=1');
        }
    }
    
    public function joinClub()
    {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        $input = json_decode(file_get_contents('php://input'), true);
        $clubId = $input['club_id'] ?? 0;
        
        if (!$clubId) {
            echo json_encode(['success' => false, 'message' => 'Invalid club ID']);
            return;
        }
        
        $clubModel = new Club();
        
        try {
            $success = $clubModel->addMember($clubId, $user['id'], 'member');
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function leaveClub()
    {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        $input = json_decode(file_get_contents('php://input'), true);
        $clubId = $input['club_id'] ?? 0;
        
        if (!$clubId) {
            echo json_encode(['success' => false, 'message' => 'Invalid club ID']);
            return;
        }
        
        $clubModel = new Club();
        
        try {
            // Don't allow owner to leave
            if ($clubModel->isClubCreator($clubId, $user['id'])) {
                echo json_encode(['success' => false, 'message' => 'Club owner cannot leave. Delete the club instead.']);
                return;
            }
            
            $success = $clubModel->removeMember($clubId, $user['id']);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteClub()
    {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        $data = json_decode(file_get_contents('php://input'), true);
        $clubId = $data['club_id'] ?? 0;
        
        $clubModel = new Club();
        
        try {
            // Only the creator can delete the club
            if (!$clubModel->isClubCreator($clubId, $user['id'])) {
                echo json_encode(['success' => false, 'message' => 'Only the club creator can delete the club']);
                return;
            }
            
            $success = $clubModel->deleteClub($clubId);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============ CLUB ADMIN PORTAL ============
    public function showClubAdminDashboard()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Verify user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $clubModel = new Club();
        
        try {
            // Get clubs where user is owner or admin
            $myClubs = $clubModel->getClubsByCreator($user['id']);
        } catch (Exception $e) {
            Logger::warning("Could not fetch clubs: " . $e->getMessage());
            $myClubs = [];
        }
        
        $data = [
            'user' => $user,
            'clubs' => $myClubs
        ];
        
        $this->viewApp('/User/community/club-admin/dashboard', $data, 'Club Admin Portal - ReidHub');
    }

    public function showClubAdminEvents()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Verify user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $data = ['user' => $user];
        $this->viewApp('/User/community/club-admin/events', $data, 'Club Events - ReidHub');
    }

    public function showClubAdminAnnouncements()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Verify user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $data = ['user' => $user];
        $this->viewApp('/User/community/club-admin/announcements', $data, 'Announcements - ReidHub');
    }

    public function showClubAdminApplications()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Verify user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs');
            exit;
        }
        
        $data = ['user' => $user];
        $this->viewApp('/User/community/club-admin/applications', $data, 'Applications - ReidHub');
    }

    // ============ EVENTS ============
    /**
     * Show all events page with pagination
     */
    public function showAllEvents()
    {
        $user = Auth_LoginController::getSessionUser(true);
        Logger::info("CommunityUserController::showAllEvents called for user: " . $user['email']);
        
        try {
            $eventModel = new Event();
            
            // Get pagination parameters
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;
            
            // Get filter status (upcoming, ongoing, completed, cancelled, all)
            $status = $_GET['status'] ?? 'upcoming';
            
            Logger::info("Fetching events with status: " . $status);
            
            if ($status === 'all' || $status === '') {
                $events = $eventModel->getAllUpcomingEvents($limit, $offset);
            } else {
                $events = $eventModel->getEventsByStatus($status, $limit, $offset);
            }
            
            Logger::info("Fetched " . count($events) . " events for status: " . $status);
            if (!empty($events)) {
                Logger::info("Sample event: " . json_encode([
                    'id' => $events[0]['id'] ?? 'N/A',
                    'title' => $events[0]['title'] ?? 'N/A',
                    'status' => $events[0]['status'] ?? 'N/A'
                ]));
            }
            
            // Get all events for calendar view (not paginated or filtered)
            $allEvents = $eventModel->getAllUpcomingEvents(1000, 0);
            Logger::info("Fetched " . count($allEvents) . " events for calendar");
            
            // Get user's registered events for UI indication
            $userEvents = $eventModel->getEventsForUser($user['id']);
            $userEventIds = array_column($userEvents, 'id');
            
            // Check if user can create events
            $isClubAdmin = $this->isClubAdminOrCreator($user['id']);
            
            $data = [
                'user' => $user,
                'events' => $events,
                'allEvents' => $allEvents,  // For calendar view
                'userEventIds' => $userEventIds,
                'currentPage' => $page,
                'currentStatus' => $status,
                'itemsPerPage' => $limit,
                'isClubAdmin' => $isClubAdmin
            ];
            
            Logger::info("Rendering all events view with " . count($events) . " events");
            $this->viewApp('/User/community/events/all-events', $data, 'Events - ReidHub');
        } catch (Exception $e) {
            Logger::error("Error in showAllEvents: " . $e->getMessage());
            $data = ['user' => $user, 'error' => 'Unable to load events', 'events' => [], 'allEvents' => [], 'isClubAdmin' => false];
            $this->viewApp('/User/community/events/all-events', $data, 'Events - ReidHub');
        }
    }

    /**
     * Show single event details page
     */
    public function showViewEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            header('Location: /dashboard/community/events');
            exit;
        }
        
        try {
            $eventModel = new Event();
            $event = $eventModel->getEventById($eventId);
            
            if (!$event) {
                header('Location: /dashboard/community/events');
                exit;
            }
            
            // Check if user is registered
            $isRegistered = $eventModel->isUserRegistered($eventId, $user['id']);
            
            // Get event attendees
            $attendees = $eventModel->getEventAttendees($eventId);
            
            // Check if user is event creator
            $isCreator = $event['creator_id'] == $user['id'];
            
            $data = [
                'user' => $user,
                'event' => $event,
                'attendees' => $attendees,
                'isRegistered' => $isRegistered,
                'isCreator' => $isCreator
            ];
            
            Logger::info("Rendering event view for event: " . $eventId);
            $this->viewApp('/User/community/events/view-event', $data, 'View Event - ReidHub');
        } catch (Exception $e) {
            Logger::error("Error in showViewEvent: " . $e->getMessage());
            header('Location: /dashboard/community/events');
            exit;
        }
    }

    /**
     * Show create event form (for club admins only)
     */
    public function showCreateEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        Logger::info("CommunityUserController::showCreateEvent called for user: " . $user['email']);
        
        // Check if user is a club admin/creator
        if (!$this->isClubAdminOrCreator($user['id'])) {
            Logger::warning("User " . $user['email'] . " attempted to create event without club admin permission");
            header('Location: /dashboard/community/events');
            exit;
        }
        
        try {
            // Get user's clubs (if they're a club admin)
            $clubModel = new Club();
            $userClubs = $clubModel->getClubsByCreator($user['id']);
            
            // Also get clubs where user is an admin/owner
            $memberClubs = $clubModel->getClubsByMember($user['id']);
            $adminClubs = array_filter($memberClubs, function($club) {
                return in_array($club['member_role'], ['admin', 'owner']);
            });
            
            // Merge and deduplicate
            $allAdminClubs = array_merge($userClubs, $adminClubs);
            
            $data = [
                'user' => $user,
                'userClubs' => $allAdminClubs,
                'isClubAdmin' => true
            ];
            
            $this->viewApp('/User/community/events/create-event', $data, 'Create Event - ReidHub');
        } catch (Exception $e) {
            Logger::error("Error in showCreateEvent: " . $e->getMessage());
            header('Location: /dashboard/community/events');
            exit;
        }
    }

    /**
     * Create a new event (API endpoint)
     */
    public function createEvent()
    {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        // Check if user is a club admin/creator
        if (!$this->isClubAdminOrCreator($user['id'])) {
            Logger::warning("User " . $user['email'] . " attempted to create event without club admin permission");
            echo json_encode(['success' => false, 'message' => 'Only club admins can create events']);
            exit;
        }
        
        Logger::info("createEvent called with POST data: " . json_encode(array_keys($_POST)));
        Logger::info("createEvent called with FILES data: " . json_encode(array_keys($_FILES)));
        
        try {
            // Validate required fields
            $required = ['title', 'description', 'event_date', 'location', 'category'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                    exit;
                }
            }
            
            $imageUrl = null;
            
            // Handle image upload if provided
            Logger::info("Checking for image upload");
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                Logger::info("Image file detected: " . $_FILES['event_image']['name']);
                $file = $_FILES['event_image'];
                $allowedMimes = ['image/jpeg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedMimes)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image format. Only JPEG and PNG are allowed.']);
                    exit;
                }
                
                // Create storage directory if it doesn't exist
                $storageDir = __DIR__ . '/../../../public/storage/events';
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0755, true);
                }
                
                // Generate unique filename
                $ext = $mimeType === 'image/jpeg' ? 'jpg' : 'png';
                $filename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filePath = $storageDir . '/' . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $imageUrl = '/storage/events/' . $filename;
                }
            }
            
            $eventModel = new Event();
            
            // Convert empty max_attendees to null
            $maxAttendees = !empty($_POST['max_attendees']) ? (int)$_POST['max_attendees'] : null;
            $clubId = !empty($_POST['club_id']) ? (int)$_POST['club_id'] : null;
            $googleFormUrl = !empty($_POST['google_form_url']) ? $_POST['google_form_url'] : null;
            
            $eventData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'creator_id' => $user['id'],
                'club_id' => $clubId,
                'event_date' => $_POST['event_date'],
                'location' => $_POST['location'],
                'category' => $_POST['category'],
                'max_attendees' => $maxAttendees,
                'image_url' => $imageUrl,
                'google_form_url' => $googleFormUrl,
                'status' => 'upcoming'
            ];
            
            $eventId = $eventModel->createEvent($eventData);
            
            if ($eventId) {
                Logger::info("Event created: ID $eventId by user " . $user['email']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Event created successfully',
                    'eventId' => $eventId,
                    'redirect' => "/dashboard/community/events/view?id=$eventId"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create event']);
            }
        } catch (Exception $e) {
            Logger::error("Error in createEvent: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit event form (for event creator)
     */
    public function showEditEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        $eventId = $_GET['id'] ?? null;
        if (!$eventId) {
            header('Location: /dashboard/community/events');
            exit;
        }
        
        try {
            $eventModel = new Event();
            $event = $eventModel->getEventById($eventId);
            
            if (!$event || $event['creator_id'] != $user['id']) {
                // User is not the creator, deny access
                header('Location: /dashboard/community/events');
                exit;
            }
            
            // Get user's clubs
            $clubModel = new Club();
            $userClubs = $clubModel->getClubsByCreator($user['id']);
            
            $data = [
                'user' => $user,
                'event' => $event,
                'userClubs' => $userClubs
            ];
            
            Logger::info("Rendering edit event view for event: " . $eventId);
            $this->viewApp('/User/community/events/edit-event', $data, 'Edit Event - ReidHub');
        } catch (Exception $e) {
            Logger::error("Error in showEditEvent: " . $e->getMessage());
            header('Location: /dashboard/community/events');
            exit;
        }
    }

    /**
     * Update event (API endpoint)
     */
    public function updateEvent()
    {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $eventId = $_POST['event_id'] ?? null;
            if (!$eventId) {
                echo json_encode(['success' => false, 'message' => 'Missing event ID']);
                exit;
            }
            
            $eventModel = new Event();
            $event = $eventModel->getEventById($eventId);
            
            // Check authorization
            if (!$event || $event['creator_id'] != $user['id']) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            // Handle image upload if provided
            $imageUrl = null;
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['event_image'];
                $allowedMimes = ['image/jpeg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedMimes)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image format. Only JPEG and PNG are allowed.']);
                    exit;
                }
                
                // Create storage directory if it doesn't exist
                $storageDir = __DIR__ . '/../../../public/storage/events';
                if (!is_dir($storageDir)) {
                    mkdir($storageDir, 0755, true);
                }
                
                // Generate unique filename
                $ext = $mimeType === 'image/jpeg' ? 'jpg' : 'png';
                $filename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filePath = $storageDir . '/' . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $imageUrl = '/storage/events/' . $filename;
                }
            }
            
            $updateData = [];
            $allowedFields = ['title', 'description', 'event_date', 'location', 'category', 'max_attendees', 'image_url', 'google_form_url', 'status'];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    // Handle max_attendees empty string
                    if ($field === 'max_attendees') {
                        $updateData[$field] = !empty($_POST[$field]) ? (int)$_POST[$field] : null;
                    } elseif ($field === 'google_form_url') {
                        $updateData[$field] = !empty($_POST[$field]) ? $_POST[$field] : null;
                    } else {
                        $updateData[$field] = $_POST[$field];
                    }
                }
            }
            
            // Add uploaded image if present
            if ($imageUrl !== null) {
                $updateData['image_url'] = $imageUrl;
            }
            
            if (empty($updateData)) {
                echo json_encode(['success' => false, 'message' => 'No data to update']);
                exit;
            }
            
            if ($eventModel->updateEvent($eventId, $updateData)) {
                Logger::info("Event updated: ID $eventId by user " . $user['email']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Event updated successfully',
                    'redirect' => "/dashboard/community/events/view?id=$eventId"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update event']);
            }
        } catch (Exception $e) {
            Logger::error("Error in updateEvent: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete event (API endpoint)
     */
    public function deleteEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $eventId = $_POST['event_id'] ?? null;
            if (!$eventId) {
                echo json_encode(['success' => false, 'message' => 'Missing event ID']);
                exit;
            }
            
            $eventModel = new Event();
            $event = $eventModel->getEventById($eventId);
            
            // Check authorization
            if (!$event || $event['creator_id'] != $user['id']) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            if ($eventModel->deleteEvent($eventId)) {
                Logger::info("Event deleted: ID $eventId by user " . $user['email']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Event deleted successfully',
                    'redirect' => '/dashboard/community/events'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
            }
        } catch (Exception $e) {
            Logger::error("Error in deleteEvent: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Register user for event (API endpoint)
     */
    public function registerForEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $eventId = $_POST['event_id'] ?? null;
            if (!$eventId) {
                echo json_encode(['success' => false, 'message' => 'Missing event ID']);
                exit;
            }
            
            $eventModel = new Event();
            
            // Check if event exists
            $event = $eventModel->getEventById($eventId);
            if (!$event) {
                echo json_encode(['success' => false, 'message' => 'Event not found']);
                exit;
            }
            
            // Check max attendees
            if ($event['max_attendees'] && $event['attendee_count'] >= $event['max_attendees']) {
                echo json_encode(['success' => false, 'message' => 'Event is at maximum capacity']);
                exit;
            }
            
            if ($eventModel->registerUserForEvent($eventId, $user['id'])) {
                Logger::info("User " . $user['email'] . " registered for event $eventId");
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully registered for event'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register for event']);
            }
        } catch (Exception $e) {
            Logger::error("Error in registerForEvent: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Unregister user from event (API endpoint)
     */
    public function unregisterFromEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $eventId = $_POST['event_id'] ?? null;
            if (!$eventId) {
                echo json_encode(['success' => false, 'message' => 'Missing event ID']);
                exit;
            }
            
            $eventModel = new Event();
            
            if ($eventModel->unregisterUserFromEvent($eventId, $user['id'])) {
                Logger::info("User " . $user['email'] . " unregistered from event $eventId");
                echo json_encode([
                    'success' => true,
                    'message' => 'Successfully unregistered from event'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to unregister from event']);
            }
        } catch (Exception $e) {
            Logger::error("Error in unregisterFromEvent: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}