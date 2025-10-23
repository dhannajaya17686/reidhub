<link rel="stylesheet" href="/css/app/user/community/club-manage.css">
<link rel="stylesheet" href="/css/app/user/community/community.css">

<!-- Main Content Area -->
<main class="community-main club-manage-main" role="main" aria-label="Manage Club">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/community" class="breadcrumb__link">Community & Social</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/community/clubs" class="breadcrumb__link">Clubs & Societies</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        <span id="club-name-breadcrumb">Loading...</span>
      </li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title" id="club-name-title">Loading...</h1>
    <button class="btn btn--primary" id="edit-profile-btn">Edit Profile</button>
  </div>

  <!-- Club Posts Section -->
  <section class="club-posts-section">
    
    <div class="posts-grid" id="club-posts-grid">
      
      <!-- Upload Post Card -->
      <div class="upload-card" id="upload-post-card">
        <div class="upload-card__content">
          <svg class="upload-card__icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
            <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <span class="upload-card__text">Upload post here</span>
        </div>
      </div>

      <!-- Posts will be loaded dynamically -->
      
    </div>
  </section>

  <!-- View Post Modal -->
  <div class="modal-overlay" id="view-post-modal" style="display: none;">
    <div class="modal modal--large">
      <div class="modal-header">
        <div class="modal-header-info">
          <div class="modal-club-logo">
            <img id="modal-club-logo" src="" alt="">
          </div>
          <div class="modal-club-info">
            <h2 id="post-title" class="modal-title"></h2>
            <p class="modal-date" id="post-date"></p>
          </div>
        </div>
        <button class="modal-close" aria-label="Close modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      
      <div class="modal-body">
        <!-- Post Image -->
        <div class="post-image">
          <img src="" alt="Post Image" id="modal-post-image">
        </div>
        
        <!-- Post Content -->
        <div class="post-content">
          <h3 class="post-content-title" id="post-content-title"></h3>
          <p class="post-content-text" id="post-content-text"></p>
        </div>
        
        <!-- Post Actions -->
        <div class="post-actions">
          <button class="btn btn--secondary" id="report-post-btn">Report Post</button>
          <button class="btn btn--danger" id="delete-post-btn">Delete Post</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Post Modal -->
  <div class="modal-overlay" id="upload-modal" style="display: none;">
    <div class="modal modal--large">
      <div class="modal-header">
        <h2 id="upload-title" class="modal-title">Upload Post</h2>
        <button class="modal-close" aria-label="Close modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      
      <form class="modal-body" id="upload-form" method="POST" enctype="multipart/form-data">
        <!-- File Upload -->
        <div class="form-group">
          <div class="file-upload-area" id="file-upload-area">
            <input type="file" id="post-image" name="post_image" accept="image/*" class="file-input" required>
            <div class="file-upload-content">
              <svg class="upload-icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
                <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <p class="upload-text">Upload post here</p>
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
        </div>
        
        <!-- Post Title -->
        <div class="form-group">
          <label for="post-title-input" class="form-label">Post Title</label>
          <input 
            type="text" 
            id="post-title-input" 
            name="post_title" 
            class="form-input" 
            placeholder="Add title here"
            required
          >
        </div>
        
        <!-- Description -->
        <div class="form-group">
          <label for="post-description" class="form-label">Description</label>
          <textarea 
            id="post-description" 
            name="post_description" 
            class="form-textarea" 
            rows="6"
            placeholder="Description"
            required
          ></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="submit" class="btn btn--primary btn--large">Upload</button>
        </div>
      </form>
    </div>
  </div>

</main>

<script>
  // Get club ID from URL
  window.CLUB_ID = '<?= $data['club_id'] ?? '' ?>';
  window.API_BASE = '/api/community/clubs';
</script>
<script type="module" src="/js/app/community/club-manage.js"></script>
<script type="module" src="/js/app/community/community.js"></script>
