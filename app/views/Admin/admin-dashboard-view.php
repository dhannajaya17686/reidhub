
    <link rel="stylesheet" href="/css/app/globals.css">
    <link rel="stylesheet" href="/css/app/admin/admin-dashboard.css">

            
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($a['first_name'] ?? 'Admin'); ?>!</p>
                </div>
                <div class="header-right">
                    <div class="current-time">
                        <span id="current-time"><?php echo date('M j, Y - g:i A'); ?></span>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon users">👥</div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($totalUsersCount ?? 0); ?></div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-change positive">+12%</div>
                        </div>
                    </div>
                    
                    <a href="/dashboard/forum/admin" style="text-decoration: none; color: inherit;">
                        <div class="stat-card">
                            <div class="stat-icon posts">📝</div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($forumPostsCount ?? 0); ?></div>
                                <div class="stat-label">Total Posts</div>
                                <div class="stat-change positive">+8%</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="/dashboard/marketplace/admin/analytics" style="text-decoration: none; color: inherit;">
                        <div class="stat-card">
                            <div class="stat-icon marketplace">🛒</div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($marketplaceCount ?? 0); ?></div>
                                <div class="stat-label">Marketplace Items</div>
                                <div class="stat-change positive">+15%</div>
                            </div>
                        </div>
                    </a>
                    
                    <div class="stat-card">
                        <div class="stat-icon reports">🚨</div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($pendingReportsCount ?? 0); ?></div>
                            <div class="stat-label">Pending Reports</div>
                            <div class="stat-change negative">+5</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                
                <!-- Left Column -->
                <div class="left-column">
                    
                    <!-- Recent Activity -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Activity</h2>
                        </div>
                        <div class="activity-list">
                            <?php 
                            $iconMap = [
                                'user' => 'person_add',
                                'blog' => 'article',
                                'marketplace' => 'shopping_bag',
                                'report' => 'flag',
                                'event' => 'event'
                            ];

                            $classMap = [
                                'user' => 'user-action',
                                'blog' => 'post-action',
                                'marketplace' => 'marketplace-action',
                                'report' => 'report-action',
                                'event' => 'event-action'
                            ];

                            if (!empty($recentActivities)):
                                foreach ($recentActivities as $activity):
                                    $type = $activity['type'];
                                    $data = $activity['data'];
                                    $iconName = $iconMap[$type] ?? 'info';
                                    $class = $classMap[$type] ?? '';
                                    $time = $activity['created_at'];
                                    $timeAgo = getTimeAgo($time);
                            ?>
                            <div class="activity-item">
                                <div class="activity-icon <?php echo $class; ?>">
                                    <span class="material-symbols-outlined"><?php echo $iconName; ?></span>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <?php 
                                        switch($type) {
                                            case 'user':
                                                $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                                                echo "New user <strong>" . htmlspecialchars($fullName ?: 'User') . "</strong> registered";
                                                break;
                                            case 'blog':
                                                $authorName = htmlspecialchars(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                                                echo "New blog post <strong>" . htmlspecialchars($data['title'] ?? 'Untitled') . "</strong> published by <strong>" . $authorName . "</strong>";
                                                break;
                                            case 'marketplace':
                                                echo "New item <strong>" . htmlspecialchars($data['title'] ?? 'Item') . "</strong> listed in marketplace";
                                                break;
                                            case 'report':
                                                echo "New report submitted for review";
                                                break;
                                            case 'event':
                                                $creatorName = htmlspecialchars(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
                                                echo "Event <strong>" . htmlspecialchars($data['title'] ?? 'Event') . "</strong> created by <strong>" . $creatorName . "</strong>";
                                                break;
                                        }
                                        ?>
                                    </div>
                                    <div class="activity-time"><?php echo $timeAgo; ?></div>
                                </div>
                            </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-text">No recent activities</div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- Quick Actions -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Quick Actions</h2>
                        </div>
                        <div class="quick-actions-grid">
                            <a href="/dashboard/users" class="quick-action-btn">
                                <span class="action-icon">
                                    <span class="material-symbols-outlined">people</span>
                                </span>
                                <span class="action-text">Manage Users</span>
                            </a>
                            
                            <a href="/dashboard/forum/admin" class="quick-action-btn">
                                <span class="action-icon">
                                    <span class="material-symbols-outlined">flag</span>
                                </span>
                                <span class="action-text">Review Reports</span>
                            </a>
                            
                            <a href="/dashboard/forum/admin" class="quick-action-btn">
                                <span class="action-icon">
                                    <span class="material-symbols-outlined">forum</span>
                                </span>
                                <span class="action-text">Moderate Posts</span>
                            </a>
                            
                            <a href="/dashboard/marketplace/admin/analytics" class="quick-action-btn">
                                <span class="action-icon">
                                    <span class="material-symbols-outlined">analytics</span>
                                </span>
                                <span class="action-text">View Analytics</span>
                            </a>
                        </div>
                    </section>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    
                    <!-- System Status -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">System Status</h2>
                        </div>
                        <div class="status-list">
                            <div class="status-item">
                                <div class="status-indicator online"></div>
                                <div class="status-content">
                                    <div class="status-name">Server Status</div>
                                    <div class="status-value">Online</div>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator online"></div>
                                <div class="status-content">
                                    <div class="status-name">Database</div>
                                    <div class="status-value">Connected</div>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator warning"></div>
                                <div class="status-content">
                                    <div class="status-name">Storage</div>
                                    <div class="status-value">78% Used</div>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator online"></div>
                                <div class="status-content">
                                    <div class="status-name">Email Service</div>
                                    <div class="status-value">Active</div>
                                </div>
                            </div>
                        </div>
                    </section>


                    <!-- Recent Users -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Users</h2>
                        </div>
                        <div class="user-list">
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <?php 
                                        $initials = getUserInitials($user['first_name'] ?? '', $user['last_name'] ?? '');
                                        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                        $status = 'Active'; // Default status - you can add status field to users table if needed
                                    ?>
                                    <div class="user-item">
                                        <div class="user-avatar"><?php echo $initials; ?></div>
                                        <div class="user-info">
                                            <div class="user-name"><?php echo htmlspecialchars($fullName ?: 'User'); ?></div>
                                            <div class="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                        </div>
                                        <div class="user-status active"><?php echo $status; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="user-item">
                                    <div style="text-align: center; width: 100%; padding: 20px; color: var(--text-secondary);">
                                        No recent users
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </div>
    </div>

    <script src="/js/app/admin/admin-dashboard.js"></script>
