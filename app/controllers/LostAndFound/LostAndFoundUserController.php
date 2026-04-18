<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/LostItem.php';
require_once __DIR__ . '/../../models/FoundItem.php';
require_once __DIR__ . '/../../models/LostAndFoundImage.php';
require_once __DIR__ . '/../../models/LostAndFoundNotification.php';
require_once __DIR__ . '/../../helpers/EmailService.php';

class LostAndFound_LostAndFoundUserController extends Controller
{
    public function showReportLostItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-lost-item-view', $data, 'Report Lost Item - ReidHub');
    }

    public function submitLostItemReport()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            // Validate required fields
            $required = ['item_name', 'category', 'description', 'location', 'date_lost', 'mobile', 'email', 'priority'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    Logger::warning("Missing required field: {$field}");
                    echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
                    return;
                }
            }

            // Prepare data for model
            $data = [
                'user_id' => $user['id'],
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

            // Create lost item
            $lostItemModel = new LostItem();
            $itemId = $lostItemModel->create($data);

            if (!$itemId) {
                Logger::error("Failed to create lost item report");
                echo json_encode(['success' => false, 'message' => 'Failed to create report. Please try again.']);
                return;
            }

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
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

                    // Upload file
                    $uploadResult = $imageModel->uploadImage($file, 'lost', $itemId, $index === 0);
                    
                    if ($uploadResult) {
                        // Save to database
                        $imageId = $imageModel->addImage(
                            'lost',
                            $itemId,
                            $uploadResult['path'],
                            $uploadResult['filename'],
                            $index === 0, // First image is main
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

                Logger::info("Uploaded {$uploadedCount} images for lost item id={$itemId}");
            }

            // Send notifications
            $notificationModel = new LostAndFoundNotification();
            
            // Broadcast to all users
            $itemName = $_POST['item_name'];
            $location = $_POST['location'];
            $priority = $_POST['priority'];
            $reporterName = $user['first_name'] . ' ' . $user['last_name'];
            
            $broadcastMsg = "Lost Item Alert: {$itemName} was lost at {$location}. Priority: {$priority}";
            $notificationModel->broadcastToAllUsers('lost', $itemId, $broadcastMsg);
            
            // Send NOC alert for Critical items
            if ($priority === 'high') { // high priority maps to Critical severity
                $nocMsg = "CRITICAL: {$itemName} lost at {$location}. Reporter: {$reporterName}";
                $notificationModel->sendNOCAlert($itemId, $nocMsg);
                
                // Send email to NOC
                EmailService::sendNOCAlert(
                    $itemId,
                    $itemName,
                    $_POST['category'],
                    'Critical',
                    $location,
                    $reporterName
                );
            }

            Logger::info("Lost item report submitted successfully by user_id={$user['id']} item_id={$itemId}");
            
            // Redirect to my submissions page
            header('Location: /dashboard/lost-and-found/my-submissions?success=true');
            exit();

        } catch (Throwable $e) {
            Logger::error("Error submitting lost item report: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        }
    }

    public function showReportFoundItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-found-item-view', $data, 'Report Found Item - ReidHub');
    }

    public function submitFoundItemReport()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            // Validate required fields
            $required = ['item_name', 'category', 'description', 'location', 'date_found', 'mobile', 'email', 'condition', 'current_location'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    Logger::warning("Missing required field: {$field}");
                    $_SESSION['error'] = "Missing required field: {$field}";
                    header('Location: /dashboard/lost-and-found/report-found-item');
                    exit();
                }
            }

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

            // Validate at least one image
            if (empty($_FILES['images']['name'][0])) {
                Logger::warning("No images uploaded");
                $_SESSION['error'] = "At least one photo is required";
                header('Location: /dashboard/lost-and-found/report-found-item');
                exit();
            }

            // Prepare data for model
            $data = [
                'user_id' => $user['id'],
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
            if (!empty($_FILES['images']['name'][0])) {
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

                    // Upload file
                    $uploadResult = $imageModel->uploadImage($file, 'found', $itemId, $index === 0);
                    
                    if ($uploadResult) {
                        // Save to database
                        $imageId = $imageModel->addImage(
                            'found',
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

                Logger::info("Uploaded {$uploadedCount} images for found item id={$itemId}");
            }

            // Send notifications
            $notificationModel = new LostAndFoundNotification();
            
            // Broadcast to all users
            $itemName = $_POST['item_name'];
            $location = $_POST['location'];
            $condition = $_POST['condition'];
            $reporterName = $user['first_name'] . ' ' . $user['last_name'];
            
            $broadcastMsg = "Found Item: {$itemName} was found at {$location}. Condition: {$condition}";
            $notificationModel->broadcastToAllUsers('found', $itemId, $broadcastMsg);
            
            // Send Students' Union alert for ALL found items
            $unionMsg = "Found Item Report: {$itemName} found at {$location}. Reporter: {$reporterName}";
            $notificationModel->sendUnionAlert($itemId, $unionMsg);
            
            // Send email to Students' Union
            EmailService::sendUnionAlert(
                $itemId,
                $itemName,
                $_POST['category'],
                ucfirst($condition),
                $location,
                $reporterName
            );

            Logger::info("Found item report submitted successfully by user_id={$user['id']} item_id={$itemId}");
            
            // Set success message
            $_SESSION['success'] = 'Found item report submitted successfully!';
            
            // Redirect to my submissions page
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

    /**
     * API endpoint to get all lost and found items with filtering
     */
    public function getAllItems()
    {
        try {
            Logger::info("getAllItems API called");
            
            $lostItemModel = new LostItem();
            $foundItemModel = new FoundItem();
            $imageModel = new LostAndFoundImage();

            // Get all items first
            $lostItems = $lostItemModel->findAll();
            $foundItems = $foundItemModel->findAll();
            
            Logger::info("Fetched from DB: " . count($lostItems) . " lost items, " . count($foundItems) . " found items");

            // Get filter parameters
            $categoryFilter = $_GET['category'] ?? null;
            $locationFilter = $_GET['location'] ?? null;
            $searchTerm = $_GET['search'] ?? null;
            $severityFilter = $_GET['severity'] ?? null;

            // Apply client-side filtering if needed
            if ($categoryFilter) {
                $lostItems = array_filter($lostItems, function($item) use ($categoryFilter) {
                    return ($item['category'] ?? '') === $categoryFilter;
                });
                $foundItems = array_filter($foundItems, function($item) use ($categoryFilter) {
                    return ($item['category'] ?? '') === $categoryFilter;
                });
            }

            if ($locationFilter) {
                $lostItems = array_filter($lostItems, function($item) use ($locationFilter) {
                    return ($item['last_known_location'] ?? '') === $locationFilter;
                });
                $foundItems = array_filter($foundItems, function($item) use ($locationFilter) {
                    return ($item['found_location'] ?? '') === $locationFilter;
                });
            }

            if ($severityFilter) {
                $lostItems = array_filter($lostItems, function($item) use ($severityFilter) {
                    return ($item['severity_level'] ?? '') === $severityFilter;
                });
            }

            if ($searchTerm) {
                $search = strtolower($searchTerm);
                $lostItems = array_filter($lostItems, function($item) use ($search) {
                    return stripos($item['item_name'] ?? '', $search) !== false ||
                           stripos($item['description'] ?? '', $search) !== false ||
                           stripos(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''), $search) !== false;
                });
                $foundItems = array_filter($foundItems, function($item) use ($search) {
                    return stripos($item['item_name'] ?? '', $search) !== false ||
                           stripos($item['description'] ?? '', $search) !== false ||
                           stripos(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''), $search) !== false;
                });
            }

            // Reindex arrays after filtering
            $lostItems = array_values($lostItems);
            $foundItems = array_values($foundItems);

            // Add images to each lost item
            foreach ($lostItems as &$item) {
                Logger::info("Fetching images for lost item id={$item['id']}");
                $item['images'] = $imageModel->getImages('lost', $item['id']);
                Logger::info("Lost item {$item['id']} has " . count($item['images']) . " images");
            }

            // Add images to each found item
            foreach ($foundItems as &$item) {
                Logger::info("Fetching images for found item id={$item['id']}");
                $item['images'] = $imageModel->getImages('found', $item['id']);
                Logger::info("Found item {$item['id']} has " . count($item['images']) . " images");
            }

            Logger::info("After filtering: " . count($lostItems) . " lost items, " . count($foundItems) . " found items");

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'lostItems' => $lostItems,
                'foundItems' => $foundItems,
                'totalLost' => count($lostItems),
                'totalFound' => count($foundItems)
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting all items: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to load items', 'error' => $e->getMessage()]);
        }
    }

    /**
     * API endpoint to get user's own lost and found items
     */
    public function getMyItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            Logger::info("getMyItems API called for user_id={$user['id']}");
            
            $lostItemModel = new LostItem();
            $foundItemModel = new FoundItem();
            $imageModel = new LostAndFoundImage();

            $myLostItems = $lostItemModel->findByUserId($user['id']);
            $myFoundItems = $foundItemModel->findByUserId($user['id']);
            
            Logger::info("User {$user['id']} has " . count($myLostItems) . " lost items and " . count($myFoundItems) . " found items");

            // Add images to each lost item
            foreach ($myLostItems as &$item) {
                Logger::info("Fetching images for user lost item id={$item['id']}");
                $item['images'] = $imageModel->getImages('lost', $item['id']);
                Logger::info("User lost item {$item['id']} has " . count($item['images']) . " images");
            }

            // Add images to each found item
            foreach ($myFoundItems as &$item) {
                Logger::info("Fetching images for user found item id={$item['id']}");
                $item['images'] = $imageModel->getImages('found', $item['id']);
                Logger::info("User found item {$item['id']} has " . count($item['images']) . " images");
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'lostItems' => $myLostItems,
                'foundItems' => $myFoundItems
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting user items: " . $e->getMessage());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to load your items', 'error' => $e->getMessage()]);
        }
    }

    /**
     * API endpoint to get item details by ID
     */
    public function getItemDetails()
    {
        try {
            $itemType = $_GET['type'] ?? null; // 'lost' or 'found'
            $itemId = $_GET['id'] ?? null;

            if (!$itemType || !$itemId) {
                echo json_encode(['success' => false, 'message' => 'Missing item type or ID']);
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
                echo json_encode(['success' => true, 'item' => $item]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
            }
        } catch (Throwable $e) {
            Logger::error("Error getting item details: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load item details']);
        }
    }

    /**
     * Update status of lost item (user can mark as Returned or Still Missing)
     */
    public function updateLostItemStatus()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $itemId = $_POST['item_id'] ?? null;
            $newStatus = $_POST['status'] ?? null;

            if (!$itemId || !$newStatus) {
                echo json_encode(['success' => false, 'message' => 'Missing item ID or status']);
                return;
            }

            // Validate status - must match database ENUM values
            $allowedStatuses = ['Still Missing', 'Returned'];
            if (!in_array($newStatus, $allowedStatuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }

            $lostItemModel = new LostItem();
            
            // Verify ownership
            $item = $lostItemModel->findByIdWithImages($itemId);
            if (!$item || $item['user_id'] != $user['id']) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Update status
            $success = $lostItemModel->updateStatus($itemId, $user['id'], $newStatus);

            if ($success) {
                Logger::info("Lost item status updated: item_id={$itemId} status={$newStatus} by user_id={$user['id']}");
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } catch (Throwable $e) {
            Logger::error("Error updating item status: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
    }

    /**
     * Get recent notifications for user
     */
    public function getNotifications()
    {
        $user = Auth_LoginController::getSessionUser(true);
        
        try {
            $notificationModel = new LostAndFoundNotification();
            $notifications = $notificationModel->getUserNotifications($user['id'], 50);

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'count' => count($notifications)
            ]);
        } catch (Throwable $e) {
            Logger::error("Error getting notifications: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load notifications']);
        }
    }
}