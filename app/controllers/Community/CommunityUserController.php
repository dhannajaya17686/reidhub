<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/CommunityPost.php';
require_once __DIR__ . '/../../models/Club.php';

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
    public function showAllEvents()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/all-events', $data, 'Events - ReidHub');
    }

    public function showViewEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/view-event', $data, 'View Event - ReidHub');
    }

    public function showCreateEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/create-event', $data, 'Create Event - ReidHub');
    }

    public function showEditEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/edit-event', $data, 'Edit Event - ReidHub');
    }
}