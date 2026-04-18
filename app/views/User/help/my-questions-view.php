<div class="help-section">
    <div class="help-header">
        <h1>My Complains</h1>
        <p>Track the status of your complains and replies from Students Union</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="questions-container">
        <?php if (empty($questions)): ?>
            <div class="empty-state">
                <p>You haven't asked any questions yet.</p>
                <a href="/dashboard/help" class="btn btn-primary">Ask a Question Now</a>
            </div>
        <?php else: ?>
            <?php foreach ($questions as $question): ?>
                <div class="question-card">
                    <div class="question-header">
                        <div class="question-title">
                            <h3><?php echo htmlspecialchars($question['subject']); ?></h3>
                            <div class="question-meta">
                                <span class="badge badge-category badge-<?php echo htmlspecialchars($question['category']); ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $question['category'])); ?>
                                </span>
                                <span class="badge badge-status badge-<?php echo htmlspecialchars($question['status']); ?>">
                                    <?php echo ucfirst($question['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="question-date">
                            <?php echo date('M d, Y', strtotime($question['created_at'])); ?>
                        </div>
                    </div>

                    <div class="question-body">
                        <p><?php echo nl2br(htmlspecialchars($question['message'])); ?></p>
                        <?php if (!empty($question['image_path'])): ?>
                            <div class="question-image" style="margin-top: 15px;">
                                <img src="<?php echo htmlspecialchars($question['image_path']); ?>" alt="Complaint Image" style="max-width: 100%; max-height: 400px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($question['status'] === 'replied' || $question['status'] === 'resolved'): ?>
                        <div class="question-replies">
                            <h4>Admin Replies:</h4>
                            <?php 
                            // Get replies for this question
                            $userQuestionModel = new UserQuestion();
                            $replies = $userQuestionModel->getReplies($question['id']);
                            
                            if (!empty($replies)):
                                foreach ($replies as $reply):
                            ?>
                                <div class="reply-item">
                                    <div class="reply-from">
                                        <strong><?php echo htmlspecialchars($reply['admin_email']); ?></strong>
                                        <span class="reply-date"><?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?></span>
                                    </div>
                                    <div class="reply-message">
                                        <?php echo nl2br(htmlspecialchars($reply['reply_message'])); ?>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($question['status'] === 'open' || $question['status'] === 'pending'): ?>
                        <div class="question-actions" style="margin-top: 15px; display: flex; gap: 10px;">
                            <a href="/dashboard/help/edit?id=<?php echo htmlspecialchars($question['id']); ?>" class="btn btn-secondary">✎ Edit</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="help-actions">
        <a href="/dashboard/help" class="btn btn-primary">Ask Another Question</a>
    </div>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
