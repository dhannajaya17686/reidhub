<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0">

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
      Blogs
    </li>
  </ol>
</nav>

<!-- Main Blogs Content Area -->
<main class="blogs-main" role="main" aria-label="Blogs Dashboard">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Community Blogs</h1>
    <p class="page-subtitle">
      Discover stories, thinking, and expertise from writers on campus
    </p>
    <a href="/dashboard/community/blogs/create" class="btn btn--primary">
      <span class="material-symbols-outlined" aria-hidden="true">add</span>
      Write a Blog
    </a>
  </div>

  <!-- Tab Navigation -->
  <nav class="tab-navigation" aria-label="Blog categories">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all">
        All Blogs
      </button>
      <button class="tab-button" data-tab="manage">
        My Blogs
      </button>
    </div>
  </nav>

  <!-- All Blogs Tab Content -->
  <div class="tab-content" data-tab-content="all">
    
    <!-- Search Bar -->
    <div class="search-section">
      <div class="search-bar">
        <span class="material-symbols-outlined search-icon" aria-hidden="true">search</span>
        <input type="text" id="blog-search" class="search-input" placeholder="Search blog posts">
      </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="category-pills">
      <button class="pill pill--active" data-category="all">All</button>
      <button class="pill" data-category="academics">Academics</button>
      <button class="pill" data-category="campus-life">Campus Life</button>
      <button class="pill" data-category="student-tips">Student Tips</button>
      <button class="pill" data-category="events">Events</button>
    </div>

    <!-- Blog Posts Grid -->
    <section class="blogs-section">
      <div class="blogs-grid" id="blogs-grid">
        <div class="empty-state">
          <div class="empty-icon">⏳</div>
          <h3>Loading blogs...</h3>
          <p>Please wait while we fetch the blogs</p>
        </div>
      </div>
    </section>
  </div>

  <!-- Manage Blogs Tab Content -->
  <div class="tab-content is-hidden" data-tab-content="manage">
    
    <section class="blogs-section">
      <div class="section-header">
        <h2 class="section-title">My Blogs</h2>
        <a href="/dashboard/community/blogs/create" class="btn btn--primary">
          <span class="material-symbols-outlined" aria-hidden="true">add</span>
          Write New Blog
        </a>
      </div>

      <div class="blogs-grid" id="my-blogs-grid">
        <div class="empty-state">
          <div class="empty-icon">⏳</div>
          <h3>Loading your blogs...</h3>
          <p>Please wait</p>
        </div>
      </div>
    </section>

  </div>

</main>

<script type="module" src="/js/app/community/blogs.js"></script>
