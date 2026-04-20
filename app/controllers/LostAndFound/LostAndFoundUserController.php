<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/LostItem.php';
require_once __DIR__ . '/../../models/FoundItem.php';
require_once __DIR__ . '/../../models/LostAndFoundImage.php';

class LostAndFound_LostAndFoundUserController extends Controller
{
    // ============================================
    // VIEW METHODS
    // ============================================
    
    public function showReportLostItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-lost-item-view', $data, 'Report Lost Item - ReidHub');
    }

    public function showReportFoundItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-found-item-view', $data, 'Report Found Item - ReidHub');
    }

    public function showLostAndFoundItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/lost-and-found-items-view', $data, 'Lost and Found Items - ReidHub');
    }

    public function showMySubmissions()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/my-submissions-view', $data, 'My Submissions - ReidHub');
    }

    // ============================================
    // SUBMISSION METHODS
    // ============================================
    
    public function submitLostItemReport()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            // Validate required fields
            $requiredFields = ['item_name', 'category', 'description', 'location', 'date_lost', 'mobile', 'email', 'priority'];
            if (!$this->validateRequiredFields($requiredFields)) {
                return;
            }

            // Prepare data
            $data = $this->prepareLostItemData($user['id']);

            // Create lost item
            $lostItemModel = new LostItem();
            $itemId = $lostItemModel->create($data);

            if (!$itemId) {
                Logger::error("Failed to create lost item report");
                echo json_encode(['success' => false, 'message' => 'Failed to create report. Please try again.']);
                return;
            }

            // Handle image uploads
            $this->handleImageUploads('lost', $itemId);

            Logger::info("Lost item report submitted successfully by user_id={$user['id']} item_id={$itemId}");
            header('Location: /dashboard/lost-and-found/my-submissions?success=true');
            exit();

        } catch (Throwable $e) {
            Logger::error("Error submitting lost item report: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }

    public function submitFoundItemReport()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            // Validate required fields
            $requiredFields = ['item_name', 'category', 'description', 'location', 'date_found', 'mobile', 'email', 'condition', 'current_location'];
            if (!$this->validateRequiredFields($requiredFields, true)) {
                return;
            }

            // Validate images
            if (empty($_FILES['images']['name'][0])) {
                Logger::warning("No images uploaded");
                $_SESSION['error'] = "At least one photo is required";
                header('Location: /dashboard/lost-and-found/report-found-item');
                exit();
            }

            // Prepare data
            $data = $this->prepareFoundItemData($user['id']);

            // Create found item
            $foundItemModel = new FoundItem();
            $itemId = $foundItemModel->create($data);

            if (!$itemId) {
                Logger::error("Failed to create found item report");
                $_SESSION['error'] = 'Failed to create report. Please try again.';
                header('Location: /dashboard/lost-and-found/report-found-item');
                exit();
            }

            // Handle image uploads
            $this->handleImageUploads('found', $itemId);

            Logger::info("Found item report submitted successfully by user_id={$user['id']} item_id={$itemId}");
            $_SESSION['success'] = 'Found item report submitted successfully!';
            header('Location: /dashboard/lost-and-found/my-submissions');
            exit();

        } catch (Throwable $e) {
            Logger::error("Error submitting found item report: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'An error occurred while submitting your report. Please try again.';
            header('Location: /dashboard/lost-and-found/report-found-item');
            exit();
        }
    }

    // ============================================
    // API METHODS
    // ============================================
    
    /**
     * Get all lost and found items with filtering
     */
    public function getAllItems()
    {
        try {
            Logger::info("getAllItems API called");
            
            // Fetch items from database
            $lostItemModel = new LostItem();
            $foundItemModel = new FoundItem();
            $lostItems = $lostItemModel->findAll();
            $foundItems = $foundItemModel->findAll();
            
            Logger::info("Fetched from DB: " . count($lostItems) . " lost items, " . count($foundItems) . " found items");

            // Apply filters
            $lostItems = $this->applyFilters($lostItems, 'lost');
            $foundItems = $this->applyFilters($foundItems, 'found');

            // Attach images
            $lostItems = $this->attachImagesToItems($lostItems, 'lost');
            $foundItems = $this->attachImagesToItems($foundItems, 'found');

            Logger::info("After filtering: " . count($lostItems) . " lost items, " . count($foundItems) . " found items");

            $this->sendJsonResponse(true, [
                'lostItems' => $lostItems,
                'foundItems' => $foundItems,
                'totalLost' => count($lostItems),
                'totalFound' => count($foundItems)
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting all items: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            $this->sendJsonResponse(false, ['message' => 'Failed to load items', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get user's own lost and found items
     */
    public function getMyItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            Logger::info("getMyItems API called for user_id={$user['id']}");
            
            // Fetch items from database
            $lostItemModel = new LostItem();
            $foundItemModel = new FoundItem();
            $myLostItems = $lostItemModel->findByUserId($user['id']);
            $myFoundItems = $foundItemModel->findByUserId($user['id']);
            
            Logger::info("User {$user['id']} has " . count($myLostItems) . " lost items and " . count($myFoundItems) . " found items");

            // Attach images
            $myLostItems = $this->attachImagesToItems($myLostItems, 'lost');
            $myFoundItems = $this->attachImagesToItems($myFoundItems, 'found');

            $this->sendJsonResponse(true, [
                'lostItems' => $myLostItems,
                'foundItems' => $myFoundItems
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting user items: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            $this->sendJsonResponse(false, ['message' => 'Failed to load your items', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get item details by ID
     */
    public function getItemDetails()
    {
        try {
            $itemType = $_GET['type'] ?? null;
            $itemId = $_GET['id'] ?? null;

            if (!$itemType || !$itemId) {
                $this->sendJsonResponse(false, ['message' => 'Missing item type or ID']);
                return;
            }

            if ($itemType === 'lost') {
                $lostItemModel = new LostItem();
                $item = $lostItemModel->findByIdWithImages($itemId);
            } else {
                $foundItemModel = new FoundItem();
                $item = $foundItemModel->findByIdWithImages($itemId);
            }

            if ($item) {
                $this->sendJsonResponse(true, ['item' => $item]);
            } else {
                $this->sendJsonResponse(false, ['message' => 'Item not found']);
            }
        } catch (Throwable $e) {
            Logger::error("Error getting item details: " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'Failed to load item details']);
        }
    }

    /**
     * Update lost item status
     */
    public function updateLostItemStatus()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $itemId = $_POST['item_id'] ?? null;
            $newStatus = $_POST['status'] ?? null;

            if (!$itemId || !$newStatus) {
                $this->sendJsonResponse(false, ['message' => 'Missing item ID or status']);
                return;
            }

            // Validate status
            $allowedStatuses = ['Still Missing', 'Returned'];
            if (!in_array($newStatus, $allowedStatuses)) {
                $this->sendJsonResponse(false, ['message' => 'Invalid status']);
                return;
            }

            $lostItemModel = new LostItem();
            
            // Verify ownership
            $item = $lostItemModel->findByIdWithImages($itemId);
            if (!$item || $item['user_id'] != $user['id']) {
                $this->sendJsonResponse(false, ['message' => 'Unauthorized']);
                return;
            }

            // Update status
            $success = $lostItemModel->updateStatus($itemId, $user['id'], $newStatus);

            if ($success) {
                Logger::info("Lost item status updated: item_id={$itemId} status={$newStatus} by user_id={$user['id']}");
                $this->sendJsonResponse(true, ['message' => 'Status updated successfully']);
            } else {
                $this->sendJsonResponse(false, ['message' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            Logger::error("Error updating item status: " . $e->getMessage());
            $this->sendJsonResponse(false, ['message' => 'An error occurred']);
        }
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    
    /**
     * Validate required fields from $_POST
     */
    private function validateRequiredFields(array $fields, bool $useSession = false): bool
    {
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                Logger::warning("Missing required field: {$field}");
                
                if ($useSession) {
                    $_SESSION['error'] = "Missing required field: {$field}";
                    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/dashboard/lost-and-found');
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Prepare lost item data from $_POST
     */
    private function prepareLostItemData(int $userId): array
    {
        return [
            'user_id' => $userId,
            'item_name' => trim($_POST['item_name']),
            'category' => trim($_POST['category']),
            'description' => trim($_POST['description']),
            'location' => trim($_POST['location']),
            'specific_area' => trim($_POST['specific_area'] ?? ''),
            'date_lost' => $_POST['date_lost'],
            'time_lost' => $_POST['time_lost'] ?? '',
            'mobile' => trim($_POST['mobile']),
            'email' => trim($_POST['email']),
            'alt_contact' => trim($_POST['alt_contact'] ?? ''),
            'priority' => $_POST['priority'],
            'reward_offered' => !empty($_POST['reward_offered']) ? 1 : 0,
            'reward_amount' => !empty($_POST['reward_amount']) ? floatval($_POST['reward_amount']) : null,
            'reward_details' => trim($_POST['reward_details'] ?? ''),
            'special_instructions' => trim($_POST['special_instructions'] ?? '')
        ];
    }

    /**
     * Prepare found item data from $_POST
     */
    private function prepareFoundItemData(int $userId): array
    {
        // Handle current location - if "other" is selected, use the other_location value
        $currentLocation = trim($_POST['current_location']);
        if ($currentLocation === 'other') {
            if (empty($_POST['other_location'])) {
                Logger::warning("Other location not specified");
                $_SESSION['error'] = "Please specify where the item is currently located";
                header('Location: /dashboard/lost-and-found/report-found-item');
                exit();
            }
            $currentLocation = trim($_POST['other_location']);
        }

        return [
            'user_id' => $userId,
            'item_name' => trim($_POST['item_name']),
            'category' => trim($_POST['category']),
            'description' => trim($_POST['description']),
            'location' => trim($_POST['location']),
            'specific_area' => trim($_POST['specific_area'] ?? ''),
            'date_found' => $_POST['date_found'],
            'time_found' => $_POST['time_found'] ?? '',
            'mobile' => trim($_POST['mobile']),
            'email' => trim($_POST['email']),
            'alt_contact' => trim($_POST['alt_contact'] ?? ''),
            'condition' => $_POST['condition'],
            'current_location' => $currentLocation,
            'special_instructions' => trim($_POST['special_instructions'] ?? '')
        ];
    }

    /**
     * Handle image uploads for an item
     */
    private function handleImageUploads(string $itemType, int $itemId): int
    {
        if (empty($_FILES['images']['name'][0])) {
            return 0;
        }

        $imageModel = new LostAndFoundImage();
        $uploadedCount = 0;

        foreach ($_FILES['images']['name'] as $index => $name) {
            if (empty($name)) continue;

            $file = [
                'name' => $_FILES['images']['name'][$index],
                'type' => $_FILES['images']['type'][$index],
                'tmp_name' => $_FILES['images']['tmp_name'][$index],
                'error' => $_FILES['images']['error'][$index],
                'size' => $_FILES['images']['size'][$index]
            ];

            $uploadResult = $imageModel->uploadImage($file, $itemType, $itemId, $index === 0);
            
            if ($uploadResult) {
                $imageId = $imageModel->addImage(
                    $itemType,
                    $itemId,
                    $uploadResult['path'],
                    $uploadResult['filename'],
                    $index === 0,
                    $uploadResult['size'],
                    $uploadResult['mime_type']
                );

                if ($imageId) {
                    $uploadedCount++;
                    Logger::info("Image uploaded successfully: {$uploadResult['filename']}");
                }
            } else {
                Logger::warning("Failed to upload image at index {$index}");
            }
        }

        Logger::info("Uploaded {$uploadedCount} images for {$itemType} item id={$itemId}");
        return $uploadedCount;
    }

    /**
     * Attach images to items array
     */
    private function attachImagesToItems(array $items, string $itemType): array
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
     * Apply filters to items array
     */
    private function applyFilters(array $items, string $itemType): array
    {
        $categoryFilter = $_GET['category'] ?? null;
        $locationFilter = $_GET['location'] ?? null;
        $searchTerm = $_GET['search'] ?? null;
        $severityFilter = $_GET['severity'] ?? null;

        // Apply category filter
        if ($categoryFilter) {
            $items = array_filter($items, function($item) use ($categoryFilter) {
                return ($item['category'] ?? '') === $categoryFilter;
            });
        }

        // Apply location filter
        if ($locationFilter) {
            $locationKey = $itemType === 'lost' ? 'last_known_location' : 'found_location';
            $items = array_filter($items, function($item) use ($locationFilter, $locationKey) {
                return ($item[$locationKey] ?? '') === $locationFilter;
            });
        }

        // Apply severity filter (lost items only)
        if ($severityFilter && $itemType === 'lost') {
            $items = array_filter($items, function($item) use ($severityFilter) {
                return ($item['severity_level'] ?? '') === $severityFilter;
            });
        }

        // Apply search term
        if ($searchTerm) {
            $search = strtolower($searchTerm);
            $items = array_filter($items, function($item) use ($search) {
                return stripos($item['item_name'] ?? '', $search) !== false ||
                       stripos($item['description'] ?? '', $search) !== false ||
                       stripos(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''), $search) !== false;
            });
        }

        return array_values($items);
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