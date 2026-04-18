<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/LostItem.php';
require_once __DIR__ . '/../../models/FoundItem.php';
require_once __DIR__ . '/../../models/LostAndFoundImage.php';
require_once __DIR__ . '/../../models/User.php';

class LostAndFound_LostAndFoundAdminController extends Controller
{
    /**
     * Show admin dashboard for Lost & Found management
     */
    public function showAdminDashboard()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        
        if (!$admin) {
            header('Location: /login', true, 302);
            exit;
        }
        
        $data = ['user' => $admin, 'admin' => $admin];
        $this->viewApp('/Admin/lost-and-found/manage-lost-and-found-view', $data, 'Lost & Found Management - Admin');
    }

    /**
     * API: Get all lost items with filtering for admin
     */
    public function getAllLostItems()
    {
        try {
            $lostItemModel = new LostItem();
            $imageModel = new LostAndFoundImage();
            
            $filter = $_GET['filter'] ?? 'all';
            
            // Get all lost items
            $items = $lostItemModel->findAll();
            
            // Filter based on status
            if ($filter === 'all') {
                $filtered = $items;
            } else {
                $filtered = array_filter($items, function($item) use ($filter) {
                    switch($filter) {
                        case 'active':
                            return $item['status'] === 'Still Missing';
                        case 'resolved':
                            return $item['status'] === 'Returned';
                        case 'expired':
                            // Items older than 90 days and still missing
                            $daysSinceReport = (time() - strtotime($item['created_at'])) / (60 * 60 * 24);
                            return $daysSinceReport > 90 && $item['status'] === 'Still Missing';
                        default:
                            return true;
                    }
                });
            }
            
            // Add images to each item
            foreach ($filtered as &$item) {
                Logger::info("Fetching images for lost item id={$item['id']}");
                $item['images'] = $imageModel->getImages('lost', $item['id']);
                Logger::info("Lost item {$item['id']} has " . count($item['images']) . " images");
            }
            
            // Count statistics
            $stats = [
                'all' => count($items),
                'active' => count(array_filter($items, fn($i) => $i['status'] === 'Still Missing')),
                'resolved' => count(array_filter($items, fn($i) => $i['status'] === 'Returned')),
                'total' => count($items)
            ];
            
            echo json_encode([
                'success' => true,
                'items' => array_values($filtered),
                'stats' => $stats
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting lost items for admin: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load lost items']);
        }
    }

    /**
     * API: Get all found items with filtering for admin
     */
    public function getAllFoundItems()
    {
        try {
            Logger::info("getAllFoundItems API called with filter: " . ($_GET['filter'] ?? 'all'));
            
            $foundItemModel = new FoundItem();
            $imageModel = new LostAndFoundImage();
            
            $filter = $_GET['filter'] ?? 'all';
            
            // Get all found items
            $items = $foundItemModel->findAll();
            Logger::info("Found " . count($items) . " total found items in database");
            
            // Filter based on status
            if ($filter === 'all') {
                $filtered = $items;
            } else {
                $filtered = array_filter($items, function($item) use ($filter) {
                    switch($filter) {
                        case 'active':
                            return $item['status'] === 'Available';
                        case 'returned':
                            return in_array($item['status'], ['Collected', 'Returned to Owner']);
                        case 'expired':
                            // Items older than 90 days and still available
                            $daysSinceReport = (time() - strtotime($item['created_at'])) / (60 * 60 * 24);
                            return $daysSinceReport > 90 && $item['status'] === 'Available';
                        default:
                            return true;
                    }
                });
            }
            
            Logger::info("After filtering: " . count($filtered) . " items for filter '{$filter}'");
            
            // Add images to each item
            foreach ($filtered as &$item) {
                Logger::info("Fetching images for found item id={$item['id']}");
                $item['images'] = $imageModel->getImages('found', $item['id']);
                Logger::info("Found item {$item['id']} has " . count($item['images']) . " images");
            }
            
            // Count statistics
            $stats = [
                'all' => count($items),
                'active' => count(array_filter($items, fn($i) => $i['status'] === 'Available')),
                'returned' => count(array_filter($items, fn($i) => in_array($i['status'], ['Collected', 'Returned to Owner']))),
                'total' => count($items)
            ];
            
            $response = [
                'success' => true,
                'items' => array_values($filtered),
                'stats' => $stats
            ];
            
            Logger::info("Sending response with " . count($response['items']) . " items");
            
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Throwable $e) {
            Logger::error("Error getting found items for admin: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to load found items', 'error' => $e->getMessage()]);
        }
    }

    /**
     * API: Get all reports with filtering
     */
    public function getAllReports()
    {
        try {
            $lostItemModel = new LostItem();
            $foundItemModel = new FoundItem();
            $imageModel = new LostAndFoundImage();
            
            $filter = $_GET['filter'] ?? 'all';
            $search = $_GET['search'] ?? '';
            $statusFilter = $_GET['status'] ?? '';
            $severityFilter = $_GET['severity'] ?? '';
            $dateFilter = $_GET['date'] ?? '';
            
            // Get all items
            $lostItems = $lostItemModel->findAll();
            $foundItems = $foundItemModel->findAll();
            
            // Combine and tag items
            $allReports = [
                ...array_map(fn($item) => [...$item, 'type' => 'lost'], $lostItems),
                ...array_map(fn($item) => [...$item, 'type' => 'found'], $foundItems)
            ];
            
            // Apply filters
            if ($filter !== 'all') {
                $allReports = array_filter($allReports, function($item) use ($filter) {
                    if ($filter === 'lost') return $item['type'] === 'lost';
                    if ($filter === 'found') return $item['type'] === 'found';
                    if ($filter === 'pending') return isset($item['approval_status']) && $item['approval_status'] === 'pending';
                    if ($filter === 'rejected') return isset($item['approval_status']) && $item['approval_status'] === 'rejected';
                    return true;
                });
            }
            
            // Search filter
            if ($search) {
                $search = strtolower($search);
                $allReports = array_filter($allReports, function($item) use ($search) {
                    return stripos($item['item_name'] ?? '', $search) !== false ||
                           stripos($item['description'] ?? '', $search) !== false ||
                           stripos($item['first_name'] . ' ' . $item['last_name'], $search) !== false ||
                           stripos($item['id'], $search) !== false;
                });
            }
            
            // Status filter
            if ($statusFilter) {
                $allReports = array_filter($allReports, function($item) use ($statusFilter) {
                    $status = strtolower($item['status'] ?? '');
                    return stripos($status, $statusFilter) !== false;
                });
            }
            
            // Severity filter (for lost items)
            if ($severityFilter) {
                $allReports = array_filter($allReports, function($item) use ($severityFilter) {
                    if ($item['type'] !== 'lost') return false;
                    $severity = strtolower($item['severity_level'] ?? '');
                    return stripos($severity, $severityFilter) !== false;
                });
            }
            
            // Date filter
            if ($dateFilter) {
                $now = time();
                $allReports = array_filter($allReports, function($item) use ($dateFilter, $now) {
                    $itemTime = strtotime($item['created_at']);
                    switch($dateFilter) {
                        case 'today':
                            return date('Y-m-d', $itemTime) === date('Y-m-d', $now);
                        case 'week':
                            return ($now - $itemTime) <= (7 * 24 * 60 * 60);
                        case 'month':
                            return ($now - $itemTime) <= (30 * 24 * 60 * 60);
                        default:
                            return true;
                    }
                });
            }
            
            // Add images to each report
            foreach ($allReports as &$report) {
                $report['images'] = $imageModel->getImages($report['type'], $report['id']);
            }
            
            // Sort by date (newest first)
            usort($allReports, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            echo json_encode([
                'success' => true,
                'reports' => array_values($allReports),
                'total' => count($allReports)
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting reports for admin: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load reports']);
        }
    }

    /**
     * API: Get detailed item information with user details
     */
    public function getItemDetails()
    {
        try {
            $itemId = $_GET['id'] ?? null;
            $itemType = $_GET['type'] ?? null;
            
            if (!$itemId || !$itemType) {
                echo json_encode(['success' => false, 'message' => 'Missing item ID or type']);
                return;
            }
            
            $imageModel = new LostAndFoundImage();
            $userModel = new User();
            
            if ($itemType === 'lost') {
                $lostItemModel = new LostItem();
                $item = $lostItemModel->findByIdWithImages($itemId);
            } else {
                $foundItemModel = new FoundItem();
                $item = $foundItemModel->findByIdWithImages($itemId);
            }
            
            if (!$item) {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                return;
            }
            
            // Get user details
            $userId = $item['user_id'];
            $user = $userModel->findById($userId);
            
            if ($user) {
                $item['user_details'] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'reg_no' => $user['reg_no'] ?? null
                ];
            }
            
            echo json_encode([
                'success' => true,
                'item' => $item
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting item details for admin: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load item details']);
        }
    }

    /**
     * API: Update item status (admin can change any status)
     */
    public function updateItemStatus()
    {
        try {
            $itemId = $_POST['item_id'] ?? null;
            $itemType = $_POST['type'] ?? null;
            $newStatus = $_POST['status'] ?? null;
            
            if (!$itemId || !$itemType || !$newStatus) {
                echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                return;
            }
            
            if ($itemType === 'lost') {
                $lostItemModel = new LostItem();
                $item = $lostItemModel->findByIdWithImages($itemId);
                
                if (!$item) {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                    return;
                }
                
                // Admin can update without user_id check
                $success = $lostItemModel->updateStatus($itemId, $item['user_id'], $newStatus);
            } else {
                $foundItemModel = new FoundItem();
                $item = $foundItemModel->findByIdWithImages($itemId);
                
                if (!$item) {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                    return;
                }
                
                $success = $foundItemModel->updateStatus($itemId, $item['user_id'], $newStatus);
            }
            
            if ($success) {
                Logger::info("Admin updated item status: {$itemType} item_id={$itemId} status={$newStatus}");
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            Logger::error("Error updating item status (admin): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
    }

    /**
     * API: Delete/Remove an item (admin action)
     */
    public function deleteItem()
    {
        try {
            $itemId = $_POST['item_id'] ?? null;
            $itemType = $_POST['type'] ?? null;
            
            if (!$itemId || !$itemType) {
                echo json_encode(['success' => false, 'message' => 'Missing item ID or type']);
                return;
            }
            
            if ($itemType === 'lost') {
                $lostItemModel = new LostItem();
                $item = $lostItemModel->findByIdWithImages($itemId);
                
                if (!$item) {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                    return;
                }
                
                $success = $lostItemModel->delete($itemId, $item['user_id']);
            } else {
                $foundItemModel = new FoundItem();
                $item = $foundItemModel->findByIdWithImages($itemId);
                
                if (!$item) {
                    echo json_encode(['success' => false, 'message' => 'Item not found']);
                    return;
                }
                
                $success = $foundItemModel->delete($itemId, $item['user_id']);
            }
            
            if ($success) {
                Logger::info("Admin deleted item: {$itemType} item_id={$itemId}");
                echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
            }
        } catch (Throwable $e) {
            Logger::error("Error deleting item (admin): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
    }

    /**
     * API: Admin creates a new lost or found item report
     */
    public function createReport()
    {
        header('Content-Type: application/json');
        
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Validate required fields
            $required = ['type', 'item_name', 'category', 'description', 'location', 'incident_date', 'email', 'mobile'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    Logger::warning("Admin create report: Missing required field: {$field}");
                    echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
                    return;
                }
            }

            $type = $_POST['type']; // 'lost' or 'found'
            
            if (!in_array($type, ['lost', 'found'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid report type']);
                return;
            }

            // Get or create anonymous user (admin creates on behalf of users)
            // For simplicity, we'll use the admin's user ID or create a special "system" entry
            $userId = $admin['user_id'] ?? 1; // Fallback to user ID 1 or create system user

            // Prepare data
            $itemData = [
                'user_id' => $userId,
                'item_name' => trim($_POST['item_name']),
                'category' => trim($_POST['category']),
                'description' => trim($_POST['description']),
                'mobile' => trim($_POST['mobile']),
                'email' => trim($_POST['email']),
                'alt_contact' => '',
                'special_instructions' => ''
            ];

            if ($type === 'lost') {
                // Create lost item
                $itemData['location'] = trim($_POST['location']);
                $itemData['specific_area'] = '';
                $itemData['date_lost'] = $_POST['incident_date'];
                $itemData['time_lost'] = '';
                $itemData['priority'] = 'medium';
                $itemData['reward_offered'] = 0;
                $itemData['reward_amount'] = null;
                $itemData['reward_details'] = '';

                $lostItemModel = new LostItem();
                $itemId = $lostItemModel->create($itemData);
                
                if (!$itemId) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create lost item']);
                    return;
                }

                Logger::info("Admin created lost item: id={$itemId}");
            } else {
                // Create found item
                $itemData['location'] = trim($_POST['location']);
                $itemData['specific_area'] = '';
                $itemData['date_found'] = $_POST['incident_date'];
                $itemData['time_found'] = '';
                $itemData['condition'] = 'good';
                $itemData['current_location'] = 'Security Office';

                $foundItemModel = new FoundItem();
                $itemId = $foundItemModel->create($itemData);
                
                if (!$itemId) {
                    echo json_encode(['success' => false, 'message' => 'Failed to create found item']);
                    return;
                }

                Logger::info("Admin created found item: id={$itemId}");
            }

            // Handle image upload if provided
            if (!empty($_FILES['image']['name'])) {
                $imageModel = new LostAndFoundImage();
                $file = [
                    'name' => $_FILES['image']['name'],
                    'type' => $_FILES['image']['type'],
                    'tmp_name' => $_FILES['image']['tmp_name'],
                    'error' => $_FILES['image']['error'],
                    'size' => $_FILES['image']['size']
                ];

                $uploadResult = $imageModel->uploadImage($file, $type, $itemId, true);
                
                if ($uploadResult) {
                    $imageModel->addImage(
                        $type,
                        $itemId,
                        $uploadResult['path'],
                        $uploadResult['filename'],
                        true,
                        $uploadResult['size'],
                        $uploadResult['mime_type']
                    );
                    Logger::info("Image uploaded for admin-created {$type} item id={$itemId}");
                }
            }

            echo json_encode([
                'success' => true,
                'message' => ucfirst($type) . ' item created successfully',
                'item_id' => $itemId,
                'type' => $type
            ]);

        } catch (Throwable $e) {
            Logger::error("Error creating report (admin): " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'An error occurred while creating the report']);
        }
    }

    /**
     * Debug endpoint to check images in database
     */
    public function debugImages()
    {
        try {
            $imageModel = new LostAndFoundImage();
            
            // Get all images from database
            $sql = "SELECT id, item_type, item_id, image_path, image_name, is_main, uploaded_at 
                    FROM lostandfound_images 
                    ORDER BY uploaded_at DESC 
                    LIMIT 50";
            $stmt = $imageModel->db->query($sql);
            $allImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Logger::info("Debug images endpoint called - found " . count($allImages) . " images");
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'total_images' => count($allImages),
                'images' => $allImages
            ]);
        } catch (Throwable $e) {
            Logger::error("Error in debugImages: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
