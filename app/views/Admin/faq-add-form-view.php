<div class="faq-form-section">
    <div class="faq-form-header">
        <h1>Add New FAQ</h1>
        <a href="/dashboard/admin/faq" class="btn btn-secondary">Back to FAQs</a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/dashboard/admin/faq/add" class="faq-form">
        <div class="form-group">
            <label for="question">Question <span class="required">*</span></label>
            <input 
                type="text" 
                id="question" 
                name="question" 
                class="form-control" 
                maxlength="500"
                placeholder="Enter the FAQ question (max 500 characters)"
                required
            >
            <small class="char-count">0/500 characters</small>
        </div>

        <div class="form-group">
            <label for="answer">Answer <span class="required">*</span></label>
            <textarea 
                id="answer" 
                name="answer" 
                class="form-control textarea-large"
                placeholder="Enter the FAQ answer"
                required
            ></textarea>
        </div>

        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input 
                type="number" 
                id="display_order" 
                name="display_order" 
                class="form-control" 
                value="1"
                min="1"
            >
            <small>Higher numbers appear lower in the list</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create FAQ</button>
            <a href="/dashboard/admin/faq" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<link rel="stylesheet" href="/css/app/help-section.css">
<link rel="stylesheet" href="/css/app/faq-admin-section.css">

<script>
document.getElementById('question').addEventListener('input', function() {
    const count = this.value.length;
    document.querySelector('.char-count').textContent = count + '/500 characters';
});
</script>
