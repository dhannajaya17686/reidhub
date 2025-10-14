        <link href="/css/app/user/edu-forum/all-questions.css" rel="stylesheet">
        <!-- Main Content -->
        <main class="forum-main" role="main">
            <!-- Content Tabs - UNCHANGED -->
            <nav class="content-tabs" role="tablist" aria-label="Question filters">
                <a href="#" class="tab-link" role="tab" data-tab="newest" aria-selected="false">Newest</a>
                <a href="#" class="tab-link" role="tab" data-tab="trending" aria-selected="false">Trending</a>
                <a href="#" class="tab-link" role="tab" data-tab="unanswered" aria-selected="false">Unanswered</a>
                <a href="#" class="tab-link is-active" role="tab" data-tab="week" aria-selected="true">Week</a>
                <a href="#" class="tab-link" role="tab" data-tab="month" aria-selected="false">Month</a>
            </nav>

            <!-- ENHANCED Questions Section -->
            <section class="questions-section">
                <h1 class="questions-header">Questions in This Week</h1>
                
                <div class="questions-list" role="list">
                    <!-- Enhanced Question 1 -->
                    <article class="question-card" role="listitem">
                        <div class="difficulty-indicator difficulty-indicator--medium" title="Medium difficulty"></div>
                        
                        <header class="question-header">
                            <div class="question-title-group">
                                <h2 class="question-title">
                                    <a href="#">Send message from a tonic stream</a>
                                </h2>
                                <div class="question-tags">
                                    <span class="question-tag">rust</span>
                                    <span class="question-tag">async</span>
                                    <span class="question-tag">streaming</span>
                                </div>
                            </div>
                            
                            <div class="question-actions">
                                <button class="question-action-btn" aria-label="Bookmark question" title="Bookmark">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v10l5-3 5 3V3a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/>
                                    </svg>
                                </button>
                                <button class="question-action-btn" aria-label="Share question" title="Share">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 6H6L2 10l4 4h4l4-4-4-4z"/>
                                    </svg>
                                </button>
                            </div>
                        </header>
                        
                        <div class="question-author">
                            <img class="author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='16' fill='%234F46E5'/%3E%3Ctext x='16' y='21' text-anchor='middle' fill='white' font-size='12' font-weight='bold'%3EJS%3C/text%3E%3C/svg%3E" alt="John Smith avatar">
                            <div class="author-info">
                                <div class="author-name">John Smith</div>
                                <time class="question-time" datetime="2025-10-12T14:30:00Z" title="October 12, 2025 at 2:30 PM">2 days ago</time>
                            </div>
                            <span class="author-badge">Expert</span>
                        </div>
                        
                        <div class="question-content">
                            <p>I guess I have to add rx into EchoServer field ? But mpsc::Receiver doesn't implement Copy trait. So how to keep the stream open, from server_streaming_echo fn and send Message from send_event fn ?</p>
                        </div>
                        
                        <footer class="question-stats">
                            <div class="stat-item stat-item--votes">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                                <span class="stat-number">01</span>
                                <span class="stat-label">Votes</span>
                            </div>
                            <div class="stat-item stat-item--answers">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M2 2h12a2 2 0 012 2v8a2 2 0 01-2 2H4l-2 2V4a2 2 0 012-2z"/>
                                </svg>
                                <span class="stat-number">02</span>
                                <span class="stat-label">Answers</span>
                            </div>
                            <div class="stat-item stat-item--views">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 2C4 2 1 8 1 8s3 6 7 6 7-6 7-6-3-6-7-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <span class="stat-number">47</span>
                                <span class="stat-label">Views</span>
                            </div>
                            <span class="reading-time" aria-label="Estimated reading time: 1 minute">1 min read</span>
                        </footer>
                    </article>

                    <!-- Enhanced Question 2 (with closed state) -->
                    <article class="question-card" role="listitem">
                        <div class="difficulty-indicator difficulty-indicator--easy" title="Easy difficulty"></div>
                        
                        <header class="question-header">
                            <div class="question-title-group">
                                <h2 class="question-title">
                                    <a href="#">I have started UNITY and I am following Brackey for learning the basics and making my first game</a>
                                </h2>
                                <div class="question-tags">
                                    <span class="question-tag">unity</span>
                                    <span class="question-tag">gamedev</span>
                                    <span class="question-tag">beginner</span>
                                </div>
                            </div>
                            
                            <div class="question-actions">
                                <span class="closed-badge">Closed</span>
                                <button class="question-action-btn" aria-label="Bookmark question" title="Bookmark">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v10l5-3 5 3V3a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/>
                                    </svg>
                                </button>
                                <button class="question-action-btn" aria-label="Share question" title="Share">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 6H6L2 10l4 4h4l4-4-4-4z"/>
                                    </svg>
                                </button>
                            </div>
                        </header>
                        
                        <div class="question-author">
                            <img class="author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='16' fill='%23059669'/%3E%3Ctext x='16' y='21' text-anchor='middle' fill='white' font-size='12' font-weight='bold'%3EMC%3C/text%3E%3C/svg%3E" alt="Mike Chen avatar">
                            <div class="author-info">
                                <div class="author-name">Mike Chen</div>
                                <time class="question-time" datetime="2025-10-11T09:15:00Z" title="October 11, 2025 at 9:15 AM">3 days ago</time>
                            </div>
                            <span class="author-badge">Newbie</span>
                        </div>
                        
                        <div class="question-content">
                            <p>I have started playing box game with Brackey so I am making this game in which there is a cube moving forward and I would like you to suggest something to try with as I am a complete beginner and would appreciate some help from the people who already knows it!!</p>
                        </div>
                        
                        <footer class="question-stats">
                            <div class="stat-item stat-item--votes">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                                <span class="stat-number">03</span>
                                <span class="stat-label">Votes</span>
                            </div>
                            <div class="stat-item stat-item--answers">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M2 2h12a2 2 0 012 2v8a2 2 0 01-2 2H4l-2 2V4a2 2 0 012-2z"/>
                                </svg>
                                <span class="stat-number">02</span>
                                <span class="stat-label">Answers</span>
                            </div>
                            <div class="stat-item stat-item--views">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 2C4 2 1 8 1 8s3 6 7 6 7-6 7-6-3-6-7-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <span class="stat-number">89</span>
                                <span class="stat-label">Views</span>
                            </div>
                            <span class="reading-time" aria-label="Estimated reading time: 2 minutes">2 min read</span>
                        </footer>
                    </article>

                    <!-- Enhanced Question 3 -->
                    <article class="question-card" role="listitem">
                        <div class="difficulty-indicator difficulty-indicator--hard" title="Hard difficulty"></div>
                        
                        <header class="question-header">
                            <div class="question-title-group">
                                <h2 class="question-title">
                                    <a href="#">The block is not getting recognized error: The method 'read' isn't defined for the type 'BuildContext'</a>
                                </h2>
                                <div class="question-tags">
                                    <span class="question-tag">flutter</span>
                                    <span class="question-tag">dart</span>
                                    <span class="question-tag">bloc</span>
                                    <span class="question-tag">error</span>
                                </div>
                            </div>
                            
                            <div class="question-actions">
                                <button class="question-action-btn" aria-label="Bookmark question" title="Bookmark">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v10l5-3 5 3V3a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/>
                                    </svg>
                                </button>
                                <button class="question-action-btn" aria-label="Share question" title="Share">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 6H6L2 10l4 4h4l4-4-4-4z"/>
                                    </svg>
                                </button>
                            </div>
                        </header>
                        
                        <div class="question-author">
                            <img class="author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='16' fill='%23DC2626'/%3E%3Ctext x='16' y='21' text-anchor='middle' fill='white' font-size='12' font-weight='bold'%3EAP%3C/text%3E%3C/svg%3E" alt="Alex Patel avatar">
                            <div class="author-info">
                                <div class="author-name">Alex Patel</div>
                                <time class="question-time" datetime="2025-10-10T16:45:00Z" title="October 10, 2025 at 4:45 PM">4 days ago</time>
                            </div>
                            <span class="author-badge">Pro</span>
                        </div>
                        
                        <div class="question-content">
                            <p>its a cloned project . Its working fine one PC , but facing issue in another . Used Bloc for state management . it giving the error :- The method 'read' isn't defined for the type 'BuildContext'. For all use of read throughout the project totally 498 times.</p>
                        </div>
                        
                        <footer class="question-stats">
                            <div class="stat-item stat-item--votes">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                                <span class="stat-number">03</span>
                                <span class="stat-label">Votes</span>
                            </div>
                            <div class="stat-item stat-item--answers">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M2 2h12a2 2 0 012 2v8a2 2 0 01-2 2H4l-2 2V4a2 2 0 012-2z"/>
                                </svg>
                                <span class="stat-number">02</span>
                                <span class="stat-label">Answers</span>
                            </div>
                            <div class="stat-item stat-item--views">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 2C4 2 1 8 1 8s3 6 7 6 7-6 7-6-3-6-7-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                                <span class="stat-number">156</span>
                                <span class="stat-label">Views</span>
                            </div>
                            <span class="reading-time" aria-label="Estimated reading time: 1 minute">1 min read</span>
                        </footer>
                    </article>
                </div>
            </section>
        </main>
 