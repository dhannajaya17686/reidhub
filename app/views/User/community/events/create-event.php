<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-form.css">

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
      <a href="/dashboard/community/events" class="breadcrumb__link">Events</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      Create Event
    </li>
  </ol>
</nav>

<main class="blog-form-main" role="main" aria-label="Create Event">
  
  <div class="page-header">
    <h1 class="page-title">Create New Event</h1>
  </div>

  <form class="blog-form" id="create-event-form" method="POST" action="/dashboard/community/events/create" enctype="multipart/form-data">
    
    <div class="form-container">
      <div class="form-column form-column--left">
        
        <div class="form-group">
          <label for="event-title" class="form-label">Event Title</label>
          <input type="text" id="event-title" name="title" class="form-input" placeholder="Enter event title" required maxlength="255">
          <div class="form-error" id="title-error"></div>
        </div>

        <div class="form-group">
          <label for="event-category" class="form-label">Category</label>
          <input type="text" id="event-category" name="category" class="form-input" placeholder="e.g. Workshop, Conference, Social" required>
          <div class="form-error" id="category-error"></div>
        </div>

        <div class="form-group">
          <label for="event-date" class="form-label">Event Date & Time</label>
          <input type="datetime-local" id="event-date" name="event_date" class="form-input" required>
          <div class="form-error" id="event-date-error"></div>
        </div>

        <div class="form-group">
          <label for="event-location" class="form-label">Location</label>
          <input type="text" id="event-location" name="location" class="form-input" placeholder="e.g. Main Auditorium, Hall A" required>
          <div class="form-error" id="location-error"></div>
        </div>

        <div class="form-group">
          <label for="max-attendees" class="form-label">Max Attendees (Optional)</label>
          <input type="number" id="max-attendees" name="max_attendees" class="form-input" placeholder="Leave blank for unlimited" min="1">
          <div class="form-error" id="max-attendees-error"></div>
        </div>

        <div class="form-group">
          <label for="google-form-url" class="form-label">Google Form URL (Optional)</label>
          <input type="url" id="google-form-url" name="google_form_url" class="form-input" placeholder="https://forms.gle/..." title="Paste the link to your Google Form here for attendee registration">
          <small style="color: #6b7280;">Attendees can fill this form to provide their details</small>
          <div class="form-error" id="google-form-url-error"></div>
        </div>

        <?php if (!empty($data['userClubs'])): ?>
        <div class="form-group">
          <label for="event-club" class="form-label">Associated Club (Optional)</label>
          <select id="event-club" name="club_id" class="form-select">
            <option value="">-- No Club --</option>
            <?php foreach ($data['userClubs'] as $club): ?>
            <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-error" id="club-id-error"></div>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label">Event Image (Optional)</label>
          <div class="file-upload-area" id="file-upload-area">
            <input type="file" id="event-image" name="event_image" accept="image/png,image/jpeg,image/jpg" class="file-input">
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
          <div class="form-error" id="image-url-error"></div>
        </div>

      </div>

      <div class="form-column form-column--right">
        <div class="form-group form-group--full">
          <label for="event-description" class="form-label">Event Description</label>
          <textarea id="event-description" name="description" class="form-textarea" rows="20" placeholder="Describe your event..." required></textarea>
          <div class="form-error" id="description-error"></div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn btn--secondary btn--large" onclick="history.back()">Cancel</button>
      <button type="submit" class="btn btn--primary btn--large">Create Event</button>
    </div>

  </form>

</main>

<script src="/js/app/community/event-form.js"></script>
