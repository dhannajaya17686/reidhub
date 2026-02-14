class BlogsManager {
  constructor() {
    this.init();
  }

  async init() {
    console.log('BlogsManager: Initializing...');
    this.setupTabs();
    this.setupSearch();
    this.setupCategoryFilters();
    this.setupDeleteButtons();

    // Load initial data
    console.log('BlogsManager: Loading blogs...');
    await this.loadAllBlogs();
    await this.loadMyBlogs();
    console.log('BlogsManager: Initialization complete');
  }

  async loadAllBlogs() {
    try {
      console.log('Fetching from: /dashboard/community/blogs/api/all');
      const response = await fetch('/dashboard/community/blogs/api/all');
      console.log('Response status:', response.status);
      console.log('Response ok:', response.ok);

      const text = await response.text();
      console.log('Raw response:', text);

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error('Failed to parse JSON:', e);
        throw new Error('Invalid JSON response: ' + text.substring(0, 100));
      }

      console.log('Parsed data:', data);

      if (data.success) {
        console.log('Rendering', data.blogs.length, 'blogs');
        this.renderBlogs(data.blogs, 'blogs-grid');
      } else {
        console.error('API returned error:', data);
        const errorMsg = data.sql_error
          ? 'Database table not found. Please run the SQL file to create the blogs table.'
          : (data.message || 'Failed to load blogs');
        document.getElementById('blogs-grid').innerHTML = `
          <div class="empty-state">
            <div class="empty-icon">⚠️</div>
            <h3>Error Loading Blogs</h3>
            <p>${errorMsg}</p>
            ${data.error ? '<p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 10px;">' + data.error + '</p>' : ''}
          </div>
        `;
      }
    } catch (error) {
      console.error('Failed to load blogs:', error);
      document.getElementById('blogs-grid').innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">⚠️</div>
          <h3>Failed to Load Blogs</h3>
          <p>Error: ${error.message}</p>
          <p style="font-size: 0.875rem; margin-top: 10px;">Check the browser console for details</p>
        </div>
      `;
    }
  }

  async loadMyBlogs() {
    try {
      const response = await fetch('/dashboard/community/blogs/api/my-blogs');

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      console.log('My blogs response:', data);

      if (data.success) {
        this.renderMyBlogs(data.blogs, 'my-blogs-grid');
      } else {
        console.error('API returned error:', data.message);
        document.getElementById('my-blogs-grid').innerHTML = '<div class="empty-state"><p>' + (data.message || 'Failed to load your blogs') + '</p></div>';
      }
    } catch (error) {
      console.error('Failed to load my blogs:', error);
      document.getElementById('my-blogs-grid').innerHTML = '<div class="empty-state"><p>Failed to load your blogs. Please try again.</p></div>';
    }
  }

  renderBlogs(blogs, containerId) {
    const container = document.getElementById(containerId);

    if (!Array.isArray(blogs)) {
      console.error('Blogs is not an array:', blogs);
      container.innerHTML = '<div class="empty-state"><div class="empty-icon">⚠️</div><h3>Error</h3><p>Invalid data received</p></div>';
      return;
    }

    if (blogs.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">📚</div>
          <h3>No Blogs Yet</h3>
          <p>Be the first to share your story!</p>
          <a href="/dashboard/community/blogs/create" class="btn btn--primary">Create First Blog</a>
        </div>
      `;
      return;
    }

    container.innerHTML = blogs.map(blog => this.createBlogCard(blog)).join('');
  }

  renderMyBlogs(blogs, containerId) {
    const container = document.getElementById(containerId);

    if (!Array.isArray(blogs)) {
      console.error('Blogs is not an array:', blogs);
      container.innerHTML = '<div class="empty-state"><div class="empty-icon">⚠️</div><h3>Error</h3><p>Invalid data received</p></div>';
      return;
    }

    if (blogs.length === 0) {
      container.innerHTML = `
        <div class="upload-card">
          <a href="/dashboard/community/blogs/create" class="upload-card__link">
            <span class="material-symbols-outlined upload-card__icon" aria-hidden="true">add_circle</span>
            <span class="upload-card__text">Upload a new blog</span>
          </a>
        </div>
      `;
      return;
    }

    const blogsHtml = blogs.map(blog => this.createMyBlogCard(blog)).join('');
    const uploadCard = `
      <div class="upload-card">
        <a href="/dashboard/community/blogs/create" class="upload-card__link">
          <span class="material-symbols-outlined upload-card__icon" aria-hidden="true">add_circle</span>
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

    // Use image path if available, otherwise use a colored placeholder
    const imageSrc = blog.image_path ? blog.image_path : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2224%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E';

    // Debug logging
    console.log(`Blog "${blog.title}" - Image path from API: ${blog.image_path || 'null/undefined'}`);
    if (blog.image_path) {
      console.log(`  Full image URL: ${window.location.origin}${blog.image_path}`);
    }

    return `
      <article class="blog-card" data-category="${blog.category}">
        <a href="/dashboard/community/blogs/view?id=${blog.id}" class="blog-card__link">
          <div class="blog-card__image">
            <img src="${imageSrc}" alt="${blog.title}" 
                 onerror="console.error('Image failed to load: ${imageSrc}'); this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2224%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E'">
          </div>
          <div class="blog-card__content">
            <h3 class="blog-card__title">${this.escapeHtml(blog.title)}</h3>
            <div class="blog-card__meta">
              <span class="blog-card__author">By ${this.escapeHtml(blog.first_name)} ${this.escapeHtml(blog.last_name)}</span>
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

    // Use image path if available, otherwise use a colored placeholder
    const imageSrc = blog.image_path ? blog.image_path : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2224%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E';

    // Debug logging
    console.log(`[My Blogs] Blog "${blog.title}" (ID:${blog.id}) - Image path: ${blog.image_path || 'null/undefined'}`);
    if (blog.image_path) {
      console.log(`  Full image URL: ${window.location.origin}${blog.image_path}`);
    }

    return `
      <article class="blog-card blog-card--owned">
        <div class="blog-card__image">
          <img src="${imageSrc}" alt="${blog.title}" 
               onerror="console.error('Image failed to load: ${imageSrc}'); this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2224%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E'">
        </div>
        <div class="blog-card__content">
          <h3 class="blog-card__title">${this.escapeHtml(blog.title)}</h3>
          <div class="blog-card__meta">
            <span class="blog-card__date">${formattedDate}</span>
          </div>
          <div class="blog-card__actions">
            <a href="/dashboard/community/blogs/view?id=${blog.id}" class="btn btn--small btn--outline">View</a>
            <a href="/dashboard/community/blogs/edit?id=${blog.id}" class="btn btn--small btn--primary">Edit</a>
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
      const response = await fetch(`/dashboard/community/blogs/api/search?q=${encodeURIComponent(query)}&category=${activeCategory}`);
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

  escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
      const response = await fetch(`/dashboard/community/blogs/api/delete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: blogId })
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
