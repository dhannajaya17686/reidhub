<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

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
      
      <!-- Blog Image -->
      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['blog']['image_path'] ?? '/public/images/placeholder-blog.jpg') ?>" 
             alt="<?= htmlspecialchars($data['blog']['title']) ?>">
      </div>

      <!-- Blog Meta -->
      <div class="blog-meta">
        <span class="blog-author">By <?= htmlspecialchars($data['blog']['first_name'] . ' ' . $data['blog']['last_name']) ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-published">Published on <?= date('F j, Y', strtotime($data['blog']['created_at'])) ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-views"><?= number_format($data['blog']['views'] ?? 0) ?> views</span>
      </div>
    </header>

    <!-- Blog Content -->
    <article class="blog-content">
      <?= nl2br(htmlspecialchars($data['blog']['description'])) ?>
    </article>

    <!-- Blog Interactions -->
    <div class="blog-interactions">
      <button class="interaction-btn interaction-btn--like" data-action="like" data-blog-id="<?= $data['blog']['id'] ?>">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M14 2a4 4 0 00-3.464 6H6a4 4 0 00-4 4v0a4 4 0 004 4h8a4 4 0 004-4v-6a4 4 0 00-4-4z" stroke="currentColor" stroke-width="2"/>
          <path d="M6 8v8" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span class="interaction-count"><?= $data['interactions']['likes'] ?></span>
      </button>
      
      <button class="interaction-btn interaction-btn--dislike" data-action="dislike" data-blog-id="<?= $data['blog']['id'] ?>">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M14 18a4 4 0 003.464-6H6a4 4 0 01-4-4v0a4 4 0 014-4h8a4 4 0 014 4v6a4 4 0 01-4 4z" stroke="currentColor" stroke-width="2"/>
          <path d="M6 12V4" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span class="interaction-count"><?= $data['interactions']['dislikes'] ?></span>
      </button>
    </div>

    <!-- Comments Section -->
    <section class="comments-section">
      <h2 class="comments-title">Comments (<?= count($data['comments']) ?>)</h2>

      <?php if (!empty($data['comments'])): ?>
        <?php 
        // Helper function to format time
        function timeAgo($datetime) {
          $time = strtotime($datetime);
          $diff = time() - $time;
          
          if ($diff < 60) return 'Just now';
          if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
          if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
          if ($diff < 604800) return floor($diff / 86400) . ' days ago';
          return date('M j, Y', $time);
        }
        ?>
        
        <?php foreach ($data['comments'] as $comment): ?>
          <?php if (!$comment['parent_id']): ?>
          <article class="comment" data-comment-id="<?= $comment['id'] ?>">
            <div class="comment-avatar">
              <img src="<?= htmlspecialchars($comment['profile_picture'] ?? '/public/images/default-avatar.png') ?>" 
                   alt="<?= htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) ?>">
            </div>
            <div class="comment-content">
              <div class="comment-header">
                <span class="comment-author"><?= htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) ?></span>
                <span class="comment-time"><?= timeAgo($comment['created_at']) ?></span>
              </div>
              <p class="comment-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
              <div class="comment-actions">
                <button class="comment-action-btn" data-action="like" data-comment-id="<?= $comment['id'] ?>">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                    <path d="M14 2a4 4 0 00-3.464 6H6a4 4 0 00-4 4v0a4 4 0 004 4h8a4 4 0 004-4v-6a4 4 0 00-4-4z" stroke="currentColor" stroke-width="2"/>
                </button>
                <button class="comment-action-btn" data-action="dislike" data-comment-id="<?= $comment['id'] ?>">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                    <path d="M14 18a4 4 0 003.464-6H6a4 4 0 01-4-4v0a4 4 0 014-4h8a4 4 0 014 4v6a4 4 0 01-4 4z" stroke="currentColor" stroke-width="2"/>
                </button>
              </div>
            </div>
          </article>

          <!-- Nested Comments -->
          <?php foreach ($data['comments'] as $reply): ?>
            <?php if ($reply['parent_id'] == $comment['id']): ?>
            <article class="comment comment--nested" data-comment-id="<?= $reply['id'] ?>">
              <div class="comment-avatar">
                <img src="<?= htmlspecialchars($reply['profile_picture'] ?? '/public/images/default-avatar.png') ?>" 
                     alt="<?= htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']) ?>">
              </div>
              <div class="comment-content">
                <div class="comment-header">
                  <span class="comment-author"><?= htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']) ?></span>
                  <span class="comment-time"><?= timeAgo($reply['created_at']) ?></span>
                </div>
                <p class="comment-text"><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                <div class="comment-actions">
                  <button class="comment-action-btn" data-action="like" data-comment-id="<?= $reply['id'] ?>">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                      <path d="M14 2a4 4 0 00-3.464 6H6a4 4 0 00-4 4v0a4 4 0 004 4h8a4 4 0 004-4v-6a4 4 0 00-4-4z" stroke="currentColor" stroke-width="2"/>
                  </button>
                  <button class="comment-action-btn" data-action="dislike" data-comment-id="<?= $reply['id'] ?>">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                      <path d="M14 18a4 4 0 003.464-6H6a4 4 0 01-4-4v0a4 4 0 014-4h8a4 4 0 014 4v6a4 4 0 01-4 4z" stroke="currentColor" stroke-width="2"/>
                  </button>
                </div>
              </div>
            </article>
            <?php endif; ?>
          <?php endforeach; ?>

          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-comments">No comments yet. Be the first to comment!</p>
      <?php endif; ?>

    </section>

    <!-- Blog Actions (for owner) -->
    <?php if ($data['isOwner']): ?>
    <div class="blog-actions" id="blog-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/blogs/edit'">Edit Blog</button>
      <button class="btn btn--danger" id="delete-blog-btn" data-blog-id="<?= $data['blog']['id'] ?>">Delete Blog</button>
    </div>
    <?php else: ?>
    <!-- Report Button (for non-owners) -->
    <div class="blog-report" id="blog-report">
      <button class="btn btn--outline" id="report-blog-btn" data-blog-id="<?= $data['blog']['id'] ?>">Report Post</button>
    </div>
    <?php endif; ?>

  </div>

  <!-- Report Modal -->
  <div class="modal-overlay" id="report-modal" role="dialog" aria-labelledby="report-title" aria-modal="true" style="display: none;">
    <div class="modal">
      <div class="modal-header">
        <h2 id="report-title" class="modal-title">Report Blog Post</h2>
        <button class="modal-close" aria-label="Close modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
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
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
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

<script type="module" src="/js/app/community/blog-view.js"></script>
<script type="module" src="/js/app/community/community.js"></script>
<script type="module" src="/js/app/community/blog-view.js"></script>