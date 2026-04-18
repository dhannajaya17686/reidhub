/**
 * Lost & Found Admin Management System
 * Handles all admin operations for lost and found items
 */

let currentItem = null;
let currentFilter = 'all';
let lostItemsData = [];
let foundItemsData = [];
let reportsData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  console.log('============================================');
  console.log('Lost & Found Admin Initialized');
  console.log('============================================');
  loadLostItems('all');
  setupEventListeners();
  
  // Log available sections
  console.log('Available sections:', Array.from(document.querySelectorAll('.lf-section')).map(s => ({
    id: s.id,
    active: s.classList.contains('active'),
    display: window.getComputedStyle(s).display
  })));
});

// Setup event listeners
function setupEventListeners() {
  // Search functionality
  const searchInput = document.getElementById('lf-report-search');
  if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
      loadReports(currentFilter);
    }, 500));
  }

  // Filter dropdowns
  ['status-filter', 'severity-filter', 'date-filter'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
      element.addEventListener('change', () => {
        loadReports(currentFilter);
      });
    }
  });

  // Close modals on backdrop click
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
      if (e.target.id === 'item-modal') {
        closeModal();
      } else if (e.target.id === 'new-report-modal') {
        closeNewReportModal();
      }
    }
  });
}

// Navigation between sections
function switchNav(sectionId, event) {
  console.log(`=== SWITCHING TO SECTION: ${sectionId} ===`);
  event.preventDefault();
  
  // Update active states
  document.querySelectorAll('.lf-section').forEach(section => {
    section.classList.remove('active');
    console.log('Deactivated section:', section.id);
  });
  document.querySelectorAll('.nav-tab').forEach(tab => {
    tab.classList.remove('active');
    tab.setAttribute('aria-selected', 'false');
  });

  const selectedSection = document.getElementById(sectionId);
  if (selectedSection) {
    selectedSection.classList.add('active');
    console.log('✓ Activated section:', sectionId);
    console.log('Section display:', window.getComputedStyle(selectedSection).display);
  } else {
    console.error('❌ Section not found:', sectionId);
  }
  
  event.target.classList.add('active');
  event.target.setAttribute('aria-selected', 'true');

  // Load appropriate data
  if (sectionId === 'lf-lost-items') {
    loadLostItems('all');
  } else if (sectionId === 'lf-found-items') {
    console.log('Loading found items...');
    loadFoundItems('all');
  } else if (sectionId === 'lf-reports') {
    loadReports('all');
  }
}

// Filter lost items
function filterLostItems(filter, evt) {
  currentFilter = filter;
  
  // Update active tab
  document.querySelectorAll('#lf-lost-items .filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  if (evt && evt.target) {
    evt.target.classList.add('active');
  }

  // Hide all grids
  document.querySelectorAll('#lf-lost-items .items-grid').forEach(grid => {
    grid.style.display = 'none';
  });

  // Show target grid
  const targetGrid = document.getElementById(`lost-items-${filter}`);
  if (targetGrid) {
    targetGrid.style.display = 'grid';
  }

  loadLostItems(filter);
}

// Filter found items
function filterFoundItems(filter, evt) {
  console.log(`=== FILTERING FOUND ITEMS: ${filter} ===`);
  currentFilter = filter;
  
  // Update active tab
  document.querySelectorAll('#lf-found-items .filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  if (evt && evt.target) {
    evt.target.classList.add('active');
    console.log('✓ Activated filter tab:', filter);
  }

  // Hide all grids
  document.querySelectorAll('#lf-found-items .items-grid').forEach(grid => {
    grid.style.display = 'none';
  });

  // Show target grid
  const targetGrid = document.getElementById(`found-items-${filter}`);
  if (targetGrid) {
    targetGrid.style.display = 'grid';
    console.log('✓ Showed target grid:', `found-items-${filter}`);
  } else {
    console.error(`❌ Target grid not found: found-items-${filter}`);
  }

  loadFoundItems(filter);
}

// Filter reports
function filterLFReports(filter, evt) {
  currentFilter = filter;
  
  // Update active tab
  document.querySelectorAll('#lf-reports .filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  if (evt && evt.target) {
    evt.target.classList.add('active');
  }

  loadReports(filter);
}

// Load lost items from backend
async function loadLostItems(filter = 'all') {
  try {
    const response = await fetch(`/dashboard/lost-and-found/admin/get-lost-items?filter=${filter}`);
    const data = await response.json();

    if (data.success) {
      lostItemsData = data.items;
      updateLostItemsStats(data.stats);
      renderLostItems(data.items, filter);
      console.log(`Loaded ${data.items.length} lost items with filter: ${filter}`);
    } else {
      console.error('Failed to load lost items:', data.message);
      showError('Failed to load lost items');
    }
  } catch (error) {
    console.error('Error loading lost items:', error);
    showError('Error loading lost items');
  }
}

// Load found items from backend
async function loadFoundItems(filter = 'all') {
  try {
    console.log(`🔄 Loading found items with filter: ${filter}`);
    const response = await fetch(`/dashboard/lost-and-found/admin/get-found-items?filter=${filter}`);
    console.log('📡 Response status:', response.status);
    const data = await response.json();
    console.log('📦 Response data:', data);
    console.log('📊 Items count:', data.items ? data.items.length : 0);
    
    if (data.items && data.items.length > 0) {
      console.log('📷 First item:', data.items[0]);
      console.log('📷 First item images:', data.items[0].images);
      console.log('📷 First item images count:', data.items[0].images ? data.items[0].images.length : 0);
    }

    if (data.success) {
      foundItemsData = data.items;
      updateFoundItemsStats(data.stats);
      renderFoundItems(data.items, filter);
      console.log(`✅ Loaded ${data.items.length} found items with filter: ${filter}`);
    } else {
      console.error('❌ Failed to load found items:', data.message);
      showError('Failed to load found items');
    }
  } catch (error) {
    console.error('❌ Error loading found items:', error);
    showError('Error loading found items');
  }
}

// Load reports from backend
async function loadReports(filter = 'all') {
  try {
    const searchTerm = document.getElementById('lf-report-search')?.value || '';
    const statusFilter = document.getElementById('status-filter')?.value || '';
    const severityFilter = document.getElementById('severity-filter')?.value || '';
    const dateFilter = document.getElementById('date-filter')?.value || '';

    const params = new URLSearchParams({
      filter: filter,
      search: searchTerm,
      status: statusFilter,
      severity: severityFilter,
      date: dateFilter
    });

    const response = await fetch(`/dashboard/lost-and-found/admin/get-reports?${params.toString()}`);
    const data = await response.json();

    if (data.success) {
      reportsData = data.reports;
      renderReports(data.reports);
    } else {
      console.error('Failed to load reports:', data.message);
      showError('Failed to load reports');
    }
  } catch (error) {
    console.error('Error loading reports:', error);
    showError('Error loading reports');
  }
}

// Render lost items
function renderLostItems(items, filter) {
  const grid = document.getElementById(`lost-items-${filter}`);
  if (!grid) {
    console.warn(`Grid not found for filter: lost-items-${filter}`);
    return;
  }

  if (items.length === 0) {
    grid.innerHTML = '<div class="empty-state"><p>No items found for this filter</p></div>';
    return;
  }

  grid.innerHTML = items.map(item => createItemCard(item, 'lost')).join('');
  console.log(`Rendered ${items.length} lost items in grid: lost-items-${filter}`);
}

// Render found items
function renderFoundItems(items, filter) {
  console.log(`=== RENDERING FOUND ITEMS ===`);
  console.log(`Filter: ${filter}`);
  console.log(`Items count: ${items.length}`);
  console.log('Items data:', items);
  
  const grid = document.getElementById(`found-items-${filter}`);
  if (!grid) {
    console.error(`❌ Grid not found: found-items-${filter}`);
    console.log('Available grids:', Array.from(document.querySelectorAll('[id^="found-items"]')).map(el => el.id));
    return;
  }

  console.log('✓ Grid element found:', grid);
  console.log('Grid current display:', window.getComputedStyle(grid).display);
  console.log('Grid parent:', grid.parentElement);
  
  // Ensure grid is visible
  grid.style.display = 'grid';
  console.log('✓ Grid display set to grid');

  if (items.length === 0) {
    grid.innerHTML = '<div class="empty-state"><p>No found items available for this filter</p></div>';
    console.log('✓ Rendered empty state message');
    return;
  }

  try {
    const cards = items.map(item => createItemCard(item, 'found'));
    grid.innerHTML = cards.join('');
    console.log(`✓ Successfully rendered ${items.length} found items`);
    console.log('Grid innerHTML length:', grid.innerHTML.length);
  } catch (error) {
    console.error('❌ Error creating item cards:', error);
    grid.innerHTML = '<div class="empty-state"><p>Error rendering items</p></div>';
  }
}

// Render reports table
function renderReports(reports) {
  const tbody = document.getElementById('lf-reports-table');
  if (!tbody) return;

  if (reports.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="empty-state">No reports found</td></tr>';
    return;
  }

  tbody.innerHTML = reports.map(report => {
    const statusClass = report.status === 'Returned' || report.status === 'Returned to Owner' ? 'resolved' : 
                       report.status === 'Still Missing' || report.status === 'Available' ? 'active' : 'pending';
    const reportDate = new Date(report.created_at).toLocaleDateString();
    const userName = report.user_email || 'Unknown User';

    return `
      <tr onclick="openItemModal(${report.id}, '${report.type}')">
        <td>#${report.id}</td>
        <td>${report.item_name}</td>
        <td>${userName}</td>
        <td>${reportDate}</td>
        <td><span class="status-badge status-${statusClass}">${report.status}</span></td>
        <td>
          <button class="action-btn" onclick="event.stopPropagation(); openItemModal(${report.id}, '${report.type}')">
            View Details
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

// Create item card
function createItemCard(item, type) {
  const userName = item.user_email || 'Unknown';
  const initials = userName.split('@')[0].substring(0, 2).toUpperCase();
  
  console.log(`📷 Creating card for item ${item.id}:`);
  console.log(`  - Type: ${type}`);
  console.log(`  - Images array:`, item.images);
  console.log(`  - Images count:`, item.images ? item.images.length : 0);
  
  const mainImage = item.images && item.images.length > 0 
    ? (item.images.find(img => img.is_main == 1) || item.images[0])
    : null;
  
  console.log(`  - Main image:`, mainImage);
  
  let imageUrl = '/assets/placeholders/product.jpeg';
  let imageStyle = 'background-size: 40px; background-repeat: no-repeat; background-position: center;';
  
  if (mainImage && mainImage.image_path) {
    imageUrl = mainImage.image_path.startsWith('/') ? mainImage.image_path : '/' + mainImage.image_path;
    // Use cover for actual images to fill the container
    imageStyle = 'background-size: cover; background-position: center;';
    console.log(`  ✓ Using image: ${imageUrl}`);
  } else {
    console.log(`  ❌ No image found for item ${item.id}, using placeholder`);
  }

  // Determine status badge
  const isResolved = item.status === 'Returned' || item.status === 'Returned to Owner' || item.status === 'Collected';
  const statusClass = isResolved ? 'resolved' : 'active';
  const statusText = item.status;

  return `
    <div class="item-card" onclick="openItemModal(${item.id}, '${type}')">
      <div class="item-avatar">${initials}</div>
      <div class="item-info">
        <div class="item-title">${item.item_name}</div>
        <div class="item-description">${item.description?.substring(0, 80) || ''}...</div>
        <div class="item-meta">
          <span>${userName}</span> • <span>${new Date(item.created_at).toLocaleDateString()}</span>
          <br>
          <span class="status-badge status-${statusClass}">${statusText}</span>
        </div>
      </div>
      <div class="item-image" style="background-image: url('${imageUrl}'); ${imageStyle}"></div>
    </div>
  `;
}

// Open item modal with details
async function openItemModal(itemId, type) {
  try {
    const response = await fetch(`/dashboard/lost-and-found/admin/get-item-details?id=${itemId}&type=${type}`);
    const data = await response.json();

    if (!data.success || !data.item) {
      showError('Failed to load item details');
      return;
    }

    currentItem = { ...data.item, type: type };
    displayItemInModal(currentItem);
    document.getElementById('item-modal').classList.add('active');
  } catch (error) {
    console.error('Error loading item details:', error);
    showError('Error loading item details');
  }
}

// Display item in modal
function displayItemInModal(item) {
  const user = item.user_details || {};
  const userName = user.email || item.user_email || 'Unknown User';
  const userInitials = userName.split('@')[0].substring(0, 2).toUpperCase();

  // Update modal avatar and user info
  const avatarEl = document.querySelector('.modal-avatar');
  if (avatarEl) avatarEl.textContent = userInitials;
  
  document.getElementById('modal-user-name').textContent = userName;
  document.getElementById('modal-user-year').textContent = user.reg_no || 'Student';

  // Update item title and description
  document.getElementById('modal-item-title').textContent = item.item_name;
  document.getElementById('modal-description').textContent = item.description;

  // Update location and time
  const location = item.last_known_location || item.found_location || 'Unknown';
  const specificArea = item.specific_area || '';
  document.getElementById('modal-location').textContent = location;
  document.getElementById('modal-location-2').textContent = specificArea ? `${location} (${specificArea})` : location;

  const dateTime = item.date_time_lost || item.date_time_found || item.created_at;
  const date = new Date(dateTime);
  document.getElementById('modal-date').textContent = date.toLocaleDateString();
  document.getElementById('modal-time').textContent = date.toLocaleTimeString();

  // Update contact info
  document.getElementById('modal-email').textContent = item.email || user.email || 'N/A';
  document.getElementById('modal-mobile').textContent = item.mobile || 'N/A';

  // Update images
  const imagesContainer = document.getElementById('modal-images');
  if (imagesContainer) {
    if (item.images && item.images.length > 0) {
      imagesContainer.innerHTML = item.images.slice(0, 3).map(img => {
        const imgUrl = img.image_path.startsWith('/') ? img.image_path : '/' + img.image_path;
        return `<div class="modal-image" style="background-image: url('${imgUrl}'); background-size: cover; background-position: center;"></div>`;
      }).join('');
    } else {
      imagesContainer.innerHTML = '<div class="modal-image" style="background-image: url(\'/assets/placeholders/product.jpeg\'); background-size: 40px; background-repeat: no-repeat; background-position: center;"></div>';
    }
  }

  // Update action buttons
  updateModalActions(item);
}

// Update modal action buttons
function updateModalActions(item) {
  const actionsContainer = document.getElementById('modal-actions');
  const isResolved = item.status === 'Returned' || item.status === 'Returned to Owner' || item.status === 'Collected';

  actionsContainer.innerHTML = `
    ${!isResolved ? `
      <button class="modal-btn btn-success" onclick="markAsResolved()">Mark as Resolved</button>
    ` : ''}
    <button class="modal-btn btn-secondary" onclick="contactOwner()">Contact Owner</button>
    <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
  `;
}

// Update lost items statistics
function updateLostItemsStats(stats) {
  const section = document.querySelector('#lf-lost-items .section-stats');
  if (section) {
    section.innerHTML = `
      <span class="stat-item">
        <span class="stat-number">${stats.all || stats.total || 0}</span>
        <span class="stat-label">All Items</span>
      </span>
      <span class="stat-item">
        <span class="stat-number">${stats.active || 0}</span>
        <span class="stat-label">Active</span>
      </span>
      <span class="stat-item">
        <span class="stat-number">${stats.resolved || 0}</span>
        <span class="stat-label">Resolved</span>
      </span>
    `;
  }
}

// Update found items statistics
function updateFoundItemsStats(stats) {
  const section = document.querySelector('#lf-found-items .section-stats');
  if (section) {
    section.innerHTML = `
      <span class="stat-item">
        <span class="stat-number">${stats.all || stats.total || 0}</span>
        <span class="stat-label">All Items</span>
      </span>
      <span class="stat-item">
        <span class="stat-number">${stats.active || 0}</span>
        <span class="stat-label">Active</span>
      </span>
      <span class="stat-item">
        <span class="stat-number">${stats.returned || 0}</span>
        <span class="stat-label">Returned</span>
      </span>
    `;
  }
}

// Admin Actions
async function markAsResolved() {
  if (!currentItem || !confirm('Mark this item as resolved?')) return;

  const newStatus = currentItem.type === 'lost' ? 'Returned' : 'Collected';

  try {
    const formData = new FormData();
    formData.append('item_id', currentItem.id);
    formData.append('type', currentItem.type);
    formData.append('status', newStatus);

    const response = await fetch('/dashboard/lost-and-found/admin/update-status', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showSuccess('Item marked as resolved');
      closeModal();
      // Reload current view with current filter (or default to all)
      if (document.getElementById('lf-lost-items').classList.contains('active')) {
        loadLostItems(currentFilter || 'all');
      } else if (document.getElementById('lf-found-items').classList.contains('active')) {
        loadFoundItems(currentFilter || 'all');
      } else {
        loadReports(currentFilter || 'all');
      }
    } else {
      showError(data.message || 'Failed to update status');
    }
  } catch (error) {
    console.error('Error updating status:', error);
    showError('Error updating status');
  }
}

async function removePost() {
  if (!currentItem || !confirm('Are you sure you want to remove this post? This action cannot be undone.')) return;

  try {
    const formData = new FormData();
    formData.append('item_id', currentItem.id);
    formData.append('type', currentItem.type);

    const response = await fetch('/dashboard/lost-and-found/admin/delete-item', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showSuccess('Item removed successfully');
      closeModal();
      // Reload current view with current filter (or default to all)
      if (document.getElementById('lf-lost-items').classList.contains('active')) {
        loadLostItems(currentFilter || 'all');
      } else if (document.getElementById('lf-found-items').classList.contains('active')) {
        loadFoundItems(currentFilter || 'all');
      } else {
        loadReports(currentFilter || 'all');
      }
    } else {
      showError(data.message || 'Failed to remove item');
    }
  } catch (error) {
    console.error('Error removing item:', error);
    showError('Error removing item');
  }
}

function contactOwner() {
  if (!currentItem) return;
  
  const user = currentItem.user_details || {};
  const email = currentItem.email || user.email;
  const mobile = currentItem.mobile;
  
  let message = `Contact Information:\n\n`;
  if (email) message += `Email: ${email}\n`;
  if (mobile) message += `Mobile: ${mobile}\n`;
  
  alert(message);
}

// Modal functions
function closeModal() {
  document.getElementById('item-modal').classList.remove('active');
  currentItem = null;
}

function openNewReportModal() {
  document.getElementById('new-report-modal').classList.add('active');
  // Setup form submission handler if not already setup
  setupNewReportForm();
}

function closeNewReportModal() {
  document.getElementById('new-report-modal').classList.remove('active');
  // Reset form
  const form = document.getElementById('new-report-form');
  if (form) {
    form.reset();
    const errorDiv = document.getElementById('form-error-global');
    if (errorDiv) errorDiv.style.display = 'none';
  }
}

function setupNewReportForm() {
  const form = document.getElementById('new-report-form');
  if (!form || form.dataset.initialized) return;
  
  form.dataset.initialized = 'true';
  
  // Character counter for description
  const descTextarea = document.getElementById('description');
  const charCount = document.getElementById('desc-char-count');
  if (descTextarea && charCount) {
    descTextarea.addEventListener('input', () => {
      charCount.textContent = descTextarea.value.length;
    });
  }
  
  // Form submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    console.log('Admin new report form submitted');
    
    const submitBtn = document.getElementById('submit-new-report');
    const errorDiv = document.getElementById('form-error-global');
    
    // Disable submit button
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Creating...';
    }
    
    // Hide any previous errors
    if (errorDiv) errorDiv.style.display = 'none';
    
    try {
      const formData = new FormData(form);
      
      const response = await fetch('/dashboard/lost-and-found/admin/create-report', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      console.log('Create report response:', data);
      
      if (data.success) {
        showSuccess('Report created successfully!');
        closeNewReportModal();
        
        // Reload the appropriate section
        const type = formData.get('type');
        if (type === 'lost') {
          loadLostItems('all');
        } else {
          loadFoundItems('all');
        }
      } else {
        if (errorDiv) {
          errorDiv.textContent = data.message || 'Failed to create report';
          errorDiv.style.display = 'block';
        }
        showError(data.message || 'Failed to create report');
      }
    } catch (error) {
      console.error('Error creating report:', error);
      if (errorDiv) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.style.display = 'block';
      }
      showError('Network error. Please try again.');
    } finally {
      // Re-enable submit button
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Report';
      }
    }
  });
}

// Utility functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function showError(message) {
  alert('Error: ' + message);
}

function showSuccess(message) {
  alert(message);
}

// Diagnostic function for debugging (call from browser console)
window.debugFoundItems = function() {
  console.log('=== FOUND ITEMS DEBUG INFO ===');
  console.log('Found Items Section:');
  const section = document.getElementById('lf-found-items');
  if (!section) {
    console.error('❌ Section #lf-found-items not found!');
  } else {
    console.log('✓ Section exists');
    console.log('  - Has active class:', section.classList.contains('active'));
    console.log('  - Display:', window.getComputedStyle(section).display);
    console.log('  - Visibility:', window.getComputedStyle(section).visibility);
  }
  
  console.log('\nFound Items Grids:');
  ['all', 'active', 'returned', 'expired'].forEach(filter => {
    const grid = document.getElementById(`found-items-${filter}`);
    if (!grid) {
      console.error(`❌ Grid #found-items-${filter} not found!`);
    } else {
      console.log(`✓ Grid #found-items-${filter}:`);
      console.log('  - Display:', window.getComputedStyle(grid).display);
      console.log('  - Has content:', grid.innerHTML.length > 0);
      console.log('  - Content preview:', grid.innerHTML.substring(0, 100) + '...');
    }
  });
  
  console.log('\nData:');
  console.log('  - foundItemsData length:', foundItemsData.length);
  console.log('  - currentFilter:', currentFilter);
  
  console.log('\nTo manually load found items, run: loadFoundItems("all")');
};
