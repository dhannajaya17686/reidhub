class BlogsManager {
  constructor() {
    this.init();
  }

  async init() {
    this.setupTabs();
    this.setupSearch();
    this.setupCategoryFilters();
    this.setupDeleteButtons();
    
    // Load initial data
    await this.loadAllBlogs();
    await this.loadMyBlogs();
  }

  async loadAllBlogs() {
    try {
      const response = await fetch('/api/community/blogs');
      const data = await response.json();
      
      if (data.success) {
        this.renderBlogs(data.blogs, 'blogs-grid');
      }
    } catch (error) {
      console.error('Failed to load blogs:', error);
      document.getElementById('blogs-grid').innerHTML = '<div class="error-state">Failed to load blogs</div>';
    }
  }

  async loadMyBlogs() {
    try {
      const response = await fetch('/api/community/blogs/my-blogs');
      const data = await response.json();
      
      if (data.success) {
        this.renderMyBlogs(data.blogs, 'my-blogs-grid');
      }
    } catch (error) {
      console.error('Failed to load my blogs:', error);
      document.getElementById('my-blogs-grid').innerHTML = '<div class="error-state">Failed to load your blogs</div>';
    }
  }

  renderBlogs(blogs, containerId) {
    const container = document.getElementById(containerId);
    
    if (blogs.length === 0) {
      container.innerHTML = '<div class="empty-state"><p>No blogs found.</p></div>';
      return;
    }

    container.innerHTML = blogs.map(blog => this.createBlogCard(blog)).join('');
  }

  renderMyBlogs(blogs, containerId) {
    const container = document.getElementById(containerId);
    
    if (blogs.length === 0) {
      container.innerHTML = `
        <div class="upload-card">
          <a href="/community/blogs/create" class="upload-card__link">
            <svg class="upload-card__icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
              <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
              <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span class="upload-card__text">Upload a new blog</span>
          </a>
        </div>
      `;
      return;
    }

    const blogsHtml = blogs.map(blog => this.createMyBlogCard(blog)).join('');
    const uploadCard = `
      <div class="upload-card">
        <a href="/community/blogs/create" class="upload-card__link">
          <svg class="upload-card__icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4"/>
            <line x1="24" y1="14" x2="24" y2="34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="14" y1="24" x2="34" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <span class="upload-card__text">Upload a new blog</span>
        </a>
      </div>
    `;
    
    container.innerHTML = blogsHtml + uploadCard;
    this.setupDeleteButtons();
  }

  createBlogCard(blog) {
    const date = new Date(blog.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    
    return `
      <article class="blog-card" data-category="${blog.category}">
        <a href="/community/blogs/view/${blog.id}" class="blog-card__link">
          <div class="blog-card__image">
            <img src="${blog.image_path || '/images/placeholder-blog.jpg'}" alt="${blog.title}">
          </div>
          <div class="blog-card__content">
            <h3 class="blog-card__title">${blog.title}</h3>
            <div class="blog-card__meta">
              <span class="blog-card__author">By ${blog.first_name} ${blog.last_name}</span>
              <span class="blog-card__separator">•</span>
              <span class="blog-card__date">${formattedDate}</span>
            </div>
          </div>
        </a>
      </article>
    `;
  }

  createMyBlogCard(blog) {
    const date = new Date(blog.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    
    return `
      <article class="blog-card blog-card--owned">
        <div class="blog-card__image">
          <img src="${blog.image_path || '/images/placeholder-blog.jpg'}" alt="${blog.title}">
        </div>
        <div class="blog-card__content">
          <h3 class="blog-card__title">${blog.title}</h3>
          <div class="blog-card__meta">
            <span class="blog-card__date">${formattedDate}</span>
          </div>
          <div class="blog-card__actions">
            <a href="/community/blogs/view/${blog.id}" class="btn btn--small btn--outline">View</a>
            <a href="/community/blogs/edit/${blog.id}" class="btn btn--small btn--primary">Edit</a>
            <button class="btn btn--small btn--danger" data-action="delete" data-blog-id="${blog.id}">Delete</button>
          </div>
        </div>
      </article>
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
    // Update buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
      btn.setAttribute('aria-selected', 'false');
    });

    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.add('tab-button--active');
    activeButton.setAttribute('aria-selected', 'true');

    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('is-hidden');
    });

    const activeContent = document.querySelector(`[data-tab-content="${tabName}"]`);
    activeContent.classList.remove('is-hidden');
  }

  setupSearch() {
    const searchInput = document.getElementById('blog-search');
    let debounceTimer;

    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        this.searchBlogs(e.target.value);
      }, 300);
    });
  }

  async searchBlogs(query) {
    const activeCategory = document.querySelector('.pill--active').getAttribute('data-category');
    
    try {
      const response = await fetch(`/api/community/blogs/search?q=${encodeURIComponent(query)}&category=${activeCategory}`);
      const data = await response.json();

      if (data.success) {
        this.renderBlogs(data.blogs, 'blogs-grid');
      }
    } catch (error) {
      console.error('Search failed:', error);
    }
  }

  setupCategoryFilters() {
    const pills = document.querySelectorAll('.pill');

    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        // Update active pill
        pills.forEach(p => p.classList.remove('pill--active'));
        pill.classList.add('pill--active');

        const category = pill.getAttribute('data-category');
        this.filterByCategory(category);
      });
    });
  }

  filterByCategory(category) {
    const blogCards = document.querySelectorAll('.blog-card');

    blogCards.forEach(card => {
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
        const blogId = button.getAttribute('data-blog-id');
        this.confirmDelete(blogId);
      });
    });
  }

  confirmDelete(blogId) {
    if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
      this.deleteBlog(blogId);
    }
  }

  async deleteBlog(blogId) {
    try {
      const response = await fetch(`/api/community/blogs/delete/${blogId}`, {
        method: 'POST'
      });

      const data = await response.json();

      if (data.success) {
        // Reload my blogs
        await this.loadMyBlogs();
        this.showNotification('Blog deleted successfully', 'success');
      } else {
        this.showNotification('Failed to delete blog', 'error');
      }
    } catch (error) {
      console.error('Delete failed:', error);
      this.showNotification('An error occurred', 'error');
    }
  }

  showNotification(message, type) {
    // Simple notification - you can enhance this
    alert(message);
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new BlogsManager();
});
