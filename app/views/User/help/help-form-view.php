<div class="help-section">
    <div class="help-header">
        <h1>Ask a Question</h1>
        <p>We're here to help! Ask us anything about ReidHub or report issues you encounter.</p>
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

    <form method="POST" action="/dashboard/help" class="help-form">
        <div class="form-group">
            <label for="category">Category <span class="required">*</span></label>
            <select id="category" name="category" required>
                <option value="general_question">General Question</option>
                <option value="bug_report">Bug Report</option>
                <option value="feature_request">Feature Request</option>
                <option value="feedback">Feedback</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">Subject <span class="required">*</span></label>
            <input 
                type="text" 
                id="subject" 
                name="subject" 
                maxlength="255" 
                placeholder="Brief subject of your question" 
                required
            >
            <small>Max 255 characters</small>
        </div>

        <div class="form-group">
            <label for="message">Message <span class="required">*</span></label>
            <textarea 
                id="message" 
                name="message" 
                maxlength="5000" 
                placeholder="Describe your question or issue in detail..." 
                rows="8"
                required
            ></textarea>
            <div class="char-counter">
                <span id="char-count">0</span> / 5000 characters
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit Question</button>
            <a href="/dashboard/help/my-questions" class="btn btn-secondary">View My Questions</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('char-count');

    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});
</script>

<link rel="stylesheet" href="/css/app/help-section.css">
