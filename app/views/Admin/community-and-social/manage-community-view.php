<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/admin/community-and-social.css">

<!-- Main Community Management Content -->
<main class="community-admin-main" role="main" aria-label="Community Management">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <div class="header-text">
        <h1 class="page-title">Community & Social Management</h1>
        <p class="page-description">Manage blog posts, clubs & societies, and community events</p>
      </div>
    </div>
  </div>

  <!-- Tab Navigation -->
  <nav class="tab-navigation" role="tablist" aria-label="Community management sections">
    <div class="tab-list">
      <button class="tab-button tab-button--active" data-tab="blog-posts" role="tab" aria-selected="true">
        Blog Posts
      </button>
      <button class="tab-button" data-tab="clubs-societies" role="tab" aria-selected="false">
        Clubs & Societies
      </button>
      <button class="tab-button" data-tab="events" role="tab" aria-selected="false">
        Events
      </button>
    </div>
  </nav>

  <!-- Blog Posts Tab Content -->
  <div class="tab-content active" id="blog-posts-content">
    <div class="content-header">
      <h2 class="content-title">Manage Blog Posts</h2>
      <div class="content-tabs">
        <button class="content-tab active" data-content-tab="all-blogs">All Blogs</button>
        <button class="content-tab" data-content-tab="active-blogs">Active Blogs</button>
        <button class="content-tab" data-content-tab="deleted-blogs">Deleted Blogs</button>
        <button class="content-tab" data-content-tab="reported-blogs">Reported Blogs by Users</button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search blogs by name, user, or ID" class="search-input">
        <button class="search-btn">🔍</button>
      </div>
      <div class="filter-controls">
        <select class="filter-select" id="status-filter">
          <option value="">Status</option>
          <option value="active">Active</option>
          <option value="reported">Reported</option>
          <option value="deleted">Deleted</option>
          <option value="pending">Pending</option>
        </select>
        <select class="filter-select" id="date-filter">
          <option value="">Date</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="older">Older</option>
        </select>
      </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Blog ID</th>
            <th>Blog Name</th>
            <th>User</th>
            <th>Date placed</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="table-row">
            <td class="blog-id">#12345</td>
            <td class="blog-name">UCSC Tech innovations</td>
            <td class="user-info">
              <div class="user-name">Dhananjaya Mudalige</div>
            </td>
            <td class="date-placed">2025-07-23</td>
            <td class="status">
              <span class="status-badge reported">Reported</span>
            </td>
            <td class="actions">
              <button class="action-btn view-btn">View</button>
            </td>
          </tr>
          
          <tr class="table-row">
            <td class="blog-id">#12346</td>
            <td class="blog-name">UCSC Wind band</td>
            <td class="user-info">
              <div class="user-name">Amasha</div>
            </td>
            <td class="date-placed">2025-07-23</td>
            <td class="status">
              <span class="status-badge reported">Reported</span>
            </td>
            <td class="actions">
              <button class="action-btn view-btn">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button class="pagination-btn" disabled>‹</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn">3</button>
      <span class="pagination-dots">...</span>
      <button class="pagination-btn">10</button>
      <button class="pagination-btn">›</button>
    </div>
  </div>

  <!-- Clubs & Societies Tab Content -->
  <div class="tab-content" id="clubs-societies-content">
    <div class="content-header">
      <h2 class="content-title">Manage Clubs & Societies</h2>
      <div class="content-tabs">
        <button class="content-tab active" data-content-tab="all-clubs">All Clubs</button>
        <button class="content-tab" data-content-tab="add-club">Add a New Club</button>
        <button class="content-tab" data-content-tab="reported-posts">Reported Posts</button>
      </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <span>Clubs & Societies</span>
      <span class="separator">/</span>
      <span>ACM Student Chapter</span>
      <span class="separator">/</span>
      <span class="current">Posts</span>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
      <div class="filter-controls">
        <select class="filter-select" id="club-status-filter">
          <option value="">Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
        </select>
        <select class="filter-select" id="club-date-filter">
          <option value="">Date</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
        </select>
      </div>
    </div>

    <!-- Clubs Table -->
    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Post ID</th>
            <th>Post Name</th>
            <th>Date placed</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="table-row">
            <td class="post-id">#12345</td>
            <td class="post-name">What is reidxtreme</td>
            <td class="date-placed">2025-07-25</td>
            <td class="status">
              <span class="status-badge active">Active</span>
            </td>
            <td class="actions">
              <button class="action-btn remove-btn">Remove</button>
            </td>
          </tr>
          
          <tr class="table-row">
            <td class="post-id">#12346</td>
            <td class="post-name">What is procrastinate</td>
            <td class="date-placed">2025-07-25</td>
            <td class="status">
              <span class="status-badge active">Active</span>
            </td>
            <td class="actions">
              <button class="action-btn remove-btn">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button class="pagination-btn" disabled>‹</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn">3</button>
      <span class="pagination-dots">...</span>
      <button class="pagination-btn">5</button>
      <button class="pagination-btn">›</button>
    </div>
  </div>

  <!-- Events Tab Content -->
  <div class="tab-content" id="events-content">
    <div class="content-header">
      <h2 class="content-title">Manage Events</h2>
      <div class="content-tabs">
        <button class="content-tab active" data-content-tab="all-events">All Events</button>
        <button class="content-tab" data-content-tab="upcoming-events">Upcoming Events</button>
        <button class="content-tab" data-content-tab="past-events">Past Events</button>
        <button class="content-tab" data-content-tab="reported-events">Reported Events</button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search events by name or organizer" class="search-input">
        <button class="search-btn">🔍</button>
      </div>
      <div class="filter-controls">
        <select class="filter-select" id="event-status-filter">
          <option value="">Status</option>
          <option value="upcoming">Upcoming</option>
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <select class="filter-select" id="event-type-filter">
          <option value="">Event Type</option>
          <option value="academic">Academic</option>
          <option value="social">Social</option>
          <option value="sports">Sports</option>
          <option value="cultural">Cultural</option>
        </select>
      </div>
    </div>

    <!-- Events Table -->
    <div class="data-table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Event ID</th>
            <th>Event Name</th>
            <th>Organizer</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr class="table-row">
            <td class="event-id">#EV001</td>
            <td class="event-name">Programming Workshop</td>
            <td class="organizer">ACM Student Chapter</td>
            <td class="event-date">2025-08-15</td>
            <td class="status">
              <span class="status-badge upcoming">Upcoming</span>
            </td>
            <td class="actions">
              <button class="action-btn view-btn">View</button>
              <button class="action-btn edit-btn">Edit</button>
            </td>
          </tr>
          
          <tr class="table-row">
            <td class="event-id">#EV002</td>
            <td class="event-name">Tech Talk Series</td>
            <td class="organizer">IEEE Student Branch</td>
            <td class="event-date">2025-08-20</td>
            <td class="status">
              <span class="status-badge upcoming">Upcoming</span>
            </td>
            <td class="actions">
              <button class="action-btn view-btn">View</button>
              <button class="action-btn edit-btn">Edit</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button class="pagination-btn" disabled>‹</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn">3</button>
      <span class="pagination-dots">...</span>
      <button class="pagination-btn">8</button>
      <button class="pagination-btn">›</button>
    </div>
  </div>

</main>

<script src="/js/app/admin/community-and-social.js"></script>