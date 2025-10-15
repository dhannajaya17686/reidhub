<link href="/css/app/admin/edu-forum/manage-forum.css" rel="stylesheet">
      <!-- Management Page Content -->
      <div class="manage-page">
        
        <!-- Page Header -->
        <div class="page-header">
          <h1 class="page-title">Manage Questions</h1>
          <p class="page-subtitle">
            Monitor, moderate, and manage all questions in your educational forum. Keep track of question status, user activity, and community engagement.
          </p>
          
          <!-- Statistics Cards -->
          <div class="page-stats">
            <div class="stat-card">
              <div class="stat-number">247</div>
              <div class="stat-label">Total Questions</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">189</div>
              <div class="stat-label">Active Questions</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">12</div>
              <div class="stat-label">Reported Questions</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">8</div>
              <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
              <div class="stat-number">38</div>
              <div class="stat-label">Deleted Questions</div>
            </div>
          </div>
        </div>
        
        <!-- Table Controls -->
        <div class="table-controls">
          <div class="controls-top">
            <div class="controls-left">
              <!-- Filter Tabs -->
              <div class="filter-tabs" role="tablist" aria-label="Question filters">
                <button class="filter-tab filter-tab--active" role="tab" aria-selected="true" data-filter="all">
                  All Questions
                  <span class="filter-tab-count">247</span>
                </button>
                <button class="filter-tab" role="tab" aria-selected="false" data-filter="active">
                  Active Questions
                  <span class="filter-tab-count">189</span>
                </button>
                <button class="filter-tab" role="tab" aria-selected="false" data-filter="deleted">
                  Deleted Questions
                  <span class="filter-tab-count">38</span>
                </button>
                <button class="filter-tab" role="tab" aria-selected="false" data-filter="reported">
                  Reported Questions
                  <span class="filter-tab-count">12</span>
                </button>
              </div>
            </div>
            
            <div class="controls-right">
              <!-- Export Button -->
              <button class="export-btn" data-export-questions>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                </svg>
                Export Data
              </button>
            </div>
          </div>
          
          <!-- Advanced Filters -->
          <div class="advanced-filters">
            <!-- Status Filter -->
            <div class="filter-group">
              <label class="filter-label" for="status-filter">Status:</label>
              <select id="status-filter" class="filter-select" data-status-filter>
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="reported">Reported</option>
                <option value="deleted">Deleted</option>
                <option value="pending">Pending Review</option>
              </select>
            </div>
            
            <!-- Date Range -->
            <div class="filter-group">
              <label class="filter-label">Date Range:</label>
              <div class="date-range-picker">
                <input type="date" class="date-input" data-date-from aria-label="From date">
                <span class="date-separator">to</span>
                <input type="date" class="date-input" data-date-to aria-label="To date">
              </div>
            </div>
            
            <!-- Search -->
            <div class="filter-group">
              <div class="table-search">
                <input 
                  type="search" 
                  class="table-search-input" 
                  placeholder="Search questions, users, or IDs..."
                  aria-label="Search questions"
                  data-table-search
                >
                <svg class="table-search-icon" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M21.71 20.29l-5.4-5.4a9 9 0 10-1.42 1.42l5.4 5.4a1 1 0 001.42-1.42zM11 18a7 7 0 117-7 7 7 0 01-7 7z"/>
                </svg>
              </div>
            </div>
          </div>
          
          <!-- Bulk Actions -->
          <div class="bulk-actions is-hidden" data-bulk-actions>
            <span class="bulk-actions-label"><span data-selection-count>0</span> selected</span>
            <button class="bulk-btn" data-bulk-action="activate">Activate</button>
            <button class="bulk-btn" data-bulk-action="deactivate">Deactivate</button>
            <button class="bulk-btn bulk-btn--danger" data-bulk-action="delete">Delete</button>
          </div>
        </div>
        
        <!-- Data Table -->
        <div class="data-table-container relative">
          <table class="data-table" data-questions-table>
            <thead class="table-header">
              <tr>
                <th>
                  <input type="checkbox" class="row-checkbox" data-select-all aria-label="Select all questions">
                </th>
                <th>
                  <div class="sortable-header" data-sort="id">
                    Question ID
                    <svg class="sort-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                  </div>
                </th>
                <th>
                  <div class="sortable-header" data-sort="title">
                    Question Name
                    <svg class="sort-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                  </div>
                </th>
                <th>
                  <div class="sortable-header" data-sort="owner">
                    Owner
                    <svg class="sort-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                  </div>
                </th>
                <th>
                  <div class="sortable-header" data-sort="date">
                    Question Placed
                    <svg class="sort-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                  </div>
                </th>
                <th>
                  <div class="sortable-header" data-sort="status">
                    Status
                    <svg class="sort-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                  </div>
                </th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-body" data-table-body>
              
              <!-- Question Row 1 -->
              <tr class="fade-in" data-question-id="12345">
                <td>
                  <input type="checkbox" class="row-checkbox" data-row-select aria-label="Select question #12345">
                </td>
                <td>
                  <span class="question-id">#12345</span>
                </td>
                <td>
                  <div class="question-title" data-question-link="12345">
                    Send message from a tonic stream
                  </div>
                </td>
                <td>
                  <div class="owner-info">
                    <div class="owner-avatar">DM</div>
                    <span class="owner-name">Dhananjaya</span>
                  </div>
                </td>
                <td>
                  <div class="date-cell">2025-07-26</div>
                </td>
                <td>
                  <div class="status-badge status-badge--active">
                    <div class="status-indicator status-indicator--active"></div>
                    Active
                  </div>
                </td>
                <td>
                  <div class="table-actions">
                    <button class="action-btn action-btn--primary" data-action="view" data-question="12345">View</button>
                    <button class="action-btn" data-action="edit" data-question="12345">Edit</button>
                    <button class="action-btn action-btn--danger" data-action="delete" data-question="12345">Delete</button>
                  </div>
                </td>
              </tr>
              
              <!-- Question Row 2 -->
              <tr class="fade-in" data-question-id="12346">
                <td>
                  <input type="checkbox" class="row-checkbox" data-row-select aria-label="Select question #12346">
                </td>
                <td>
                  <span class="question-id">#12346</span>
                </td>
                <td>
                  <div class="question-title" data-question-link="12346">
                    What is a private key?
                  </div>
                </td>
                <td>
                  <div class="owner-info">
                    <div class="owner-avatar">JO</div>
                    <span class="owner-name">Jonty</span>
                  </div>
                </td>
                <td>
                  <div class="date-cell">2025-07-25</div>
                </td>
                <td>
                  <div class="status-badge status-badge--reported">
                    <div class="status-indicator status-indicator--reported"></div>
                    Reported
                  </div>
                </td>
                <td>
                  <div class="table-actions">
                    <button class="action-btn action-btn--primary" data-action="view" data-question="12346">View</button>
                    <button class="action-btn" data-action="review" data-question="12346">Review</button>
                    <button class="action-btn action-btn--danger" data-action="delete" data-question="12346">Delete</button>
                  </div>
                </td>
              </tr>
              
              <!-- Question Row 3 -->
              <tr class="fade-in" data-question-id="12347">
                <td>
                  <input type="checkbox" class="row-checkbox" data-row-select aria-label="Select question #12347">
                </td>
                <td>
                  <span class="question-id">#12347</span>
                </td>
                <td>
                  <div class="question-title" data-question-link="12347">
                    How to implement React hooks with TypeScript?
                  </div>
                </td>
                <td>
                  <div class="owner-info">
                    <div class="owner-avatar">SK</div>
                    <span class="owner-name">Sarah Kim</span>
                  </div>
                </td>
                <td>
                  <div class="date-cell">2025-07-24</div>
                </td>
                <td>
                  <div class="status-badge status-badge--active">
                    <div class="status-indicator status-indicator--active"></div>
                    Active
                  </div>
                </td>
                <td>
                  <div class="table-actions">
                    <button class="action-btn action-btn--primary" data-action="view" data-question="12347">View</button>
                    <button class="action-btn" data-action="edit" data-question="12347">Edit</button>
                    <button class="action-btn action-btn--danger" data-action="delete" data-question="12347">Delete</button>
                  </div>
                </td>
              </tr>
              
              <!-- Question Row 4 -->
              <tr class="fade-in" data-question-id="12348">
                <td>
                  <input type="checkbox" class="row-checkbox" data-row-select aria-label="Select question #12348">
                </td>
                <td>
                  <span class="question-id">#12348</span>
                </td>
                <td>
                  <div class="question-title" data-question-link="12348">
                    Database optimization techniques for large datasets
                  </div>
                </td>
                <td>
                  <div class="owner-info">
                    <div class="owner-avatar">MR</div>
                    <span class="owner-name">Mike Ross</span>
                  </div>
                </td>
                <td>
                  <div class="date-cell">2025-07-23</div>
                </td>
                <td>
                  <div class="status-badge status-badge--pending">
                    <div class="status-indicator status-indicator--pending"></div>
                    Pending
                  </div>
                </td>
                <td>
                  <div class="table-actions">
                    <button class="action-btn action-btn--primary" data-action="view" data-question="12348">View</button>
                    <button class="action-btn" data-action="approve" data-question="12348">Approve</button>
                    <button class="action-btn action-btn--danger" data-action="reject" data-question="12348">Reject</button>
                  </div>
                </td>
              </tr>
              
              <!-- Question Row 5 -->
              <tr class="fade-in" data-question-id="12349">
                <td>
                  <input type="checkbox" class="row-checkbox" data-row-select aria-label="Select question #12349">
                </td>
                <td>
                  <span class="question-id">#12349</span>
                </td>
                <td>
                  <div class="question-title" data-question-link="12349">
                    Best practices for API security in Node.js
                  </div>
                </td>
                <td>
                  <div class="owner-info">
                    <div class="owner-avatar">AL</div>
                    <span class="owner-name">Alex Lin</span>
                  </div>
                </td>
                <td>
                  <div class="date-cell">2025-07-22</div>
                </td>
                <td>
                  <div class="status-badge status-badge--deleted">
                    <div class="status-indicator status-indicator--deleted"></div>
                    Deleted
                  </div>
                </td>
                <td>
                  <div class="table-actions">
                    <button class="action-btn action-btn--primary" data-action="view" data-question="12349">View</button>
                    <button class="action-btn" data-action="restore" data-question="12349">Restore</button>
                    <button class="action-btn action-btn--danger" data-action="permanent-delete" data-question="12349">Permanent Delete</button>
                  </div>
                </td>
              </tr>
              
            </tbody>
          </table>
          
          <!-- Loading Overlay -->
          <div class="loading-overlay is-hidden" data-loading-overlay>
            <div class="loading-spinner"></div>
          </div>
        </div>
        
        <!-- Table Footer -->
        <div class="table-footer">
          <div class="table-info">
            Showing <span data-showing-start>1</span>-<span data-showing-end>10</span> of <span data-total-count>247</span> questions
          </div>
          
          <div class="per-page-selector">
            <span class="filter-label">Show:</span>
            <select class="per-page-select" data-per-page-select>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="filter-label">per page</span>
          </div>
          
          <div class="pagination" data-pagination>
            <button class="pagination-btn pagination-btn--disabled" data-page="prev" aria-label="Previous page" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
              </svg>
            </button>
            
            <button class="pagination-btn pagination-btn--active" data-page="1">1</button>
            <button class="pagination-btn" data-page="2">2</button>
            <button class="pagination-btn" data-page="3">3</button>
            <span class="pagination-ellipsis">...</span>
            <button class="pagination-btn" data-page="10">10</button>
            
            <button class="pagination-btn" data-page="next" aria-label="Next page">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
              </svg>
            </button>
          </div>
        </div>
        
      </div>