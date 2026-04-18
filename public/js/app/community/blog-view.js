class BlogViewManager {
  constructor() {
    this.blogId = this.getBlogIdFromUrl();
    this.currentReport = { type: null, id: null };
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
    // Report modal (submission handled by generic handler)
    const reportModal = document.getElementById('report-modal');
    const reportForm = document.getElementById('report-form');

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
    // Attach generic handlers to any report-icon button with data-report-type and data-id
    const reportButtons = document.querySelectorAll('.report-icon[data-report-type][data-id]');
    console.log('Found report buttons:', reportButtons.length, reportButtons);

    reportButtons.forEach(btn => {
      console.log('Attaching listener to button:', btn);
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('Report button clicked via event listener!');
        const type = btn.getAttribute('data-report-type');
        const id = btn.getAttribute('data-id');
        console.log('Report type:', type, 'ID:', id);
        this.currentReport.type = type;
        this.currentReport.id = id;
        const reportModal = document.getElementById('report-modal');
        console.log('Opening modal:', reportModal);
        if (reportModal) reportModal.style.display = 'flex';
      });
    });
  }

  async handleReport() {
  \n    const description = document.getElementById('report-description').value; \n    const { type, id } = this.currentReport; \n\n    if (!type || !id) { \n      alert('Unable to determine report target.'); \n      return; \n } \n\n    // Map to API path: /api/community/{type}s/report\n    const plural = type.endsWith('s') ? type : type + 's';\n    const endpoint = `/api/community/${plural}/report`;\n\n    try {\n      const payload = { description, id };\n      console.log('Submitting report to:', endpoint);\n      console.log('Payload:', payload);\n\n      const response = await fetch(endpoint, {\n        method: 'POST',\n        headers: {\n          'Content-Type': 'application/json'\n        },\n        body: JSON.stringify(payload)\n      });\n\n      console.log('Response status:', response.status);\n      const responseText = await response.text();\n      console.log('Response text:', responseText);\n      \n      let data;\n      try {\n        data = JSON.parse(responseText);\n      } catch (parseError) {\n        console.error('Failed to parse JSON:', parseError);\n        console.error('Response was:', responseText.substring(0, 200));\n        alert('Server error: Invalid response format');\n        return;\n      }\n      \n      console.log('Response data:', data);\n\n      if (data.success) {\n        document.getElementById('report-form').reset();\n        document.getElementById('report-modal').style.display = 'none';\n        alert('Report submitted successfully');\n      } else {\n        alert('Failed to submit report: ' + (data.message || 'Unknown error'));\n      }\n    } catch (error) {\n      console.error('Report failed:', error);\n      alert('An error occurred: ' + error.message);\n    }\n  }

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
