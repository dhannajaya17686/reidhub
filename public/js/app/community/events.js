/**
 * Events JavaScript
 */

// Tab Navigation
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
  button.addEventListener('click', () => {
    const targetTab = button.dataset.tab;
    
    // Update active tab button
    tabButtons.forEach(btn => {
      btn.classList.remove('tab-button--active');
      btn.setAttribute('aria-selected', 'false');
      btn.setAttribute('tabindex', '-1');
    });
    
    button.classList.add('tab-button--active');
    button.setAttribute('aria-selected', 'true');
    button.setAttribute('tabindex', '0');
    
    // Update visible tab content
    tabContents.forEach(content => {
      content.classList.add('is-hidden');
    });
    
    const targetContent = document.querySelector(`[data-tab-content="${targetTab}"]`);
    if (targetContent) {
      targetContent.classList.remove('is-hidden');
    }
  });
});

// Today Button
const todayBtn = document.getElementById('today-btn');
if (todayBtn) {
  todayBtn.addEventListener('click', () => {
    console.log('Navigating to today');
    // Implementation: scroll to current week
  });
}

// Week Navigation
const prevWeekBtn = document.getElementById('prev-week');
const nextWeekBtn = document.getElementById('next-week');
const dateRangeText = document.getElementById('date-range-text');

if (prevWeekBtn) {
  prevWeekBtn.addEventListener('click', () => {
    console.log('Previous week');
    // Implementation: load previous week's events
  });
}

if (nextWeekBtn) {
  nextWeekBtn.addEventListener('click', () => {
    console.log('Next week');
    // Implementation: load next week's events
  });
}

// Month Navigation
const prevMonthBtn = document.getElementById('prev-month');
const nextMonthBtn = document.getElementById('next-month');

if (prevMonthBtn) {
  prevMonthBtn.addEventListener('click', () => {
    console.log('Previous month');
    // Implementation: update calendar to previous month
  });
}

if (nextMonthBtn) {
  nextMonthBtn.addEventListener('click', () => {
    console.log('Next month');
    // Implementation: update calendar to next month
  });
}

// Event Card Click
const eventCards = document.querySelectorAll('.event-card');

eventCards.forEach(card => {
  card.addEventListener('click', (e) => {
    // Don't navigate if clicking expand button
    if (e.target.closest('.event-expand')) {
      return;
    }
    
    const eventId = card.dataset.eventId;
    console.log(`Opening event ${eventId}`);
    // window.location.href = `/community/events/${eventId}`;
  });
});

// Event Expand Button
const expandButtons = document.querySelectorAll('.event-expand');

expandButtons.forEach(button => {
  button.addEventListener('click', (e) => {
    e.stopPropagation();
    const card = button.closest('.event-card');
    console.log('Expanding event details');
    // Implementation: show event details in modal or expand card
  });
});

// Calendar Date Click
const calendarDates = document.querySelectorAll('.calendar-grid td:not(.other-month)');

calendarDates.forEach(date => {
  date.addEventListener('click', () => {
    console.log(`Selected date: ${date.textContent}`);
    // Implementation: update weekly view to selected date
  });
});

// File Upload Handling
const fileInput = document.getElementById('event-image');
const fileUploadArea = document.getElementById('file-upload-area');
const uploadTrigger = document.getElementById('upload-trigger');
const filePreview = document.getElementById('file-preview');
const previewImage = document.getElementById('preview-image');
const previewRemove = document.getElementById('preview-remove');

// Trigger file input
if (uploadTrigger) {
  uploadTrigger.addEventListener('click', (e) => {
    e.preventDefault();
    fileInput.click();
  });
}

// Click on upload area
if (fileUploadArea) {
  fileUploadArea.addEventListener('click', () => {
    fileInput.click();
  });
}

// Handle file selection
if (fileInput) {
  fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    
    if (file) {
      // Validate file type
      const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
      if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image file (PNG, JPEG, or JPG)');
        fileInput.value = '';
        return;
      }
      
      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        fileInput.value = '';
        return;
      }
      
      // Display preview
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImage.src = e.target.result;
        fileUploadArea.style.display = 'none';
        filePreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });
}

// Remove file
if (previewRemove) {
  previewRemove.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.value = '';
    previewImage.src = '';
    fileUploadArea.style.display = 'block';
    filePreview.style.display = 'none';
  });
}

// Drag and drop
if (fileUploadArea) {
  fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#0466C8';
    fileUploadArea.style.backgroundColor = '#f3f8ff';
  });
  
  fileUploadArea.addEventListener('dragleave', () => {
    fileUploadArea.style.borderColor = '#d1d5db';
    fileUploadArea.style.backgroundColor = '#f9fafb';
  });
  
  fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#d1d5db';
    fileUploadArea.style.backgroundColor = '#f9fafb';
    
    const file = e.dataTransfer.files[0];
    if (file) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      fileInput.files = dataTransfer.files;
      
      // Trigger change event
      const event = new Event('change', { bubbles: true });
      fileInput.dispatchEvent(event);
    }
  });
}

// Add Event Form Submission
const addEventForm = document.getElementById('add-event-form');

if (addEventForm) {
  addEventForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const formData = new FormData(addEventForm);
    
    // Validate required fields
    const eventName = document.getElementById('event-name').value;
    const eventDate = document.getElementById('event-date').value;
    const eventTime = document.getElementById('event-time').value;
    const eventVenue = document.getElementById('event-venue').value;
    const eventDescription = document.getElementById('event-description').value;
    const eventImage = fileInput.files[0];
    
    if (!eventName || !eventDate || !eventTime || !eventVenue || !eventDescription || !eventImage) {
      alert('Please fill in all required fields');
      return;
    }
    
    console.log('Creating event...');
    console.log('Event Name:', eventName);
    console.log('Date:', eventDate);
    console.log('Time:', eventTime);
    console.log('Venue:', eventVenue);
    console.log('Description:', eventDescription);
    console.log('Image:', eventImage.name);
    
    // Send to backend
    // fetch('/api/events/create', {
    //   method: 'POST',
    //   body: formData
    // })
    
    alert('Event created successfully!');
    addEventForm.reset();
    fileUploadArea.style.display = 'block';
    filePreview.style.display = 'none';
    
    // Switch to events tab
    const eventsTab = document.querySelector('[data-tab="events"]');
    if (eventsTab) {
      eventsTab.click();
    }
  });
}

console.log('Events page loaded');