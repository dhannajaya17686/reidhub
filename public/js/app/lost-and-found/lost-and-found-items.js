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
    this.setupModal();
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
    const modal = document.getElementById('item-details-modal');
    const closeBtn = modal?.querySelector('.modal-close');
    const backdrop = modal?.querySelector('.modal-backdrop');

    // Close handlers
    [closeBtn, backdrop].forEach(el => {
      if (el) el.addEventListener('click', () => this.closeDetailsModal());
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal?.getAttribute('aria-hidden') === 'false') {
        this.closeDetailsModal();
      }
    });
  }

  closeDetailsModal() {
    const modal = document.getElementById('item-details-modal');
    if (modal) {
      modal.setAttribute('aria-hidden', 'true');
      modal.style.display = 'none';
    }
  }

  async showItemDetails(itemId, itemType) {
    const modal = document.getElementById('item-details-modal');
    const content = document.getElementById('item-details-content');
    
    if (!modal || !content) return;

    // Show modal with loading state
    modal.setAttribute('aria-hidden', 'false');
    modal.style.display = 'flex';
    content.innerHTML = `
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Loading details...</p>
      </div>
    `;

    try {
      const response = await fetch(`/dashboard/lost-and-found/items/details?id=${itemId}&type=${itemType}`);
      const data = await response.json();

      if (data.success && data.item) {
        this.renderItemDetails(data.item, itemType);
      } else {
        content.innerHTML = `
          <div class="error-message">
            <p>Failed to load item details. Please try again.</p>
          </div>
        `;
      }
    } catch (error) {
      console.error('Error loading item details:', error);
      content.innerHTML = `
        <div class="error-message">
          <p>An error occurred while loading item details.</p>
        </div>
      `;
    }
  }

  renderItemDetails(item, itemType) {
    const content = document.getElementById('item-details-content');
    if (!content) return;

    const isLost = itemType === 'lost';
    const statusBadge = item.status === 'Returned' || item.status === 'Returned to Owner' ? 'Resolved' : (isLost ? 'Lost' : 'Found');
    const statusClass = item.status === 'Returned' || item.status === 'Returned to Owner' ? 'claimed' : itemType;

    // Format date and time
    const dateTimeLost = new Date(item.date_time_lost || item.date_time_found);
    const formattedDate = dateTimeLost.toLocaleDateString('en-US', { 
      year: 'numeric', month: 'long', day: 'numeric' 
    });
    const formattedTime = dateTimeLost.toLocaleTimeString('en-US', { 
      hour: '2-digit', minute: '2-digit' 
    });

    content.innerHTML = `
      <div class="item-details-grid">
        <!-- Images Section -->
        <div class="details-images">
          ${item.images && item.images.length > 0 ? `
            <div class="main-image-container">
              <img src="${item.images.find(img => img.is_main == 1)?.image_path || item.images[0].image_path}" 
                   alt="${item.item_name}" 
                   class="main-detail-image"
                   onerror="this.src='/assets/placeholders/product.jpeg'">
            </div>
            ${item.images.length > 1 ? `
              <div class="thumbnail-grid">
                ${item.images.map((img, idx) => `
                  <img src="${img.image_path}" 
                       alt="Image ${idx + 1}" 
                       class="thumbnail-image ${img.is_main == 1 ? 'active' : ''}"
                       onclick="document.querySelector('.main-detail-image').src = this.src"
                       onerror="this.src='/assets/placeholders/product.jpeg'">
                `).join('')}
              </div>
            ` : ''}
          ` : `
            <div class="main-image-container">
              <img src="/assets/placeholders/product.jpeg" alt="No image" class="main-detail-image">
            </div>
          `}
        </div>

        <!-- Details Section -->
        <div class="details-info">
          <div class="details-header">
            <div>
              <h3 class="details-title">${item.item_name}</h3>
              <div class="details-badges">
                <span class="status-badge status-badge--${statusClass}">${statusBadge}</span>
                ${item.severity_level === 'Critical' ? '<span class="priority-badge">🚨 Critical</span>' : ''}
                ${item.severity_level === 'Important' ? '<span class="priority-badge priority-badge--medium">⚠️ Important</span>' : ''}
              </div>
            </div>
          </div>

          <div class="details-section">
            <h4 class="section-title">Item Information</h4>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">Category:</span>
                <span class="info-value">${this.getCategoryName(item.category)}</span>
              </div>
              <div class="info-item">
                <span class="info-label">${isLost ? 'Last Known Location:' : 'Found Location:'}</span>
                <span class="info-value">${this.getLocationName(item.last_known_location || item.found_location)}</span>
              </div>
              ${item.specific_area ? `
                <div class="info-item">
                  <span class="info-label">Specific Area:</span>
                  <span class="info-value">${item.specific_area}</span>
                </div>
              ` : ''}
              <div class="info-item">
                <span class="info-label">${isLost ? 'Date Lost:' : 'Date Found:'}</span>
                <span class="info-value">${formattedDate} at ${formattedTime}</span>
              </div>
              ${!isLost && item.item_condition ? `
                <div class="info-item">
                  <span class="info-label">Condition:</span>
                  <span class="info-value">${item.item_condition}</span>
                </div>
              ` : ''}
              ${!isLost && item.current_location ? `
                <div class="info-item">
                  <span class="info-label">Current Location:</span>
                  <span class="info-value">${item.current_location}</span>
                </div>
              ` : ''}
            </div>
          </div>

          <div class="details-section">
            <h4 class="section-title">Description</h4>
            <p class="description-text">${item.description || 'No description provided.'}</p>
          </div>

          ${item.special_instructions ? `
            <div class="details-section">
              <h4 class="section-title">Special Instructions</h4>
              <p class="description-text">${item.special_instructions}</p>
            </div>
          ` : ''}

          ${isLost && item.reward_offered ? `
            <div class="details-section reward-section">
              <h4 class="section-title">💰 Reward Offered</h4>
              ${item.reward_amount ? `<p class="reward-amount">LKR ${parseFloat(item.reward_amount).toFixed(2)}</p>` : ''}
              ${item.reward_details ? `<p class="description-text">${item.reward_details}</p>` : ''}
            </div>
          ` : ''}

          <div class="details-section">
            <h4 class="section-title">Contact Information</h4>
            <div class="contact-grid">
              <div class="contact-item">
                <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                <div class="contact-info">
                  <span class="contact-label">Reported by:</span>
                  <span class="contact-value">${item.first_name} ${item.last_name}</span>
                </div>
              </div>
              ${item.mobile ? `
                <div class="contact-item">
                  <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                  <div class="contact-info">
                    <span class="contact-label">Mobile:</span>
                    <a href="tel:${item.mobile}" class="contact-value contact-link">${item.mobile}</a>
                  </div>
                </div>
              ` : ''}
              ${item.email ? `
                <div class="contact-item">
                  <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                  <div class="contact-info">
                    <span class="contact-label">Email:</span>
                    <a href="mailto:${item.email}" class="contact-value contact-link">${item.email}</a>
                  </div>
                </div>
              ` : ''}
              ${item.alt_contact ? `
                <div class="contact-item">
                  <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                  <div class="contact-info">
                    <span class="contact-label">Alternate Contact:</span>
                    <span class="contact-value">${item.alt_contact}</span>
                  </div>
                </div>
              ` : ''}
            </div>
          </div>

          <div class="details-footer">
            <p class="posted-date">Posted on ${new Date(item.created_at).toLocaleDateString('en-US', { 
              year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
            })}</p>
          </div>
        </div>
      </div>
    `;
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
      <div class="item-card item-card--${item.type} ${isResolved ? 'item-card--claimed' : ''}" 
           data-item-id="${item.id}" 
           data-item-type="${item.type}"
           onclick="window.lostFoundManager.showItemDetails('${item.id}', '${item.type}')"
           style="cursor: pointer;">
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
                <button class="action-btn action-btn--view" onclick="event.stopPropagation(); window.lostFoundManager.showItemDetails('${item.id}', '${item.type}')">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  View Details
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.lostFoundManager = new LostFoundManager();
});