/**
 * Lost and Found Items Manager - Minimal Implementation
 */
class LostFoundManager {
  constructor() {
    this.items = [];
    this.filteredItems = [];
    this.currentTab = 'all';
    this.currentPage = 1;
    this.itemsPerPage = 12;
    this.filters = { search: '', category: '', location: '', date: '' };
    this.currentUserId = 'user123';
    this.init();
  }

  init() {
    this.loadMockData();
    this.setupEventListeners();
    this.applyFilters();
  }

  loadMockData() {
    this.items = [
      {
        id: '1', title: 'Black iPhone 14 Pro',
        description: 'Lost my black iPhone 14 Pro with a blue case. Has a small crack on the back. Last seen in the library study area.',
        category: 'electronics', location: 'library', type: 'lost', status: 'active',
        image: '/assets/placeholders/product.jpeg', reporter_id: 'user456', reporter_name: 'John Doe', reporter_role: 'Student',
        created_at: '2024-01-25T10:30:00Z', contact_method: 'email'
      },
      {
        id: '2', title: 'Brown Leather Wallet',
        description: 'Found a brown leather wallet with some cards inside. No cash visible. Please describe contents to claim.',
        category: 'bags', location: 'cafeteria', type: 'found', status: 'active',
        image: '/assets/placeholders/product.jpeg', reporter_id: 'user789', reporter_name: 'Sarah Smith', reporter_role: 'Faculty',
        created_at: '2024-01-24T14:20:00Z', contact_method: 'both'
      },
      {
        id: '3', title: 'Set of Keys with Red Keychain',
        description: 'Keys successfully returned to owner. Thank you for reporting!',
        category: 'keys', location: 'parking', type: 'found', status: 'claimed',
        image: '/assets/placeholders/product.jpeg', reporter_id: 'user101', reporter_name: 'Mike Johnson', reporter_role: 'Staff',
        created_at: '2024-01-22T16:30:00Z', claimed_at: '2024-01-24T09:15:00Z', contact_method: 'phone'
      },
      {
        id: '4', title: 'Blue Backpack with Laptop',
        description: 'Lost my blue Jansport backpack containing a laptop, textbooks, and notebooks. Very important for my studies.',
        category: 'bags', location: 'classroom', type: 'lost', status: 'active',
        image: '/assets/placeholders/product.jpeg', reporter_id: this.currentUserId, reporter_name: 'Current User', reporter_role: 'Student',
        created_at: '2024-01-23T09:15:00Z', contact_method: 'email'
      },
      {
        id: '5', title: 'Programming Textbook',
        description: 'Found "Introduction to Algorithms" textbook in the computer lab. Has someone\'s name written inside.',
        category: 'books', location: 'classroom', type: 'found', status: 'active',
        image: '/assets/placeholders/product.jpeg', reporter_id: 'user234', reporter_name: 'Emily Davis', reporter_role: 'Student',
        created_at: '2024-01-21T11:45:00Z', contact_method: 'email'
      }
    ];
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
        element.addEventListener('change', (e) => {
          this.filters[type] = e.target.value;
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

    // Report buttons
    const reportLostBtn = document.getElementById('report-lost-btn');
    const reportFoundBtn = document.getElementById('report-found-btn');
    
    if (reportLostBtn) reportLostBtn.addEventListener('click', () => this.openModal('lost'));
    if (reportFoundBtn) reportFoundBtn.addEventListener('click', () => this.openModal('found'));

    // Modal
    this.setupModal();

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
          case 'lost': return item.type === 'lost' && item.status === 'active';
          case 'found': return item.type === 'found' && item.status === 'active';
          case 'my-reports': return item.reporter_id === this.currentUserId;
          case 'claimed': return item.status === 'claimed';
          default: return true;
        }
      });
    }

    // Search filter
    if (this.filters.search) {
      const search = this.filters.search.toLowerCase();
      filtered = filtered.filter(item => 
        item.title.toLowerCase().includes(search) ||
        item.description.toLowerCase().includes(search) ||
        item.reporter_name.toLowerCase().includes(search)
      );
    }

    // Other filters
    ['category', 'location'].forEach(key => {
      if (this.filters[key]) {
        filtered = filtered.filter(item => item[key] === this.filters[key]);
      }
    });

    // Date filter
    if (this.filters.date) {
      const now = new Date();
      filtered = filtered.filter(item => {
        const itemDate = new Date(item.created_at);
        switch (this.filters.date) {
          case 'today': return itemDate.toDateString() === now.toDateString();
          case 'week': return itemDate >= new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
          case 'month': return itemDate >= new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
          default: return true;
        }
      });
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
      lost: this.items.filter(i => i.type === 'lost' && i.status === 'active').length,
      found: this.items.filter(i => i.type === 'found' && i.status === 'active').length,
      'my-reports': this.items.filter(i => i.reporter_id === this.currentUserId).length,
      claimed: this.items.filter(i => i.status === 'claimed').length
    };

    Object.entries(counts).forEach(([tab, count]) => {
      const el = document.getElementById(`count-${tab}`);
      if (el) el.textContent = count;
    });
  }

  renderItems() {
    const grid = document.getElementById('items-grid');
    const emptyState = document.getElementById('empty-state');
    
    if (!grid) return;

    const start = (this.currentPage - 1) * this.itemsPerPage;
    const items = this.filteredItems.slice(start, start + this.itemsPerPage);

    if (items.length === 0) {
      grid.innerHTML = '';
      if (emptyState) emptyState.style.display = 'flex';
      return;
    }

    if (emptyState) emptyState.style.display = 'none';

    grid.innerHTML = items.map(item => `
      <div class="item-card item-card--${item.type} ${item.status === 'claimed' ? 'item-card--claimed' : ''}" data-item-id="${item.id}">
        <div class="item-image-container">
          <img src="${item.image}" alt="${item.title}" class="item-image">
          <div class="item-status-badge item-status-badge--${item.status === 'claimed' ? 'claimed' : item.type}">
            ${item.status === 'claimed' ? 'Resolved' : (item.type === 'lost' ? 'Lost' : 'Found')}
          </div>
          <div class="item-date">${this.getTimeAgo(item.created_at)}</div>
        </div>
        
        <div class="item-content">
          <div class="item-header">
            <h3 class="item-title">${item.title}</h3>
            <div class="item-category">${this.getCategoryName(item.category)}</div>
          </div>
          
          <div class="item-details">
            <div class="item-location">
              <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              ${this.getLocationName(item.location)}
            </div>
            
            <div class="item-description">
              ${item.description.length > 120 ? item.description.substring(0, 120) + '...' : item.description}
            </div>
          </div>
          
          <div class="item-footer">
            ${item.status === 'claimed' ? `
              <div class="claimed-info">
                <svg class="success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Successfully reunited</span>
              </div>
            ` : `
              <div class="item-reporter">
                <div class="reporter-avatar">
                  <img src="/images/placeholders/user.png" alt="${item.reporter_name}">
                </div>
                <div class="reporter-info">
                  <span class="reporter-name">${item.reporter_name}</span>
                  <span class="reporter-role">${item.reporter_role}</span>
                </div>
              </div>
              
              <div class="item-actions">
                <button class="action-btn action-btn--contact" onclick="contactReporter('${item.id}')">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  Contact
                </button>
                ${item.type === 'lost' ? `
                  <button class="action-btn action-btn--help" onclick="markAsFound('${item.id}')">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M9 12l2 2 4-4"/>
                    </svg>
                    Found It
                  </button>
                ` : `
                  <button class="action-btn action-btn--claim" onclick="claimItem('${item.id}')">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    </svg>
                    Mine
                  </button>
                `}
              </div>
            `}
          </div>
        </div>
      </div>
    `).join('');
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
    this.filters = { search: '', category: '', location: '', date: '' };
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('location-filter').value = '';
    document.getElementById('date-filter').value = '';
    this.applyFilters();
  }

  openModal(type) {
    const modal = document.getElementById('report-modal');
    const modalTitle = document.getElementById('modal-title');
    const reportType = document.getElementById('report-type');
    
    if (modal && modalTitle && reportType) {
      modalTitle.textContent = type === 'lost' ? 'Report Lost Item' : 'Report Found Item';
      reportType.value = type;
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
    }
  }

  closeModal() {
    const modal = document.getElementById('report-modal');
    const form = document.getElementById('report-form');
    
    if (modal) {
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
    }
    
    if (form) {
      form.reset();
      this.removeImage();
    }
  }

  handleFileUpload(file) {
    if (!file || file.size > 5 * 1024 * 1024) {
      alert('File must be less than 5MB');
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      const preview = document.getElementById('upload-preview');
      const image = document.getElementById('preview-image');
      const placeholder = document.querySelector('.upload-placeholder');

      if (preview && image && placeholder) {
        image.src = e.target.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
      }
    };
    reader.readAsDataURL(file);
  }

  removeImage() {
    const preview = document.getElementById('upload-preview');
    const placeholder = document.querySelector('.upload-placeholder');
    const fileInput = document.getElementById('item-image');

    if (preview && placeholder && fileInput) {
      preview.style.display = 'none';
      placeholder.style.display = 'block';
      fileInput.value = '';
    }
  }

  submitReport() {
    alert('Report submitted successfully!');
    this.closeModal();
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
function contactReporter(itemId) {
  alert(`Contacting reporter for item ${itemId}`);
}

function markAsFound(itemId) {
  alert(`Marking item ${itemId} as found`);
}

function claimItem(itemId) {
  alert(`Claiming item ${itemId}`);
}

function closeReportModal() {
  if (window.lostFoundManager) {
    window.lostFoundManager.closeModal();
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.lostFoundManager = new LostFoundManager();
});