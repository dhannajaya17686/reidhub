<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-form.css">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard" class="breadcrumb__link">Dashboard</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community" class="breadcrumb__link">Community</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community/clubs" class="breadcrumb__link">Clubs</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      Create Club
    </li>
  </ol>
</nav>

<main class="blog-form-main" role="main" aria-label="Create Club">
  
  <div class="page-header">
    <h1 class="page-title">Create New Club</h1>
  </div>

  <form class="blog-form" id="create-club-form" method="POST" enctype="multipart/form-data">
    
    <div class="form-container">
      <div class="form-column form-column--left">
        
        <div class="form-group">
          <label for="club-name" class="form-label">Club Name</label>
          <input type="text" id="club-name" name="club_name" class="form-input" placeholder="Enter club name" required maxlength="200">
          <div class="form-error" id="club-name-error"></div>
        </div>

        <div class="form-group">
          <label for="category" class="form-label">Category</label>
          <select id="category" name="category" class="form-select" required>
            <option value="">Select Category</option>
            <?php if (isset($data['categories'])): ?>
              <?php foreach ($data['categories'] as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <div class="form-error" id="category-error"></div>
        </div>

        <div class="form-group">
          <label for="contact-email" class="form-label">Contact Email</label>
          <input type="email" id="contact-email" name="contact_email" class="form-input" placeholder="club@example.com" required>
          <div class="form-error" id="contact-email-error"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Club Logo</label>
          <div class="file-upload-area" id="file-upload-area">
            <input type="file" id="club-image" name="club_image" accept="image/png,image/jpeg,image/jpg" class="file-input" required>
            <div class="file-upload-content">
              <button type="button" class="btn btn--primary" id="upload-trigger">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path d="M10 14V6M10 6L7 9M10 6L13 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3 14v2a2 2 0 002 2h10a2 2 0 002-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Upload an image
              </button>
              <p class="upload-text">Or Drop an image here</p>
              <p class="upload-hint">Supported formats: png, jpeg, jpg</p>
            </div>
          </div>
          <div class="file-preview" id="file-preview" style="display: none;">
            <img id="preview-image" src="" alt="Preview" class="preview-image">
            <button type="button" class="preview-remove" id="preview-remove">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="form-error" id="club-image-error"></div>
        </div>

      </div>

      <div class="form-column form-column--right">
        <div class="form-group form-group--full">
          <label for="description" class="form-label">Club Description</label>
          <textarea id="description" name="description" class="form-textarea" rows="20" placeholder="Describe your club..." required></textarea>
          <div class="form-error" id="description-error"></div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn btn--secondary btn--large" onclick="history.back()">Cancel</button>
      <button type="submit" class="btn btn--primary btn--large">Create Club</button>
    </div>

  </form>

</main>

<script>
  window.CLUB_API_BASE = '/api/community/clubs';
</script>
<script type="module" src="/js/app/community/club-form.js"></script>
