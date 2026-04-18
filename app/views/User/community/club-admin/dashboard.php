<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard" class="breadcrumb__link">Dashboard</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      Club Admin Portal
    </li>
  </ol>
</nav>

<main class="blogs-main" role="main" aria-label="Club Admin Dashboard">
  
  <div class="page-header">
    <h1 class="page-title">Club Admin Portal</h1>
    <p class="page-subtitle">
      Manage your clubs, members, and events
    </p>
    <a href="/dashboard/community/clubs/create" class="btn btn--primary">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <line x1="10" y1="4" x2="10" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="4" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      Create New Club
    </a>
  </div>

  <section class="blogs-section">
    <?php if (empty($data['clubs'])): ?>
    <div class="empty-state">
      <div class="empty-icon">👥</div>
      <h3>You haven't created any clubs yet</h3>
      <p>Create your first club and start building your community!</p>
      <a href="/dashboard/community/clubs/create" class="btn btn--primary">Create Your First Club</a>
    </div>
    <?php else: ?>
    <div class="blogs-grid">
      <?php foreach ($data['clubs'] as $club): ?>
      <article class="blog-card">
        <div class="blog-card__link">
          <div class="blog-card__image">
            <img src="<?= htmlspecialchars($club['image_url'] ?? 'https://via.placeholder.com/400x400/667EEA/ffffff?text=' . urlencode(substr($club['name'], 0, 1))) ?>" 
                 alt="<?= htmlspecialchars($club['name']) ?>">
            <span class="badge-role badge-role--owner">Owner</span>
          </div>
          <div class="blog-card__content">
            <h3 class="blog-card__title"><?= htmlspecialchars($club['name']) ?></h3>
            <p class="blog-card__excerpt"><?= htmlspecialchars(substr($club['description'] ?? '', 0, 80)) ?><?= strlen($club['description'] ?? '') > 80 ? '...' : '' ?></p>
            <div class="blog-card__meta">
              <span class="blog-card__author">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?= (int)($club['actual_member_count'] ?? $club['member_count'] ?? 0) ?> members
              </span>
            </div>
            <div class="club-admin-actions">
              <a href="/dashboard/community/clubs/view?id=<?= $club['id'] ?>" class="btn btn--secondary btn--sm">View Club</a>
              <a href="/dashboard/community/clubs/edit?id=<?= $club['id'] ?>" class="btn btn--secondary btn--sm">Edit Club</a>
            </div>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

</main>

<style>
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}
.empty-state h3 {
  font-size: 1.5rem;
  margin: 0 0 0.5rem;
}
.empty-state p {
  color: #666;
  margin: 0 0 1.5rem;
}

.club-admin-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  flex-wrap: wrap;
}

.btn--sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.badge-role--owner {
  background-color: #667EEA;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
}
</style>
