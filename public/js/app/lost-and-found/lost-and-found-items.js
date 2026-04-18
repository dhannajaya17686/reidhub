/**
 * Lost and Found Items Manager - Backend Integration
 */
class LostFoundManager {
  constructor() {
    this.items = [];
    this.filteredItems = [];
    this.currentTab = 'all';
    this.currentPage = 1;
    this.itemsPerPage = 12;
    this.filters = { search: '', category: '', location: '', date: '', severity: '' };
    this.currentUserId = window.currentUserId || null; // Get from window
    this.init();
  }

  async init() {
    console.log('Initializing Lost & Found Manager...'); // Debug
    console.log('Current User ID:', this.currentUserId); // Debug
    this.setupEventListeners();
    const loaded = await this.loadItems();
    console.log('Items loaded:', loaded, 'Total items:', this.items.length); // Debug
    this.applyFilters();
  }

  async loadItems() {
    try {
      const params = new URLSearchParams();
      if (this.filters.category) params.append('category', this.filters.category);
      if (this.filters.location) params.append('location', this.filters.location);
      if (this.filters.date) params.append('date_filter', this.filters.date);
      if (this.filters.severity) params.append('severity', this.filters.severity);
      if (this.filters.search) params.append('search', this.filters.search);

      const url = `/dashboard/lost-and-found/items/get-all${params.toString() ? '?' + params.toString() : ''}`;
      const response = await fetch(url);
      const data = await response.json();

      console.log('🔄 API Response:', data); // Debug log
      console.log('📊 Lost items count:', data.lostItems ? data.lostItems.length : 0);
      console.log('📊 Found items count:', data.foundItems ? data.foundItems.length : 0);
      
      if (data.lostItems && data.lostItems.length > 0) {
        console.log('📷 First lost item:', data.lostItems[0]);
        console.log('📷 First lost item images:', data.lostItems[0].images);
      }
      if (data.foundItems && data.foundItems.length > 0) {
        console.log('📷 First found item:', data.foundItems[0]);
        console.log('📷 First found item images:', data.foundItems[0].images);
      }

      if (data.success) {
        // Combine lost and found items
        this.items = [
          ...data.lostItems.map(item => ({ ...item, type: 'lost', user_id: parseInt(item.user_id) })),
          ...data.foundItems.map(item => ({ ...item, type: 'found', user_id: parseInt(item.user_id) }))
        ];
        
        console.log('✅ Loaded items:', this.items.length, 'items total');
        console.log('Current user ID:', this.currentUserId);
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
    // Search
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.filters.search = e.target.value;
        this.applyFilters();
      });
    }

    // Filters
    ['category', 'location', 'date'].forEach(type => {
      const element = document.getElementById(`${type}-filter`);
      if (element) {
        element.addEventListener('change', async (e) => {
          this.filters[type] = e.target.value;
          await this.loadItems();
          this.applyFilters();
        });
      }
    });

    // Clear filters
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => this.clearFilters());
    }

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

    // Report buttons - Navigate to dedicated pages
    const reportLostBtn = document.getElementById('report-lost-btn');
    const reportFoundBtn = document.getElementById('report-found-btn');
    
    if (reportLostBtn) {
      reportLostBtn.addEventListener('click', () => {
        window.location.href = '/dashboard/lost-and-found/report-lost-item';
      });
    }
    if (reportFoundBtn) {
      reportFoundBtn.addEventListener('click', () => {
        window.location.href = '/dashboard/lost-and-found/report-found-item';
      });
    }

    // Pagination
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        if (this.currentPage > 1) {
          this.currentPage--;
          this.renderItems();
          this.updatePagination();
        }
      });
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(this.filteredItems.length / this.itemsPerPage);
        if (this.currentPage < totalPages) {
          this.currentPage++;
          this.renderItems();
          this.updatePagination();
        }
      });
    }
  }

  setupModal() {
    const modal = document.getElementById('report-modal');
    const closeBtn = modal?.querySelector('.modal-close');
    const backdrop = modal?.querySelector('.modal-backdrop');
    const form = document.getElementById('report-form');
    const fileInput = document.getElementById('item-image');
    const uploadArea = document.getElementById('file-upload-area');
    const textarea = document.getElementById('item-description');
    const charCount = document.getElementById('char-count');

    // Close handlers
    [closeBtn, backdrop].forEach(el => {
      if (el) el.addEventListener('click', () => this.closeModal());
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal?.getAttribute('aria-hidden') === 'false') {
        this.closeModal();
      }
    });

    // File upload
    if (uploadArea && fileInput) {
      uploadArea.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', (e) => this.handleFileUpload(e.target.files[0]));
    }

    // Character counter
    if (textarea && charCount) {
      textarea.addEventListener('input', () => {
        const length = textarea.value.length;
        charCount.textContent = length;
        if (length >= 500) {
          textarea.value = textarea.value.substring(0, 500);
          charCount.textContent = '500';
        }
      });
    }

    // Form submit
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this.submitReport();
      });
    }

    // Remove image
    const removeBtn = document.getElementById('remove-image');
    if (removeBtn) {
      removeBtn.addEventListener('click', () => this.removeImage());
    }
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
          case 'my-reports': 
            return this.currentUserId && parseInt(item.user_id) === parseInt(this.currentUserId);
          case 'claimed': 
            return item.status === 'Returned' || item.status === 'Returned to Owner';
          default: return true;
        }
      });
    }

    // Search filter (client-side filtering on top of server-side)
    if (this.filters.search) {
      const search = this.filters.search.toLowerCase();
      filtered = filtered.filter(item => 
        (item.item_name || item.title || '').toLowerCase().includes(search) ||
        (item.description || '').toLowerCase().includes(search) ||
        (item.first_name + ' ' + item.last_name).toLowerCase().includes(search)
      );
    }

    this.filteredItems = filtered;
    this.currentPage = 1;
    this.updateCounts();
    this.renderItems();
    this.updatePagination();
  }

  updateCounts() {
    const counts = {
      all: this.items.length,
      lost: this.items.filter(i => i.type === 'lost' && i.status === 'Still Missing').length,
      found: this.items.filter(i => i.type === 'found' && (i.status === 'Available' || i.status === 'Collected')).length,
      'my-reports': this.currentUserId ? this.items.filter(i => parseInt(i.user_id) === parseInt(this.currentUserId)).length : 0,
      claimed: this.items.filter(i => i.status === 'Returned' || i.status === 'Returned to Owner').length
    };

    Object.entries(counts).forEach(([tab, count]) => {
      const el = document.getElementById(`count-${tab}`);
      if (el) el.textContent = count;
    });
  }

  renderItems() {
    const grid = document.getElementById('items-grid');
    const emptyState = document.getElementById('empty-state');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    if (!grid) return;

    // Remove loading spinner
    if (loadingSpinner) loadingSpinner.remove();

    const start = (this.currentPage - 1) * this.itemsPerPage;
    const items = this.filteredItems.slice(start, start + this.itemsPerPage);

    console.log('Rendering items. Total filtered:', this.filteredItems.length, 'Current page items:', items.length); // Debug

    if (items.length === 0) {
      grid.innerHTML = '';
      if (emptyState) emptyState.style.display = 'flex';
      return;
    }

    if (emptyState) emptyState.style.display = 'none';

    grid.innerHTML = items.map(item => {
      const itemName = item.item_name || item.title || 'Untitled Item';
      const mainImage = item.images && item.images.length > 0 
        ? item.images.find(img => img.is_main == 1) || item.images[0]
        : null;
      
      // Ensure image path starts with / if it doesn't already
      let imageUrl = '/assets/placeholders/product.jpeg';
      if (mainImage && mainImage.image_path) {
        imageUrl = mainImage.image_path.startsWith('/') ? mainImage.image_path : '/' + mainImage.image_path;
      }
      
      const isResolved = item.status === 'Returned' || item.status === 'Returned to Owner';
      const reporterName = `${item.first_name || ''} ${item.last_name || ''}`.trim() || 'Anonymous';
      
      return `
      <div class="item-card item-card--${item.type} ${isResolved ? 'item-card--claimed' : ''}" data-item-id="${item.id}">
        <div class="item-image-container">
          <img src="${imageUrl}" alt="${itemName}" class="item-image" onerror="this.src='/assets/placeholders/product.jpeg'">
          <div class="item-status-badge item-status-badge--${isResolved ? 'claimed' : item.type}">
            ${isResolved ? 'Resolved' : (item.type === 'lost' ? 'Lost' : 'Found')}
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
                <span>${item.status === 'Returned' ? 'Item Returned' : 'Report Cancelled'}</span>
              </div>
            ` : `
              <div class="item-reporter">
                <div class="reporter-avatar">
                  ${reporterName.charAt(0).toUpperCase()}
                </div>
                <div class="reporter-info">
                  <span class="reporter-name">${reporterName}</span>
                </div>
              </div>
              
              <div class="item-actions">
                <button class="action-btn action-btn--contact" onclick="contactReporter('${item.id}', '${item.type}')">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  Contact
                </button>
              </div>
            `}
          </div>
        </div>
      </div>
    `;
    }).join('');
  }

  updatePagination() {
    const total = this.filteredItems.length;
    const totalPages = Math.ceil(total / this.itemsPerPage);
    const start = (this.currentPage - 1) * this.itemsPerPage + 1;
    const end = Math.min(this.currentPage * this.itemsPerPage, total);

    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalItems = document.getElementById('total-items');

    if (showingStart) showingStart.textContent = start;
    if (showingEnd) showingEnd.textContent = end;
    if (totalItems) totalItems.textContent = total;

    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (prevBtn) prevBtn.disabled = this.currentPage === 1;
    if (nextBtn) nextBtn.disabled = this.currentPage === totalPages;
  }

  clearFilters() {
    this.filters = { search: '', category: '', location: '', date: '', severity: '' };
    
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';
    
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) categoryFilter.value = '';
    
    const locationFilter = document.getElementById('location-filter');
    if (locationFilter) locationFilter.value = '';
    
    const dateFilter = document.getElementById('date-filter');
    if (dateFilter) dateFilter.value = '';
    
    this.loadItems().then(() => this.applyFilters());
  }

  clearFilters() {
    this.filters = { search: '', category: '', location: '', date: '', severity: '' };
    
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';
    
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) categoryFilter.value = '';
    
    const locationFilter = document.getElementById('location-filter');
    if (locationFilter) locationFilter.value = '';
    
    const dateFilter = document.getElementById('date-filter');
    if (dateFilter) dateFilter.value = '';
    
    this.loadItems().then(() => this.applyFilters());
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
function contactReporter(itemId, itemType) {
  // Show contact info modal (can be implemented later)
  alert(`Contact information for item ${itemId} will be displayed here`);
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.lostFoundManager = new LostFoundManager();
});