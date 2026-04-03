<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/admin/community-and-social.css">

<main class="community-admin-main" role="main" aria-label="Community Management">
  <div class="page-header">
    <div class="header-content">
      <div class="header-text">
        <h1 class="page-title">Community & Social Management</h1>
        <p class="page-description">Manage admins, reported blogs, clubs, and events</p>
      </div>
    </div>
  </div>

  <nav class="tab-navigation" role="tablist" aria-label="Community management sections">
    <div class="tab-list">
      <button class="tab-button tab-button--active" data-tab="community-admins" role="tab" aria-selected="true">
        Community Admins
      </button>
      <button class="tab-button" data-tab="blog-posts" role="tab" aria-selected="false">
        Reported Blogs
      </button>
      <button class="tab-button" data-tab="clubs-societies" role="tab" aria-selected="false">
        Clubs & Societies
      </button>
      <button class="tab-button" data-tab="events" role="tab" aria-selected="false">
        Events
      </button>
    </div>
  </nav>

  <div class="tab-content active" id="community-admins-content">
    <div class="content-header">
      <h2 class="content-title">Manage Community Admins</h2>
    </div>

    <div class="filters-section">
      <div class="filter-controls" style="width: 100%;">
        <input
          type="text"
          id="admin-user-search"
          class="search-input"
          placeholder="Search username, email or reg no"
          list="admin-user-results"
          style="min-width: 280px;"
        >
        <datalist id="admin-user-results"></datalist>

        <select class="filter-select" id="admin-role-select">
          <option value="">Permission</option>
          <option value="club_admin">Club Admin</option>
          <option value="event_coordinator">Event Coordinator</option>
          <option value="moderator">Moderator</option>
        </select>

        <button class="action-btn view-btn" id="add-community-admin-btn" type="button">Add Admin</button>
      </div>
    </div>

    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Permission</th>
            <th>Added At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="community-admins-tbody">
          <tr class="table-row">
            <td colspan="6">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-content" id="blog-posts-content">
    <div class="content-header">
      <h2 class="content-title">Reported Blogs</h2>
    </div>

    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search reported blogs" class="search-input" id="reported-blogs-search">
        <button class="search-btn" type="button">🔍</button>
      </div>
    </div>

    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Blog ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Date</th>
            <th>Status</th>
            <th>Reports</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="reported-blogs-tbody">
          <tr class="table-row">
            <td colspan="7">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-content" id="clubs-societies-content">
    <div class="content-header">
      <h2 class="content-title">All Clubs & Societies</h2>
    </div>

    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search clubs" class="search-input" id="clubs-search">
        <button class="search-btn" type="button">🔍</button>
      </div>
    </div>

    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Club ID</th>
            <th>Name</th>
            <th>Creator</th>
            <th>Members</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="clubs-tbody">
          <tr class="table-row">
            <td colspan="6">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-content" id="events-content">
    <div class="content-header">
      <h2 class="content-title">All Events</h2>
    </div>

    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search events" class="search-input" id="events-search">
        <button class="search-btn" type="button">🔍</button>
      </div>
    </div>

    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Event ID</th>
            <th>Name</th>
            <th>Organizer</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="events-tbody">
          <tr class="table-row">
            <td colspan="6">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script src="/js/app/admin/community-and-social.js"></script>
