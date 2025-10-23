class BlogViewManager {
  constructor() {
    this.blogId = this.getBlogIdFromUrl();
    this.init();
  }

  getBlogIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
  }

  init() {
    this.setupInteractions();
    this.setupModals();
    this.setupDeleteButton();
    this.setupReportButton();
  }

  setupInteractions() {
    // Like/Dislike buttons
    document.querySelectorAll('.interaction-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const action = btn.getAttribute('data-action');
        const blogId = btn.getAttribute('data-blog-id');
        await this.handleInteraction(action, blogId, btn);
      });
    });

    // Comment actions
    document.querySelectorAll('.comment-action-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const action = btn.getAttribute('data-action');
        const commentId = btn.getAttribute('data-comment-id');
        await this.handleCommentInteraction(action, commentId, btn);
      });
    });
  }

  async handleInteraction(action, blogId, button) {
    try {
      const response = await fetch(`/community/blogs/${action}/${blogId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        }
      });

      const data = await response.json();

      if (data.success) {
        const countSpan = button.querySelector('.interaction-count');
        countSpan.textContent = data[action + 's'] || data.likes || data.dislikes;
        button.classList.toggle('active');
      }
    } catch (error) {
      console.error('Interaction failed:', error);
    }
  }

  async handleCommentInteraction(action, commentId, button) {
    // Similar to handleInteraction but for comments
    console.log(`Comment ${action} for ${commentId}`);
  }

  setupModals() {
    // Report modal
    const reportBtn = document.getElementById('report-blog-btn');
    const reportModal = document.getElementById('report-modal');
    const reportForm = document.getElementById('report-form');

    reportBtn?.addEventListener('click', () => {
      reportModal.style.display = 'flex';
    });

    reportForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      await this.handleReport();
    });

    // Delete modal
    const deleteBtn = document.getElementById('delete-blog-btn');
    const deleteModal = document.getElementById('delete-modal');
    const confirmDelete = document.getElementById('confirm-delete');
    const cancelDelete = document.getElementById('cancel-delete');

    deleteBtn?.addEventListener('click', () => {
      deleteModal.style.display = 'flex';
    });

    confirmDelete?.addEventListener('click', async () => {
      await this.handleDelete();
    });

    cancelDelete?.addEventListener('click', () => {
      deleteModal.style.display = 'none';
    });

    // Close modals
    document.querySelectorAll('.modal-close').forEach(btn => {
      btn.addEventListener('click', () => {
        btn.closest('.modal-overlay').style.display = 'none';
      });
    });

    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          overlay.style.display = 'none';
        }
      });
    });
  }

  setupDeleteButton() {
    const deleteBtn = document.getElementById('delete-blog-btn');
    deleteBtn?.addEventListener('click', () => {
      document.getElementById('delete-modal').style.display = 'flex';
    });
  }

  setupReportButton() {
    const reportBtn = document.getElementById('report-blog-btn');
    reportBtn?.addEventListener('click', () => {
      document.getElementById('report-modal').style.display = 'flex';
    });
  }

  async handleReport() {
    const description = document.getElementById('report-description').value;
    const blogId = this.blogId;

    try {
      const response = await fetch(`/api/community/blogs/${blogId}/report`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ description })
      });

      const data = await response.json();

      if (data.success) {
        document.getElementById('report-modal').style.display = 'none';
        alert('Report submitted successfully');
      } else {
        alert('Failed to submit report');
      }
    } catch (error) {
      console.error('Report failed:', error);
      alert('An error occurred');
    }
  }

  async handleDelete() {
    const blogId = this.blogId;

    try {
      const response = await fetch(`/api/community/blogs/${blogId}`, {
        method: 'DELETE'
      });

      const data = await response.json();

      if (data.success) {
        window.location.href = '/dashboard/community/blogs';
      } else {
        alert('Failed to delete blog');
      }
    } catch (error) {
      console.error('Delete failed:', error);
      alert('An error occurred');
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new BlogViewManager();
});
