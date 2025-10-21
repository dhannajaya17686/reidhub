<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/events.css">

<!-- Main Events Content Area -->
<main class="community-main events-main" role="main" aria-label="Events Dashboard">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Events</h1>
  </div>

  <!-- Tab Navigation -->
  <nav class="tab-navigation" aria-label="Event categories">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" data-tab="events">
        Events
      </button>
      <button class="tab-button" data-tab="add">
        Add events
      </button>
    </div>
  </nav>

  <!-- Events Tab Content -->
  <div class="tab-content" data-tab-content="events">
    
    <!-- Calendar Controls -->
    <div class="calendar-controls">
      <button class="btn btn--secondary btn--icon" id="today-btn">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <rect x="3" y="4" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
          <path d="M3 8h14M7 2v4M13 2v4" stroke="currentColor" stroke-width="2"/>
        </svg>
        Today
      </button>
      
      <div class="date-range">
        <button class="date-nav-btn" id="prev-week">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M12 16l-6-6 6-6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <span class="date-range-text" id="date-range-text">Loading...</span>
        <button class="date-nav-btn" id="next-week">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M8 16l6-6-6-6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
      </div>

      <div class="view-controls">
        <button class="btn btn--secondary" id="schedule-btn">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="2"/>
          </svg>
          Schedule
        </button>
      </div>
    </div>

    <!-- Weekly Schedule View -->
    <div class="weekly-schedule" id="weekly-schedule">
      <!-- Will be populated by JavaScript -->
      <div class="loading-state">Loading events...</div>
    </div>

    <!-- This Month Section -->
    <section class="content-section">
      <h2 class="section-title">This Month</h2>
      <div class="events-list" id="this-month-events">
        <div class="loading-state">Loading events...</div>
      </div>
    </section>

  </div>

  <!-- Add Events Tab Content -->
  <div class="tab-content is-hidden" data-tab-content="add">
    
    <div class="add-event-container">
      <h2 class="page-subtitle">Add Events</h2>

      <form class="event-form" id="add-event-form" method="POST" enctype="multipart/form-data">
        
        <div class="form-grid">
          
          <!-- Left Column -->
          <div class="form-column">
            
            <!-- Event Name -->
            <div class="form-group">
              <label for="event-name" class="form-label">Event Name</label>
              <input 
                type="text" 
                id="event-name" 
                name="event_name" 
                class="form-input" 
                placeholder="Enter event name"
                required
              >
            </div>

            <!-- Date -->
            <div class="form-group">
              <label for="event-date" class="form-label">Date</label>
              <input 
                type="date" 
                id="event-date" 
                name="event_date" 
                class="form-input" 
                required
              >
            </div>

            <!-- Time -->
            <div class="form-group">
              <label for="event-time" class="form-label">Time</label>
              <input 
                type="time" 
                id="event-time" 
                name="event_time" 
                class="form-input" 
                required
              >
            </div>

            <!-- Venue -->
            <div class="form-group">
              <label for="event-venue" class="form-label">Venue (Hall ID)</label>
              <input 
                type="text" 
                id="event-venue" 
                name="event_venue" 
                class="form-input" 
                placeholder="Enter Venue"
                required
              >
            </div>

            <!-- Event Image -->
            <div class="form-group">
              <label class="form-label">Event Image</label>
              <div class="file-upload-area" id="file-upload-area">
                <input type="file" id="event-image" name="event_image" accept="image/*" class="file-input" required>
                <div class="file-upload-content">
                  <button type="button" class="btn btn--primary" id="upload-trigger">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                      <path d="M10 14V6M10 6L7 9M10 6L13 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M3 14v2a2 2 0 002 2h10a2 2 0 002-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Upload an image
                  </button>
                  <p class="upload-text">Or Drop an image here</p>
                  <p class="upload-hint">Supported formats: png, jpeg, jpg</p>
                </div>
              </div>
              <div class="file-preview" id="file-preview" style="display: none;">
                <img id="preview-image" src="" alt="Preview" class="preview-image">
                <button type="button" class="preview-remove" id="preview-remove">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <line x1="5" y1="5" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="15" y1="5" x2="5" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>
            </div>

          </div>

          <!-- Right Column -->
          <div class="form-column">
            
            <!-- Event Description -->
            <div class="form-group form-group--full">
              <label for="event-description" class="form-label">Event Description</label>
              <textarea 
                id="event-description" 
                name="event_description" 
                class="form-textarea" 
                rows="20"
                placeholder="Add description"
                required
              ></textarea>
            </div>

          </div>

        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button type="submit" class="btn btn--primary btn--large">Add event</button>
        </div>

      </form>
    </div>

  </div>

</main>

<script>
  window.COMMUNITY_MODULE = 'events';
  window.API_BASE = '/api/community/events';
</script>
<script type="module" src="/js/app/community/community.js"></script>
<script type="module" src="/js/app/community/events.js"></script>
