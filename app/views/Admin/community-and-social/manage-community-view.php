<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/admin/community-and-social.css?v=20260403-5">

<main class="community-main community-admin-main" role="main" aria-label="Community Management">
  <div class="page-header">
    <h1 class="page-title">Community & Social Management</h1>
    <p class="page-subtitle">Admin console for community admins, reported blogs, clubs, and events.</p>
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

  <div class="tab-content" id="community-admins-content">
    <section class="manage-section">
      <div class="section-header">
        <h2 class="section-title">Manage Community Admins</h2>
      </div>

      <div class="search-section">
        <div class="search-bar admin-search-row">
          <div class="admin-user-search-wrap">
            <input
              type="text"
              id="admin-user-search"
              class="search-input"
              placeholder="Search name, email, or reg no"
              autocomplete="off"
            >
            <div id="admin-user-results" class="admin-user-results is-hidden" role="listbox" aria-label="User search results"></div>
          </div>

          <span class="status-badge active" title="Fixed permission">Club Admin</span>

          <button class="action-btn view-btn action-btn--add" id="add-community-admin-btn" type="button">Add Admin</button>
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
    </section>
  </div>

  <div class="tab-content is-hidden" id="blog-posts-content">
    <section class="manage-section">
      <div class="section-header">
        <h2 class="section-title">Reported Blogs</h2>
      </div>

      <div class="search-section">
        <div class="search-bar">
          <input type="text" placeholder="Search reported blogs" class="search-input" id="reported-blogs-search">
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
    </section>
  </div>

  <div class="tab-content is-hidden" id="clubs-societies-content">
    <section class="manage-section">
      <div class="section-header">
        <h2 class="section-title">All Clubs & Societies</h2>
      </div>

      <div class="search-section">
        <div class="search-bar">
          <input type="text" placeholder="Search clubs" class="search-input" id="clubs-search">
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
    </section>
  </div>

  <div class="tab-content is-hidden" id="events-content">
    <section class="manage-section">
      <div class="section-header">
        <h2 class="section-title">All Events</h2>
      </div>

      <div class="search-section">
        <div class="search-bar">
          <input type="text" placeholder="Search events" class="search-input" id="events-search">
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

    </section>
  </div>
</main>

<script src="/js/app/admin/community-and-social.js?v=20260403-6"></script>