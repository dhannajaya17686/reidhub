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
      Edit Event
    </li>
  </ol>
</nav>

<main class="blog-form-main" role="main" aria-label="Edit Event">
  
  <div class="page-header">
    <h1 class="page-title">Edit Event</h1>
  </div>

  <form class="blog-form" id="edit-event-form" method="POST" action="/dashboard/community/events/update" enctype="multipart/form-data">
    
    <input type="hidden" name="event_id" value="<?= $data['event']['id'] ?>">
    
    <div class="form-container">
      <div class="form-column form-column--left">
        
        <div class="form-group">
          <label for="event-title" class="form-label">Event Title</label>
          <input type="text" id="event-title" name="title" class="form-input" placeholder="Enter event title" value="<?= htmlspecialchars($data['event']['title']) ?>" required maxlength="255">
          <div class="form-error" id="title-error"></div>
        </div>

        <div class="form-group">
          <label for="event-category" class="form-label">Category</label>
          <input type="text" id="event-category" name="category" class="form-input" placeholder="e.g. Workshop, Conference, Social" value="<?= htmlspecialchars($data['event']['category']) ?>" required>
          <div class="form-error" id="category-error"></div>
        </div>

        <div class="form-group">
          <label for="event-date" class="form-label">Event Date & Time</label>
          <input type="datetime-local" id="event-date" name="event_date" class="form-input" value="<?= date('Y-m-d\TH:i', strtotime($data['event']['event_date'])) ?>" required>
          <div class="form-error" id="event-date-error"></div>
        </div>

        <div class="form-group">
          <label for="event-location" class="form-label">Location</label>
          <input type="text" id="event-location" name="location" class="form-input" placeholder="e.g. Main Auditorium, Hall A" value="<?= htmlspecialchars($data['event']['location']) ?>" required>
          <div class="form-error" id="location-error"></div>
        </div>

        <div class="form-group">
          <label for="max-attendees" class="form-label">Max Attendees (Optional)</label>
          <input type="number" id="max-attendees" name="max_attendees" class="form-input" placeholder="Leave blank for unlimited" 
                 value="<?= !empty($data['event']['max_attendees']) ? $data['event']['max_attendees'] : '' ?>" min="1">
          <div class="form-error" id="max-attendees-error"></div>
        </div>

        <div class="form-group">
          <label for="google-form-url" class="form-label">Google Form URL (Optional)</label>
          <input type="url" id="google-form-url" name="google_form_url" class="form-input" placeholder="https://forms.gle/..." 
                 value="<?= !empty($data['event']['google_form_url']) ? htmlspecialchars($data['event']['google_form_url']) : '' ?>" title="Paste the link to your Google Form here for attendee registration">
          <small style="color: #6b7280;">Attendees can fill this form to provide their details</small>
          <div class="form-error" id="google-form-url-error"></div>
        </div>

        <div class="form-group">
          <label for="event-status" class="form-label">Status</label>
          <select id="event-status" name="status" class="form-select" required>
            <option value="upcoming" <?= $data['event']['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
            <option value="ongoing" <?= $data['event']['status'] === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
            <option value="completed" <?= $data['event']['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= $data['event']['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
          <div class="form-error" id="status-error"></div>
        </div>

        <div class="form-group">
          <label class="form-label">Event Image</label>
          <div class="file-upload-area" id="file-upload-area">
            <input type="file" id="event-image" name="event_image" accept="image/png,image/jpeg,image/jpg" class="file-input">
            <div class="file-upload-content">
              <button type="button" class="btn btn--primary" id="upload-trigger">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path d="M10 14V6M10 6L7 9M10 6L13 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M3 14v2a2 2 0 002 2h10a2 2 0 002-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Upload a new image
              </button>
              <p class="upload-text">Or Drop an image here</p>
              <p class="upload-hint">Supported formats: png, jpeg, jpg</p>
            </div>
          </div>
          <div class="file-preview" id="file-preview">
            <img id="preview-image" src="<?= htmlspecialchars($data['event']['image_url'] ?? '') ?>" alt="Current image" class="preview-image">
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
          <textarea id="event-description" name="description" class="form-textarea" rows="20" required><?= htmlspecialchars($data['event']['description']) ?></textarea>
          <div class="form-error" id="description-error"></div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn btn--secondary btn--large" onclick="history.back()">Cancel</button>
      <button type="submit" class="btn btn--primary btn--large">Update Event</button>
    </div>

  </form>

</main>

<script src="/js/app/community/event-form.js"></script>
