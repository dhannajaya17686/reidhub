<div class="help-admin-detail-section">
    <div class="detail-header">
        <a href="/dashboard/admin/help" class="btn btn-back">← Back to Dashboard</a>
        <h1><?php echo htmlspecialchars($question['subject']); ?></h1>
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

    <div class="question-detail-content">
        <div class="question-info">
            <div class="question-meta-info">
                <span class="badge badge-category badge-<?php echo htmlspecialchars($question['category']); ?>">
                    <?php echo ucwords(str_replace('_', ' ', $question['category'])); ?>
                </span>
                <span class="badge badge-status badge-<?php echo htmlspecialchars($question['status']); ?>">
                    <?php echo ucfirst($question['status']); ?>
                </span>
                <span class="date-info">
                    Asked on <?php echo date('M d, Y H:i', strtotime($question['created_at'])); ?>
                </span>
            </div>

            <div class="user-info">
                <h4>User Information</h4>
                <?php if ($userData): ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>User ID:</strong> <?php echo $question['user_id']; ?></p>
                <?php else: ?>
                    <p><strong>User ID:</strong> <?php echo $question['user_id']; ?></p>
                    <p><em>User information not found</em></p>
                <?php endif; ?>
            </div>

            <div class="question-message">
                <h4>Question:</h4>
                <p><?php echo nl2br(htmlspecialchars($question['message'])); ?></p>
                <?php if (!empty($question['image_path'])): ?>
                    <div class="question-image" style="margin-top: 15px;">
                        <h4>Attached Image:</h4>
                        <img src="<?php echo htmlspecialchars($question['image_path']); ?>" alt="Question Image" style="max-width: 100%; max-height: 500px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                        <div style="margin-top: 10px;">
                            <a href="/dashboard/admin/help/download-image?question_id=<?php echo htmlspecialchars($question['id']); ?>" class="btn btn-secondary" download>
                                ⬇ Download Image
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="replies-section">
            <h3>Admin Replies (<?php echo count($replies); ?>)</h3>
            
            <?php if (empty($replies)): ?>
                <p class="no-replies">No replies yet. Be the first to respond!</p>
            <?php else: ?>
                <div class="replies-list">
                    <?php foreach ($replies as $reply): ?>
                        <div class="reply-card">
                            <div class="reply-header">
                                <strong><?php echo htmlspecialchars($reply['admin_email']); ?></strong>
                                <span class="reply-date"><?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?></span>
                            </div>
                            <div class="reply-body">
                                <?php echo nl2br(htmlspecialchars($reply['reply_message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="admin-actions">
            <form id="reply-form" method="POST" action="/dashboard/admin/help/reply">
                <div class="form-group">
                    <label for="reply-message">Your Reply</label>
                    <textarea 
                        id="reply-message" 
                        name="reply_message" 
                        maxlength="2000" 
                        placeholder="Type your reply here..." 
                        rows="6"
                        required
                    ></textarea>
                    <small id="reply-char-count">0 / 2000 characters</small>
                </div>

                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary" id="submit-reply">Send Reply</button>
                    <button type="button" class="btn btn-secondary" id="mark-resolved">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
<script src="/js/app/help-section.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyTextarea = document.getElementById('reply-message');
    const charCount = document.getElementById('reply-char-count');
    const submitBtn = document.getElementById('submit-reply');
    const resolveBtn = document.getElementById('mark-resolved');
    const replyForm = document.getElementById('reply-form');

    // Character counter
    if (replyTextarea) {
        replyTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length + ' / 2000 characters';
        });
    }

    // Submit reply
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const questionId = document.querySelector('input[name="question_id"]').value;
            const replyMessage = replyTextarea.value.trim();

            if (!replyMessage) {
                alert('Please enter a reply message');
                return;
            }

            const formData = new FormData();
            formData.append('question_id', questionId);
            formData.append('reply_message', replyMessage);

            fetch('/dashboard/admin/help/reply', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reply sent successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to send reply'));
                }
            });
        });
    }

    // Mark as resolved
    if (resolveBtn) {
        resolveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Mark this question as resolved?')) return;

            const questionId = document.querySelector('input[name="question_id"]').value;

            const formData = new FormData();
            formData.append('question_id', questionId);

            fetch('/dashboard/admin/help/resolve', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Question marked as resolved!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to resolve question'));
                }
            });
        });
    }
});
</script>
