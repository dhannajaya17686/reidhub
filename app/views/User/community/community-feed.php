<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/user/community/community-feed.css">

<main class="community-feed-main">
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Community Feed</h1>
      <p class="page-description">Stay connected with the campus community</p>
    </div>
    <?php if ($data['isCommunityAdmin']): ?>
    <div class="header-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/create-post'">
        <span class="icon">+</span> Create Post
      </button>
      <button class="btn btn--secondary" onclick="window.location.href='/dashboard/community/my-posts'">
        My Posts
      </button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Community Admin Badge -->
  <?php if ($data['isCommunityAdmin']): ?>
  <div class="admin-badge-container">
    <div class="admin-badge">
      <span class="badge-icon">⭐</span>
      <span>You are a Community Admin</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- Feed Container -->
  <div class="feed-container">
    
    <?php if (empty($data['posts'])): ?>
    <!-- Empty State -->
    <div class="empty-state">
      <div class="empty-icon">📝</div>
      <h3>No posts yet</h3>
      <p>Be the first to share something with the community!</p>
      <?php if ($data['isCommunityAdmin']): ?>
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/create-post'">
        Create First Post
      </button>
      <?php endif; ?>
    </div>
    <?php else: ?>
    
    <!-- Posts Feed -->
    <?php foreach ($data['posts'] as $post): ?>
    <article class="post-card">
      <!-- Post Header -->
      <div class="post-header">
        <div class="post-author">
          <div class="author-avatar">
            <?= strtoupper(substr($post['first_name'] ?? 'U', 0, 1)) ?>
          </div>
          <div class="author-info">
            <h4 class="author-name"><?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?></h4>
            <span class="post-time"><?= date('M j, Y • g:i A', strtotime($post['created_at'])) ?></span>
          </div>
        </div>
        
        <?php if ($data['isCommunityAdmin'] && (int)$post['author_id'] === (int)$data['user']['id']): ?>
        <div class="post-actions">
          <button class="btn-icon" onclick="editPost(<?= $post['id'] ?>)">✏️</button>
          <button class="btn-icon" onclick="deletePost(<?= $post['id'] ?>)">🗑️</button>
        </div>
        <?php endif; ?>
      </div>

      <!-- Post Content -->
      <div class="post-body">
        <?php if (!empty($post['title'])): ?>
        <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
        <?php endif; ?>
        
        <div class="post-content">
          <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>

        <!-- Post Images -->
        <?php if (!empty($post['images'])): ?>
        <?php $images = json_decode($post['images'], true); ?>
        <?php if (is_array($images) && count($images) > 0): ?>
        <div class="post-images <?= count($images) > 1 ? 'post-images--grid' : '' ?>">
          <?php foreach ($images as $image): ?>
          <img src="<?= htmlspecialchars($image) ?>" alt="Post image" class="post-image">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- Post Footer -->
      <div class="post-footer">
        <button class="post-action" onclick="likePost(<?= $post['id'] ?>)">
          <span class="icon">❤️</span>
          <span><?= $post['likes_count'] ?? 0 ?> Likes</span>
        </button>
        <button class="post-action" onclick="toggleComments(<?= $post['id'] ?>)">
          <span class="icon">💬</span>
          <span><?= $post['comments_count'] ?? 0 ?> Comments</span>
        </button>
        <button class="post-action" onclick="sharePost(<?= $post['id'] ?>)">
          <span class="icon">🔗</span>
          <span>Share</span>
        </button>
      </div>

      <!-- Comments Section (Hidden by default) -->
      <div class="comments-section" id="comments-<?= $post['id'] ?>" style="display: none;">
        <div class="comments-list">
          <!-- Comments will be loaded here -->
        </div>
        <div class="comment-input">
          <textarea placeholder="Write a comment..." class="comment-textarea"></textarea>
          <button class="btn btn--primary btn--small" onclick="postComment(<?= $post['id'] ?>)">Comment</button>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
    
    <?php endif; ?>
  </div>

  <!-- Quick Links Sidebar -->
  <aside class="quick-links">
    <div class="quick-links-card">
      <h3>Explore Community</h3>
      <ul class="links-list">
        <li><a href="/dashboard/community/clubs">👥 Clubs & Societies</a></li>
        <li><a href="/dashboard/community/events">📅 Events</a></li>
        <li><a href="/dashboard/community/blogs">📝 Blogs</a></li>
      </ul>
    </div>
  </aside>
</main>

<script>
function editPost(postId) {
  window.location.href = `/dashboard/community/edit-post?id=${postId}`;
}

function deletePost(postId) {
  if (confirm('Are you sure you want to delete this post?')) {
    fetch(`/dashboard/community/delete-post`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({post_id: postId})
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Failed to delete post');
      }
    });
  }
}

function likePost(postId) {
  fetch(`/dashboard/community/like-post`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({post_id: postId})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    }
  });
}

function toggleComments(postId) {
  const commentsSection = document.getElementById(`comments-${postId}`);
  commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
}

function postComment(postId) {
  const textarea = document.querySelector(`#comments-${postId} .comment-textarea`);
  const content = textarea.value.trim();
  
  if (!content) return;
  
  fetch(`/dashboard/community/add-comment`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({post_id: postId, content: content})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    }
  });
}

function sharePost(postId) {
  const url = `${window.location.origin}/dashboard/community/post/${postId}`;
  navigator.clipboard.writeText(url).then(() => {
    alert('Link copied to clipboard!');
  });
}
</script>
