<div class="help-section">
    <div class="help-header">
        <a href="/dashboard/help/my-questions" style="margin-bottom: 10px; display: inline-block;">← Back to My Complains</a>
        <h1>Edit Your Complain</h1>
        <p>Update your complaint details</p>
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

    <form method="POST" action="/dashboard/help/save-edit" class="help-form" enctype="multipart/form-data">
        <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question['id']); ?>">

        <div class="form-group">
            <label for="category">Category <span class="required">*</span></label>
            <select id="category" name="category" required>
                <option value="academic_issues" <?php echo ($question['category'] === 'academic_issues') ? 'selected' : ''; ?>>Academic Issues</option>
                <option value="extracurricular_issues" <?php echo ($question['category'] === 'extracurricular_issues') ? 'selected' : ''; ?>>Extracurricular Issues</option>
                <option value="sports_issues" <?php echo ($question['category'] === 'sports_issues') ? 'selected' : ''; ?>>Sports Issues</option>
                <option value="infrastructure_issues" <?php echo ($question['category'] === 'infrastructure_issues') ? 'selected' : ''; ?>>Infrastructure Issues</option>
                <option value="other_issues" <?php echo ($question['category'] === 'other_issues') ? 'selected' : ''; ?>>Other Issues</option>
                <option value="feedbacks" <?php echo ($question['category'] === 'feedbacks') ? 'selected' : ''; ?>>Feedbacks</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">Subject <span class="required">*</span></label>
            <input 
                type="text" 
                id="subject" 
                name="subject" 
                maxlength="255" 
                placeholder="Brief subject of your complaint" 
                value="<?php echo htmlspecialchars($question['subject']); ?>"
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
                placeholder="Describe your complaint or issue in detail..." 
                rows="8"
                required
            ><?php echo htmlspecialchars($question['message']); ?></textarea>
            <div class="char-counter">
                <span id="char-count"><?php echo strlen($question['message']); ?></span> / 5000 characters
            </div>
        </div>

        <?php if (!empty($question['image_path'])): ?>
            <div class="form-group">
                <label>Current Image</label>
                <div style="margin-bottom: 15px;">
                    <img src="<?php echo htmlspecialchars($question['image_path']); ?>" alt="Current Image" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                </div>
                <label>
                    <input type="checkbox" name="remove_image" value="1">
                    Remove current image
                </label>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="image">Upload New Image (Optional)</label>
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
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/dashboard/help/my-questions" class="btn btn-secondary">Cancel</a>
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
