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
  </div>

  <nav class="tab-navigation" aria-label="Club categories">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all">All Clubs</button>
      <button class="tab-button" data-tab="manage">My Clubs</button>
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
      <button class="pill" data-category="academic">Academic</button>
      <button class="pill" data-category="cultural">Cultural</button>
      <button class="pill" data-category="sports">Sports</button>
      <button class="pill" data-category="technology">Technology</button>
    </div>

    <section class="blogs-section">
      <div class="blogs-grid" id="clubs-grid">
        <!-- Sample Clubs -->
        <article class="blog-card" data-category="technology">
          <a href="/dashboard/community/clubs/view?id=1" class="blog-card__link">
            <div class="blog-card__image">
              <img src="https://via.placeholder.com/400x400/4A90E2/ffffff?text=Tech+Club" alt="Tech Club">
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title">Technology & Innovation Club</h3>
              <div class="blog-card__meta">
                <span class="blog-card__author">50 members</span>
              </div>
            </div>
          </a>
        </article>

        <article class="blog-card" data-category="sports">
          <a href="/dashboard/community/clubs/view?id=2" class="blog-card__link">
            <div class="blog-card__image">
              <img src="https://via.placeholder.com/400x400/2ECC71/ffffff?text=Sports" alt="Sports">
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title">Campus Sports Club</h3>
              <div class="blog-card__meta">
                <span class="blog-card__author">75 members</span>
              </div>
            </div>
          </a>
        </article>

        <article class="blog-card" data-category="academic">
          <a href="/dashboard/community/clubs/view?id=3" class="blog-card__link">
            <div class="blog-card__image">
              <img src="https://via.placeholder.com/400x400/9B59B6/ffffff?text=Debate" alt="Debate">
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title">Debate Society</h3>
              <div class="blog-card__meta">
                <span class="blog-card__author">30 members</span>
              </div>
            </div>
          </a>
        </article>
      </div>
    </section>
  </div>

  <!-- My Clubs Tab -->
  <div class="tab-content is-hidden" data-tab-content="manage">
    <section class="manage-blogs-section">
      <div class="section-header">
        <h2 class="section-title">My Clubs</h2>
        <a href="/dashboard/community/clubs/create" class="btn btn--primary">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <line x1="10" y1="4" x2="10" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="4" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Create a new club
        </a>
      </div>

      <div class="blogs-grid" id="my-clubs-grid">
        <div class="upload-card">
          <a href="/dashboard/community/clubs/create" class="upload-card__link">
            <svg class="upload-card__icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
              <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
              <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="upload-card__text">Create a new club</span>
          </a>
        </div>
      </div>
    </section>
  </div>

</main>

<script>
  window.COMMUNITY_MODULE = 'clubs';
  window.API_BASE = '/api/community/clubs';
</script>
<script type="module" src="/js/app/community/clubs.js"></script>
