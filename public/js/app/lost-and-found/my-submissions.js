/**
 * My Submissions Manager - Manage user's own lost and found reports
 */
class MySubmissionsManager {
  constructor() {
    this.items = [];
    this.filteredItems = [];
    this.currentTab = 'all';
    this.init();
  }

  async init() {
    this.setupEventListeners();
    await this.loadMyItems();
    this.applyFilters();
  }

  async loadMyItems() {
    try {
      const response = await fetch('/dashboard/lost-and-found/items/get-my-items');
      const data = await response.json();

      console.log('🔄 API Response:', data); // Debug log
      console.log('📊 Lost items count:', data.lostItems ? data.lostItems.length : 0);
      console.log('📊 Found items count:', data.foundItems ? data.foundItems.length : 0);
      
      if (data.lostItems && data.lostItems.length > 0) {
        console.log('📷 First lost item images:', data.lostItems[0].images);
      }
      if (data.foundItems && data.foundItems.length > 0) {
        console.log('📷 First found item images:', data.foundItems[0].images);
      }

      if (data.success) {
        // Combine lost and found items
        this.items = [
          ...data.lostItems.map(item => ({ ...item, type: 'lost' })),
          ...data.foundItems.map(item => ({ ...item, type: 'found' }))
        ];
        console.log('✅ Loaded items:', this.items.length); // Debug log
        return true;
      } else {
        console.error('❌ Failed to load items:', data.message);
        return false;
      }
    } catch (error) {
      console.error('❌ Error loading items:', error);
      return false;
    }
  }

  setupEventListeners() {
    // Tabs
    document.querySelectorAll('.tab-button').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-button').forEach(t => {
          t.classList.remove('tab-button--active');
          t.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('tab-button--active');
        tab.setAttribute('aria-selected', 'true');
        this.currentTab = tab.dataset.tab;
        this.applyFilters();
      });
    });
  }

  applyFilters() {
    let filtered = [...this.items];

    // Tab filter
    if (this.currentTab !== 'all') {
      filtered = filtered.filter(item => {
        switch (this.currentTab) {
          case 'lost': 
            return item.type === 'lost' && item.status === 'Still Missing';
          case 'found': 
            return item.type === 'found' && (item.status === 'Available' || item.status === 'Collected');
          case 'resolved': 
            return item.status === 'Returned' || item.status === 'Returned to Owner';
          default: 
            return true;
        }
      });
    }

    this.filteredItems = filtered;
    this.updateCounts();
    this.renderItems();
  }

  updateCounts() {
    const counts = {
      all: this.items.length,
      lost: this.items.filter(i => i.type === 'lost' && i.status === 'Still Missing').length,
      found: this.items.filter(i => i.type === 'found' && (i.status === 'Available' || i.status === 'Collected')).length,
      resolved: this.items.filter(i => i.status === 'Returned' || i.status === 'Returned to Owner').length
    };

    Object.entries(counts).forEach(([key, value]) => {
      const el = document.getElementById(`count-${key}`);
      if (el) el.textContent = value;
    });
  }

  renderItems() {
    const grid = document.getElementById('items-grid');
    const emptyState = document.getElementById('empty-state');
    
    if (!grid) return;

    if (this.filteredItems.length === 0) {
      grid.innerHTML = '';
      if (emptyState) emptyState.style.display = 'flex';
      return;
    }

    if (emptyState) emptyState.style.display = 'none';

    grid.innerHTML = this.filteredItems.map(item => {
      const itemName = item.item_name || 'Untitled Item';
      const mainImage = item.images && item.images.length > 0 
        ? item.images.find(img => img.is_main == 1) || item.images[0]
        : null;
      
      // Ensure image path starts with / if it doesn't already
      let imageUrl = '/assets/placeholders/product.jpeg';
      if (mainImage && mainImage.image_path) {
        imageUrl = mainImage.image_path.startsWith('/') ? mainImage.image_path : '/' + mainImage.image_path;
      }
      
      console.log('Rendering item:', itemName, 'Image:', imageUrl, 'Images array:', item.images); // Debug log
      
      const isResolved = item.status === 'Returned' || item.status === 'Returned to Owner';
      
      return `
      <div class="item-card item-card--${item.type} ${isResolved ? 'item-card--claimed' : ''}" data-item-id="${item.id}">
        <div class="item-image-container">
          <img src="${imageUrl}" alt="${itemName}" class="item-image" onerror="this.src='/assets/placeholders/product.jpeg'">
          <div class="item-status-badge item-status-badge--${isResolved ? 'claimed' : item.type}">
            ${isResolved ? item.status : (item.type === 'lost' ? 'Lost' : 'Found')}
          </div>
          ${item.severity_level && item.severity_level === 'Critical' ? `
            <div class="item-priority-badge">🚨 Critical</div>
          ` : ''}
          <div class="item-date">${this.getTimeAgo(item.created_at)}</div>
        </div>
        
        <div class="item-content">
          <div class="item-header">
            <h3 class="item-title">${itemName}</h3>
            <div class="item-category">${this.getCategoryName(item.category)}</div>
          </div>
          
          <div class="item-details">
            <div class="item-location">
              <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              ${this.getLocationName(item.last_known_location || item.found_location || item.location)}
            </div>
            
            <div class="item-description">
              ${item.description && item.description.length > 120 ? item.description.substring(0, 120) + '...' : item.description || ''}
            </div>
          </div>
          
          <div class="item-footer">
            ${isResolved ? `
              <div class="claimed-info">
                <svg class="success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>${item.status === 'Returned' || item.status === 'Returned to Owner' ? 'Item Recovered' : item.status}</span>
              </div>
            ` : `
              <div class="item-actions">
                ${item.type === 'lost' ? `
                  <button class="action-btn action-btn--primary" onclick="updateItemStatus(${item.id}, 'Returned')">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark as Found
                  </button>
                  <button class="action-btn action-btn--secondary" onclick="openStatusModal(${item.id})">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Update Status
                  </button>
                ` : `
                  <button class="action-btn action-btn--secondary" onclick="openStatusModal(${item.id})">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Update Status
                  </button>
                `}
              </div>
            `}
          </div>
        </div>
      </div>
    `;
    }).join('');
  }

  getCategoryName(category) {
    const categories = {
      electronics: 'Electronics', clothing: 'Clothing & Accessories', bags: 'Bags & Wallets',
      books: 'Books & Stationery', jewelry: 'Jewelry', keys: 'Keys & Cards',
      sports: 'Sports Equipment', other: 'Other'
    };
    return categories[category] || category;
  }

  getLocationName(location) {
    const locations = {
      library: 'Library', cafeteria: 'Cafeteria', classroom: 'Classroom',
      parking: 'Parking Area', 'sports-complex': 'Sports Complex', dormitory: 'Dormitory',
      'admin-building': 'Admin Building', 'other-location': 'Other'
    };
    return locations[location] || location;
  }

  getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const days = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (days === 0) return 'Today';
    if (days === 1) return '1 day ago';
    if (days < 7) return `${days} days ago`;
    if (days < 30) return `${Math.floor(days / 7)} week${Math.floor(days / 7) !== 1 ? 's' : ''} ago`;
    return `${Math.floor(days / 30)} month${Math.floor(days / 30) !== 1 ? 's' : ''} ago`;
  }
}

// Global functions
async function updateItemStatus(itemId, newStatus) {
  if (!confirm(`Are you sure you want to mark this item as ${newStatus}?`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('status', newStatus);

    const response = await fetch('/dashboard/lost-and-found/items/update-status', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      alert(data.message);
      location.reload(); // Reload to show updated status
    } else {
      alert('Error: ' + data.message);
    }
  } catch (error) {
    console.error('Error updating status:', error);
    alert('Failed to update item status. Please try again.');
  }
}

function openStatusModal(itemId) {
  const modal = document.getElementById('status-modal');
  const itemIdInput = document.getElementById('status-item-id');
  
  if (modal && itemIdInput) {
    itemIdInput.value = itemId;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
}

function closeStatusModal() {
  const modal = document.getElementById('status-modal');
  const form = document.getElementById('status-form');
  
  if (modal) {
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  if (form) {
    form.reset();
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.mySubmissionsManager = new MySubmissionsManager();

  // Setup status form
  const statusForm = document.getElementById('status-form');
  if (statusForm) {
    statusForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const itemId = document.getElementById('status-item-id').value;
      const newStatus = document.getElementById('status-select').value;
      
      await updateItemStatus(itemId, newStatus);
      closeStatusModal();
    });
  }

  // Close modal on backdrop click
  const modalBackdrop = document.querySelector('.modal-backdrop');
  if (modalBackdrop) {
    modalBackdrop.addEventListener('click', closeStatusModal);
  }

  // Close modal on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('status-modal');
      if (modal && modal.getAttribute('aria-hidden') === 'false') {
        closeStatusModal();
      }
    }
  });
});
