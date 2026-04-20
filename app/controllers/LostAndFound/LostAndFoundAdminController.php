<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/LostItem.php';
require_once __DIR__ . '/../../models/FoundItem.php';
require_once __DIR__ . '/../../models/LostAndFoundImage.php';
require_once __DIR__ . '/../../models/User.php';

class LostAndFound_LostAndFoundAdminController extends Controller
{
    // ============================================
    // VIEW METHODS
    // ============================================
    
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

    // ============================================
    // API METHODS - GET ITEMS
    // ============================================
    
    /**
     * Get all lost items with filtering
     */
    public function getAllLostItems()
    {
        try {
            $lostItemModel = new LostItem();
            $filter = $_GET['filter'] ?? 'all';
            
            // Get all lost items
            $items = $lostItemModel->findAll();
            
            // Apply filter
            $filtered = $this->filterByStatus($items, $filter, [
                'active' => 'Still Missing',
                'resolved' => 'Returned'
            ]);
            
            // Attach images
            $filtered = $this->attachImages($filtered, 'lost');
            
            // Calculate statistics
            $stats = [
                'all' => count($items),
                'active' => $this->countByStatus($items, 'Still Missing'),
                'resolved' => $this->countByStatus($items, 'Returned'),
                'total' => count($items)
            ];
            
            $this->sendJsonResponse(true, [
                'items' => array_values($filtered),
                'stats' => $stats
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting lost items for admin: " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'Failed to load lost items']);
        }
    }

    /**
     * Get all found items with filtering
     */
    public function getAllFoundItems()
    {
        try {
            Logger::info("getAllFoundItems API called with filter: " . ($_GET['filter'] ?? 'all'));
            
            $foundItemModel = new FoundItem();
            $filter = $_GET['filter'] ?? 'all';
            
            // Get all found items
            $items = $foundItemModel->findAll();
            Logger::info("Found " . count($items) . " total found items in database");
            
            // Apply filter
            $filtered = $this->filterByStatus($items, $filter, [
                'active' => 'Available',
                'returned' => ['Collected', 'Returned to Owner']
            ]);
            
            Logger::info("After filtering: " . count($filtered) . " items for filter '{$filter}'");
            
            // Attach images
            $filtered = $this->attachImages($filtered, 'found');
            
            // Calculate statistics
            $stats = [
                'all' => count($items),
                'active' => $this->countByStatus($items, 'Available'),
                'returned' => $this->countByStatus($items, ['Collected', 'Returned to Owner']),
                'total' => count($items)
            ];
            
            Logger::info("Sending response with " . count($filtered) . " items");
            
            $this->sendJsonResponse(true, [
                'items' => array_values($filtered),
                'stats' => $stats
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting found items for admin: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            $this->sendJsonResponse(false, ['message' => 'Failed to load found items', 'error' => $e->getMessage()]);
        }
    }

    // ============================================
    // API METHODS - ITEM OPERATIONS
    // ============================================
    
    /**
     * Get detailed item information with user details
     */
    public function getItemDetails()
    {
        try {
            $itemId = $_GET['id'] ?? null;
            $itemType = $_GET['type'] ?? null;
            
            if (!$itemId || !$itemType) {
                $this->sendJsonResponse(false, ['message' => 'Missing item ID or type']);
                return;
            }
            
            // Get item
            $item = $this->getItemById($itemId, $itemType);
            
            if (!$item) {
                $this->sendJsonResponse(false, ['message' => 'Item not found']);
                return;
            }
            
            // Attach user details
            $userModel = new User();
            $user = $userModel->findById($item['user_id']);
            
            if ($user) {
                $item['user_details'] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'reg_no' => $user['reg_no'] ?? null
                ];
            }
            
            $this->sendJsonResponse(true, ['item' => $item]);
        } catch (Throwable $e) {
            Logger::error("Error getting item details for admin: " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'Failed to load item details']);
        }
    }

    /**
     * Update item status
     */
    public function updateItemStatus()
    {
        try {
            $itemId = $_POST['item_id'] ?? null;
            $itemType = $_POST['type'] ?? null;
            $newStatus = $_POST['status'] ?? null;
            
            if (!$itemId || !$itemType || !$newStatus) {
                $this->sendJsonResponse(false, ['message' => 'Missing required parameters']);
                return;
            }
            
            // Get item
            $item = $this->getItemById($itemId, $itemType);
            
            if (!$item) {
                $this->sendJsonResponse(false, ['message' => 'Item not found']);
                return;
            }
            
            // Update status
            $success = $this->updateItemStatusById($itemId, $itemType, $item['user_id'], $newStatus);
            
            if ($success) {
                Logger::info("Admin updated item status: {$itemType} item_id={$itemId} status={$newStatus}");
                $this->sendJsonResponse(true, ['message' => 'Status updated successfully']);
            } else {
                $this->sendJsonResponse(false, ['message' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            Logger::error("Error updating item status (admin): " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'An error occurred']);
        }
    }

    /**
     * Delete an item
     */
    public function deleteItem()
    {
        try {
            $itemId = $_POST['item_id'] ?? null;
            $itemType = $_POST['type'] ?? null;
            
            if (!$itemId || !$itemType) {
                $this->sendJsonResponse(false, ['message' => 'Missing item ID or type']);
                return;
            }
            
            // Get item
            $item = $this->getItemById($itemId, $itemType);
            
            if (!$item) {
                $this->sendJsonResponse(false, ['message' => 'Item not found']);
                return;
            }
            
            // Delete item
            $success = $this->deleteItemById($itemId, $itemType, $item['user_id']);
            
            if ($success) {
                Logger::info("Admin deleted item: {$itemType} item_id={$itemId}");
                $this->sendJsonResponse(true, ['message' => 'Item deleted successfully']);
            } else {
                $this->sendJsonResponse(false, ['message' => 'Failed to delete item']);
            }
        } catch (Throwable $e) {
            Logger::error("Error deleting item (admin): " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'An error occurred']);
        }
    }

    /**
     * Create a new report (admin)
     */
    public function createReport()
    {
        try {
            $admin = Auth_LoginController::getSessionAdmin(true);
            if (!$admin) {
                $this->sendJsonResponse(false, ['message' => 'Unauthorized']);
                return;
            }

            // Validate required fields
            $required = ['type', 'item_name', 'category', 'description', 'location', 'incident_date', 'email', 'mobile'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    Logger::warning("Admin create report: Missing required field: {$field}");
                    $this->sendJsonResponse(false, ['message' => "Missing required field: {$field}"]);
                    return;
                }
            }

            $type = $_POST['type'];
            
            if (!in_array($type, ['lost', 'found'])) {
                $this->sendJsonResponse(false, ['message' => 'Invalid report type']);
                return;
            }

            // Create item
            $userId = $admin['user_id'] ?? 1;
            $itemData = $this->prepareAdminReportData($userId, $type);
            $itemId = $this->createItemByType($type, $itemData);
            
            if (!$itemId) {
                $this->sendJsonResponse(false, ['message' => "Failed to create {$type} item"]);
                return;
            }

            Logger::info("Admin created {$type} item: id={$itemId}");

            // Handle image upload
            $this->handleAdminImageUpload($type, $itemId);

            $this->sendJsonResponse(true, [
                'message' => ucfirst($type) . ' item created successfully',
                'item_id' => $itemId,
                'type' => $type
            ]);

        } catch (Throwable $e) {
            Logger::error("Error creating report (admin): " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            $this->sendJsonResponse(false, ['message' => 'An error occurred while creating the report']);
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
            
            $this->sendJsonResponse(true, [
                'total_images' => count($allImages),
                'images' => $allImages
            ]);
        } catch (Throwable $e) {
            Logger::error("Error in debugImages: " . $e->getMessage());
            $this->sendJsonResponse(false, ['error' => $e->getMessage()]);
        }
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    
    /**
     * Get item by ID and type
     */
    private function getItemById(int $itemId, string $itemType): ?array
    {
        if ($itemType === 'lost') {
            $lostItemModel = new LostItem();
            return $lostItemModel->findByIdWithImages($itemId);
        } else {
            $foundItemModel = new FoundItem();
            return $foundItemModel->findByIdWithImages($itemId);
        }
    }

    /**
     * Update item status by ID
     */
    private function updateItemStatusById(int $itemId, string $itemType, int $userId, string $newStatus): bool
    {
        if ($itemType === 'lost') {
            $lostItemModel = new LostItem();
            return $lostItemModel->updateStatus($itemId, $userId, $newStatus);
        } else {
            $foundItemModel = new FoundItem();
            return $foundItemModel->updateStatus($itemId, $userId, $newStatus);
        }
    }

    /**
     * Delete item by ID
     */
    private function deleteItemById(int $itemId, string $itemType, int $userId): bool
    {
        if ($itemType === 'lost') {
            $lostItemModel = new LostItem();
            return $lostItemModel->delete($itemId, $userId);
        } else {
            $foundItemModel = new FoundItem();
            return $foundItemModel->delete($itemId, $userId);
        }
    }

    /**
     * Prepare data for admin report creation
     */
    private function prepareAdminReportData(int $userId, string $type): array
    {
        $data = [
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
            $data['location'] = trim($_POST['location']);
            $data['specific_area'] = '';
            $data['date_lost'] = $_POST['incident_date'];
            $data['time_lost'] = '';
            $data['priority'] = 'medium';
            $data['reward_offered'] = 0;
            $data['reward_amount'] = null;
            $data['reward_details'] = '';
        } else {
            $data['location'] = trim($_POST['location']);
            $data['specific_area'] = '';
            $data['date_found'] = $_POST['incident_date'];
            $data['time_found'] = '';
            $data['condition'] = 'good';
            $data['current_location'] = 'Security Office';
        }

        return $data;
    }

    /**
     * Create item by type
     */
    private function createItemByType(string $type, array $data)
    {
        if ($type === 'lost') {
            $lostItemModel = new LostItem();
            return $lostItemModel->create($data);
        } else {
            $foundItemModel = new FoundItem();
            return $foundItemModel->create($data);
        }
    }

    /**
     * Handle admin image upload
     */
    private function handleAdminImageUpload(string $type, int $itemId): void
    {
        if (empty($_FILES['image']['name'])) {
            return;
        }

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
    
    /**
     * Filter items by status
     */
    private function filterByStatus(array $items, string $filter, array $statusMap): array
    {
        if ($filter === 'all') {
            return $items;
        }
        
        return array_filter($items, function($item) use ($filter, $statusMap) {
            if (!isset($statusMap[$filter])) {
                return true;
            }
            
            $targetStatus = $statusMap[$filter];
            $itemStatus = $item['status'] ?? '';
            
            // Handle array of statuses
            if (is_array($targetStatus)) {
                return in_array($itemStatus, $targetStatus);
            }
            
            return $itemStatus === $targetStatus;
        });
    }

    /**
     * Count items by status
     */
    private function countByStatus(array $items, $status): int
    {
        // Handle array of statuses
        if (is_array($status)) {
            return count(array_filter($items, fn($i) => in_array($i['status'] ?? '', $status)));
        }
        
        return count(array_filter($items, fn($i) => ($i['status'] ?? '') === $status));
    }

    /**
     * Attach images to items
     */
    private function attachImages(array $items, string $itemType): array
    {
        $imageModel = new LostAndFoundImage();
        
        foreach ($items as &$item) {
            Logger::info("Fetching images for {$itemType} item id={$item['id']}");
            $item['images'] = $imageModel->getImages($itemType, $item['id']);
            Logger::info("{$itemType} item {$item['id']} has " . count($item['images']) . " images");
        }
        
        return $items;
    }

    /**
     * Send JSON response
     */
    private function sendJsonResponse(bool $success, array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success], $data));
    }
}
