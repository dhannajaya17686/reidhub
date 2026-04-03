<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0">

<!-- Main Content Area -->
<main class="blog-view-main" role="main" aria-label="Blog Post">
  
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
        <?= htmlspecialchars($data['blog']['title']) ?>
      </li>
    </ol>
  </nav>

  <!-- Blog Container -->
  <div class="blog-container">
    
    <!-- Blog Header -->
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['blog']['title']) ?></h1>
      
      <?php if ($data['hasReports'] ?? false): ?>
      <div class="reported-tag" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 8px 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; font-size: 0.875rem; font-weight: 500;">
        <span class="material-symbols-outlined" style="font-size: 18px;">flag</span>
        <span>This post has been reported</span>
      </div>
      <?php endif; ?>
      
      <!-- Blog Image -->
      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['blog']['image_path'] ?? '/assets/placeholders/product.jpeg') ?>" 
             alt="<?= htmlspecialchars($data['blog']['title']) ?>">
      </div>

      <!-- Blog Meta -->
      <div class="blog-meta">
        <span class="blog-author">By <?= htmlspecialchars($data['blog']['first_name'] . ' ' . $data['blog']['last_name']) ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-published">Published on <?= date('F j, Y', strtotime($data['blog']['created_at'])) ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-views"><?= number_format($data['blog']['views'] ?? 0) ?> views</span>
        <?php if (!$data['isOwner']): ?>
        <span class="blog-separator">•</span>
        <button class="blog-report-icon" id="report-blog-btn" data-report-type="blog" data-id="<?= $data['blog']['id'] ?>" title="Report" aria-label="Report blog" style="background: none; border: none; cursor: pointer; padding: 0; color: var(--text-muted); transition: color 0.2s;">
          <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">flag</span>
        </button>
        <?php endif; ?>
      </div>
    </header>

    <!-- Blog Content -->
    <article class="blog-content">
      <?= nl2br(htmlspecialchars($data['blog']['content'] ?? '')) ?>
    </article>
    
    <!-- Blog Actions (for owner) -->
    <?php if ($data['isOwner']): ?>
    <div class="blog-actions" id="blog-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/blogs/edit'">Edit Blog</button>
      <button class="btn btn--danger" id="delete-blog-btn" data-blog-id="<?= $data['blog']['id'] ?>">Delete Blog</button>
    </div>
    <?php endif; ?>

  </div>

  <!-- Report Modal -->
  <div class="modal-overlay" id="report-modal" role="dialog" aria-labelledby="report-title" aria-modal="true" style="display: none;">
    <div class="modal">
      <div class="modal-header">
        <h2 id="report-title" class="modal-title">Report Blog Post</h2>
        <button class="modal-close" aria-label="Close modal">
          <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
      </div>
      
      <form class="modal-body" id="report-form">
        <div class="form-group">
          <label for="report-description" class="form-label">Description</label>
          <textarea id="report-description" name="description" class="form-textarea" rows="6" placeholder="Tell us what's wrong with this blog post..." required></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="submit" class="btn btn--primary">Submit Report</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="delete-modal" role="dialog" aria-labelledby="delete-title" aria-modal="true" style="display: none;">
    <div class="modal modal--small">
      <div class="modal-header">
        <h2 id="delete-title" class="modal-title">Delete Blog Post</h2>
        <button class="modal-close" aria-label="Close modal">
          <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
      </div>
      
      <div class="modal-body">
        <p>Are you sure you want to delete this blog post? This action cannot be undone.</p>
        
        <div class="modal-actions">
          <button type="button" class="btn btn--secondary" id="cancel-delete">Cancel</button>
          <button type="button" class="btn btn--danger" id="confirm-delete">Delete</button>
        </div>
      </div>
    </div>
  </div>

</main>

<script>
// Inline report handler
document.addEventListener('DOMContentLoaded', function() {
  const reportBtn = document.getElementById('report-blog-btn');
  const reportForm = document.getElementById('report-form');
  let currentReportData = { type: null, id: null };
  
  if (reportBtn) {
    reportBtn.addEventListener('click', function(e) {
      e.preventDefault();
      currentReportData.type = 'blog';
      currentReportData.id = reportBtn.getAttribute('data-id');
      const modal = document.getElementById('report-modal');
      if (modal) modal.style.display = 'flex';
    });
  }
  
  // Handle form submission
  if (reportForm) {
    reportForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const description = document.getElementById('report-description').value;
      if (!description) {
        alert('Please enter a description');
        return;
      }
      
      const payload = { 
        description: description, 
        id: currentReportData.id 
      };
      
      console.log('Submitting report:', payload);
      
      try {
        const response = await fetch('/api/community/blogs/report', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        
        const textResponse = await response.text();
        console.log('Response:', textResponse);
        
        const data = JSON.parse(textResponse);
        
        if (data.success) {
          alert('Report submitted successfully!');
          reportForm.reset();
          document.getElementById('report-modal').style.display = 'none';
          
          // Add a reported tag to the blog
          const blogHeader = document.querySelector('.blog-header');
          if (blogHeader && !document.querySelector('.reported-tag')) {
            const tag = document.createElement('div');
            tag.className = 'reported-tag';
            tag.innerHTML = '<span class="material-symbols-outlined">flag</span> Reported';
            tag.style.cssText = 'background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 8px 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; font-size: 0.875rem; font-weight: 500;';
            blogHeader.appendChild(tag);
          }
        } else {
          alert('Failed to submit report: ' + data.message);
        }
      } catch (error) {
        console.error('Report error:', error);
        alert('Error submitting report: ' + error.message);
      }
    });
  }
  
  // Close modals
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', function() {
      const overlay = this.closest('.modal-overlay');
      if (overlay) overlay.style.display = 'none';
    });
  });
  
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === this) this.style.display = 'none';
    });
  });
});
</script>

<script type="module" src="/js/app/community/blog-view.js"></script>
<script type="module" src="/js/app/community/community.js"></script>