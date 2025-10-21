<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$showDashboardNav = str_starts_with($path, '/dashboard');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isAdmin = isset($_SESSION['admin_id']);

// Check if user is in seller mode
$isSellerMode = str_contains($path, '/seller/');

// User sidebar items (buyer mode)
$userItems = [
    [ 
        'label' => 'Edu Hub', 
        'href' => '/dashboard/edu-hub', 
        'icon' => 'edu',
        'children' => [
            ['label' => 'All Questions', 'href' => '/dashboard/forum/all'],
            ['label' => 'Ask Question', 'href' => '/dashboard/forum/add'],
        ]
    ],
    [ 
        'label' => 'Community', 
        'href' => '/dashboard/community', 
        'icon' => 'community',
        'children' => [
            ['label' => 'Forum', 'href' => '/dashboard/community/forum'],
            ['label' => 'Events', 'href' => '/dashboard/community/events'],
            ['label' => 'Groups', 'href' => '/dashboard/community/groups'],
            ['label' => 'Chat', 'href' => '/dashboard/community/chat'],
        ]
    ],
    [ 
        'label' => 'Marketplace', 
        'href' => '/dashboard/marketplace', 
        'icon' => 'market',
        'children' => [
            ['label' => 'Merch Store', 'href' => '/dashboard/marketplace/merch-store'],
            ['label' => 'My Cart', 'href' => '/dashboard/marketplace/my-cart'],
            ['label' => 'My Orders', 'href' => '/dashboard/marketplace/orders'],
        ]
    ],
    [ 
        'label' => 'Lost & Found', 
        'href' => '/dashboard/lost-and-found', 
        'icon' => 'lost',
        'children' => [
            ['label' => 'Report Lost Item', 'href' => '/dashboard/lost-and-found/report-lost'],
            ['label' => 'Report Found Item', 'href' => '/dashboard/lost-and-found/report-found'],
            ['label' => 'Browse Items', 'href' => '/dashboard/lost-and-found/browse'],
            ['label' => 'My Reports', 'href' => '/dashboard/lost-and-found/my-reports']
        ]
    ],
];

// Seller sidebar items
$sellerItems = [
    [ 
        'label' => 'Edu Hub', 
        'href' => '/dashboard/edu-hub', 
        'icon' => 'edu',
        'children' => [
            ['label' => 'My Courses', 'href' => '/dashboard/edu-hub/courses'],
            ['label' => 'Assignments', 'href' => '/dashboard/edu-hub/assignments'],
            ['label' => 'Timetable', 'href' => '/dashboard/edu-hub/timetable'],
            ['label' => 'Results', 'href' => '/dashboard/edu-hub/results'],
        ]
    ],
    [ 
        'label' => 'Community', 
        'href' => '/dashboard/community', 
        'icon' => 'community',
        'children' => [
            ['label' => 'Forum', 'href' => '/dashboard/community/forum'],
            ['label' => 'Events', 'href' => '/dashboard/community/events'],
            ['label' => 'Groups', 'href' => '/dashboard/community/groups'],
            ['label' => 'Chat', 'href' => '/dashboard/community/chat'],
        ]
    ],
    [ 
        'label' => 'Seller Portal', 
        'href' => '/dashboard/marketplace/seller', 
        'icon' => 'seller',
        'children' => [
            ['label' => 'Analytics', 'href' => '/dashboard/marketplace/seller/analytics'],
            ['label' => 'Add Items', 'href' => '/dashboard/marketplace/seller/add'],
            ['label' => 'Active Items', 'href' => '/dashboard/marketplace/seller/active'],
            ['label' => 'Archived Items', 'href' => '/dashboard/marketplace/seller/archived'],
            ['label' => 'Orders', 'href' => '/dashboard/marketplace/seller/orders'],
        ]
    ],
    [ 
        'label' => 'Lost & Found', 
        'href' => '/dashboard/lost-and-found', 
        'icon' => 'lost',
        'children' => [
            ['label' => 'Report Lost Item', 'href' => '/dashboard/lost-and-found/report-lost'],
            ['label' => 'Report Found Item', 'href' => '/dashboard/lost-and-found/report-found'],
            ['label' => 'Browse Items', 'href' => '/dashboard/lost-and-found/browse'],
            ['label' => 'My Reports', 'href' => '/dashboard/lost-and-found/my-reports']
        ]
    ],
];

// Admin sidebar items (flat)
$adminItems = [
    [ 'label' => 'Edu Hub',             'href' => '/dashboard/edu-hub',         'icon' => 'edu' ],
    [ 'label' => 'Community & Social',  'href' => '/dashboard/community',       'icon' => 'community' ],
    [ 'label' => 'Marketplace',         'href' => '/dashboard/marketplace',     'icon' => 'market' ],
    [ 'label' => 'Lost & Found',        'href' => '/dashboard/lost-and-found',  'icon' => 'lost' ],
];

function is_active(string $href, string $path): bool {
    $h = rtrim($href, '/'); $p = rtrim($path, '/');
    return $p === $h || ($h !== '/dashboard' && str_starts_with($p, $h . '/'));
}
function has_active_child(array $children, string $path): bool {
    foreach ($children as $child) {
        if (is_active($child['href'], $path)) {
            return true;
        }
    }
    return false;
}
function svg_icon(string $name): string {
    switch ($name) {
        case 'edu':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 1 8l11 5 8-3.7V15h2V8L12 3zm-6 9.5V15c0 2.5 3.6 4.5 6 4.5s6-2 6-4.5v-2.5l-6 2.5-6-2.5z"/></svg>';
        case 'community':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm10 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6ZM7 13c2.67 0 8 1.34 8 4v2H-1v-2c0-2.66 5.33-4 8-4Zm9-0.95A8.2 8.2 0 0 1 20 16v2h-4v-2c0-.7-.1-1.3-.3-1.95Z"/></svg>';
        case 'market':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6"/></svg>';
        case 'lost':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 2a8 8 0 1 1-5.3 14l-2.8 2.8 1.4 1.4 2.8-2.8A8 8 0 0 1 10 2Zm0 2a6 6 0 1 0 6 6 6 6 0 0 0-6-6Z"/></svg>';
        case 'logout':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>';
        case 'seller':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        case 'buyer':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6"/></svg>';
        case 'chevron':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>';
        default:
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>';
    }
}

// Choose the appropriate items based on mode
$currentItems = $isSellerMode ? $sellerItems : $userItems;
?>
<!-- Sidebar Navigation -->
<aside class="forum-sidebar" role="navigation" aria-label="Main navigation">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon" aria-hidden="true"></div>
        <span class="sidebar-brand-text">ReidHub</span>
    </div>
    <nav>
        <ul class="sidebar-nav" role="list">
            <?php if ($showDashboardNav): ?>
                <?php if ($isAdmin): ?>
                    <?php foreach ($adminItems as $it): ?>
                        <?php $active = is_active($it['href'], $path); ?>
                        <li class="sidebar-nav-item">
                            <a class="sidebar-nav-link<?php echo $active ? ' is-active' : ''; ?>"
                               href="<?php echo htmlspecialchars($it['href']); ?>"
                               <?php echo $active ? 'aria-current="page"' : ''; ?>>
                                <span class="sidebar-nav-icon"><?php echo svg_icon($it['icon']); ?></span>
                                <span class="sidebar-link-text"><?php echo htmlspecialchars($it['label']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($currentItems as $it): ?>
                        <?php 
                            $active = is_active($it['href'], $path); 
                            $hasChildren = !empty($it['children']);
                            $hasActiveChild = $hasChildren && has_active_child($it['children'], $path);
                            $isExpanded = $active || $hasActiveChild;
                            $isMarketplaceSection = ($isSellerMode && $it['label'] === 'Seller Portal') || (!$isSellerMode && $it['label'] === 'Marketplace');
                        ?>
                        <li class="sidebar-nav-item">
                            <?php if ($hasChildren): ?>
                                <button class="sidebar-nav-link sidebar-nav-toggle<?php echo ($active || $hasActiveChild) ? ' is-active' : ''; ?>"
                                        data-toggle="dropdown"
                                        aria-expanded="<?php echo $isExpanded ? 'true' : 'false'; ?>">
                                    <span class="sidebar-nav-icon"><?php echo svg_icon($it['icon']); ?></span>
                                    <span class="sidebar-link-text"><?php echo htmlspecialchars($it['label']); ?></span>
                                    <span class="sidebar-nav-chevron<?php echo $isExpanded ? ' is-expanded' : ''; ?>">
                                        <?php echo svg_icon('chevron'); ?>
                                    </span>
                                </button>
                                <ul class="sidebar-submenu<?php echo $isExpanded ? ' is-expanded' : ''; ?>" role="list">
                                    <?php foreach ($it['children'] as $child): ?>
                                        <?php $childActive = is_active($child['href'], $path); ?>
                                        <li class="sidebar-submenu-item">
                                            <a class="sidebar-submenu-link<?php echo $childActive ? ' is-active' : ''; ?>"
                                               href="<?php echo htmlspecialchars($child['href']); ?>"
                                               <?php echo $childActive ? 'aria-current="page"' : ''; ?>>
                                                <?php echo htmlspecialchars($child['label']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    
                                    <?php if ($isMarketplaceSection): ?>
                                        <!-- Account Switch Button -->
                                        <li class="sidebar-submenu-item">
                                            <?php if ($isSellerMode): ?>
                                                <a class="sidebar-submenu-link sidebar-account-switch" 
                                                   href="/dashboard/marketplace/merch-store"
                                                   title="Switch to Buyer Account">
                                                    <span class="account-switch-icon"><?php echo svg_icon('buyer'); ?></span>
                                                    <span class="account-switch-text">Switch to Buyer Account</span>
                                                </a>
                                            <?php else: ?>
                                                <a class="sidebar-submenu-link sidebar-account-switch" 
                                                   href="/dashboard/marketplace/seller/analytics"
                                                   title="Switch to Seller Account">
                                                    <span class="account-switch-icon"><?php echo svg_icon('seller'); ?></span>
                                                    <span class="account-switch-text">Switch to Seller Account</span>
                                                </a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <a class="sidebar-nav-link<?php echo $active ? ' is-active' : ''; ?>"
                                   href="<?php echo htmlspecialchars($it['href']); ?>"
                                   <?php echo $active ? 'aria-current="page"' : ''; ?>>
                                    <span class="sidebar-nav-icon"><?php echo svg_icon($it['icon']); ?></span>
                                    <span class="sidebar-link-text"><?php echo htmlspecialchars($it['label']); ?></span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <!-- Logout Button at bottom -->
                <li class="sidebar-nav-item" style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: var(--space-md);">
                    <form method="POST" action="/logout" style="margin: 0;">
                        <button type="submit" class="sidebar-nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: var(--space-sm) var(--space-md); border-radius: var(--radius-md); display: flex; align-items: center; gap: var(--space-sm);">
                            <span class="sidebar-nav-icon"><?php echo svg_icon('logout'); ?></span>
                            <span class="sidebar-link-text">Log out</span>
                        </button>
                    </form>
                </li>
            <?php else: ?>
                <li class="sidebar-nav-item">
                    <a href="/forum" class="sidebar-nav-link is-active" aria-current="page">
                        <span class="sidebar-nav-icon"><?php echo svg_icon('community'); ?></span>
                        <span class="sidebar-link-text">Answer Questions</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="/forum/ask" class="sidebar-nav-link">
                        <span class="sidebar-nav-icon"><?php echo svg_icon('edu'); ?></span>
                        <span class="sidebar-link-text">Ask Questions</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>
