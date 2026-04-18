<div class="help-section">
    <div class="help-header">
        <h1>Report Your Complain/Feedback Here</h1>
        <p>We're here to help! Ask us anything about UCSC, ReidHub or report issues you encounter.</p>
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

    <form method="POST" action="/dashboard/help" class="help-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category">Category <span class="required">*</span></label>
            <select id="category" name="category" required>
                <option value="academic_issues">Academic Issues</option>
                <option value="extracurricular_issues">Extracurricular Issues</option>
                <option value="sports_issues">Sports Issues</option>
                <option value="infrastructure_issues">Infrastructure Issues</option>
                <option value="other_issues">Other Issues</option>
                <option value="feedbacks">Feedbacks</option>
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

        <div class="form-group">
            <label for="image">Upload Image (Optional)</label>
            <input 
                type="file" 
                id="image" 
                name="image" 
                accept="image/png,image/jpeg,image/jpg,image/gif" 
                onchange="validateImageFile(this)"
            >
            <small>Supported formats: PNG, JPG, JPEG, GIF. Max size: 5MB</small>
            <div id="file-error" class="alert alert-error" style="display: none; margin-top: 10px;"></div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit Question</button>
            <a href="/dashboard/help/my-questions" class="btn btn-secondary">View My Complains</a>
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

function validateImageFile(input) {
    const fileError = document.getElementById('file-error');
    fileError.style.display = 'none';
    
    if (!input.files || input.files.length === 0) return;
    
    const file = input.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    
    if (!allowedTypes.includes(file.type)) {
        fileError.textContent = 'Invalid file type. Please upload PNG, JPG, JPEG, or GIF.';
        fileError.style.display = 'block';
        input.value = '';
        return;
    }
    
    if (file.size > maxSize) {
        fileError.textContent = 'File size exceeds 5MB limit.';
        fileError.style.display = 'block';
        input.value = '';
        return;
    }
}
</script>

<link rel="stylesheet" href="/css/app/help-section.css">
