<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

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
      <?= htmlspecialchars($data['club']['name'] ?? 'Club') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Club Details">
  
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['club']['name']) ?></h1>
      
      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['club']['image_url'] ?? 'https://via.placeholder.com/900x400/667EEA/ffffff?text=' . urlencode(substr($data['club']['name'], 0, 1))) ?>" 
             alt="<?= htmlspecialchars($data['club']['name']) ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author">Category: <?= htmlspecialchars(ucfirst($data['club']['category'])) ?></span>
        <span class="blog-separator">•</span>
        <span class="blog-published"><?= (int)($data['club']['actual_member_count'] ?? $data['club']['member_count'] ?? 0) ?> members</span>
        <span class="blog-separator">•</span>
        <span class="blog-views">Created <?= date('M j, Y', strtotime($data['club']['created_at'])) ?></span>
      </div>
      
      <div class="blog-creator">
        <p><strong>Created by:</strong> <?= htmlspecialchars($data['club']['creator_first_name'] . ' ' . $data['club']['creator_last_name']) ?></p>
      </div>
    </header>

    <article class="blog-content">
      <?php if (!empty($data['club']['description'])): ?>
        <p><?= nl2br(htmlspecialchars($data['club']['description'])) ?></p>
      <?php else: ?>
        <p>No description available for this club yet.</p>
      <?php endif; ?>
    </article>

    <div class="blog-interactions">
      <?php if ($data['isMember']): ?>
        <?php if ($data['isOwner']): ?>
          <span class="badge-owner">You are the owner</span>
        <?php elseif ($data['isAdmin']): ?>
          <span class="badge-admin">You are an admin</span>
          <button class="btn btn--danger" onclick="leaveClub(<?= $data['club']['id'] ?>)">Leave Club</button>
        <?php else: ?>
          <span class="badge-member">✓ Member</span>
          <button class="btn btn--danger" onclick="leaveClub(<?= $data['club']['id'] ?>)">Leave Club</button>
        <?php endif; ?>
      <?php else: ?>
        <button class="btn btn--primary" onclick="joinClub(<?= $data['club']['id'] ?>)">Join Club</button>
      <?php endif; ?>
    </div>

    <?php if ($data['isOwner'] || ($data['isAdmin'] && $data['isCommunityAdmin'])): ?>
    <div class="blog-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/clubs/edit?id=<?= $data['club']['id'] ?>'">Edit Club</button>
      <?php if ($data['isOwner']): ?>
      <button class="btn btn--danger" onclick="deleteClub(<?= $data['club']['id'] ?>)">Delete Club</button>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="blog-report">
      <button class="report-icon" id="report-club-btn" data-report-type="club" data-id="<?= $data['club']['id'] ?>" title="Report" aria-label="Report club">
        <span class="material-symbols-outlined" aria-hidden="true">report</span>
      </button>
    </div>
    <?php endif; ?>
  </div>

</main>

<!-- Shared Report Modal -->
<div class="modal-overlay" id="report-modal" role="dialog" aria-labelledby="report-title" aria-modal="true" style="display: none;">
  <div class="modal">
    <div class="modal-header">
      <h2 id="report-title" class="modal-title">Report</h2>
      <button class="modal-close" aria-label="Close modal">
        <span class="material-symbols-outlined" aria-hidden="true">close</span>
      </button>
    </div>
    <form class="modal-body" id="report-form">
      <div class="form-group">
        <label for="report-description" class="form-label">Description</label>
        <textarea id="report-description" name="description" class="form-textarea" rows="6" placeholder="Tell us what's wrong..." required></textarea>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn btn--primary">Submit Report</button>
      </div>
    </form>
  </div>
</div>

<script type="module" src="/js/app/community/blog-view.js"></script>

<style>
.badge-owner, .badge-admin, .badge-member {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
}
.badge-owner {
  background: #0466C8;
  color: white;
}
.badge-admin {
  background: #f39c12;
  color: white;
}
.badge-member {
  background: #2ecc71;
  color: white;
}
.blog-creator {
  margin: 1rem 0;
  color: #666;
  font-size: 0.875rem;
}
</style>

<script>
function joinClub(clubId) {
  if (!confirm('Do you want to join this club?')) return;
  
  fetch('/dashboard/community/clubs/join', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({club_id: clubId})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Failed to join club: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => alert('Error: ' + err.message));
}

function leaveClub(clubId) {
  if (!confirm('Are you sure you want to leave this club?')) return;
  
  fetch('/dashboard/community/clubs/leave', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({club_id: clubId})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Failed to leave club: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => alert('Error: ' + err.message));
}

function deleteClub(clubId) {
  if (!confirm('Are you sure you want to DELETE this club? This cannot be undone!')) return;
  
  fetch('/dashboard/community/clubs/delete', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({club_id: clubId})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      window.location.href = '/dashboard/community/clubs';
    } else {
      alert('Failed to delete club: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => alert('Error: ' + err.message));
}

function reportClub(clubId) {
  alert('Report functionality coming soon!');
}
</script>

