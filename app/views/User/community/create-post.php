<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/user/community/community-feed.css">

<main class="community-feed-main">
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Create Community Post</h1>
      <p class="page-description">Share updates with the campus community</p>
    </div>
  </div>

  <!-- Create Post Form -->
  <div class="create-post-container">
    <form method="POST" action="/dashboard/community/create-post" class="create-post-form" enctype="multipart/form-data">
      
      <!-- Post Type -->
      <div class="form-group">
        <label for="post_type" class="form-label">Post Type</label>
        <select name="post_type" id="post_type" class="form-input" required>
          <option value="general">General</option>
          <option value="announcement">Announcement</option>
          <option value="event">Event</option>
          <option value="club">Club Update</option>
        </select>
      </div>

      <!-- Title (Optional) -->
      <div class="form-group">
        <label for="title" class="form-label">Title (Optional)</label>
        <input 
          type="text" 
          name="title" 
          id="title" 
          class="form-input" 
          placeholder="Add a title to your post..."
        >
      </div>

      <!-- Content -->
      <div class="form-group">
        <label for="content" class="form-label">Content *</label>
        <textarea 
          name="content" 
          id="content" 
          class="form-textarea" 
          rows="8" 
          placeholder="What's on your mind?"
          required
        ></textarea>
        <small class="form-hint">Share your thoughts, updates, or announcements with the community</small>
      </div>

      <!-- Images (Future) -->
      <div class="form-group">
        <label for="images" class="form-label">Images (Optional)</label>
        <input 
          type="file" 
          name="images[]" 
          id="images" 
          class="form-input" 
          accept="image/*"
          multiple
        >
        <small class="form-hint">You can upload multiple images</small>
      </div>

      <!-- Action Buttons -->
      <div class="form-actions">
        <button type="button" class="btn btn--secondary" onclick="window.location.href='/dashboard/community'">
          Cancel
        </button>
        <button type="submit" class="btn btn--primary">
          Publish Post
        </button>
      </div>
    </form>
  </div>

  <!-- Preview Section -->
  <aside class="post-preview">
    <h3>Preview</h3>
    <div class="preview-card">
      <div class="post-header">
        <div class="post-author">
          <div class="author-avatar">
            <?= strtoupper(substr($data['user']['first_name'] ?? 'U', 0, 1)) ?>
          </div>
          <div class="author-info">
            <h4 class="author-name"><?= htmlspecialchars($data['user']['first_name'] . ' ' . $data['user']['last_name']) ?></h4>
            <span class="post-time">Just now</span>
          </div>
        </div>
      </div>
      <div class="post-body">
        <h3 class="post-title" id="preview-title"></h3>
        <div class="post-content" id="preview-content">Start typing to see preview...</div>
      </div>
    </div>
  </aside>
</main>

<style>
.create-post-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-bottom: 2rem;
}

.create-post-form {
  max-width: 800px;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 0.5rem;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-family: inherit;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-textarea {
  resize: vertical;
  min-height: 150px;
}

.form-hint {
  display: block;
  margin-top: 0.5rem;
  color: #666;
  font-size: 0.875rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.post-preview {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.post-preview h3 {
  margin: 0 0 1rem;
  font-size: 1.125rem;
  font-weight: 700;
}

.preview-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1rem;
}

#preview-title {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

#preview-content {
  color: #666;
  font-size: 0.875rem;
  line-height: 1.6;
}

@media (max-width: 968px) {
  .post-preview {
    display: none;
  }
}
</style>

<script>
// Live preview
document.getElementById('title').addEventListener('input', function(e) {
  const preview = document.getElementById('preview-title');
  preview.textContent = e.target.value || '';
  preview.style.display = e.target.value ? 'block' : 'none';
});

document.getElementById('content').addEventListener('input', function(e) {
  const preview = document.getElementById('preview-content');
  preview.textContent = e.target.value || 'Start typing to see preview...';
});
</script>
