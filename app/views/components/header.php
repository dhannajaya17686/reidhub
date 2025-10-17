<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isAdmin = isset($_SESSION['admin_id']);
$u = $user ?? ($_SESSION['user'] ?? null);
$displayName = $u['first_name'] ?? $u['email'] ?? 'User';
?>
<!-- Main Header -->
        <header class="forum-header" role="banner">
            <div class="header-content">
                <!-- Greeting and Logout -->
                <div class="header-greeting">
                    <?php if ($isAdmin): ?>
                        <strong class="welcome-name">Welcome Students Union!</strong>
                    <?php else: ?>
                        <span class="welcome-prefix">Welcome,</span>
                        <strong class="welcome-name"><?php echo htmlspecialchars($displayName); ?></strong>
                    <?php endif; ?>
                </div>

                <?php if (!$isAdmin): ?>
                <!-- Search -->
                <div class="header-search">
                    <label for="search" class="sr-only">Search questions and topics</label>
                    <input 
                        type="search" 
                        id="search" 
                        class="search-input" 
                        placeholder="Search"
                        autocomplete="off"
                    >
                    <svg class="search-icon" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- User Actions -->
                <div class="header-actions">
                    <button class="notification-btn" aria-label="Notifications">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </button>
                    <div class="user-avatar">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' fill='%230466C8'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='16' font-weight='bold'%3EU%3C/text%3E%3C/svg%3E" alt="User avatar">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </header>