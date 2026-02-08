<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard" class="breadcrumb__link">Dashboard</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community" class="breadcrumb__link">Community</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      Clubs
    </li>
  </ol>
</nav>

<main class="blogs-main" role="main" aria-label="Clubs Dashboard">
  
  <div class="page-header">
    <h1 class="page-title">Clubs & Societies</h1>
    <p class="page-subtitle">
      Join clubs, meet new people, and explore your interests
    </p>
    <?php if ($data['isClubAdmin']): ?>
    <a href="/dashboard/community/clubs/create" class="btn btn--primary">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <line x1="10" y1="4" x2="10" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="4" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      Create New Club
    </a>
    <?php endif; ?>
  </div>

  <nav class="tab-navigation" aria-label="Club categories">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all">All Clubs</button>
      <?php if ($data['isClubAdmin']): ?>
      <button class="tab-button" data-tab="manage">My Clubs</button>
      <?php endif; ?>
      <button class="tab-button" data-tab="joined">Joined Clubs</button>
    </div>
  </nav>

  <!-- All Clubs Tab -->
  <div class="tab-content" data-tab-content="all">
    <div class="search-section">
      <div class="search-bar">
        <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="2"/>
          <line x1="14" y1="14" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" id="club-search" class="search-input" placeholder="Search clubs">
      </div>
    </div>

    <div class="category-pills">
      <button class="pill pill--active" data-category="all">All</button>
      <?php foreach ($data['categories'] as $key => $label): ?>
        <?php if ($key !== 'all'): ?>
        <button class="pill" data-category="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></button>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <section class="blogs-section">
      <?php if (empty($data['clubs'])): ?>
      <div class="empty-state">
        <div class="empty-icon">🎯</div>
        <h3>No Clubs Yet</h3>
        <p>Be the first to create a club!</p>
        <?php if ($data['isClubAdmin']): ?>
        <a href="/dashboard/community/clubs/create" class="btn btn--primary">Create First Club</a>
        <?php endif; ?>
      </div>
      <?php else: ?>
      <div class="blogs-grid" id="clubs-grid">
        <?php foreach ($data['clubs'] as $club): ?>
        <article class="blog-card" data-category="<?= htmlspecialchars($club['category']) ?>">
          <a href="/dashboard/community/clubs/view?id=<?= $club['id'] ?>" class="blog-card__link">
            <div class="blog-card__image">
              <img src="<?= htmlspecialchars($club['image_url'] ?? 'https://via.placeholder.com/400x400/667EEA/ffffff?text=' . urlencode(substr($club['name'], 0, 1))) ?>" 
                   alt="<?= htmlspecialchars($club['name']) ?>">
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title"><?= htmlspecialchars($club['name']) ?></h3>
              <p class="blog-card__excerpt"><?= htmlspecialchars(substr($club['description'] ?? '', 0, 100)) ?><?= strlen($club['description'] ?? '') > 100 ? '...' : '' ?></p>
              <div class="blog-card__meta">
                <span class="blog-card__author"><?= (int)($club['actual_member_count'] ?? $club['member_count'] ?? 0) ?> members</span>
                <span class="blog-card__category"><?= htmlspecialchars(ucfirst($club['category'])) ?></span>
              </div>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>

  <?php if ($data['isClubAdmin']): ?>
  <!-- My Clubs Tab -->
  <div class="tab-content is-hidden" data-tab-content="manage">
    <section class="manage-blogs-section">
      <div class="section-header">
        <h2 class="section-title">My Clubs</h2>
      </div>

      <?php if (empty($data['myClubs'])): ?>
      <div class="empty-state">
        <div class="empty-icon">👥</div>
        <h3>You haven't created any clubs yet</h3>
        <p>Create your own club and build your community!</p>
        <a href="/dashboard/community/clubs/create" class="btn btn--primary">Create New Club</a>
      </div>
      <?php else: ?>
      <div class="blogs-grid" id="my-clubs-grid">
        <?php foreach ($data['myClubs'] as $club): ?>
        <article class="blog-card">
          <a href="/dashboard/community/clubs/view?id=<?= $club['id'] ?>" class="blog-card__link">
            <div class="blog-card__image">
              <img src="<?= htmlspecialchars($club['image_url'] ?? 'https://via.placeholder.com/400x400/667EEA/ffffff?text=' . urlencode(substr($club['name'], 0, 1))) ?>" 
                   alt="<?= htmlspecialchars($club['name']) ?>">
              <?php if (isset($club['member_role'])): ?>
              <span class="badge-role"><?= htmlspecialchars(ucfirst($club['member_role'])) ?></span>
              <?php endif; ?>
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title"><?= htmlspecialchars($club['name']) ?></h3>
              <p class="blog-card__excerpt"><?= htmlspecialchars(substr($club['description'] ?? '', 0, 80)) ?><?= strlen($club['description'] ?? '') > 80 ? '...' : '' ?></p>
              <div class="blog-card__meta">
                <span class="blog-card__author"><?= (int)($club['actual_member_count'] ?? $club['member_count'] ?? 0) ?> members</span>
              </div>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>
  <?php endif; ?>

  <!-- Joined Clubs Tab -->
  <div class="tab-content is-hidden" data-tab-content="joined">
    <section class="manage-blogs-section">
      <div class="section-header">
        <h2 class="section-title">Joined Clubs</h2>
      </div>

      <?php if (empty($data['joinedClubs'])): ?>
      <div class="empty-state">
        <div class="empty-icon">🎯</div>
        <h3>You haven't joined any clubs yet</h3>
        <p>Explore clubs and join ones that interest you!</p>
        <button onclick="document.querySelector('[data-tab=all]').click()" class="btn btn--secondary">Browse Clubs</button>
      </div>
      <?php else: ?>
      <div class="blogs-grid" id="joined-clubs-grid">
        <?php foreach ($data['joinedClubs'] as $club): ?>
        <article class="blog-card">
          <a href="/dashboard/community/clubs/view?id=<?= $club['id'] ?>" class="blog-card__link">
            <div class="blog-card__image">
              <img src="<?= htmlspecialchars($club['image_url'] ?? 'https://via.placeholder.com/400x400/667EEA/ffffff?text=' . urlencode(substr($club['name'], 0, 1))) ?>" 
                   alt="<?= htmlspecialchars($club['name']) ?>">
              <?php if (isset($club['member_role'])): ?>
              <span class="badge-role"><?= htmlspecialchars(ucfirst($club['member_role'])) ?></span>
              <?php endif; ?>
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title"><?= htmlspecialchars($club['name']) ?></h3>
              <p class="blog-card__excerpt"><?= htmlspecialchars(substr($club['description'] ?? '', 0, 80)) ?><?= strlen($club['description'] ?? '') > 80 ? '...' : '' ?></p>
              <div class="blog-card__meta">
                <span class="blog-card__author"><?= (int)($club['actual_member_count'] ?? $club['member_count'] ?? 0) ?> members</span>
              </div>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>

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
.badge-role {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #667eea;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.blog-card__excerpt {
  color: #666;
  font-size: 0.875rem;
  line-height: 1.5;
  margin: 0.5rem 0;
}
.blog-card__category {
  background: #f0f0f0;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
}
</style>

<script>
  window.COMMUNITY_MODULE = 'clubs';
  window.API_BASE = '/api/community/clubs';
</script>
<script type="module" src="/js/app/community/clubs.js"></script>
