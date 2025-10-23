
    <link rel="stylesheet" href="/css/app/globals.css">
    <link rel="stylesheet" href="/css/app/admin/admin-dashboard.css">

            
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($a['first_name'] ?? 'Admin'); ?>!</p>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn notification-btn">
                            <span class="icon">🔔</span>
                            <span class="badge">3</span>
                        </button>
                        <button class="action-btn settings-btn">
                            <span class="icon">⚙️</span>
                        </button>
                    </div>
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
                            <div class="stat-number">1,247</div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-change positive">+12%</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon posts">📝</div>
                        <div class="stat-content">
                            <div class="stat-number">856</div>
                            <div class="stat-label">Total Posts</div>
                            <div class="stat-change positive">+8%</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon marketplace">🛒</div>
                        <div class="stat-content">
                            <div class="stat-number">342</div>
                            <div class="stat-label">Marketplace Items</div>
                            <div class="stat-change positive">+15%</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon reports">🚨</div>
                        <div class="stat-content">
                            <div class="stat-number">23</div>
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
                            <button class="view-all-btn">View All</button>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon user-action">👤</div>
                                <div class="activity-content">
                                    <div class="activity-text">New user <strong>John Doe</strong> registered</div>
                                    <div class="activity-time">2 minutes ago</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon post-action">📝</div>
                                <div class="activity-content">
                                    <div class="activity-text">New blog post published by <strong>Alice Smith</strong></div>
                                    <div class="activity-time">15 minutes ago</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon marketplace-action">🛒</div>
                                <div class="activity-content">
                                    <div class="activity-text">New item listed in marketplace</div>
                                    <div class="activity-time">1 hour ago</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon report-action">🚨</div>
                                <div class="activity-content">
                                    <div class="activity-text">New report submitted for review</div>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon event-action">📅</div>
                                <div class="activity-content">
                                    <div class="activity-text">Event <strong>Programming Workshop</strong> created</div>
                                    <div class="activity-time">3 hours ago</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Quick Actions -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Quick Actions</h2>
                        </div>
                        <div class="quick-actions-grid">
                            <button class="quick-action-btn">
                                <span class="action-icon">👥</span>
                                <span class="action-text">Manage Users</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <span class="action-icon">🚨</span>
                                <span class="action-text">Review Reports</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <span class="action-icon">📝</span>
                                <span class="action-text">Moderate Posts</span>
                            </button>
                            
                            <button class="quick-action-btn">
                                <span class="action-icon">📊</span>
                                <span class="action-text">View Analytics</span>
                            </button>
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

                    <!-- Pending Tasks -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Pending Tasks</h2>
                            <span class="task-count">7</span>
                        </div>
                        <div class="task-list">
                            <div class="task-item high-priority">
                                <div class="task-content">
                                    <div class="task-title">Review flagged content</div>
                                    <div class="task-meta">3 items • High Priority</div>
                                </div>
                                <div class="task-priority high"></div>
                            </div>
                            
                            <div class="task-item medium-priority">
                                <div class="task-content">
                                    <div class="task-title">Approve marketplace listings</div>
                                    <div class="task-meta">12 items • Medium Priority</div>
                                </div>
                                <div class="task-priority medium"></div>
                            </div>
                            
                            <div class="task-item low-priority">
                                <div class="task-content">
                                    <div class="task-title">Update user permissions</div>
                                    <div class="task-meta">5 users • Low Priority</div>
                                </div>
                                <div class="task-priority low"></div>
                            </div>
                            
                            <div class="task-item medium-priority">
                                <div class="task-content">
                                    <div class="task-title">Process lost & found reports</div>
                                    <div class="task-meta">8 reports • Medium Priority</div>
                                </div>
                                <div class="task-priority medium"></div>
                            </div>
                        </div>
                    </section>

                    <!-- Recent Users -->
                    <section class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Users</h2>
                            <button class="view-all-btn">View All</button>
                        </div>
                        <div class="user-list">
                            <div class="user-item">
                                <div class="user-avatar">JS</div>
                                <div class="user-info">
                                    <div class="user-name">John Smith</div>
                                    <div class="user-email">john@ucsc.cmb.ac.lk</div>
                                </div>
                                <div class="user-status active">Active</div>
                            </div>
                            
                            <div class="user-item">
                                <div class="user-avatar">AM</div>
                                <div class="user-info">
                                    <div class="user-name">Alice Miller</div>
                                    <div class="user-email">alice@ucsc.cmb.ac.lk</div>
                                </div>
                                <div class="user-status active">Active</div>
                            </div>
                            
                            <div class="user-item">
                                <div class="user-avatar">BJ</div>
                                <div class="user-info">
                                    <div class="user-name">Bob Johnson</div>
                                    <div class="user-email">bob@ucsc.cmb.ac.lk</div>
                                </div>
                                <div class="user-status inactive">Inactive</div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
    </div>

    <script src="/js/app/admin/admin-dashboard.js"></script>
