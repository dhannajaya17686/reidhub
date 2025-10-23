<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">

<!-- Main Content Area -->
<main class="blog-form-main" role="main" aria-label="Edit Blog">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/community" class="breadcrumb__link">Community & Social</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/dashboard/community/blogs" class="breadcrumb__link">Blogs</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        Edit Blog
      </li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Edit Blog</h1>
  </div>

  <!-- Blog Form -->
  <form class="blog-form" id="edit-blog-form" method="POST" action="/dashboard/community/blogs/edit" enctype="multipart/form-data">
    
    <div class="form-container">
      <!-- Left Column -->
      <div class="form-column form-column--left">
        
        <!-- Blog Name -->
        <div class="form-group">
          <label for="blog-name" class="form-label">Blog Name</label>
          <input 
            type="text" 
            id="blog-name" 
            name="blog_name" 
            class="form-input" 
            value="<?= htmlspecialchars($data['blog']['title']) ?>"
            required
            maxlength="200"
          >
          <div class="form-error" id="blog-name-error"></div>
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="form-label">Category</label>
          <select id="category" name="category" class="form-select" required>
            <option value="">Select Category</option>
            <?php if (isset($data['categories'])): ?>
              <?php foreach ($data['categories'] as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>" <?= $data['blog']['category'] === $key ? 'selected' : '' ?>>
                  <?= htmlspecialchars($label) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <div class="form-error" id="category-error"></div>
        </div>

        <!-- Tags -->
        <div class="form-group">
          <label for="tags" class="form-label">Tags</label>
          <input 
            type="text" 
            id="tags" 
            name="tags" 
            class="form-input" 
            value="<?= htmlspecialchars($data['blog']['tags'] ?? '') ?>"
            placeholder="Enter keywords"
          >
          <div class="form-hint">Separate tags with commas</div>
          <div class="form-error" id="tags-error"></div>
        </div>

        <!-- Blog Image -->
        <div class="form-group">
          <label class="form-label">Blog Image</label>
          <div class="file-upload-area" id="file-upload-area" style="display: none;">
            <input type="file" id="blog-image" name="blog_image" accept="image/png,image/jpeg,image/jpg" class="file-input">
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
          <div class="file-preview" id="file-preview">
            <img id="preview-image" src="<?= htmlspecialchars($data['blog']['image_path']) ?>" alt="Preview" class="preview-image">
            <button type="button" class="preview-remove" id="preview-remove">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="form-error" id="blog-image-error"></div>
        </div>

      </div>

      <!-- Right Column -->
      <div class="form-column form-column--right">
        
        <!-- Description -->
        <div class="form-group form-group--full">
          <label for="description" class="form-label">Description</label>
          <textarea 
            id="description" 
            name="description" 
            class="form-textarea" 
            rows="20"
            required
          ><?= htmlspecialchars($data['blog']['description']) ?></textarea>
          <div class="form-error" id="description-error"></div>
        </div>

      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <button type="button" class="btn btn--secondary btn--large" onclick="history.back()">Cancel</button>
      <button type="submit" class="btn btn--primary btn--large">Update</button>
    </div>

  </form>

</main>

<script type="module" src="/js/app/community/blog-form.js"></script>
<script type="module" src="/js/app/community/community.js"></script>