<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Marketplace_MarketplaceUserController extends Controller
{
    public function showMerchStore()
    {
        $user = Auth_LoginController::getSessionUser(true);

        // Fetch items for both tabs
        $mp = new MarketPlace();

        // Tab 1: Merchandise (brand new category)
        $merchRows = $mp->findPublicByCategory('merchandise'); // or pass condition 'brand_new' if you want
        $merchandiseItems = array_map(function ($r) {
            $imgs = json_decode($r['images'] ?? '[]', true);
            $firstImage = (is_array($imgs) && !empty($imgs)) ? $imgs[0] : null;

            // Stock badge mapping for merchandise
            $qty = (int)$r['stock_quantity'];
            if ($qty <= 0) {
                $badgeText = 'Sold';
                $badgeClass = 'stock-badge--out-of-stock';
            } elseif ($qty <= 3) {
                $badgeText = 'Low Stock';
                $badgeClass = 'stock-badge--low-stock';
            } else {
                $badgeText = 'In Stock';
                $badgeClass = 'stock-badge--in-stock';
            }

            return [
                'id' => (int)$r['id'],
                'title' => $r['title'],
                'price' => (float)$r['price'],
                'condition' => $r['condition_type'], // 'brand_new' | 'used'
                'image' => $firstImage,              // '/storage/marketplace/..' or null
                'stock_quantity' => $qty,
                'stock_badge_text' => $badgeText,
                'stock_badge_class' => $badgeClass,
                'product_type' => $r['product_type'] ?? null,
            ];
        }, $merchRows);

        // Tab 2: Second Hand Items
        $secondRows = $mp->findPublicByCategory('second-hand'); // or pass condition 'used' if desired
        $secondHandItems = array_map(function ($r) {
            $imgs = json_decode($r['images'] ?? '[]', true);
            $firstImage = (is_array($imgs) && !empty($imgs)) ? $imgs[0] : null;

            // Stock badge mapping for second-hand
            $qty = (int)$r['stock_quantity'];
            if ($qty <= 0) {
                $badgeText = 'Sold';
                $badgeClass = 'stock-badge--out-of-stock';
            } else {
                // second-hand examples used 'Available' in your markup
                $badgeText = 'Available';
                $badgeClass = 'stock-badge--in-stock';
            }

            return [
                'id' => (int)$r['id'],
                'title' => $r['title'],
                'price' => (float)$r['price'],
                'condition' => $r['condition_type'],
                'image' => $firstImage,
                'stock_quantity' => $qty,
                'stock_badge_text' => $badgeText,
                'stock_badge_class' => $badgeClass,
                'product_type' => $r['product_type'] ?? null,
            ];
        }, $secondRows);

        $this->viewApp(
            '/User/marketplace/merch-store-view',
            [
                'user' => $user,
                'merchandiseItems' => $merchandiseItems,
                'secondHandItems' => $secondHandItems,
                'merchandiseCount' => count($merchandiseItems),
                'secondHandCount' => count($secondHandItems),
            ],
            'Merch Store - ReidHub Marketplace'
        );
    }
    public function showSecondHandStore()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/second-hand-store-view', ['user' => $user], 'Second Hand Store - ReidHub Marketplace');
    }
    public function showSpecificProduct()
    {
        $user = Auth_LoginController::getSessionUser(true);

        $id = (int)($_GET['id'] ?? 0);
        $product = null;
        if ($id > 0) {
            $mp = new MarketPlace();
            $row = $mp->findPublicItemById($id);

            if ($row) {
                // Decode images
                $imgs = [];
                if (!empty($row['images'])) {
                    $decoded = json_decode($row['images'], true);
                    if (is_array($decoded)) $imgs = array_values(array_filter($decoded));
                }
                $mainImage = $imgs[0] ?? '/images/placeholders/product.png';
                $thumbs = $imgs;

                // Condition and stock badges
                $cond = $row['condition_type'] === 'brand_new' ? 'Brand New' : 'Used';
                $condClass = $row['condition_type'] === 'brand_new' ? 'condition-badge--new' : 'condition-badge--used';

                $qty = (int)$row['stock_quantity'];
                if ($qty <= 0) {
                    $stockText = 'Sold';
                    $stockClass = 'stock-badge--out-of-stock';
                    $availText = 'Out of Stock';
                    $availClass = 'availability--out-of-stock';
                } elseif ($qty <= 3) {
                    $stockText = 'Low Stock';
                    $stockClass = 'stock-badge--low-stock';
                    $availText = "Low Stock ({$qty} available)";
                    $availClass = 'availability--low-stock';
                } else {
                    $stockText = 'In Stock';
                    $stockClass = 'stock-badge--in-stock';
                    $availText = "In Stock ({$qty} available)";
                    $availClass = 'availability--in-stock';
                }

                // Category label for breadcrumb header (keep your classes)
                $categoryLabel = ($row['category'] === 'second-hand') ? 'Second Hand' : 'Merchandise';
                $productTypeLabel = ucfirst((string)($row['product_type'] ?? ''));

                $product = [
                    'id' => (int)$row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'price' => (float)$row['price'],
                    'category' => $row['category'],
                    'product_type' => $row['product_type'],
                    'condition' => $row['condition_type'],
                    'condition_text' => $cond,
                    'condition_class' => $condClass,
                    'stock_quantity' => $qty,
                    'stock_text' => $stockText,
                    'stock_class' => $stockClass,
                    'availability_text' => $availText,
                    'availability_class' => $availClass,
                    'main_image' => $mainImage,
                    'images' => $thumbs,
                    'category_label' => $categoryLabel,
                    'product_type_label' => $productTypeLabel,
                    'seller_id' => (int)$row['seller_id'],
                ];
            }
        }

        // If not found, render view and let it show a simple popup + back
        $this->viewApp(
            '/User/marketplace/specific-product-view',
            ['user' => $user, 'product' => $product],
            'Product Details - ReidHub Marketplace'
        );
    }
    public function showMyCart()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/my-cart-view', ['user' => $user], 'My Cart - ReidHub Marketplace');
    }
    public function showMyOrders()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/my-orders-view', ['user' => $user], 'My Orders - ReidHub Marketplace');
    }
    public function showSellerPortalOrders()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-orders-view', ['user' => $user], 'Seller Portal Orders - ReidHub Marketplace');
    }
    public function showSellerPortalAnalytics()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-analytics-view', ['user' => $user], 'Seller Portal Analytics - ReidHub Marketplace');
    }
    public function showSellerPortalAddItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-add-items-view', ['user' => $user], 'Seller Portal Add Items - ReidHub Marketplace');
    }
    public function showSellerPortalActiveItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $items = [];

        if ($user) {
            $mp = new MarketPlace();
            $rows = $mp->findActiveBySellerMinimal((int)$user['id']);
            foreach ($rows as $r) {
                $imgs = json_decode($r['images'] ?? '[]', true);
                $items[] = [
                    'id' => (int)$r['id'],
                    'title' => $r['title'],
                    'price' => (float)$r['price'],
                    'condition' => $r['condition_type'], // brand_new | used
                    'stock' => (int)$r['stock_quantity'],
                    'image' => (is_array($imgs) && !empty($imgs)) ? $imgs[0] : null,
                    'updated_at' => $r['updated_at'],
                ];
            }
        }

        $this->viewApp(
            '/User/marketplace/seller-portal-active-items-view',
            ['user' => $user, 'items' => $items],
            'Seller Portal Active Items - ReidHub Marketplace'
        );
    }
    public function showSellerPortalArchivedItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $items = [];

        if ($user) {
            $mp = new MarketPlace();
            $rows = $mp->findArchivedBySellerMinimal((int)$user['id']);
            foreach ($rows as $r) {
                $imgs = json_decode($r['images'] ?? '[]', true);
                $items[] = [
                    'id' => (int)$r['id'],
                    'title' => $r['title'],
                    'price' => (float)$r['price'],
                    'condition' => $r['condition_type'],
                    'stock' => (int)$r['stock_quantity'],
                    'image' => (is_array($imgs) && !empty($imgs)) ? $imgs[0] : null,
                    'updated_at' => $r['updated_at'],
                ];
            }
        }

        $this->viewApp(
            '/User/marketplace/seller-portal-archived-items-view',
            ['user' => $user, 'items' => $items],
            'Archived Items - ReidHub Marketplace'
        );
    }
    public function showSellerPortalEditItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        if (!$user) {
            $this->viewApp('/User/marketplace/seller-portal-edit-items-view', ['user' => null, 'item' => null], 'Seller Portal Edit Items - ReidHub Marketplace');
            return;
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            Logger::warning('showSellerPortalEditItems: missing or invalid id');
            $this->viewApp('/User/marketplace/seller-portal-edit-items-view', ['user' => $user, 'item' => null], 'Seller Portal Edit Items - ReidHub Marketplace');
            return;
        }

        $mp = new MarketPlace();
        $item = $mp->findItemByIdForSeller($id, (int)$user['id']);
        if (!$item) {
            Logger::warning("showSellerPortalEditItems: item {$id} not found for seller {$user['id']}");
            $this->viewApp('/User/marketplace/seller-portal-edit-items-view', ['user' => $user, 'item' => null], 'Seller Portal Edit Items - ReidHub Marketplace');
            return;
        }

        $this->viewApp('/User/marketplace/seller-portal-edit-items-view', ['user' => $user, 'item' => $item], 'Seller Portal Edit Items - ReidHub Marketplace');
    }

    /**
     * POST /dashboard/marketplace/seller/edit
     * Updates an existing item owned by the current seller.
     */
    public function updateItem()
    {
        header('Content-Type: application/json');

        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
                return;
            }

            $mp = new MarketPlace();
            $existing = $mp->findItemByIdForSeller($id, (int)$user['id']);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not found']);
                return;
            }

            // Basic validation
            $itemName    = trim($_POST['item_name'] ?? '');
            $productType = $_POST['category'] ?? '';
            $condition   = $_POST['condition'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $price       = $_POST['item_price'] ?? '';
            $qty         = $_POST['item_quantity'] ?? '';

            if ($itemName === '' || $productType === '' || $condition === '' || $description === '' ||
                !is_numeric($price) || !ctype_digit((string)$qty)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid form data']);
                return;
            }

            // Payments and bank details
            $validPayments = ['cash_on_delivery', 'preorder'];
            $pm = array_values(array_intersect((array)($_POST['payment_methods'] ?? []), $validPayments));

            $bankName = $_POST['bank_name'] ?? null;
            $bankBranch = trim($_POST['bank_branch'] ?? '') ?: null;
            $accountName = trim($_POST['account_name'] ?? '') ?: null;
            $accountNumber = preg_replace('/\D/', '', $_POST['account_number'] ?? '') ?: null;

            if (in_array('preorder', $pm, true)) {
                if (!$bankName || !$bankBranch || !$accountName || !preg_match('/^\d{10,18}$/', (string)$accountNumber)) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Invalid bank details for preorder']);
                    return;
                }
            } else {
                $bankName = $bankBranch = $accountName = $accountNumber = null;
            }

            // Derive category from condition
            $category = ($condition === 'brand_new') ? 'merchandise' : 'second-hand';

            // Existing images into slots 0..3
            $existingImages = json_decode($existing['images'] ?? '[]', true);
            if (!is_array($existingImages)) $existingImages = [];
            $bySlot = [0 => null, 1 => null, 2 => null, 3 => null];
            foreach (array_values($existingImages) as $i => $path) {
                if ($i >= 0 && $i <= 3 && !empty($path)) $bySlot[$i] = $path;
            }

            // Overwrite per-slot with same filename if new file provided
            if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $slots = $_POST['image_slot'] ?? []; // from hidden inputs
                $count = count($_FILES['images']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $file = [
                        'name'     => $_FILES['images']['name'][$i] ?? '',
                        'type'     => $_FILES['images']['type'][$i] ?? '',
                        'tmp_name' => $_FILES['images']['tmp_name'][$i] ?? '',
                        'error'    => $_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                        'size'     => $_FILES['images']['size'][$i] ?? 0,
                    ];
                    if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) continue;

                    $slotIndex = isset($slots[$i]) ? (int)$slots[$i] : $i;
                    if ($slotIndex < 0 || $slotIndex > 3) continue;

                    $allowed = ['image/jpeg'=>'jpg','image/jpg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                    if (!isset($allowed[$file['type']]) || ($file['size'] > 5 * 1024 * 1024)) continue;

                    if (!empty($bySlot[$slotIndex])) {
                        $publicUrl = $bySlot[$slotIndex];
                        $diskPath = $this->publicPathToDisk($publicUrl);
                        if ($diskPath) {
                            @unlink($diskPath);
                            @mkdir(dirname($diskPath), 0775, true);
                            if (@move_uploaded_file($file['tmp_name'], $diskPath)) {
                                // Keep same URL/name
                                $bySlot[$slotIndex] = $publicUrl;
                            }
                        }
                    } else {
                        // New file for empty slot
                        $savedUrl = $this->saveUploadedImageForSeller($file, (int)$user['id']);
                        if ($savedUrl) $bySlot[$slotIndex] = $savedUrl;
                    }
                }
            }

            // Rebuild images in order 0..3
            $finalImages = [];
            for ($i = 0; $i <= 3; $i++) {
                if (!empty($bySlot[$i])) $finalImages[] = $bySlot[$i];
            }

            $data = [
                'title'            => $itemName,
                'description'      => $description,
                'price'            => number_format((float)$price, 2, '.', ''),
                'category'         => $category,
                'product_type'     => $productType,
                'condition_type'   => $condition,
                'stock_quantity'   => (int)$qty,
                'payment_methods'  => json_encode($pm, JSON_UNESCAPED_SLASHES),
                'images'           => json_encode($finalImages, JSON_UNESCAPED_SLASHES),
                'bank_name'        => $bankName,
                'bank_branch'      => $bankBranch,
                'account_name'     => $accountName,
                'account_number'   => $accountNumber,
            ];

            $ok = $mp->updateItemForSeller($id, (int)$user['id'], $data);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Update failed']);
                return;
            }

            // Always JSON
            echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
        } catch (Throwable $e) {
            Logger::error('updateItem error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /marketplace/seller/add-item
     * Handles adding a new marketplace item into marketplace_items.
     */
    public function addItem()
    {
        header('Content-Type: application/json');

        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            // Debug when files are missing due to PHP limits
            if (empty($_FILES) || !isset($_FILES['images'])) {
                Logger::warning('No files received. upload_max_filesize=' . ini_get('upload_max_filesize') .
                    ', post_max_size=' . ini_get('post_max_size') . ', max_file_uploads=' . ini_get('max_file_uploads'));
            }

            // Validate minimal fields
            $itemName = trim($_POST['item_name'] ?? '');
            $productType = $_POST['category'] ?? '';
            $condition = $_POST['condition'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $price = $_POST['item_price'] ?? '';
            $qty = $_POST['item_quantity'] ?? '';

            if ($itemName === '' || $productType === '' || $condition === '' || $description === '' ||
                !is_numeric($price) || !ctype_digit((string)$qty)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid form data']);
                return;
            }

            // Payment methods
            $validPayments = ['cash_on_delivery', 'preorder'];
            $pm = array_values(array_intersect((array)($_POST['payment_methods'] ?? []), $validPayments));

            $bankName = $_POST['bank_name'] ?? null;
            $bankBranch = trim($_POST['bank_branch'] ?? '') ?: null;
            $accountName = trim($_POST['account_name'] ?? '') ?: null;
            $accountNumber = preg_replace('/\D/', '', $_POST['account_number'] ?? '') ?: null;

            if (in_array('preorder', $pm, true)) {
                if (!$bankName || !$bankBranch || !$accountName || !preg_match('/^\d{10,18}$/', (string)$accountNumber)) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Invalid bank details for preorder']);
                    return;
                }
            } else {
                $bankName = $bankBranch = $accountName = $accountNumber = null;
            }

            // Category from condition
            $category = ($condition === 'brand_new') ? 'merchandise' : 'second-hand';

            // Save images to storage
            $images = $this->saveImages($_FILES['images'] ?? null);

            $data = [
                'seller_id' => (int)$user['id'],
                'title' => $itemName,
                'description' => $description,
                'price' => number_format((float)$price, 2, '.', ''),
                'category' => $category,
                'product_type' => $productType,
                'condition_type' => $condition,
                'stock_quantity' => (int)$qty,
                'status' => 'active',
                'payment_methods' => json_encode($pm, JSON_UNESCAPED_SLASHES),
                'images' => json_encode($images, JSON_UNESCAPED_SLASHES),
                'bank_name' => $bankName,
                'bank_branch' => $bankBranch,
                'account_name' => $accountName,
                'account_number' => $accountNumber,
            ];

            $model = new MarketPlace();
            $id = $model->createItem($data);
            if ($id === false) {
                throw new Exception('Insert failed');
            }

            echo json_encode(['success' => true, 'id' => $id, 'images' => $images]);
        } catch (Throwable $e) {
            Logger::error('Add item error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * Save up to 4 images to storage/marketplace and return URLs /storage/marketplace/{name}
     */
    private function saveImages(?array $files): array
    {
        $saved = [];
        if (!$files || empty($files['name']) || !is_array($files['name'])) {
            Logger::warning('saveImages: $_FILES[images] missing or malformed');
            return $saved;
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $max = 5 * 1024 * 1024; // 5MB
        $limit = min(4, count($files['name']));

        $projectRoot = dirname(__DIR__, 3); // /var/www/html
        $storageDir  = $projectRoot . '/storage/marketplace';

        // Log user and perms context
        $uid = function_exists('posix_geteuid') ? posix_geteuid() : null;
        $uname = ($uid !== null && function_exists('posix_getpwuid')) ? (posix_getpwuid($uid)['name'] ?? 'unknown') : 'unknown';
        Logger::debug("saveImages: running as uid={$uid} ({$uname}), storageDir={$storageDir}");

        // Ensure dir exists
        if (!is_dir($storageDir)) {
            if (!mkdir($storageDir, 0775, true)) {
                $err = error_get_last()['message'] ?? 'unknown';
                Logger::error("Failed to create directory: {$storageDir}. Reason: {$err}");
                return $saved;
            }
        }
        if (!is_writable($storageDir)) {
            Logger::error("Directory not writable: {$storageDir}");
            return $saved;
        }

        for ($i = 0; $i < $limit; $i++) {
            $errCode = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($errCode !== UPLOAD_ERR_OK) {
                Logger::warning("Upload error idx {$i}: code {$errCode}");
                continue;
            }

            $tmp = $files['tmp_name'][$i];
            if (!is_uploaded_file($tmp)) {
                Logger::warning("Not an uploaded file at idx {$i}");
                continue;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $type  = $finfo ? finfo_file($finfo, $tmp) : ($files['type'][$i] ?? '');
            if ($finfo) finfo_close($finfo);

            $size = (int)($files['size'][$i] ?? 0);
            if (!isset($allowed[$type])) {
                Logger::warning("Invalid mime at idx {$i}: {$type}");
                continue;
            }
            if ($size > $max) {
                Logger::warning("Too large at idx {$i}: {$size}");
                continue;
            }

            $ext  = $allowed[$type];
            $name = 'mp_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $path = $storageDir . '/' . $name;

            if (!move_uploaded_file($tmp, $path)) {
                $e = error_get_last()['message'] ?? 'unknown';
                Logger::warning("move_uploaded_file failed to {$path}: {$e}");
                continue;
            }

            // Public URL via symlink public/storage/marketplace -> storage/marketplace
            $saved[] = '/storage/marketplace/' . $name;
        }

        Logger::info('Saved images: ' . json_encode($saved));
        return $saved;
    }
    
    /**
     * GET /dashboard/marketplace/seller/active/get
     * Returns minimal JSON for active items.
     */
    public function getActiveItems()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $model = new MarketPlace();
            $rows = $model->findActiveBySellerMinimal((int)$user['id']);

            // Map to minimal payload and pick first image only
            $items = [];
            foreach ($rows as $r) {
                $imgs = [];
                if (!empty($r['images'])) {
                    $decoded = json_decode($r['images'], true);
                    if (is_array($decoded)) {
                        $imgs = $decoded;
                    }
                }
                $firstImage = $imgs[0] ?? null;

                $items[] = [
                    'id' => (int)$r['id'],
                    'title' => $r['title'],
                    'price' => (float)$r['price'],
                    'condition' => $r['condition_type'], // 'brand_new' | 'used'
                    'stock' => (int)$r['stock_quantity'],
                    'image' => $firstImage, // e.g. /storage/marketplace/xxx.webp
                    'updated_at' => $r['updated_at'],
                ];
            }

            echo json_encode(['success' => true, 'items' => $items]);
        } catch (Throwable $e) {
            Logger::error('getActiveItems error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/seller/archived/update
     * Archives a product owned by the current seller.
     */
    public function archiveItem()
    {
        header('Content-Type: application/json');

        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
                return;
            }
            $mp = new MarketPlace();
            $ok = $mp->archiveItemForSeller($id, (int)$user['id']);

            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not found or already archived']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Item archived successfully']);
        } catch (Throwable $e) {
            Logger::error('updateArchiveItem error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST/PUT /dashboard/marketplace/seller/archived/unarchive
     * Unarchives a product owned by the current seller.
     */
    public function updateUnarchiveItem()
    {
        header('Content-Type: application/json');

        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
                return;
            }

            $mp = new MarketPlace();
            $ok = $mp->unarchiveItemForSeller($id, (int)$user['id']);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not found or not archived']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Item unarchived successfully']);
        } catch (Throwable $e) {
            Logger::error('updateUnarchiveItem error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/cart/add
     * Adds an item to the user's cart (creates or increments).
     */
    public function addToCart()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart.']);
                return;
            }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $qty = (int)($_POST['quantity'] ?? 1);
            $preferred = strtolower(trim((string)($_POST['payment_method'] ?? '')));
            if ($productId <= 0 || $qty <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
                return;
            }

            $mp = new MarketPlace();
            $product = $mp->findPublicItemById($productId);
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found or unavailable']);
                return;
            }

            $stock = (int)($product['stock_quantity'] ?? 0);
            if ($stock <= 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Out of stock']);
                return;
            }

            // Determine allowed payment methods and pick final method
            $pms = json_decode($product['payment_methods'] ?? '[]', true);
            $allowsCOD = is_array($pms) && in_array('cash_on_delivery', $pms, true);
            $allowsPre = is_array($pms) && in_array('preorder', $pms, true);

            $finalMethod = null;
            if (in_array($preferred, ['cash_on_delivery', 'preorder'], true)) {
                if (($preferred === 'cash_on_delivery' && $allowsCOD) || ($preferred === 'preorder' && $allowsPre)) {
                    $finalMethod = $preferred;
                }
            }
            if ($finalMethod === null) {
                $finalMethod = $allowsCOD ? 'cash_on_delivery' : ($allowsPre ? 'preorder' : null);
            }
            if ($finalMethod === null) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'No valid payment method for this product']);
                return;
            }

            $unitPrice = (float)$product['price'];
            $cart = new Cart();
            $ok = $cart->addOrIncrement((int)$user['id'], $productId, $qty, $unitPrice, $finalMethod);

            if (!$ok) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Added to cart', 'payment_method' => $finalMethod]);
        } catch (Throwable $e) {
            Logger::error('addToCart error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * GET /dashboard/marketplace/cart/get
     * Returns user's cart with product info.
     */
    public function getCartItemsApi()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $cart = new Cart();
            $rows = $cart->getItemsForUser((int)$user['id']);

            $items = [];
            $subtotal = 0;

            foreach ($rows as $r) {
                $imgs = [];
                if (!empty($r['images'])) {
                    $decoded = json_decode($r['images'], true);
                    if (is_array($decoded)) $imgs = array_values(array_filter($decoded));
                }
                $firstImage = $imgs[0] ?? '/images/placeholders/product.png';

                $qty = max(1, (int)$r['quantity']);
                $price = (float)$r['unit_price'];
                $subtotal += ($price * $qty);

                $pms = json_decode($r['payment_methods'] ?? '[]', true);
                $allowsCOD = is_array($pms) && in_array('cash_on_delivery', $pms, true);
                $allowsPre = is_array($pms) && in_array('preorder', $pms, true);

                // Ensure selected method is valid; if not, fallback
                $selected = $r['payment_method'] ?? null;
                if (!in_array($selected, ['cash_on_delivery', 'preorder'], true) ||
                    ($selected === 'cash_on_delivery' && !$allowsCOD) ||
                    ($selected === 'preorder' && !$allowsPre)) {
                    $selected = $allowsCOD ? 'cash_on_delivery' : ($allowsPre ? 'preorder' : null);
                }

                $items[] = [
                    'product_id' => (int)$r['product_id'],
                    'title' => $r['title'],
                    'price' => $price,
                    'quantity' => $qty,
                    'condition' => $r['condition_type'],
                    'seller_label' => 'Seller #' . (int)$r['seller_id'],
                    'image' => $firstImage,
                    'allowsCOD' => $allowsCOD,
                    'isPreorder' => $allowsPre,
                    'payment_method' => $selected,
                    'stock_quantity' => (int)$r['stock_quantity'],
                    'status' => $r['status'],
                ];
            }

            echo json_encode(['success' => true, 'items' => $items, 'subtotal' => $subtotal]);
        } catch (Throwable $e) {
            Logger::error('getCartItemsApi error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/cart/remove
     * Remove a single product from the cart.
     */
    public function removeFromCart()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            if ($productId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid product']);
                return;
            }

            $cart = new Cart();
            $ok = $cart->removeItem((int)$user['id'], $productId);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not in cart']);
                return;
            }

            echo json_encode(['success' => true, 'message' => 'Removed from cart']);
        } catch (Throwable $e) {
            Logger::error('removeFromCart error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/cart/update
     * Update quantity for an item in the cart.
     */
    public function updateCartQuantity()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $qty = (int)($_POST['quantity'] ?? 1);
            if ($productId <= 0 || $qty <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
                return;
            }

            // Clamp by available stock
            $mp = new MarketPlace();
            $prod = $mp->findPublicItemById($productId);
            $stock = (int)($prod['stock_quantity'] ?? 0);
            if ($stock > 0) {
                $qty = min($qty, $stock);
            }

            $cart = new Cart();
            $ok = $cart->updateQuantity((int)$user['id'], $productId, $qty);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Item not in cart']);
                return;
            }

            echo json_encode(['success' => true, 'quantity' => $qty]);
        } catch (Throwable $e) {
            Logger::error('updateCartQuantity error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/cart/payment-method
     * Update the payment method for a cart item.
     */
    public function updateCartPaymentMethod()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }

            $productId = (int)($_POST['product_id'] ?? 0);
            $method = strtolower(trim((string)($_POST['payment_method'] ?? '')));

            if ($productId <= 0 || !in_array($method, ['cash_on_delivery','preorder'], true)) {
                http_response_code(422);
                echo json_encode(['success'=>false,'message'=>'Invalid input']);
                return;
            }

            // Validate against product allowed methods
            $mp = new MarketPlace();
            $prod = $mp->findPublicItemById($productId);
            if (!$prod) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Product not found']); return; }

            $pms = json_decode($prod['payment_methods'] ?? '[]', true);
            $allowsCOD = is_array($pms) && in_array('cash_on_delivery', $pms, true);
            $allowsPre = is_array($pms) && in_array('preorder', $pms, true);

            if ($method === 'cash_on_delivery' && !$allowsCOD) {
                http_response_code(409); echo json_encode(['success'=>false,'message'=>'Cash on delivery not allowed']); return;
            }
            if ($method === 'preorder' && !$allowsPre) {
                http_response_code(409); echo json_encode(['success'=>false,'message'=>'Pre-order not allowed']); return;
            }

            $cart = new Cart();
            $ok = $cart->updatePaymentMethod((int)$user['id'], $productId, $method);
            if (!$ok) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Item not in cart']); return; }

            echo json_encode(['success'=>true,'payment_method'=>$method]);
        } catch (Throwable $e) {
            Logger::error('updateCartPaymentMethod error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }

    // Rename the private helper to avoid name clash (optional)
    // private function updateCartPaymentMethod(int $userId, int $productId, string $method): bool { ... }

    // Map public URL (/storage/...) to disk path
    private function publicPathToDisk(string $publicPath): ?string
    {
        $publicPath = trim($publicPath);
        if ($publicPath === '' || $publicPath[0] !== '/') return null;
        $publicRoot = realpath(__DIR__ . '/../../../public');
        if (!$publicRoot) return null;
        return $publicRoot . $publicPath;
    }

    // Save new image file (new name)
    private function saveUploadedImageForSeller(array $file, int $sellerId): ?string
    {
        $allowed = ['image/jpeg'=>'jpg','image/jpg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (empty($allowed[$file['type'] ?? ''])) return null;
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) return null;

        $ext = $allowed[$file['type']];
        $baseDir = __DIR__ . '/../../../public/storage/marketplace/' . $sellerId;
        if (!is_dir($baseDir)) @mkdir($baseDir, 0775, true);

        $name = uniqid('p_', true) . '.' . $ext;
        $dest = $baseDir . '/' . $name;
        if (!@move_uploaded_file($file['tmp_name'], $dest)) return null;

        return '/storage/marketplace/' . $sellerId . '/' . $name;
    }

    public function showCheckout()
    {
        $user = Auth_LoginController::getSessionUser(true);
        if (!$user) { header('Location: /login'); return; }

        $cart = new Cart();
        $mp = new MarketPlace();

        $rows = $cart->getItemsForUser((int)$user['id']);
        $lines = [];
        $subtotal = 0.0;

        foreach ($rows as $r) {
            $prod = $mp->findPublicItemById((int)$r['product_id']);
            if (!$prod) continue;

            $pms = json_decode($prod['payment_methods'] ?? '[]', true);
            $allowsCOD = is_array($pms) && in_array('cash_on_delivery', $pms, true);
            $allowsPre = is_array($pms) && in_array('preorder', $pms, true);

            // Selected method from cart row; fallback to allowed
            $selected = $r['payment_method'] ?? null;
            if (!in_array($selected, ['cash_on_delivery','preorder'], true) ||
                ($selected === 'cash_on_delivery' && !$allowsCOD) ||
                ($selected === 'preorder' && !$allowsPre)) {
                $selected = $allowsCOD ? 'cash_on_delivery' : ($allowsPre ? 'preorder' : null);
            }
            if ($selected === null) continue; // skip invalid

            $imgs = [];
            if (!empty($prod['images'])) {
                $decoded = json_decode($prod['images'], true);
                if (is_array($decoded)) $imgs = array_values(array_filter($decoded));
            }
            $img = $imgs[0] ?? '/images/placeholders/product.png';

            $qty = max(1, (int)$r['quantity']);
            $price = (float)($r['unit_price'] ?? $prod['price']);
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;

            $lines[] = [
                'product_id' => (int)$r['product_id'],
                'seller_id' => (int)$prod['seller_id'],
                'title' => $r['title'] ?? $prod['title'],
                'image' => $img,
                'quantity' => $qty,
                'unit_price' => $price,
                'line_total' => $lineTotal,
                'allowsCOD' => $allowsCOD,
                'isPreorder' => $allowsPre,
                'payment_method' => $selected,
                'bank_name' => $selected === 'preorder' ? ($prod['bank_name'] ?? null) : null,
                'bank_branch' => $selected === 'preorder' ? ($prod['bank_branch'] ?? null) : null,
                'account_name' => $selected === 'preorder' ? ($prod['account_name'] ?? null) : null,
                'account_number' => $selected === 'preorder' ? ($prod['account_number'] ?? null) : null,
            ];
        }

        $this->viewApp(
            '/User/marketplace/checkout-view',
            ['lines' => $lines, 'subtotal' => $subtotal],
            'Checkout - ReidHub Marketplace'
        );
    }

    // Submit: create 1 transaction + N orders (each item is its own entity)
    public function submitCheckout()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }

            $mp = new MarketPlace();
            $txModel = new Transaction();
            $orderModel = new Order();

            // Prefer posted arrays; fallback to cart content (and cart payment_method)
            $productIds = array_map('intval', (array)($_POST['product_id'] ?? []));
            $quantities = (array)($_POST['quantity'] ?? []);
            $methodsMap = (array)($_POST['payment_method'] ?? []);

            if (empty($productIds)) {
                $cart = new Cart();
                $rows = $cart->getItemsForUser((int)$user['id']);
                foreach ($rows as $r) {
                    $pid = (int)$r['product_id'];
                    $productIds[] = $pid;
                    $quantities[] = (int)$r['quantity'];
                    $methodsMap[$pid] = $r['payment_method'] ?? null;
                }
            }

            if (empty($productIds)) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'No items to checkout']); return; }

            $total = 0.0;
            $payloads = [];

            foreach ($productIds as $idx => $pid) {
                if ($pid <= 0) continue;
                $qty = max(1, (int)($quantities[$idx] ?? 1));

                $prod = $mp->findPublicItemById($pid);
                if (!$prod) throw new RuntimeException("Product unavailable ({$pid})");

                $pms = json_decode($prod['payment_methods'] ?? '[]', true);
                $allowsCOD = is_array($pms) && in_array('cash_on_delivery', $pms, true);
                $allowsPre = is_array($pms) && in_array('preorder', $pms, true);

                $method = $methodsMap[$pid] ?? null;
                if (!in_array($method, ['cash_on_delivery','preorder'], true) ||
                    ($method === 'cash_on_delivery' && !$allowsCOD) ||
                    ($method === 'preorder' && !$allowsPre)) {
                    $method = $allowsCOD ? 'cash_on_delivery' : ($allowsPre ? 'preorder' : null);
                }
                if (!$method) throw new RuntimeException("No valid payment method for product {$pid}");

                $price = (float)$prod['price'];
                $lineTotal = $qty * $price;
                $total += $lineTotal;

                // Slip if preorder (support slips[pid] or payment_slips[index][file] with posted product_id)
                $slipPath = null;
                if ($method === 'preorder') {
                    $file = $this->getSlipFileForProduct($pid);
                    if (!$file) throw new RuntimeException("Payment slip missing for product {$pid}");
                    $slipPath = $this->saveSlipFile($file, (int)$user['id'], $pid);
                    if (!$slipPath) throw new RuntimeException("Failed to save slip for product {$pid}");
                }

                $payloads[] = [
                    'buyer_id' => (int)$user['id'],
                    'seller_id' => (int)$prod['seller_id'],
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'payment_method' => $method,
                    'slip_path' => $slipPath,
                    'bank_name' => $method === 'preorder' ? ($prod['bank_name'] ?? null) : null,
                    'bank_branch' => $method === 'preorder' ? ($prod['bank_branch'] ?? null) : null,
                    'account_name' => $method === 'preorder' ? ($prod['account_name'] ?? null) : null,
                    'account_number' => $method === 'preorder' ? ($prod['account_number'] ?? null) : null,
                ];
            }

            if (empty($payloads)) throw new RuntimeException('Nothing to checkout');

            $txId = $txModel->create([
                'buyer_id' => (int)$user['id'],
                'item_count' => count($payloads),
                'total_amount' => $total,
            ]);
            if (!$txId) throw new RuntimeException('Failed to create transaction');

            foreach ($payloads as $p) {
                $ok = $orderModel->create($p + ['transaction_id' => $txId]);
                if (!$ok) throw new RuntimeException('Failed to create order');
            }

            // Clear
            $cart = new Cart();
            foreach ($productIds as $pid) {
                $cart->removeItem((int)$user['id'], (int)$pid);
            }

            echo json_encode(['success'=>true,'message'=>'Order placed','transaction_id'=>$txId]);
        } catch (Throwable $e) {
            Logger::error('submitCheckout error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Checkout failed']);
        }
    }


    private function getSlipFileForProduct(int $productId): ?array
    {
        // slips[pid]
        if (!empty($_FILES['slips']) && isset($_FILES['slips']['name'][$productId])) {
            return [
                'name' => $_FILES['slips']['name'][$productId] ?? '',
                'type' => $_FILES['slips']['type'][$productId] ?? '',
                'tmp_name' => $_FILES['slips']['tmp_name'][$productId] ?? '',
                'error' => $_FILES['slips']['error'][$productId] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES['slips']['size'][$productId] ?? 0,
            ];
        }

        // payment_slips[i][file] with POST payment_slips[i][product_id]
        if (!empty($_POST['payment_slips']) && is_array($_POST['payment_slips']) && !empty($_FILES['payment_slips'])) {
            foreach ($_POST['payment_slips'] as $i => $meta) {
                $pid = (int)($meta['product_id'] ?? 0);
                if ($pid !== $productId) continue;
                return [
                    'name'     => $_FILES['payment_slips']['name'][$i]['file'] ?? '',
                    'type'     => $_FILES['payment_slips']['type'][$i]['file'] ?? '',
                    'tmp_name' => $_FILES['payment_slips']['tmp_name'][$i]['file'] ?? '',
                    'error'    => $_FILES['payment_slips']['error'][$i]['file'] ?? UPLOAD_ERR_NO_FILE,
                    'size'     => $_FILES['payment_slips']['size'][$i]['file'] ?? 0,
                ];
            }
        }

        return null;
    }

    private function saveSlipFile(array $file, int $buyerId, int $productId): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        $allowed = [
            'image/jpeg' => 'jpg', 'image/jpg' => 'jpg',
            'image/png' => 'png', 'image/webp' => 'webp',
            'application/pdf' => 'pdf'
        ];
        if (!isset($allowed[$file['type'] ?? ''])) return null;
        if (($file['size'] ?? 0) > 8 * 1024 * 1024) return null;

        $ext = $allowed[$file['type']];
        $baseDir = __DIR__ . '/../../../public/storage/orders/' . $buyerId;
        if (!is_dir($baseDir)) @mkdir($baseDir, 0775, true);

        $name = 'slip_' . $productId . '_' . date('Ymd_His') . '.' . $ext;
        $dest = $baseDir . '/' . $name;
        if (!@move_uploaded_file($file['tmp_name'], $dest)) return null;

        return '/storage/orders/' . $buyerId . '/' . $name;
    }

    /**
     * GET /dashboard/marketplace/orders/get
     * Returns current user's orders with product info.
     */
    public function getOrdersApi()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }

            $orderModel = new Order();
            $rows = $orderModel->getOrdersForBuyer((int)$user['id']);

            $items = [];
            foreach ($rows as $r) {
                $imgs = [];
                if (!empty($r['images'])) {
                    $decoded = json_decode($r['images'], true);
                    if (is_array($decoded)) $imgs = array_values(array_filter($decoded));
                }
                $image = $imgs[0] ?? '/images/placeholders/product.png';

                // Map DB status -> UI tabs/status text
                $db = strtolower((string)$r['status']);
                $status = 'pending';
                $statusText = 'Yet to Ship';
                $statusMessage = 'Your order is being prepared for shipment';

                if ($db === 'shipped') {
                    $status = 'shipped';
                    $statusText = 'Shipped';
                    $statusMessage = 'Your order has been shipped';
                } elseif ($db === 'delivered') {
                    $status = 'delivered';
                    $statusText = 'Delivered';
                    $statusMessage = 'Delivered';
                } elseif ($db === 'cancelled') {
                    $status = 'cancelled';
                    $statusText = 'Cancelled';
                    $statusMessage = 'Order cancelled';
                } elseif ($db === 'yet_to_ship' || $db === 'processing') {
                    $status = 'pending';
                    $statusText = 'Yet to Ship';
                    $statusMessage = 'Your order is being prepared for shipment';
                }

                $items[] = [
                    'id' => (int)$r['id'],
                    'title' => $r['title'],
                    'price' => (float)$r['unit_price'],
                    'quantity' => (int)$r['quantity'],
                    'ordered_at' => $r['created_at'],
                    'status' => $status,
                    'statusText' => $statusText,
                    'statusMessage' => $statusMessage,
                    'image' => $image,
                ];
            }

            echo json_encode(['success'=>true,'items'=>$items, 'count'=>count($items)]);
        } catch (Throwable $e) {
            Logger::error('getOrdersApi error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }

    /**
     * GET /dashboard/marketplace/seller/analytics/data
     * Returns aggregated analytics for the logged-in seller.
     * Query params:
     *   range: 7d | 30d | 90d (default 30d)
     */
    public function sellerAnalyticsData()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }

            $sellerId = (int)$user['id']; // assuming user id == seller id
            $range = strtolower(trim($_GET['range'] ?? '30d'));
            $days = 30;
            if ($range === '7d') $days = 7;
            elseif ($range === '90d') $days = 90;

            $to = (new DateTime('now'))->format('Y-m-d 23:59:59');
            $from = (new DateTime("-{$days} days"))->format('Y-m-d 00:00:00');

            $orderModel = new Order();
            $analytics = $orderModel->getSellerAnalytics($sellerId, $from, $to, 5);

            echo json_encode(['success'=>true, 'rangeDays'=>$days] + $analytics);
        } catch (Throwable $e) {
            Logger::error('sellerAnalyticsData error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }
    public function getSellerOrdersApi()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }

            $sellerId = (int)$user['id'];
            $order = new Order();
            $rows = $order->getOrdersForSeller($sellerId);

            $items = array_map(function($r) {
                // Map DB status -> UI status class used in tabs and badges
                $db = strtolower((string)$r['status']);
                $uiStatus = 'yet-to-ship';
                if ($db === 'delivered') $uiStatus = 'delivered';
                elseif ($db === 'cancelled') $uiStatus = 'canceled';
                elseif ($db === 'returned') $uiStatus = 'returned';
                else $uiStatus = 'yet-to-ship'; // includes yet_to_ship, processing, shipped

                $pm = strtolower((string)$r['payment_method']);
                $payment = ($pm === 'preorder') ? 'preorder' : 'cod';

                $buyerName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: ('Buyer #' . (int)$r['buyer_id']);

                return [
                    'id' => (int)$r['id'],
                    'title' => $r['title'],
                    'buyer_name' => $buyerName,
                    'payment' => $payment,                  // 'preorder' | 'cod'
                    'created_at' => $r['created_at'],
                    'status' => $uiStatus,                  // 'yet-to-ship' | 'delivered' | 'canceled' | 'returned'
                    'slip_path' => $r['slip_path'] ?: null,
                ];
            }, $rows);

            echo json_encode(['success'=>true,'items'=>$items, 'count'=>count($items)]);
        } catch (Throwable $e) {
            Logger::error('getSellerOrdersApi error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/seller/orders/mark-delivered
     */
    public function markSellerOrderDelivered()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }

            $orderId = (int)($_POST['order_id'] ?? 0);
            if ($orderId <= 0) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Invalid order']); return; }

            $order = new Order();
            $ok = $order->markDelivered((int)$user['id'], $orderId);
            if (!$ok) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Cannot mark as delivered']); return; }

            echo json_encode(['success'=>true]);
        } catch (Throwable $e) {
            Logger::error('markSellerOrderDelivered error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }

    /**
     * POST /dashboard/marketplace/seller/orders/cancel
     */
    public function cancelSellerOrder()
    {
        header('Content-Type: application/json');
        try {
            $user = Auth_LoginController::getSessionUser(true);
            if (!$user) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Unauthorized']); return; }
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); return; }

            $orderId = (int)($_POST['order_id'] ?? 0);
            $reason = trim((string)($_POST['reason'] ?? ''));
            if ($orderId <= 0 || $reason === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Invalid input']); return; }

            $order = new Order();
            $ok = $order->cancel((int)$user['id'], $orderId, $reason);
            if (!$ok) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Cannot cancel this order']); return; }

            echo json_encode(['success'=>true]);
        } catch (Throwable $e) {
            Logger::error('cancelSellerOrder error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success'=>false,'message'=>'Server error']);
        }
    }
}