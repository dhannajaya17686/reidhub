# ReidHub Code Standards & Conventions

## Table of Contents
1. [Overview](#overview)
2. [Naming Conventions](#naming-conventions)
3. [Folder Structure Rules](#folder-structure-rules)
4. [PHP Code Standards](#php-code-standards)
5. [Adding New Features](#adding-new-features)
6. [File Organization](#file-organization)
7. [Comment & Documentation](#comment--documentation)
8. [Error Handling](#error-handling)
9. [Database Operations](#database-operations)
10. [Security Best Practices](#security-best-practices)
11. [Code Review Checklist](#code-review-checklist)

---

## Overview

ReidHub follows a **module-based MVC architecture** with consistent naming conventions and folder organization. These standards ensure code is maintainable, scalable, and easy to understand for all developers.

### Core Principles
- 📦 **Consistency** - Same patterns across all modules
- 🎯 **Clarity** - Self-documenting code and naming
- 📁 **Organization** - Logical folder structure reflecting features
- ⚡ **Performance** - Efficient queries and operations
- 🔒 **Security** - Input validation and SQL injection prevention
- 📝 **Documentation** - Clear comments and docblocks

---

## Naming Conventions

### Class Names

**Pattern**: `Module_FeatureController` or `FeatureName` (for models)

#### Controllers
```php
// Format: [Module]_[Feature]Controller
// Location: app/controllers/[Module]/[Feature]Controller.php

class Auth_LoginController extends Controller { }           // ✅ Good
class Marketplace_MarketplaceUserController extends Controller { }  // ✅ Good
class Forum_ForumAdminController extends Controller { }    // ✅ Good
class Community_CommunityUserController extends Controller { } // ✅ Good
class WrongNameController extends Controller { }           // ❌ Bad: Missing module prefix
```

**Underscore Naming Rule**: 
- Underscores in class names translate to directory separators in file paths
- `Auth_LoginController` → `app/controllers/Auth/LoginController.php`
- `Forum_ForumAdminController` → `app/controllers/Forum/ForumAdminController.php`

#### Models
```php
// Format: [EntityName] (PascalCase, no Controller suffix)
// Location: app/models/[EntityName].php

class User extends Model { }           // ✅ Good: Singular noun
class Product extends Model { }        // ✅ Bad naming but acceptable: Should be "Product"
class MarketPlace extends Model { }    // ✅ Good: CamelCase for multi-word
class Cart extends Model { }           // ✅ Good: Singular
class Order extends Model { }          // ✅ Good: Singular
class BlogPost extends Model { }       // ✅ Good: Descriptive multi-word

class Products extends Model { }       // ❌ Bad: Plural form
class user_model extends Model { }     // ❌ Bad: Not PascalCase
```

#### Core Classes
```php
// Location: app/core/[ClassName].php
// Format: [FunctionalName]

class Router { }                       // ✅ Good
class Controller { }                   // ✅ Good
class Model { }                        // ✅ Good
class Database { }                     // ✅ Good
class Logger { }                       // ✅ Good
class View { }                         // ✅ Good
```

### Method Names

**Pattern**: `camelCase` starting with a verb

```php
class MarketplaceUserController extends Controller {
    // Display methods
    public function showMerchStore() { }           // ✅ Display a view
    public function showMyCart() { }               // ✅ Display user's cart
    public function showCheckout() { }             // ✅ Display checkout page
    
    // Action methods
    public function addToCart() { }                // ✅ Action: add item
    public function submitCheckout() { }           // ✅ Action: process form
    public function updateCartQuantity() { }       // ✅ Action: update
    
    // API methods
    public function getCartItemsApi() { }          // ✅ API endpoint
    public function getOrdersApi() { }             // ✅ API endpoint
    
    // Query methods (in Models)
    public function findByEmail($email) { }        // ✅ Find single record
    public function findActiveBySellerMinimal() { } // ✅ Find with conditions
    public function getAllProducts() { }           // ✅ Get all records
    
    // Private helper methods
    private function isAjax() { }                  // ✅ Helper method
    private function validateInput($data) { }      // ✅ Validation helper
    
    // Bad examples
    public function getdata() { }                  // ❌ Wrong case
    public function FetchItems() { }               // ❌ Wrong convention
    public function items_list() { }               // ❌ Snake case
    public function shw_cart() { }                 // ❌ Abbreviated
}
```

### Property Names

**Pattern**: `camelCase`, use `$this->` for instance variables

```php
class UserDashboardController extends Controller {
    // Instance properties
    private $userData;                 // ✅ Good
    protected $isAuthenticated;        // ✅ Good
    public $userSession;               // ✅ Good (rare, usually avoid public)
    
    // Local variables
    $userId = 123;                     // ✅ Good
    $user_data = [];                   // ❌ Bad: Use camelCase
    
    // Constants
    const MAX_CART_ITEMS = 50;         // ✅ Good: UPPER_SNAKE_CASE
    const DEFAULT_TIMEOUT = 300;       // ✅ Good
}
```

### Variable Names

**Pattern**: `camelCase`, descriptive and meaningful

```php
// ✅ Good
$userId = 123;
$productTitle = "Hoodie";
$cartItems = [];
$isLoggedIn = true;
$totalPrice = 99.99;
$maxRetries = 3;
$currentPage = 1;
$emailAddress = "user@example.com";

// ❌ Bad
$uid = 123;                            // Too abbreviated
$pt = "Hoodie";                        // Cryptic
$cart = [];                            // Not specific enough
$logged = true;                        // Missing "is" prefix
$price = 99.99;                        // Not descriptive
$m = 3;                                // Single letter
$p = 1;                                // Single letter
$email = "user@example.com";           // Acceptable but less descriptive
```

### Database Table Names

**Pattern**: `snake_case`, plural form

```sql
-- ✅ Good
users
products
cart_items
transactions
orders
admin_logs

-- ❌ Bad
user                   -- Singular
Products               -- Pascal case
user_data              -- Misleading
users_table            -- Redundant suffix
```

### Route Paths

**Pattern**: `/path/to/resource` (lowercase, hyphens for multi-word)

```php
// ✅ Good
$routes['/login']
$routes['/dashboard/user']
$routes['/dashboard/marketplace/merch-store']
$routes['/dashboard/marketplace/seller/active-items']
$routes['/dashboard/community/blogs']
$routes['/dashboard/lost-and-found/items']

// ❌ Bad
$routes['/Login']                      // Uppercase
$routes['/dashboardUser']              -- No separator
$routes['/marketplace_merch_store']    -- Underscores
$routes['/Marketplace/MerchStore']     -- Pascal case
```

### File Names

**Pattern**: Matches class name with extension

```
Controllers:
  Auth/LoginController.php             // Class: Auth_LoginController
  Marketplace/MarketplaceUserController.php  // Class: Marketplace_MarketplaceUserController
  Forum/ForumAdminController.php       // Class: Forum_ForumAdminController

Models:
  User.php                             // Class: User
  Product.php                          // Class: Product
  MarketPlace.php                      // Class: MarketPlace

Views:
  Auth/log-in-view.php                 // Filename: kebab-case-view.php
  Home/home-view.php
  User/user-dashboard-view.php
  User/marketplace/merch-store-view.php

CSS:
  app/globals.css
  app/components/header.css
  app/user/marketplace/my-cart.css     // Feature-specific
  auth/sign-up.css

JavaScript:
  app/user-dashboard.js
  app/marketplace/my-cart.js           // Feature-specific
  app/community/blogs.js

Core:
  core/Router.php                      // Class: Router
  core/Database.php                    // Class: Database
```

---

## Folder Structure Rules

### Module Folder Naming Rules

**Pattern**: 
- Controllers: `[Module]/[Feature]Controller.php` 
- Views: `[Module]/[feature-name]/[page-name]-view.php`
- CSS: `app/[role]/[module]/[feature-name].css`
- JS: `app/[module]/[feature-name].js`

**Module Examples**:
- **Auth** - Authentication
- **Home** - Public landing page
- **Dashboard** - User/Admin panels
- **Marketplace** - Buy/sell functionality
- **Community** - Blogs, clubs, events
- **Forum** - Discussion threads
- **LostAndFound** - Lost item reporting

---

## PHP Code Standards

### Class Structure

```php
<?php

/**
 * Class: User
 * Purpose: User account data operations
 * Table: users
 * 
 * Methods:
 *   - findByEmail($email): ?array
 *   - findByRegNo($regNo): ?array
 *   - create(array $data): bool
 */
class User extends Model
{
    // Table name
    protected $table = 'users';

    /**
     * Finds a user by email address.
     *
     * @param string $email User's email
     * @return array|null User record or null
     * 
     * @example
     * $user = (new User())->findByEmail('user@example.com');
     */
    public function findByEmail(string $email): ?array
    {
        Logger::info("Searching for user with email: $email");

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($result) {
            Logger::info("User found with email: $email");
        } else {
            Logger::warning("No user found with email: $email");
        }

        return $result;
    }

    /**
     * Updates user details.
     *
     * @param int $userId User ID
     * @param array $data Columns to update [key => value]
     * @return bool Success status
     * 
     * @throws InvalidArgumentException
     */
    public function update(int $userId, array $data): bool
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data provided for update');
        }

        try {
            Logger::info("Updating user: $userId with data: " . json_encode($data));

            // Build SET clause dynamically
            $columns = array_keys($data);
            $setClause = implode(', ', array_map(fn($col) => "$col = ?", $columns));
            $values = array_values($data);
            $values[] = $userId;

            $sql = "UPDATE {$this->table} SET $setClause WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($values);

            if ($success) {
                Logger::info("User $userId updated successfully");
            } else {
                Logger::error("Failed to update user $userId");
            }

            return $success;
        } catch (Throwable $e) {
            Logger::error("Database error updating user: " . $e->getMessage());
            return false;
        }
    }
}
```

### Controller Structure

```php
<?php

/**
 * MarketplaceUserController
 * 
 * Purpose: Handle user marketplace operations (browse, cart, checkout)
 * 
 * Features:
 *   - Product browsing and filtering
 *   - Shopping cart management
 *   - Checkout and order placement
 *   - Transaction history
 * 
 * Dependencies:
 *   - MarketPlace model
 *   - Cart model
 *   - Order model
 */
class Marketplace_MarketplaceUserController extends Controller
{
    /**
     * Display the merch store
     * 
     * GET /dashboard/marketplace/merch-store
     * 
     * @return void Renders merch-store-view
     */
    public function showMerchStore()
    {
        // Validate user is logged in
        if (empty($_SESSION['user_id'])) {
            header('Location: /login', true, 303);
            exit;
        }

        try {
            $marketplace = new MarketPlace();
            
            // Get filter parameters
            $category = $_GET['category'] ?? 'merchandise';
            $productType = $_GET['type'] ?? null;
            $condition = $_GET['condition'] ?? null;
            $page = (int)($_GET['page'] ?? 1);
            
            // Fetch products with filters
            $products = $marketplace->findByFilters([
                'category' => $category,
                'product_type' => $productType,
                'condition_type' => $condition,
                'status' => 'active'
            ], 20, ($page - 1) * 20);

            $data = [
                'products' => $products,
                'currentCategory' => $category,
                'currentPage' => $page,
            ];

            $this->viewApp('User/marketplace/merch-store-view', $data, 'Merch Store');
        } catch (Throwable $e) {
            Logger::error("Error in showMerchStore: " . $e->getMessage());
            header('Location: /dashboard/user', true, 303);
            exit;
        }
    }

    /**
     * Add item to user's shopping cart
     * 
     * POST /dashboard/marketplace/cart/add
     * Expected POST data: product_id, quantity
     * 
     * @return void JSON response
     */
    public function addToCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // Check if AJAX
        if (!$this->isAjax()) {
            http_response_code(400);
            exit;
        }

        try {
            $userId = $_SESSION['user_id'] ?? null;
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);

            // Validation
            if (!$userId || !$productId || $quantity < 1) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'Invalid input']);
                return;
            }

            // Add to cart
            $cart = new Cart();
            $success = $cart->addItem([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);

            header('Content-Type: application/json');
            if ($success) {
                http_response_code(200);
                echo json_encode(['ok' => true, 'message' => 'Added to cart']);
            } else {
                http_response_code(400);
                echo json_encode(['ok' => false, 'message' => 'Failed to add to cart']);
            }
        } catch (Throwable $e) {
            Logger::error("Error in addToCart: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Server error']);
        }
    }

    /**
     * Check if request is AJAX
     *
     * @return bool
     */
    private function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['HTTP_ACCEPT']) && 
                strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
```

### Indentation & Formatting

```php
// ✅ Good: 4 spaces, clear structure
class Example {
    public function method() {
        if ($condition) {
            // 4 spaces per level
            $result = [
                'key1' => 'value1',
                'key2' => 'value2'
            ];
        }
    }
}

// ❌ Bad: Inconsistent spacing
class Example{
public function method(){
if($condition){
$result=['key1'=>'value1','key2'=>'value2'];
}}}
```

### Line Length & Readability

```php
// ✅ Good: Break long lines
$stmt = $this->db->prepare(
    "SELECT * FROM products WHERE category = ? AND status = ? LIMIT ?"
);

$marketplace->findByFilters([
    'category' => $category,
    'product_type' => $productType,
    'condition' => $condition
]);

// ❌ Bad: Lines too long
$stmt = $this->db->prepare("SELECT id, title, price, category, condition_type, stock_quantity, created_at, updated_at FROM products WHERE category = ? AND status = ? ORDER BY created_at DESC LIMIT ?");
```

---

## Adding New Features

### Step-by-Step Process

#### 1. Plan the Feature

Define what you're building:
- Feature name (e.g., "User Notifications")
- Database tables needed
- Controllers required
- Views/pages needed
- Routes needed

#### 2. Create Database Tables

**File**: `sql/[module]/[feature].sql`

```sql
-- sql/notifications/create-notifications.sql
USE reidhub;

CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user (user_id),
  INDEX idx_is_read (is_read)
);
```

#### 3. Create Model

**File**: `app/models/Notification.php`

```php
<?php

/**
 * Notification Model
 * Handles notification data operations
 */
class Notification extends Model
{
    protected $table = 'notifications';

    /**
     * Get unread notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnreadByUser(int $userId, int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE user_id = ? AND is_read = FALSE 
                    ORDER BY created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error("getUnreadByUser error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET is_read = TRUE WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notificationId]);
        } catch (Throwable $e) {
            Logger::error("markAsRead error: " . $e->getMessage());
            return false;
        }
    }
}
```

#### 4. Create Controller

**File**: `app/controllers/Notifications/NotificationsController.php`

```php
<?php

/**
 * NotificationsController
 * 
 * Handles user notifications
 */
class Notifications_NotificationsController extends Controller
{
    /**
     * Show notifications page
     * 
     * GET /dashboard/notifications
     */
    public function showNotifications()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            header('Location: /login', true, 303);
            exit;
        }

        try {
            $notification = new Notification();
            $notifications = $notification->getByUser($userId);

            $this->viewApp(
                'User/notifications-view',
                ['notifications' => $notifications],
                'Notifications'
            );
        } catch (Throwable $e) {
            Logger::error("Error showing notifications: " . $e->getMessage());
            $this->viewApp('User/notifications-view', ['notifications' => []], 'Notifications');
        }
    }

    /**
     * Mark notification as read (API)
     * 
     * POST /dashboard/notifications/mark-read
     */
    public function markAsRead()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isAjax()) {
            http_response_code(405);
            exit;
        }

        try {
            $notificationId = (int)($_POST['notification_id'] ?? 0);
            
            if (!$notificationId) {
                http_response_code(422);
                echo json_encode(['ok' => false, 'message' => 'Invalid notification ID']);
                return;
            }

            $notification = new Notification();
            $success = $notification->markAsRead($notificationId);

            header('Content-Type: application/json');
            echo json_encode(['ok' => $success]);
        } catch (Throwable $e) {
            Logger::error("markAsRead error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false]);
        }
    }

    /**
     * Check if request is AJAX
     */
    private function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}
```

#### 5. Create Views

**File**: `app/views/User/notifications-view.php`

```php
<div class="notifications-container">
    <h1>Notifications</h1>
    
    <?php if (empty($notifications)): ?>
        <p class="empty-state">No notifications yet</p>
    <?php else: ?>
        <div class="notifications-list">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item" data-id="<?= $notification['id'] ?>">
                    <h3><?= htmlspecialchars($notification['title']) ?></h3>
                    <p><?= htmlspecialchars($notification['message']) ?></p>
                    <time><?= date('M d, Y H:i', strtotime($notification['created_at'])) ?></time>
                    
                    <?php if (!$notification['is_read']): ?>
                        <button class="mark-read-btn" data-id="<?= $notification['id'] ?>">
                            Mark as Read
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
```

#### 6. Create CSS

**File**: `public/css/app/user/notifications.css`

```css
/* ==========================================================================
   Notifications
   ========================================================================== */

.notifications-container {
    padding: 24px;
    max-width: 800px;
}

.notifications-list {
    display: grid;
    gap: 16px;
}

.notification-item {
    background: var(--surface);
    border-radius: 8px;
    padding: 16px;
    box-shadow: var(--card-shadow);
    transition: background-color var(--transition-fast);
}

.notification-item:hover {
    background: var(--surface-hover);
}

.notification-item h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
}

.notification-item time {
    font-size: 12px;
    color: var(--text-muted);
}
```

#### 7. Create JavaScript

**File**: `public/js/app/notifications.js`

```javascript
/**
 * Notifications
 * Handle notification interactions
 */
class Notifications {
    constructor() {
        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
    }

    cacheDOM() {
        this.markReadButtons = document.querySelectorAll('.mark-read-btn');
    }

    bindEvents() {
        this.markReadButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleMarkAsRead(e));
        });
    }

    async handleMarkAsRead(event) {
        const button = event.target;
        const notificationId = button.dataset.id;

        try {
            const response = await fetch('/dashboard/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `notification_id=${notificationId}`
            });

            if (response.ok) {
                const item = button.closest('.notification-item');
                item.classList.add('read');
                button.remove();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Notifications();
});
```

#### 8. Add Routes

**File**: `app/routes/web.php`

```php
// Notifications routes
$routes['/dashboard/notifications'] = [
    'GET' => 'Notifications_NotificationsController@showNotifications'
];

$routes['/dashboard/notifications/mark-read'] = [
    'POST' => 'Notifications_NotificationsController@markAsRead'
];
```

#### 9. Include in Layout

Add to `app/views/layout.php`:

```php
<link rel="stylesheet" href="/css/app/user/notifications.css">
```

At end of layout:

```php
<script type="module" src="/js/app/notifications.js"></script>
```

---

## File Organization

### When to Create New Files

1. **Controllers**: One controller per module feature (or split into User/Admin variants)
2. **Models**: One model per database table/entity
3. **Views**: One view per page (group related pages in subfolders)
4. **CSS**: One CSS file per feature (matches view structure)
5. **JavaScript**: One JS file per feature (matches view structure)

### File Size Guidelines

- **Controllers**: Should not exceed 500 lines (split if needed)
- **Models**: Should not exceed 400 lines (separate concerns)
- **Views**: Should not exceed 300 lines (break into partials)
- **CSS**: Should not exceed 500 lines (split by feature)
- **JavaScript**: Should not exceed 400 lines (split logic)

**Breaking Down Large Files**:

```php
// ❌ Bad: One large controller
class UserController extends Controller {
    // 600 lines of code mixing different features
}

// ✅ Good: Split by feature
class User_ProfileController extends Controller {
    // 200 lines: user profile operations
}

class User_SettingsController extends Controller {
    // 200 lines: user settings operations
}
```

---

## Comment & Documentation

### File Headers

```php
<?php

/**
 * File: UserController.php
 * 
 * Purpose: Handle user authentication and account management
 * 
 * Classes:
 *   - Auth_LoginController
 * 
 * Dependencies:
 *   - User model
 *   - Logger
 */
```

### Class Documentation

```php
/**
 * MarketPlace Model
 * 
 * Purpose: CRUD operations for marketplace products
 * 
 * Table: products
 * 
 * Responsibilities:
 *   - Create, read, update, delete products
 *   - Filter products by category, condition, price
 *   - Manage seller inventory
 *   - Archive/restore products
 * 
 * Usage:
 *   $marketplace = new MarketPlace();
 *   $products = $marketplace->findByCategory('merchandise');
 */
class MarketPlace extends Model
{
    //...
}
```

### Method Documentation

```php
/**
 * Find products by multiple filters
 * 
 * @param array $filters [
 *   'category' => 'merchandise|second-hand',
 *   'product_type' => 'apparel|accessories|...',
 *   'condition_type' => 'brand_new|used',
 *   'min_price' => 100,
 *   'max_price' => 5000,
 *   'status' => 'active|archived'
 * ]
 * @param int $limit Number of results to return
 * @param int $offset Result offset for pagination
 * 
 * @return array Array of products or empty array
 * 
 * @throws InvalidArgumentException If filters are invalid
 * 
 * @example
 * $products = $mp->findByFilters([
 *   'category' => 'merchandise',
 *   'condition_type' => 'brand_new'
 * ], 20, 0);
 */
public function findByFilters(array $filters, int $limit = 20, int $offset = 0): array
{
    //...
}
```

### Inline Comments

```php
// ✅ Good: Explains WHY, not WHAT
if ($user && password_verify($password, $user['password'])) {
    // Hash comparison successful; create authenticated session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}

// ❌ Bad: States obvious WHAT
if ($user && password_verify($password, $user['password'])) {
    // Check if user exists and password matches
    session_regenerate_id(true);  // Regenerate session
    $_SESSION['user_id'] = $user['id'];  // Set user ID
}
```

---

## Error Handling

### Try-Catch Pattern

```php
public function createProduct(array $data): int|false
{
    try {
        // Validate input
        if (empty($data['title']) || empty($data['price'])) {
            throw new InvalidArgumentException('Title and price required');
        }

        // Execute operation
        Logger::info("Creating product: " . $data['title']);
        
        $stmt = $this->db->prepare(
            "INSERT INTO products (title, price, seller_id) VALUES (?, ?, ?)"
        );
        
        $success = $stmt->execute([
            $data['title'],
            $data['price'],
            $data['seller_id']
        ]);

        if (!$success) {
            Logger::error("Failed to create product: " . implode(' | ', $stmt->errorInfo()));
            return false;
        }

        $id = (int)$this->db->lastInsertId();
        Logger::info("Product created with ID: $id");
        
        return $id;
    } catch (InvalidArgumentException $e) {
        Logger::warning("Validation error: " . $e->getMessage());
        return false;
    } catch (Throwable $e) {
        Logger::error("Unexpected error creating product: " . $e->getMessage());
        return false;
    }
}
```

### User-Facing Errors

```php
public function checkout()
{
    try {
        // Validate session
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode([
                'ok' => false, 
                'message' => 'Please log in to proceed'
            ]);
            return;
        }

        // Validate cart
        $cart = new Cart();
        $items = $cart->getByUser($userId);
        
        if (empty($items)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Your cart is empty'
            ]);
            return;
        }

        // Process checkout
        // ...

    } catch (Throwable $e) {
        Logger::error("Checkout error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'message' => 'An error occurred. Please try again.'
        ]);
    }
}
```

---

## Database Operations

### Query Best Practices

```php
// ✅ Good: Prepared statements, clear intent
$stmt = $this->db->prepare(
    "SELECT id, title, price FROM products 
     WHERE category = ? AND status = ? 
     ORDER BY created_at DESC 
     LIMIT ?"
);
$stmt->execute([$category, 'active', $limit]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ❌ Bad: String concatenation, SQL injection risk
$query = "SELECT * FROM products WHERE category = '$category' LIMIT $limit";
$products = $this->db->query($query)->fetchAll();

// ✅ Good: Use table property to avoid hardcoding
$sql = "DELETE FROM {$this->table} WHERE status = 'archived'";

// ❌ Bad: Hardcoded table name
$sql = "DELETE FROM products WHERE status = 'archived'";
```

### Batch Operations

```php
// ✅ Good: Batch insert for performance
public function createMultiple(array $records): bool
{
    try {
        $placeholders = implode(', ', array_fill(0, count($records), '(?, ?, ?)'));
        $values = [];
        
        foreach ($records as $record) {
            $values[] = $record['user_id'];
            $values[] = $record['product_id'];
            $values[] = $record['quantity'];
        }

        $sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES $placeholders";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    } catch (Throwable $e) {
        Logger::error("Batch insert error: " . $e->getMessage());
        return false;
    }
}
```

### Pagination

```php
// ✅ Good: Implements pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM products WHERE status = 'active' LIMIT ? OFFSET ?";
$stmt = $this->db->prepare($sql);
$stmt->execute([$perPage, $offset]);
$products = $stmt->fetchAll();

// Get total count for pagination UI
$count = $this->countActive();
$totalPages = ceil($count / $perPage);
```

---

## Security Best Practices

### Input Validation

```php
// ✅ Good: Validate all inputs
public function updateProfile(array $data): bool
{
    // Validate email format
    if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Invalid email format');
    }

    // Validate price is positive number
    $price = filter_var($data['price'] ?? '', FILTER_VALIDATE_FLOAT, [
        'options' => ['min_range' => 0]
    ]);
    if ($price === false) {
        throw new InvalidArgumentException('Price must be positive number');
    }

    // Validate string length
    if (strlen($data['title'] ?? '') < 3 || strlen($data['title']) > 255) {
        throw new InvalidArgumentException('Title must be 3-255 characters');
    }

    // Proceed with validated data
    //...
}
```

### Output Escaping

```php
<!-- ✅ Good: Escape output -->
<h1><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></h1>
<p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>

<!-- ❌ Bad: Unescaped output (XSS vulnerability) -->
<h1><?= $product['title'] ?></h1>
<p><?= $product['description'] ?></p>
```

### Session Management

```php
// ✅ Good: Session security
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'use_strict_mode' => 1,
        'use_only_cookies' => 1,
        'httponly' => true,
        'secure' => true  // HTTPS only in production
    ]);
}

// Always regenerate session after login
if ($userAuthenticated) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
}

// Validate session on protected pages
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /login', true, 303);
    exit;
}
```

### Password Management

```php
// ✅ Good: Use bcrypt
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

// Never store plain passwords or use weak hashing
// ❌ Bad
$hashedPassword = md5($password);
$hashedPassword = sha1($password);

// ✅ Good: Verify password
if (password_verify($inputPassword, $storedHash)) {
    // Password is correct
}

// ✅ Good: Rehash if algorithm changed
if (password_needs_rehash($storedHash, PASSWORD_BCRYPT)) {
    $newHash = password_hash($inputPassword, PASSWORD_BCRYPT);
    // Update database with new hash
}
```

---

## Code Review Checklist

Before submitting code for review, ensure:

### Naming & Structure
- [ ] Class names follow `Module_FeatureController` pattern
- [ ] Method names are descriptive verbs in camelCase
- [ ] Variables use meaningful camelCase names
- [ ] No abbreviations or single-letter variables (except loop counters)
- [ ] File names match class names exactly
- [ ] Files in correct folder structure

### PHP Code Quality
- [ ] No syntax errors (lint check)
- [ ] 4-space indentation throughout
- [ ] Lines under 100 characters (break long lines)
- [ ] Opening braces on same line: `function name() {`
- [ ] Use type hints where possible: `function name(int $id): bool`

### Documentation
- [ ] File header comments describing purpose
- [ ] Class documentation with purpose and table
- [ ] Method docblocks with @param, @return, @throws
- [ ] Inline comments explaining "why", not "what"
- [ ] No commented-out code left in

### Database
- [ ] All queries use prepared statements
- [ ] Foreign keys have ON DELETE rules
- [ ] Indexes on frequently queried columns
- [ ] Transaction handling for multi-step operations
- [ ] Proper error handling and logging

### Error Handling
- [ ] Try-catch blocks around risky operations
- [ ] User-friendly error messages (JSON for APIs)
- [ ] All errors logged with Logger class
- [ ] HTTP status codes appropriate (401, 422, 500, etc.)

### Security
- [ ] All user input validated
- [ ] Output escaped with htmlspecialchars()
- [ ] Session checks on protected routes
- [ ] No SQL injection vulnerabilities
- [ ] No hardcoded credentials or secrets

### Testing
- [ ] Manual testing of happy path
- [ ] Manual testing of error cases
- [ ] Works in production-like environment
- [ ] No console errors in browser DevTools
- [ ] Database queries are performant

### Performance
- [ ] No N+1 query problems
- [ ] LIMIT used on list queries
- [ ] Pagination implemented for large datasets
- [ ] Indexes created for filtering/sorting
- [ ] No unnecessary database queries

---

## Summary

ReidHub's code standards emphasize:
- **Consistency** across the entire codebase
- **Clarity** in naming and organization
- **Security** through validation and prepared statements
- **Maintainability** with proper documentation and structure
- **Performance** through efficient queries and pagination

Following these standards ensures new developers can quickly understand the codebase and contribute effectively, while maintaining high code quality as ReidHub grows.
