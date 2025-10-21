class CommunityManager {
  constructor() {
    this.module = window.COMMUNITY_MODULE || 'blogs';
    this.apiBase = window.API_BASE || '/api/community/blogs';
    this.init();
  }

  async init() {
    this.setupTabs();
    this.setupSearch();
    this.setupCategoryFilters();
    this.setupDeleteButtons();
    
    // Load initial data
    await this.loadAllContent();
    await this.loadMyContent();
  }

  async loadAllContent() {
    try {
      const response = await fetch(`${this.apiBase}`);
      const data = await response.json();
      
      if (data.success) {
        this.renderContent(data.items || data.blogs || data.clubs || data.events, `${this.module}-grid`);
      }
    } catch (error) {
      console.error('Failed to load content:', error);
      document.getElementById(`${this.module}-grid`).innerHTML = '<div class="error-state">Failed to load content</div>';
    }
  }

  async loadMyContent() {
    try {
      const response = await fetch(`${this.apiBase}/my-${this.module}`);
      const data = await response.json();
      
      if (data.success) {
        this.renderMyContent(data.items || data.blogs || data.clubs || data.events, `my-${this.module}-grid`);
      }
    } catch (error) {
      console.error('Failed to load my content:', error);
      document.getElementById(`my-${this.module}-grid`).innerHTML = '<div class="error-state">Failed to load your content</div>';
    }
  }

  renderContent(items, containerId) {
    const container = document.getElementById(containerId);
    
    if (!items || items.length === 0) {
      container.innerHTML = '<div class="empty-state"><p>No content found.</p></div>';
      return;
    }

    container.innerHTML = items.map(item => this.createContentCard(item)).join('');
  }

  renderMyContent(items, containerId) {
    const container = document.getElementById(containerId);
    
    if (!items || items.length === 0) {
      container.innerHTML = this.getUploadCard();
      return;
    }

    const cardsHtml = items.map(item => this.createMyContentCard(item)).join('');
    container.innerHTML = cardsHtml + this.getUploadCard();
    this.setupDeleteButtons();
  }

  createContentCard(item) {
    const date = new Date(item.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    
    return `
      <article class="content-card" data-category="${item.category || ''}">
        <a href="/dashboard/community/${this.module}/view/${item.id}" class="content-card__link">
          <div class="content-card__image">
            <img src="${item.image_path || '/public/images/placeholder.jpg'}" alt="${item.title || item.name}">
          </div>
          <div class="content-card__content">
            <h3 class="content-card__title">${item.title || item.name}</h3>
            <div class="content-card__meta">
              <span class="content-card__author">By ${item.first_name} ${item.last_name}</span>
              <span class="content-card__separator">•</span>
              <span class="content-card__date">${formattedDate}</span>
            </div>
          </div>
        </a>
      </article>
    `;
  }

  createMyContentCard(item) {
    const date = new Date(item.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    
    return `
      <article class="content-card content-card--owned">
        <div class="content-card__image">
          <img src="${item.image_path || '/public/images/placeholder.jpg'}" alt="${item.title || item.name}">
        </div>
        <div class="content-card__content">
          <h3 class="content-card__title">${item.title || item.name}</h3>
          <div class="content-card__meta">
            <span class="content-card__date">${formattedDate}</span>
          </div>
          <div class="content-card__actions">
            <a href="/dashboard/community/${this.module}/view/${item.id}" class="btn btn--small btn--outline">View</a>
            <a href="/dashboard/community/${this.module}/edit/${item.id}" class="btn btn--small btn--primary">Edit</a>
            <button class="btn btn--small btn--danger" data-action="delete" data-id="${item.id}">Delete</button>
          </div>
        </div>
      </article>
    `;
  }

  getUploadCard() {
    const labels = {
      blogs: 'Upload a new blog',
      clubs: 'Create a new club',
      events: 'Create a new event'
    };
    
    return `
      <div class="upload-card">
        <a href="/dashboard/community/${this.module}/create" class="upload-card__link">
          <svg class="upload-card__icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
            <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <span class="upload-card__text">${labels[this.module] || 'Create new'}</span>
        </a>
      </div>
    `;
  }

  setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const tabName = button.getAttribute('data-tab');
        this.switchTab(tabName);
      });
    });
  }

  switchTab(tabName) {
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
    });

    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.add('tab-button--active');

    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('is-hidden');
    });

    const activeContent = document.querySelector(`[data-tab-content="${tabName}"]`);
    activeContent.classList.remove('is-hidden');
  }

  setupSearch() {
    const searchInput = document.getElementById(`${this.module}-search`) || document.querySelector('.search-input');
    if (!searchInput) return;

    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        this.searchContent(e.target.value);
      }, 300);
    });
  }

  async searchContent(query) {
    const activeCategory = document.querySelector('.pill--active')?.getAttribute('data-category') || 'all';
    
    try {
      const response = await fetch(`${this.apiBase}/search?q=${encodeURIComponent(query)}&category=${activeCategory}`);
      const data = await response.json();

      if (data.success) {
        this.renderContent(data.items || data.blogs || data.clubs || data.events, `${this.module}-grid`);
      }
    } catch (error) {
      console.error('Search failed:', error);
    }
  }

  setupCategoryFilters() {
    const pills = document.querySelectorAll('.pill');

    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('pill--active'));
        pill.classList.add('pill--active');

        const category = pill.getAttribute('data-category');
        this.filterByCategory(category);
      });
    });
  }

  filterByCategory(category) {
    const contentCards = document.querySelectorAll('.content-card');

    contentCards.forEach(card => {
      if (category === 'all' || card.getAttribute('data-category') === category) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }

  setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');

    deleteButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const itemId = button.getAttribute('data-id');
        this.confirmDelete(itemId);
      });
    });
  }

  confirmDelete(itemId) {
    if (confirm('Are you sure you want to delete this? This action cannot be undone.')) {
      this.deleteContent(itemId);
    }
  }

  async deleteContent(itemId) {
    try {
      const response = await fetch(`${this.apiBase}/delete/${itemId}`, {
        method: 'POST'
      });

      const data = await response.json();

      if (data.success) {
        await this.loadMyContent();
        this.showNotification('Deleted successfully', 'success');
      } else {
        this.showNotification('Failed to delete', 'error');
      }
    } catch (error) {
      console.error('Delete failed:', error);
      this.showNotification('An error occurred', 'error');
    }
  }

  showNotification(message, type) {
    alert(message); // You can enhance this with a proper notification system
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new CommunityManager();
});
