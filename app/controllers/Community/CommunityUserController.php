<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/CommunityPost.php';
require_once __DIR__ . '/../../models/Club.php';
require_once __DIR__ . '/../../models/Event.php';
require_once __DIR__ . '/../../models/AdminRequest.php';
require_once __DIR__ . '/../../models/Blog.php';

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
            // Check if user has approved club admin status in community_admins
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM community_admins WHERE user_id = ? AND role_type = 'club_admin'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && (int)$result['count'] > 0) {
                return true;
            }
            
            // Also check if they have an approved request
            $adminRequestModel = new AdminRequest();
            return $adminRequestModel->isApprovedClubAdmin($userId);
        } catch (Exception $e) {
            Logger::warning("Error checking club admin status: " . $e->getMessage());
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
        
        $blogId = $_GET['id'] ?? null;
        if (!$blogId) {
            $_SESSION['error'] = 'Blog ID is required';
            header('Location: /dashboard/community/blogs');
            exit;
        }
        
        $blogModel = new Blog();
        $blog = $blogModel->getBlogById($blogId);
        
        if (!$blog) {
            $_SESSION['error'] = 'Blog not found';
            header('Location: /dashboard/community/blogs');
            exit;
        }
        
        // Check if user is the author
        if ((int)$blog['author_id'] !== (int)$user['id']) {
            $_SESSION['error'] = 'You are not authorized to edit this blog';
            header('Location: /dashboard/community/blogs');
            exit;
        }
        
        // Process tags if JSON
        if (!empty($blog['tags'])) {
            $tagsArray = json_decode($blog['tags'], true);
            $blog['tags'] = is_array($tagsArray) ? implode(', ', $tagsArray) : '';
        } else {
            $blog['tags'] = '';
        }
        
        $data = [
            'user' => $user,
            'blog' => $blog,
            'categories' => [
                'academics' => 'Academics',
                'campus-life' => 'Campus Life',
                'student-tips' => 'Student Tips',
                'events' => 'Events'
            ]
        ];
        
        $this->viewApp('/User/community/blogs/edit-blog', $data, 'Edit Blog - ReidHub');
    }

    // ============ BLOG API ENDPOINTS ============
    
    /**
     * API: Get all published blogs
     */
    public function getBlogsApi()
    {
        header('Content-Type: application/json');
        error_log("getBlogsApi called");
        
        try {
            // Check if Blog model exists
            if (!class_exists('Blog')) {
                throw new Exception('Blog model not found');
            }
            
            $blogModel = new Blog();
            error_log("Blog model instantiated");
            
            $blogs = $blogModel->getAllBlogs(100, 0);
            error_log("Blogs fetched: " . count($blogs));
            
            echo json_encode([
                'success' => true,
                'blogs' => $blogs,
                'count' => count($blogs)
            ]);
            exit;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            http_response_code(200); // Change to 200 so JS can parse the error
            echo json_encode([
                'success' => false,
                'message' => 'Database error. Please ensure the blogs table exists.',
                'error' => $e->getMessage(),
                'sql_error' => true
            ]);
            exit;
        } catch (Exception $e) {
            error_log("Error fetching blogs: " . $e->getMessage());
            http_response_code(200); // Change to 200 so JS can parse the error
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch blogs: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    /**
     * API: Get current user's blogs
     */
    public function getMyBlogsApi()
    {
        header('Content-Type: application/json');
        error_log("getMyBlogsApi called");
        
        try {
            $user = Auth_LoginController::getSessionUser(true);
            error_log("User ID: " . $user['id']);
            
            if (!class_exists('Blog')) {
                throw new Exception('Blog model not found');
            }
            
            $blogModel = new Blog();
            $blogs = $blogModel->getBlogsByAuthor($user['id']);
            error_log("User blogs fetched: " . count($blogs));
            
            echo json_encode([
                'success' => true,
                'blogs' => $blogs
            ]);
            exit;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'message' => 'Database error. Please ensure the blogs table exists.',
                'error' => $e->getMessage(),
                'sql_error' => true
            ]);
            exit;
        } catch (Exception $e) {
            error_log("Error fetching user blogs: " . $e->getMessage());
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch your blogs: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
    
    /**
     * API: Search blogs
     */
    public function searchBlogsApi()
    {
        header('Content-Type: application/json');
        
        try {
            $query = $_GET['q'] ?? '';
            $category = $_GET['category'] ?? 'all';
            
            $blogModel = new Blog();
            
            if (!empty($query)) {
                $blogs = $blogModel->searchBlogs($query, $category);
            } elseif ($category !== 'all') {
                $blogs = $blogModel->getBlogsByCategory($category);
            } else {
                $blogs = $blogModel->getAllBlogs(100, 0);
            }
            
            echo json_encode([
                'success' => true,
                'blogs' => $blogs
            ]);
        } catch (Exception $e) {
            Logger::error("Blog search failed: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Search failed'
            ]);
        }
    }
    
    /**
     * API: Delete a blog post
     */
    public function deleteBlogApi()
    {
        header('Content-Type: application/json');
        
        try {
            $user = Auth_LoginController::getSessionUser(true);
            
            // Get JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            $blogId = $input['id'] ?? null;
            
            if (!$blogId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Blog ID is required'
                ]);
                return;
            }
            
            $blogModel = new Blog();
            
            // Check if user is the author
            if (!$blogModel->isAuthor($blogId, $user['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You are not authorized to delete this blog'
                ]);
                return;
            }
            
            $success = $blogModel->deleteBlog($blogId);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Blog deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete blog'
                ]);
            }
        } catch (Exception $e) {
            Logger::error("Failed to delete blog: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred'
            ]);
        }
    }

    /**
     * Handle blog creation form submission
     */
    public function createBlog()
    {
        try {
            $user = Auth_LoginController::getSessionUser(true);
            error_log("=== Blog Creation Started ===");
            error_log("User ID: " . $user['id']);
            
            // Validate required fields
            if (empty($_POST['blog_name']) || empty($_POST['description']) || empty($_POST['category'])) {
                error_log("Validation failed: Missing required fields");
                $_SESSION['error'] = 'All fields are required';
                header('Location: /dashboard/community/blogs/create');
                exit;
            }
            
            error_log("Title: " . $_POST['blog_name']);
            error_log("Category: " . $_POST['category']);
            
            // Handle file upload
            $imagePath = null;
            if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                error_log("File upload detected");
                $imagePath = $this->handleImageUpload($_FILES['blog_image'], 'blogs');
                if (!$imagePath) {
                    error_log("⚠️ Image upload failed, but continuing without image");
                    $_SESSION['warning'] = 'Image upload failed, but blog was created without image';
                } else {
                    error_log("✓ Image successfully uploaded: $imagePath");
                }
            } else if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                error_log("File upload error: " . $_FILES['blog_image']['error']);
                $fileErrors = [
                    1 => 'File too large (server limit)',
                    2 => 'File too large (form limit)',
                    3 => 'Partial upload',
                    4 => 'No file selected',
                    6 => 'No temp directory',
                    7 => 'Cannot write to disk',
                    8 => 'Upload blocked'
                ];
                $errMsg = $fileErrors[$_FILES['blog_image']['error']] ?? 'Unknown error';
                $_SESSION['error'] = 'Failed to upload image: ' . $errMsg;
                header('Location: /dashboard/community/blogs/create');
                exit;
            } else {
                error_log("No image file uploaded");
            }
            
            // Process tags
            $tags = [];
            if (!empty($_POST['tags'])) {
                $tags = array_map('trim', explode(',', $_POST['tags']));
                error_log("Tags: " . implode(', ', $tags));
            }
            
            // Create blog
            $blogModel = new Blog();
            $blogData = [
                'author_id' => $user['id'],
                'title' => trim($_POST['blog_name']),
                'content' => trim($_POST['description']),
                'image_path' => $imagePath,
                'category' => $_POST['category'],
                'tags' => $tags,
                'status' => 'published'
            ];
            
            error_log("Creating blog with data...");
            $blogId = $blogModel->createBlog($blogData);
            
            if ($blogId) {
                error_log("✓✓✓ Blog created successfully! ID: $blogId");
                if ($imagePath) {
                    error_log("Image path stored: $imagePath");
                }
                $_SESSION['success'] = 'Blog created successfully!';
                header('Location: /dashboard/community/blogs');
            } else {
                error_log("❌ Blog creation failed");
                $_SESSION['error'] = 'Failed to create blog';
                header('Location: /dashboard/community/blogs/create');
            }
            exit;
            
        } catch (Exception $e) {
            error_log("❌ Exception in createBlog: " . $e->getMessage());
            Logger::error("Failed to create blog: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while creating the blog: ' . $e->getMessage();
            header('Location: /dashboard/community/blogs/create');
            exit;
        }
    }

    /**
     * Handle blog update form submission
     */
    public function updateBlog()
    {
        try {
            $user = Auth_LoginController::getSessionUser(true);
            error_log("=== Blog Update Started ===");
            
            $blogId = $_POST['blog_id'] ?? null;
            if (!$blogId) {
                error_log("❌ No blog ID provided");
                $_SESSION['error'] = 'Blog ID is required';
                header('Location: /dashboard/community/blogs');
                exit;
            }
            
            error_log("Blog ID: $blogId, User ID: " . $user['id']);
            
            $blogModel = new Blog();
            
            // Check if user is the author
            if (!$blogModel->isAuthor($blogId, $user['id'])) {
                error_log("❌ User not authorized to edit blog $blogId");
                $_SESSION['error'] = 'You are not authorized to edit this blog';
                header('Location: /dashboard/community/blogs');
                exit;
            }
            
            // Validate required fields
            if (empty($_POST['blog_name']) || empty($_POST['description']) || empty($_POST['category'])) {
                error_log("❌ Validation failed: Missing required fields");
                $_SESSION['error'] = 'All fields are required';
                header('Location: /dashboard/community/blogs/edit?id=' . $blogId);
                exit;
            }
            
            error_log("Title: " . $_POST['blog_name']);
            
            // Handle file upload (optional for edit)
            $imagePath = $_POST['existing_image'] ?? null;
            error_log("Existing image: " . ($imagePath ?? 'none'));
            
            if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                error_log("New image file uploaded");
                $newImagePath = $this->handleImageUpload($_FILES['blog_image'], 'blogs');
                if ($newImagePath) {
                    error_log("✓ New image uploaded: $newImagePath");
                    // Delete old image if exists
                    if ($imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                        error_log("Deleting old image: $imagePath");
                        unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
                    }
                    $imagePath = $newImagePath;
                } else {
                    error_log("⚠️ New image upload failed, keeping existing");
                }
            }
            
            // Process tags
            $tags = [];
            if (!empty($_POST['tags'])) {
                $tags = array_map('trim', explode(',', $_POST['tags']));
                error_log("Tags: " . implode(', ', $tags));
            }
            
            // Update blog
            $blogData = [
                'title' => trim($_POST['blog_name']),
                'content' => trim($_POST['description']),
                'image_path' => $imagePath,
                'category' => $_POST['category'],
                'tags' => $tags,
                'status' => 'published'
            ];
            
            error_log("Updating blog with image_path: " . ($imagePath ?? 'null'));
            $success = $blogModel->updateBlog($blogId, $blogData);
            
            if ($success) {
                error_log("✓✓✓ Blog updated successfully!");
                $_SESSION['success'] = 'Blog updated successfully!';
                header('Location: /dashboard/community/blogs');
            } else {
                error_log("❌ Blog update failed");
                $_SESSION['error'] = 'Failed to update blog';
                header('Location: /dashboard/community/blogs/edit?id=' . $blogId);
            }
            exit;
            
        } catch (Exception $e) {
            Logger::error("Failed to update blog: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while updating the blog';
            header('Location: /dashboard/community/blogs');
            exit;
        }
    }

    /**
     * Handle image upload for blogs
     */
    private function handleImageUpload($file, $subfolder = 'blogs')
    {
        try {
            error_log("=== Image Upload Debug ===");
            error_log("File name: " . $file['name']);
            error_log("File type: " . $file['type']);
            error_log("File size: " . $file['size'] . " bytes");
            error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
            
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file['type'], $allowedTypes)) {
                error_log("❌ Invalid file type: " . $file['type']);
                return false;
            }
            
            // Check file size (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                error_log("❌ File too large: " . ($file['size'] / 1024 / 1024) . " MB (max 5 MB)");
                return false;
            }
            
            // Create upload directory if it doesn't exist
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/storage/{$subfolder}/";
            error_log("Upload directory: $uploadDir");
            
            if (!is_dir($uploadDir)) {
                error_log("Creating directory...");
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("❌ Failed to create directory");
                    return false;
                }
                error_log("✓ Directory created");
            } else {
                error_log("✓ Directory exists");
            }
            
            // Generate unique filename
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'blog_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            error_log("Generated filename: $filename");
            error_log("Full upload path: $uploadPath");
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $storagePath = "/storage/{$subfolder}/{$filename}";
                error_log("✓ File uploaded successfully");
                error_log("Storage path: $storagePath");
                return $storagePath;
            } else {
                error_log("❌ move_uploaded_file() failed");
                error_log("Temp path exists: " . (file_exists($file['tmp_name']) ? 'yes' : 'no'));
                error_log("Directory writable: " . (is_writable($uploadDir) ? 'yes' : 'no'));
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Exception in handleImageUpload: " . $e->getMessage());
            Logger::error("Image upload failed: " . $e->getMessage());
            return false;
        }
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
    
    /**
     * Show admin request form for club admin access
     */
    public function showAdminRequestForm() {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Check if user is already a club admin
        if ($this->checkIfClubAdmin($user['id'])) {
            header('Location: /dashboard/community/clubs/create');
            exit;
        }
        
        // Check if user already has a pending request
        $adminRequestModel = new AdminRequest();
        $pendingRequest = $adminRequestModel->getPendingRequest($user['id'], 'club_admin');
        
        $data = [
            'user' => $user,
            'hasPendingRequest' => $pendingRequest !== null,
            'pendingRequest' => $pendingRequest
        ];
        
        $this->viewApp('/User/community/admin-request', $data, 'Request Club Admin - ReidHub');
    }
    
    /**
     * Submit admin request for club admin access
     */
    public function submitAdminRequest() {
        header('Content-Type: application/json');
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            // Check if user is already a club admin
            if ($this->checkIfClubAdmin($user['id'])) {
                echo json_encode(['success' => false, 'message' => 'You are already a club admin']);
                exit;
            }
            
            // Check for required fields
            if (empty($_POST['reason'])) {
                echo json_encode(['success' => false, 'message' => 'Please provide a reason for your request']);
                exit;
            }
            
            // Create admin request
            $adminRequestModel = new AdminRequest();
            $requestId = $adminRequestModel->createRequest($user['id'], 'club_admin', $_POST['reason']);
            
            Logger::info("Admin request created: ID $requestId for user " . $user['email']);
            echo json_encode([
                'success' => true,
                'message' => 'Your request has been submitted to system administrators. You will be notified once it is reviewed.',
                'requestId' => $requestId
            ]);
        } catch (Exception $e) {
            Logger::error("Error in submitAdminRequest: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function showCreateClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        // Check if user has club admin permission
        if (!$this->checkIfClubAdmin($user['id'])) {
            Logger::warning("Non-admin user " . $user['email'] . " attempted to create club");
            header('Location: /dashboard/community/request-admin');
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