<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$showDashboardNav = str_starts_with($path, '/dashboard');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isAdmin = isset($_SESSION['admin_id']);

// Check if user is in seller mode
$isSellerMode = str_contains($path, '/seller/');
// Check if user is in club admin mode
$isClubAdminMode = str_contains($path, '/club-admin/');

// User sidebar items (buyer mode)
$userItems = [
    [ 
        'label' => 'Dashboard', 
        'href' => '/dashboard/user', 
        'icon' => 'dashboard' 
    ],
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
            ['label' => 'Clubs', 'href' => '/dashboard/community/clubs'],
            ['label' => 'Events', 'href' => '/dashboard/community/events'],
            ['label' => 'Blogs', 'href' => '/dashboard/community/blogs'],
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
            ['label' => 'My Transactions', 'href' => '/dashboard/marketplace/transactions']
        ]
    ],
    [ 
        'label' => 'Lost & Found', 
        'href' => '/dashboard/lost-and-found', 
        'icon' => 'lost',
        'children' => [
            ['label' => 'Report Lost Item', 'href' => '/dashboard/lost-and-found/report-lost-item'],
            ['label' => 'Report Found Item', 'href' => '/dashboard/lost-and-found/report-found-item'],
            ['label' => 'Lost & Found Items', 'href' => '/dashboard/lost-and-found/items']
        ]
    ],
];

// Seller sidebar items
$sellerItems = [
    [ 
        'label' => 'Dashboard', 
        'href' => '/dashboard/user', 
        'icon' => 'dashboard' 
    ],
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
            ['label' => 'Clubs', 'href' => '/dashboard/community/clubs'],
            ['label' => 'Events', 'href' => '/dashboard/community/events'],
            ['label' => 'Blogs', 'href' => '/dashboard/community/blogs'],
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
            ['label' => 'Report Lost Item', 'href' => '/dashboard/lost-and-found/report-lost-item'],
            ['label' => 'Report Found Item', 'href' => '/dashboard/lost-and-found/report-found-item'],
        ]
    ],
];

// Club Admin sidebar items
$clubAdminItems = [
    [ 
        'label' => 'Dashboard', 
        'href' => '/dashboard/user', 
        'icon' => 'dashboard' 
    ],
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
        'label' => 'Club Admin Portal', 
        'href' => '/dashboard/club-admin', 
        'icon' => 'club-admin',
        'children' => [
            ['label' => 'Dashboard', 'href' => '/dashboard/club-admin/dashboard'],
            ['label' => 'Members', 'href' => '/dashboard/club-admin/members'],
            ['label' => 'Events', 'href' => '/dashboard/club-admin/events'],
            ['label' => 'Announcements', 'href' => '/dashboard/club-admin/announcements'],
            ['label' => 'Applications', 'href' => '/dashboard/club-admin/applications'],
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
            ['label' => 'My Transactions', 'href' => '/dashboard/marketplace/transactions']
        ]
    ],
    [ 
        'label' => 'Lost & Found', 
        'href' => '/dashboard/lost-and-found', 
        'icon' => 'lost',
        'children' => [
            ['label' => 'Report Lost Item', 'href' => '/dashboard/lost-and-found/report-lost-item'],
            ['label' => 'Report Found Item', 'href' => '/dashboard/lost-and-found/report-found-item'],
            ['label' => 'Lost & Found Items', 'href' => '/dashboard/lost-and-found/items']
        ]
    ],
];

// Admin sidebar items (with dropdown support)
$adminItems = [
    [ 
        'label' => 'Dashboard', 
        'href' => '/dashboard/admin', 
        'icon' => 'dashboard' 
    ],
    [ 
        'label' => 'Forum', 
        'href' => '/dashboard/forum/admin', 
        'icon' => 'edu'
    ],
    [ 
        'label' => 'Community', 
        'href' => '/dashboard/community/admin', 
        'icon' => 'community',
        
    ],
    [ 
        'label' => 'Marketplace', 
        'href' => '/dashboard/marketplace/admin', 
        'icon' => 'market',
        'children' => [
            ['label' => 'Analytics', 'href' => '/dashboard/marketplace/admin/analytics'],
            ['label' => 'Reported Items', 'href' => '/dashboard/marketplace/admin/reported'],
        ]
    ],
    [ 
        'label' => 'Lost & Found', 
        'href' => '/dashboard/lost-and-found/admin', 
        'icon' => 'lost',
    ],
    /*[ 
        'label' => 'User Management', 
        'href' => '/dashboard/admin/users', 
        'icon' => 'users',
        'children' => [
            ['label' => 'All Users', 'href' => '/dashboard/admin/users/all'],
            ['label' => 'User Roles', 'href' => '/dashboard/admin/users/roles'],
            ['label' => 'Banned Users', 'href' => '/dashboard/admin/users/banned'],
            ['label' => 'Registration Requests', 'href' => '/dashboard/admin/users/requests'],
        ]
    ],
    [ 
        'label' => 'System Settings', 
        'href' => '/dashboard/admin/settings', 
        'icon' => 'settings',
        'children' => [
            ['label' => 'General Settings', 'href' => '/dashboard/admin/settings/general'],
            ['label' => 'Security Settings', 'href' => '/dashboard/admin/settings/security'],
            ['label' => 'Email Templates', 'href' => '/dashboard/admin/settings/email'],
            ['label' => 'Backup & Maintenance', 'href' => '/dashboard/admin/settings/maintenance'],
        ]
    ],
    */
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
        case 'dashboard':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>';
        case 'edu':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 1 8l11 5 8-3.7V15h2V8L12 3zm-6 9.5V15c0 2.5 3.6 4.5 6 4.5s6-2 6-4.5v-2.5l-6 2.5-6-2.5z"/></svg>';
        case 'community':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm10 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6ZM7 13c2.67 0 8 1.34 8 4v2H-1v-2c0-2.66 5.33-4 8-4Zm9-0.95A8.2 8.2 0 0 1 20 16v2h-4v-2c0-.7-.1-1.3-.3-1.95Z"/></svg>';
        case 'market':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6"/></svg>';
        case 'lost':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 2a8 8 0 1 1-5.3 14l-2.8 2.8 1.4 1.4 2.8-2.8A8 8 0 0 1 10 2Zm0 2a6 6 0 1 0 6 6 6 6 0 0 0-6-6Z"/></svg>';
        case 'users':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
        case 'settings':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.82,11.69,4.82,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></svg>';
        case 'logout':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>';
        case 'seller':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        case 'buyer':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6"/></svg>';
        case 'club-admin':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        case 'club-user':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm10 0a3 3 0 1 1 0-6 3 3 0 0 1 0 6ZM7 13c2.67 0 8 1.34 8 4v2H-1v-2c0-2.66 5.33-4 8-4Zm9-0.95A8.2 8.2 0 0 1 20 16v2h-4v-2c0-.7-.1-1.3-.3-1.95Z"/></svg>';
        case 'chevron':
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>';
        default:
            return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>';
    }
}

// Choose the appropriate items based on user type and mode
if ($isAdmin) {
    $currentItems = $adminItems;
} elseif ($isClubAdminMode) {
    $currentItems = $clubAdminItems;
} elseif ($isSellerMode) {
    $currentItems = $sellerItems;
} else {
    $currentItems = $userItems;
}
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
                <?php foreach ($currentItems as $it): ?>
                    <?php 
                        $active = is_active($it['href'], $path); 
                        $hasChildren = !empty($it['children']);
                        $hasActiveChild = $hasChildren && has_active_child($it['children'], $path);
                        $isExpanded = $active || $hasActiveChild;
                        $isMarketplaceSection = !$isAdmin && !$isClubAdminMode && (($isSellerMode && $it['label'] === 'Seller Portal') || (!$isSellerMode && $it['label'] === 'Marketplace'));
                        $isCommunitySection = !$isAdmin && !$isSellerMode && $it['label'] === 'Community';
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
                                    <!-- Account Switch Button for marketplace -->
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

                                <?php if ($isCommunitySection): ?>
                                    <!-- Club Admin Switch Button for community -->
                                    <li class="sidebar-submenu-item">
                                        <?php if ($isClubAdminMode): ?>
                                            <a class="sidebar-submenu-link sidebar-account-switch" 
                                               href="/dashboard/community/clubs"
                                               title="Switch to Regular User">
                                                <span class="account-switch-icon"><?php echo svg_icon('club-user'); ?></span>
                                                <span class="account-switch-text">Switch to Regular User</span>
                                            </a>
                                        <?php else: ?>
                                            <a class="sidebar-submenu-link sidebar-account-switch" 
                                               href="/dashboard/club-admin/dashboard"
                                               title="Switch to Club Admin">
                                                <span class="account-switch-icon"><?php echo svg_icon('club-admin'); ?></span>
                                                <span class="account-switch-text">Switch to Club Admin</span>
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
