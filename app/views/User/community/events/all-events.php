<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blogs.css">

<?php
// Debug output - remove after testing
echo "<!-- DEBUG: Events data -->";
echo "<!-- Events count: " . count($data['events'] ?? []) . " -->";
echo "<!-- All Events count: " . count($data['allEvents'] ?? []) . " -->";
if (!empty($data['events'])) {
  echo "<!-- First event: " . htmlspecialchars($data['events'][0]['title'] ?? 'NO TITLE') . " -->";
}
?>

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
      Events
    </li>
  </ol>
</nav>

<main class="blogs-main" role="main" aria-label="Events Dashboard">
  
  <div class="page-header">
    <h1 class="page-title">Community Events</h1>
    <p class="page-subtitle">
      Discover and join exciting events happening around campus
    </p>
    <?php if ($data['isClubAdmin']): ?>
    <a href="/dashboard/community/events/create" class="btn btn--primary">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <line x1="10" y1="4" x2="10" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="4" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      Create New Event
    </a>
    <?php endif; ?>
  </div>

  <nav class="tab-navigation" aria-label="Event filters">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all">All Events</button>
      <button class="tab-button" data-tab="registered">My Events</button>
      <button class="tab-button" data-tab="calendar" style="margin-left: auto;">
        <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
          <rect x="3" y="4" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
          <path d="M3 8h14M7 1v6M13 1v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Calendar View
      </button>
    </div>
  </nav>

  <!-- All Events Tab -->
  <div class="tab-content" data-tab-content="all">
    <div class="search-section">
      <div class="search-bar">
        <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="2"/>
          <line x1="14" y1="14" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" id="event-search" class="search-input" placeholder="Search events by name">
      </div>
    </div>

    <div class="category-pills">
      <button class="pill pill--active" data-status="upcoming">Upcoming</button>
      <button class="pill" data-status="ongoing">Ongoing</button>
      <button class="pill" data-status="completed">Completed</button>
    </div>

    <section class="blogs-section">
      <?php if (empty($data['events'])): ?>
      <div class="empty-state">
        <div class="empty-icon">🎉</div>
        <h3>No Events Yet</h3>
        <p>Be the first to create an event!</p>
        <a href="/dashboard/community/events/create" class="btn btn--primary">Create First Event</a>
      </div>
      <?php else: ?>
      <div class="blogs-grid" id="events-grid">
        <?php foreach ($data['events'] as $event): ?>
        <article class="blog-card event-card" data-event-id="<?= $event['id'] ?>" data-status="<?= htmlspecialchars($event['status']) ?>">
          <a href="/dashboard/community/events/view?id=<?= $event['id'] ?>" class="blog-card__link">
            <div class="blog-card__image event-card__image">
              <img src="<?= htmlspecialchars($event['image_url'] ?? 'https://via.placeholder.com/400x400/E74C3C/ffffff?text=' . urlencode(substr($event['title'], 0, 1))) ?>" 
                   alt="<?= htmlspecialchars($event['title']) ?>">
              <span class="event-date-badge">
                <?= date('M d', strtotime($event['event_date'])) ?>
              </span>
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title"><?= htmlspecialchars($event['title']) ?></h3>
              <p class="blog-card__excerpt"><?= htmlspecialchars(substr($event['description'] ?? '', 0, 100)) ?><?= strlen($event['description'] ?? '') > 100 ? '...' : '' ?></p>
              <div class="blog-card__meta">
                <span class="blog-card__author">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 4px;">
                    <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M2 18a8 8 0 0116 0" stroke="currentColor" stroke-width="1.5"/>
                  </svg>
                  <?= htmlspecialchars($event['attendee_count'] ?? 0) ?> going
                </span>
                <span class="blog-card__category"><?= htmlspecialchars(ucfirst($event['status'])) ?></span>
              </div>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>

  <!-- My Registered Events Tab -->
  <div class="tab-content is-hidden" data-tab-content="registered">
    <section class="manage-blogs-section">
      <div class="section-header">
        <h2 class="section-title">Events I'm Attending</h2>
      </div>

      <?php 
      $registeredEvents = !empty($data['userEventIds']) && !empty($data['events']) 
        ? array_filter($data['events'], function($e) use ($data) { return in_array($e['id'], $data['userEventIds']); })
        : [];
      ?>

      <?php if (empty($registeredEvents)): ?>
      <div class="empty-state">
        <div class="empty-icon">📋</div>
        <h3>You haven't registered for any events yet</h3>
        <p>Browse events and join ones that interest you!</p>
        <button class="btn btn--primary" onclick="document.querySelector('[data-tab=\"all\"]').click()">Browse Events</button>
      </div>
      <?php else: ?>
      <div class="blogs-grid">
        <?php foreach ($registeredEvents as $event): ?>
        <article class="blog-card event-card">
          <a href="/dashboard/community/events/view?id=<?= $event['id'] ?>" class="blog-card__link">
            <div class="blog-card__image event-card__image">
              <img src="<?= htmlspecialchars($event['image_url'] ?? 'https://via.placeholder.com/400x400/E74C3C/ffffff?text=' . urlencode(substr($event['title'], 0, 1))) ?>" 
                   alt="<?= htmlspecialchars($event['title']) ?>">
              <span class="event-date-badge">
                <?= date('M d', strtotime($event['event_date'])) ?>
              </span>
              <span class="badge-attending">✓ Attending</span>
            </div>
            <div class="blog-card__content">
              <h3 class="blog-card__title"><?= htmlspecialchars($event['title']) ?></h3>
              <p class="blog-card__excerpt"><?= htmlspecialchars(substr($event['description'] ?? '', 0, 100)) ?></p>
              <div class="blog-card__meta">
                <span class="blog-card__author"><?= date('M j, Y', strtotime($event['event_date'])) ?></span>
                <span class="blog-card__category"><?= htmlspecialchars(ucfirst($event['status'])) ?></span>
              </div>
            </div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>
  </div>

  <!-- Calendar View Tab -->
  <div class="tab-content is-hidden" data-tab-content="calendar">
    <div class="calendar-section">
      <div class="calendar-header">
        <button class="btn btn--outline btn--sm" id="prev-month">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
            <path d="M12 16l-6-6 6-6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
        <h2 id="current-month-year" style="flex: 1; text-align: center; margin: 0;"></h2>
        <button class="btn btn--outline btn--sm" id="next-month">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
            <path d="M8 16l6-6-6-6" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
      </div>
      
      <div class="calendar-grid" id="calendar-grid">
        <!-- Calendar will be generated by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Event Details Modal -->
  <div id="event-modal" class="modal is-hidden">
    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modal-date-title"></h2>
        <button class="modal-close" id="modal-close">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M15 5L5 15M5 5l10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      <div class="modal-body" id="modal-events-list">
        <!-- Events for selected day will be rendered here -->
      </div>
    </div>
  </div>

</main>

<style>
.calendar-section {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.calendar-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.calendar-header h2 {
  flex: 1;
  text-align: center;
}

.btn--sm {
  padding: 0.5rem;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-weekday {
  text-align: center;
  font-weight: 600;
  padding: 0.75rem;
  color: #6b7280;
  font-size: 0.875rem;
  text-transform: uppercase;
}

.calendar-day {
  min-height: 100px;
  padding: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: #f9fafb;
  cursor: pointer;
  transition: all 0.2s;
}

.calendar-day:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

.calendar-day.other-month {
  background: #fafbfc;
  color: #d1d5db;
}

.calendar-day.today {
  background: #dbeafe;
  border-color: #0284c7;
}

.calendar-day-header {
  font-weight: 600;
  font-size: 0.875rem;
  margin-bottom: 0.25rem;
}

.calendar-day-events {
  font-size: 0.75rem;
  color: #6b7280;
}

.calendar-event-dot {
  display: inline-block;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #e74c3c;
  margin-right: 0.25rem;
}

.calendar-day.has-events {
  background: #eff6ff;
  border-color: #0284c7;
}

.calendar-day.has-events:hover {
  background: #e0f2fe;
  border-color: #0284c7;
}

/* Modal Styles */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.2s;
}

.modal.is-hidden {
  display: none;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}

.modal-content {
  position: relative;
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 500px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
  z-index: 1001;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  background: white;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #1f2937;
}

.modal-body {
  padding: 1rem;
}

.modal-event-item {
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 1rem;
  transition: all 0.2s;
  background: #f9fafb;
}

.modal-event-item:hover {
  border-color: #0284c7;
  background: #eff6ff;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.modal-event-item__header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.modal-event-item__title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
}

.modal-event-item__time {
  font-size: 0.875rem;
  font-weight: 600;
  color: #0284c7;
  white-space: nowrap;
}

.modal-event-item__location {
  margin: 0.5rem 0;
  font-size: 0.875rem;
  color: #4b5563;
  display: flex;
  align-items: center;
}

.modal-event-item__attendees {
  margin: 0.5rem 0;
  font-size: 0.875rem;
  color: #6b7280;
  display: flex;
  align-items: center;
}

.modal-event-item__category {
  margin: 0.5rem 0;
  font-size: 0.75rem;
}

.modal-event-item__status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #dbeafe;
  color: #0284c7;
  border-radius: 20px;
  font-weight: 600;
  text-transform: capitalize;
}
</style>

<style>
.event-card {
  position: relative;
}

.event-card__image {
  position: relative;
}

.event-date-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: rgba(255, 255, 255, 0.95);
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.75rem;
  color: #333;
  backdrop-filter: blur(10px);
}

.badge-attending,
.badge-creator {
  position: absolute;
  bottom: 12px;
  right: 12px;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.75rem;
  background: rgba(52, 211, 153, 0.95);
  color: white;
}

.badge-creator {
  background: rgba(59, 130, 246, 0.95);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log('All Events page loaded');
  
  // Tab switching
  document.querySelectorAll('.tab-button').forEach(btn => {
    btn.addEventListener('click', function() {
      const tabName = this.dataset.tab;
      console.log('Tab clicked:', tabName);
      
      // Remove active class from all buttons
      document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('tab-button--active'));
      this.classList.add('tab-button--active');
      
      // Hide all contents
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('is-hidden');
      });
      
      // Show selected content
      const targetContent = document.querySelector(`[data-tab-content="${tabName}"]`);
      if (targetContent) {
        targetContent.classList.remove('is-hidden');
        console.log('Showed tab content:', tabName);
        
        // If calendar tab, render it
        if (tabName === 'calendar') {
          renderCalendar();
        }
      } else {
        console.error('Tab content not found:', tabName);
      }
    });
  });

  // Search functionality
  const eventSearch = document.getElementById('event-search');
  if (eventSearch) {
    eventSearch.addEventListener('input', function(e) {
      const query = e.target.value.toLowerCase();
      document.querySelectorAll('.event-card').forEach(card => {
        const titleElement = card.querySelector('.blog-card__title');
        if (titleElement) {
          const title = titleElement.textContent.toLowerCase();
          card.style.display = title.includes(query) ? '' : 'none';
        }
      });
    });
  }

  // Status filter
  document.querySelectorAll('.category-pills .pill').forEach(pill => {
    pill.addEventListener('click', function() {
      const status = this.dataset.status;
      
      document.querySelectorAll('.category-pills .pill').forEach(p => p.classList.remove('pill--active'));
      this.classList.add('pill--active');
      
      document.querySelectorAll('.event-card').forEach(card => {
        const cardStatus = card.dataset.status;
        card.style.display = !status || cardStatus === status ? '' : 'none';
      });
    });
  });
  
  // Calendar functionality
  let currentDate = new Date();

  function renderCalendar() {
    console.log('Rendering calendar for:', currentDate);
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'];
    const headerElement = document.getElementById('current-month-year');
    if (headerElement) {
      headerElement.textContent = `${monthNames[month]} ${year}`;
    }
    
    // Clear calendar
    const grid = document.getElementById('calendar-grid');
    if (!grid) {
      console.error('Calendar grid not found');
      return;
    }
    
    grid.innerHTML = '';
    
    // Add weekday headers
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(day => {
      const header = document.createElement('div');
      header.className = 'calendar-weekday';
      header.textContent = day;
      grid.appendChild(header);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    // Add previous month's days
    for (let i = firstDay - 1; i >= 0; i--) {
      const day = document.createElement('div');
      day.className = 'calendar-day other-month';
      day.innerHTML = `<div class="calendar-day-header">${daysInPrevMonth - i}</div>`;
      grid.appendChild(day);
    }
    
    // Add current month's days
    const today = new Date();
    const events = <?= json_encode($data['allEvents'] ?? $data['events'] ?? []) ?>;
    
    console.log('Calendar rendering with', events.length, 'events');
    console.log('Events data:', events);
    
    for (let i = 1; i <= daysInMonth; i++) {
      const day = document.createElement('div');
      day.className = 'calendar-day';
      
      // Check if it's today
      if (today.getFullYear() === year && today.getMonth() === month && today.getDate() === i) {
        day.classList.add('today');
      }
      
      // Check for events on this day
      const dayDate = new Date(year, month, i);
      const dayEvents = events.filter(event => {
        const eventDate = new Date(event.event_date);
        return eventDate.getFullYear() === year && 
               eventDate.getMonth() === month && 
               eventDate.getDate() === i;
      });
      
      if (dayEvents.length > 0) {
        console.log(`Day ${i}: ${dayEvents.length} event(s)`, dayEvents);
      }
      
      day.innerHTML = `<div class="calendar-day-header">${i}</div>`;
      if (dayEvents.length > 0) {
        day.classList.add('has-events');
        day.innerHTML += `<div class="calendar-day-events">${dayEvents.length} event${dayEvents.length > 1 ? 's' : ''}</div>`;
      }
      
      // Add click handler to show events for this day
      if (dayEvents.length > 0) {
        day.style.cursor = 'pointer';
        day.addEventListener('click', () => {
          showDayEvents(i, month, year, dayEvents);
        });
      }
      
      grid.appendChild(day);
    }
    
    // Add next month's days
    const totalCells = grid.children.length - 7; // Subtract weekday headers
    const remainingCells = 42 - totalCells; // 6 rows × 7 days
    for (let i = 1; i <= remainingCells; i++) {
      const day = document.createElement('div');
      day.className = 'calendar-day other-month';
      day.innerHTML = `<div class="calendar-day-header">${i}</div>`;
      grid.appendChild(day);
    }
    
    console.log('Calendar rendered successfully');
  }

  // Navigation
  const prevBtn = document.getElementById('prev-month');
  const nextBtn = document.getElementById('next-month');
  
  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
    });
  }

  // Initial calendar render for initial page load if calendar tab is active
  console.log('DOM Content Loaded - scripts initialized');
  
  // Modal functionality
  const modal = document.getElementById('event-modal');
  const modalClose = document.getElementById('modal-close');
  const modalOverlay = document.getElementById('modal-overlay');
  
  function showDayEvents(day, month, year, dayEvents) {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'];
    
    const dateTitle = document.getElementById('modal-date-title');
    dateTitle.textContent = `${monthNames[month]} ${day}, ${year}`;
    
    const eventsList = document.getElementById('modal-events-list');
    eventsList.innerHTML = '';
    
    dayEvents.forEach(event => {
      const eventTime = new Date(event.event_date);
      const timeStr = eventTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      
      const eventEl = document.createElement('div');
      eventEl.className = 'modal-event-item';
      eventEl.innerHTML = `
        <div class="modal-event-item__header">
          <h3 class="modal-event-item__title">${htmlEscape(event.title)}</h3>
          <span class="modal-event-item__time">${timeStr}</span>
        </div>
        <p class="modal-event-item__location">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 6px;">
            <path d="M10 1C5.58 1 2 4.58 2 10c0 7 8 11 8 11s8-4 8-11c0-5.42-3.58-9-8-9z" stroke="currentColor" stroke-width="1.5" fill="none"/>
          </svg>
          ${htmlEscape(event.location || 'Location TBD')}
        </p>
        <p class="modal-event-item__attendees">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" style="display: inline; margin-right: 6px;">
            <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 18a8 8 0 0116 0" stroke="currentColor" stroke-width="1.5"/>
          </svg>
          ${event.attendee_count || 0} attending
        </p>
        <p class="modal-event-item__category">
          <span class="modal-event-item__status">${htmlEscape(event.status)}</span>
        </p>
        <a href="/dashboard/community/events/view?id=${event.id}" class="btn btn--primary btn--sm" style="margin-top: 12px; width: 100%;">
          View Event Details
        </a>
      `;
      eventsList.appendChild(eventEl);
    });
    
    // Show modal
    if (modal) {
      modal.classList.remove('is-hidden');
    }
  }
  
  function htmlEscape(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
  }
  
  // Close modal
  if (modalClose) {
    modalClose.addEventListener('click', () => {
      if (modal) {
        modal.classList.add('is-hidden');
      }
    });
  }
  
  if (modalOverlay) {
    modalOverlay.addEventListener('click', () => {
      if (modal) {
        modal.classList.add('is-hidden');
      }
    });
  }
  
  // Close modal with Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && !modal.classList.contains('is-hidden')) {
      modal.classList.add('is-hidden');
    }
  });
});
</script>
