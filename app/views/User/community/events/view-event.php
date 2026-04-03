<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

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
      <?= htmlspecialchars($data['event']['title'] ?? 'Event') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Event Details">
  
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['event']['title']) ?></h1>
      
      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['event']['image_url'] ?? 'https://via.placeholder.com/900x400/E74C3C/ffffff?text=' . urlencode(substr($data['event']['title'], 0, 1))) ?>" 
             alt="<?= htmlspecialchars($data['event']['title']) ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
            <rect x="1" y="4" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
            <path d="M1 8h18M5 1v6M15 1v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <?= htmlspecialchars($data['event']['category']) ?>
        </span>
        <span class="blog-separator">•</span>
        <span class="blog-published">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
            <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/>
            <path d="M10 5v5l4 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <?= date('M j, Y \a\t g:ia', strtotime($data['event']['event_date'])) ?>
        </span>
        <span class="blog-separator">•</span>
        <span class="blog-views">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
            <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 18a8 8 0 0116 0" stroke="currentColor" stroke-width="1.5"/>
          </svg>
          <?= htmlspecialchars($data['event']['attendee_count'] ?? 0) ?> attending
        </span>
      </div>

      <div class="blog-location">
        <p>
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
            <path d="M10 2c-3.314 0-6 2.686-6 6 0 3.314 2.686 6 6 6s6-2.686 6-6-2.686-6-6-6zm0 11c-2.762 0-5-2.238-5-5s2.238-5 5-5 5 2.238 5 5-2.238 5-5 5z" stroke="currentColor" stroke-width="1"/>
          </svg>
          <strong><?= htmlspecialchars($data['event']['location']) ?></strong>
        </p>
      </div>

      <?php if (!empty($data['event']['club_name'])): ?>
      <div class="blog-meta">
        <p><strong>Club:</strong> <?= htmlspecialchars($data['event']['club_name']) ?></p>
      </div>
      <?php endif; ?>

      <div class="blog-creator">
        <p><strong>Created by:</strong> <?= htmlspecialchars($data['event']['creator_first_name'] . ' ' . $data['event']['creator_last_name']) ?></p>
      </div>
    </header>

    <article class="blog-content">
      <?php if (!empty($data['event']['description'])): ?>
        <p><?= nl2br(htmlspecialchars($data['event']['description'])) ?></p>
      <?php else: ?>
        <p>No description available for this event yet.</p>
      <?php endif; ?>

      <?php if (!empty($data['event']['max_attendees'])): ?>
      <div class="event-info">
        <h3>Capacity</h3>
        <p><?= htmlspecialchars($data['event']['attendee_count'] ?? 0) ?> / <?= htmlspecialchars($data['event']['max_attendees']) ?> attendees</p>
      </div>
      <?php endif; ?>

      <?php if (!empty($data['event']['google_form_url'])): ?>
      <div class="event-info" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
        <h3>Event Registration Form</h3>
        <p>Click below to fill in your details for this event:</p>
        <a href="<?= htmlspecialchars($data['event']['google_form_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="margin-top: 0.5rem;">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
            <path d="M10 5.5h4a2.5 2.5 0 0 1 2.5 2.5v5a2.5 2.5 0 0 1-2.5 2.5h-4m-5-10v10a2.5 2.5 0 0 0 2.5 2.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 10h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          Fill Registration Form
        </a>
      </div>
      <?php endif; ?>
    </article>

    <div class="blog-interactions">
      <?php if ($data['isRegistered']): ?>
        <span class="badge-member">✓ You're going</span>
        <button class="btn btn--danger" onclick="unregisterEvent(<?= $data['event']['id'] ?>)">Cancel attendance</button>
      <?php else: ?>
        <button class="btn btn--primary" onclick="registerEvent(<?= $data['event']['id'] ?>)">Register for Event</button>
      <?php endif; ?>
      <button class="btn btn--outline" onclick="addToCalendar()" style="margin-left: auto;">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
          <rect x="3" y="4" width="14" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
          <path d="M3 8h14M7 1v6M13 1v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Add to Calendar
      </button>
      <button class="report-icon" id="report-event-btn" data-report-type="event" data-id="<?= $data['event']['id'] ?>" title="Report" aria-label="Report event" style="margin-left:8px;">
        <span class="material-symbols-outlined" aria-hidden="true">report</span>
      </button>
    </div>

    <?php if ($data['isCreator']): ?>
    <div class="blog-actions">
      <button class="btn btn--primary" onclick="window.location.href='/dashboard/community/events/edit?id=<?= $data['event']['id'] ?>'">Edit Event</button>
      <button class="btn btn--danger" onclick="deleteEvent(<?= $data['event']['id'] ?>)">Delete Event</button>
    </div>
    <?php endif; ?>

    <?php if (!empty($data['attendees'])): ?>
    <div class="attendees-section">
      <h3>Attendees (<?= count($data['attendees']) ?>)</h3>
      <div class="attendees-grid">
        <?php foreach ($data['attendees'] as $attendee): ?>
        <div class="attendee-card">
          <img src="https://via.placeholder.com/80x80/667EEA/ffffff?text=<?= urlencode(substr($attendee['first_name'], 0, 1)) ?>" 
               alt="<?= htmlspecialchars($attendee['first_name'] . ' ' . $attendee['last_name']) ?>"
               class="attendee-avatar">
          <p class="attendee-name"><?= htmlspecialchars($attendee['first_name'] . ' ' . $attendee['last_name']) ?></p>
          <p class="attendee-email"><?= htmlspecialchars($attendee['email']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>

</main>

<style>
.badge-member {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  background: #34D399;
  color: white;
  margin-right: 1rem;
}

.event-info {
  background: #f3f4f6;
  padding: 1rem;
  border-radius: 8px;
  margin: 1rem 0;
}

.event-info h3 {
  margin-top: 0;
  font-size: 0.875rem;
  color: #6b7280;
  text-transform: uppercase;
}

.attendees-section {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.attendees-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.attendee-card {
  text-align: center;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f9fafb;
}

.attendee-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-bottom: 0.5rem;
}

.attendee-name {
  font-weight: 600;
  font-size: 0.875rem;
  margin: 0.5rem 0 0.25rem 0;
}

.attendee-email {
  font-size: 0.75rem;
  color: #6b7280;
  margin: 0;
  word-break: break-word;
}

.blog-location {
  margin: 1rem 0;
  font-size: 0.95rem;
}

.blog-location p {
  margin: 0;
  color: #6b7280;
}
</style>

<script>
function registerEvent(eventId) {
  if (!confirm('Register for this event?')) return;
  
  fetch('/dashboard/community/events/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'event_id=' + eventId
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + (data.message || 'Failed to register'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

function unregisterEvent(eventId) {
  if (!confirm('Cancel your attendance?')) return;
  
  fetch('/dashboard/community/events/unregister', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'event_id=' + eventId
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + (data.message || 'Failed to unregister'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

function deleteEvent(eventId) {
  if (!confirm('Are you sure you want to delete this event? This cannot be undone.')) return;
  
  fetch('/dashboard/community/events/delete', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'event_id=' + eventId
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      window.location.href = data.redirect || '/dashboard/community/events';
    } else {
      alert('Error: ' + (data.message || 'Failed to delete event'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

function addToCalendar() {
  // Event data
  const title = <?= json_encode($data['event']['title']) ?>;
  const description = <?= json_encode($data['event']['description']) ?>;
  const location = <?= json_encode($data['event']['location']) ?>;
  const startDate = new Date(<?= json_encode($data['event']['event_date']) ?>);
  const eventUrl = window.location.href;
  
  // Format dates for iCalendar format (YYYYMMDDTHHMMSSZ)
  const pad = (num) => String(num).padStart(2, '0');
  const formatDate = (date) => {
    return date.getUTCFullYear() +
      pad(date.getUTCMonth() + 1) +
      pad(date.getUTCDate()) + 'T' +
      pad(date.getUTCHours()) +
      pad(date.getUTCMinutes()) +
      pad(date.getUTCSeconds()) + 'Z';
  };
  
  // End time is 2 hours after start
  const endDate = new Date(startDate.getTime() + 2 * 60 * 60 * 1000);
  
  // Create iCalendar content
  const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ReidHub//Events//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:${title}
X-WR-TIMEZONE:UTC
BEGIN:VEVENT
UID:${startDate.getTime()}@reidhub.local
DTSTAMP:${formatDate(new Date())}
DTSTART:${formatDate(startDate)}
DTEND:${formatDate(endDate)}
SUMMARY:${title}
DESCRIPTION:${description}
LOCATION:${location}
URL:${eventUrl}
END:VEVENT
END:VCALENDAR`;
  
  // Create blob and download
  const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.ics`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>

<!-- Shared Report Modal -->
<div class="modal-overlay" id="report-modal" role="dialog" aria-labelledby="report-title" aria-modal="true" style="display: none;">
  <div class="modal">
    <div class="modal-header">
      <h2 id="report-title" class="modal-title">Report</h2>
      <button class="modal-close" aria-label="Close modal">
        <span class="material-symbols-outlined" aria-hidden="true">close</span>
      </button>
    </div>
    <form class="modal-body" id="report-form">
      <div class="form-group">
        <label for="report-description" class="form-label">Description</label>
        <textarea id="report-description" name="description" class="form-textarea" rows="6" placeholder="Tell us what's wrong..." required></textarea>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn btn--primary">Submit Report</button>
      </div>
    </form>
  </div>
</div>

<script type="module" src="/js/app/community/blog-view.js"></script>
