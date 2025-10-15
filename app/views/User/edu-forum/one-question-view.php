        <link href="/css/app/user/edu-forum/one-question.css" rel="stylesheet">

        <!-- Main Content -->
            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="#" class="breadcrumb-link">Newest</a>
                <span class="breadcrumb-separator" aria-hidden="true">›</span>
                <span class="breadcrumb-current">Send message from a tonic stream</span>
            </nav>

            <!-- Enhanced Question Header -->
            <section class="question-detail-header">
                <div class="question-author-info">
                    <img class="question-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%230466C8'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='16' font-weight='bold'%3EDM%3C/text%3E%3C/svg%3E" alt="Dhananjaya Mudalige avatar">
                    <div class="author-details">
                        <h2 class="author-name">Dhananjaya Mudalige</h2>
                        <span class="author-badge">2nd Year Undergraduate</span>
                    </div>
                </div>

                <h1 class="question-title-main">Send message from a tonic stream</h1>

                <div class="question-content-main">
                    <p>I use tonic to create a grpc stream. But the message is never received. I guess I have to add rx into EchoServer field ? But mpsc::Receiver doesn't implement Copy trait. So how to keep the stream open, from server_streaming_echo fn and send Message from send_event fn ?</p>
                </div>

                <div class="vote-section">
                    <button class="vote-button" aria-label="Vote for this question">
                        <svg class="vote-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                        </svg>
                        <span class="vote-text">Vote</span>
                    </button>

                    <div class="question-actions">
                        <button class="action-button" aria-label="Share question">Share</button>
                        <button class="action-button" aria-label="Bookmark question">Bookmark</button>
                    </div>
                </div>
            </section>

            <!-- Enhanced Answers Section -->
            <section class="answers-section">
                <div class="answers-header">
                    <h2 class="answers-title">Answers</h2>
                    <span class="answers-count">4</span>
                </div>

                <!-- Answer 1 -->
                <article class="answer-card">
                    <div class="answer-header">
                        <img class="answer-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23059669'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3EAR%3C/text%3E%3C/svg%3E" alt="Amasha Ranasinghe avatar">
                        <div class="answer-author-info">
                            <div class="answer-author-name">Amasha Ranasinghe</div>
                            <div class="answer-timestamp">2 days ago</div>
                        </div>
                    </div>
                    
                    <div class="answer-content">
                        <p>This is a great initiative! I'm excited to see how these changes will impact our campus.</p>
                    </div>
                    
                    <div class="answer-actions">
                        <div class="answer-vote">
                            <button class="answer-vote-btn upvote" aria-label="Upvote answer">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                            </button>
                            <span class="answer-vote-count">12</span>
                        </div>
                        <button class="reply-button">Reply</button>
                    </div>
                </article>

                <!-- Answer 2 -->
                <article class="answer-card">
                    <div class="answer-header">
                        <img class="answer-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%230466C8'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3EDM%3C/text%3E%3C/svg%3E" alt="Dhananjaya Mudalige avatar">
                        <div class="answer-author-info">
                            <div class="answer-author-name">Dhananjaya Mudalige</div>
                            <div class="answer-timestamp">1 day ago</div>
                        </div>
                    </div>
                    
                    <div class="answer-content">
                        <p>I agree, Sophia! It's inspiring to see our university taking such proactive steps towards sustainability.</p>
                    </div>
                    
                    <div class="answer-actions">
                        <div class="answer-vote">
                            <button class="answer-vote-btn upvote" aria-label="Upvote answer">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                            </button>
                            <span class="answer-vote-count">8</span>
                        </div>
                        <button class="reply-button">Reply</button>
                    </div>
                </article>

                <!-- Answer 3 -->
                <article class="answer-card">
                    <div class="answer-header">
                        <img class="answer-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%23059669'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3EAR%3C/text%3E%3C/svg%3E" alt="Amasha Ranasinghe avatar">
                        <div class="answer-author-info">
                            <div class="answer-author-name">Amasha Ranasinghe</div>
                            <div class="answer-timestamp">2 days ago</div>
                        </div>
                    </div>
                    
                    <div class="answer-content">
                        <p>This is a great initiative! I'm excited to see how these changes will impact our campus.</p>
                    </div>
                    
                    <div class="answer-actions">
                        <div class="answer-vote">
                            <button class="answer-vote-btn upvote" aria-label="Upvote answer">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                            </button>
                            <span class="answer-vote-count">12</span>
                        </div>
                        <button class="reply-button">Reply</button>
                    </div>
                </article>

                <!-- Answer 4 -->
                <article class="answer-card">
                    <div class="answer-header">
                        <img class="answer-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%230466C8'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3EDM%3C/text%3E%3C/svg%3E" alt="Dhananjaya Mudalige avatar">
                        <div class="answer-author-info">
                            <div class="answer-author-name">Dhananjaya Mudalige</div>
                            <div class="answer-timestamp">1 day ago</div>
                        </div>
                    </div>
                    
                    <div class="answer-content">
                        <p>I agree, Sophia! It's inspiring to see our university taking such proactive steps towards sustainability.</p>
                    </div>
                    
                    <div class="answer-actions">
                        <div class="answer-vote">
                            <button class="answer-vote-btn upvote" aria-label="Upvote answer">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                            </button>
                            <span class="answer-vote-count">8</span>
                        </div>
                        <button class="reply-button">Reply</button>
                    </div>
                </article>

                <!-- Enhanced Answer Input Section -->
                <section class="answer-input-section">
                    <div class="answer-input-header">
                        <img class="current-user-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%230466C8'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3EDM%3C/text%3E%3C/svg%3E" alt="dhannajaya17686 avatar">
                        <label for="answer-input" class="sr-only">Type your answer</label>
                    </div>
                    
                    <textarea 
                        id="answer-input" 
                        class="answer-textarea" 
                        placeholder="Type an answer"
                        rows="4"
                    ></textarea>
                    
                    <div class="answer-submit-section">
                        <div class="input-info">
                            <small class="text-muted">Use Ctrl+Enter to submit quickly</small>
                        </div>
                        <button class="submit-button" disabled>
                            <svg class="submit-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1.724 2.5a.5.5 0 01.7-.447l12 5.5a.5.5 0 010 .894l-12 5.5a.5.5 0 01-.7-.447L2.382 8.5H8a.5.5 0 000-1H2.382L1.724 2.5z"/>
                            </svg>
                            <span class="submit-text">Post Answer</span>
                        </button>
                    </div>
                </section>
            </section>

            <!-- Report Section -->
            <div class="report-section">
                <button class="report-button">Report Question</button>
            </div>

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true"></div>

    <!-- Scripts -->
     <script type="module" src="/js/app/edu-forum/one-question.js"></script>