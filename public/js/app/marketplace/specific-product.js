class ProductPage {
  constructor() {
    this.init();
  }

  init() {
    this.setupImageGallery();
    this.setupQuantityControls();
    this.setupReportModal();
  }

  setupImageGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image__img');

    thumbnails.forEach((thumbnail, index) => {
      thumbnail.addEventListener('click', () => {
        // Remove active class from all thumbnails
        thumbnails.forEach(t => t.classList.remove('thumbnail--active'));
        // Add active class to clicked thumbnail
        thumbnail.classList.add('thumbnail--active');
        
        // Update main image (in a real app, you'd have an array of image URLs)
        const imageUrls = [
          'https://via.placeholder.com/500x500/1e3a8a/ffffff?text=UCSC+T-Shirt+Front',
          'https://via.placeholder.com/500x500/1e3a8a/ffffff?text=UCSC+T-Shirt+Back',
          'https://via.placeholder.com/500x500/1e3a8a/ffffff?text=UCSC+T-Shirt+Side'
        ];
        
        if (mainImage && imageUrls[index]) {
          mainImage.src = imageUrls[index];
        }
      });
    });
  }

  setupQuantityControls() {
    const quantityInput = document.querySelector('.quantity-input');
    const minusBtn = document.querySelector('.quantity-btn--minus');
    const plusBtn = document.querySelector('.quantity-btn--plus');

    if (!quantityInput || !minusBtn || !plusBtn) return;

    minusBtn.addEventListener('click', () => {
      const currentValue = parseInt(quantityInput.value);
      const min = parseInt(quantityInput.min) || 1;
      if (currentValue > min) {
        quantityInput.value = currentValue - 1;
      }
    });

    plusBtn.addEventListener('click', () => {
      const currentValue = parseInt(quantityInput.value);
      const max = parseInt(quantityInput.max) || 99;
      if (currentValue < max) {
        quantityInput.value = currentValue + 1;
      }
    });

    // Validate input
    quantityInput.addEventListener('input', () => {
      const value = parseInt(quantityInput.value);
      const min = parseInt(quantityInput.min) || 1;
      const max = parseInt(quantityInput.max) || 99;

      if (value < min) quantityInput.value = min;
      if (value > max) quantityInput.value = max;
    });
  }

  setupReportModal() {
    const reportBtn = document.querySelector('.btn--report');
    const modal = document.getElementById('report-modal');
    const closeBtn = document.querySelector('.modal-close');
    const cancelBtn = document.getElementById('cancel-report');
    const reportForm = document.getElementById('report-form');

    if (!reportBtn || !modal) return;

    // Open modal
    reportBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
      // Focus first form element
      const firstInput = modal.querySelector('select, input, textarea');
      if (firstInput) {
        setTimeout(() => firstInput.focus(), 100);
      }
    });

    // Close modal function
    const closeModal = () => {
      modal.style.display = 'none';
      document.body.style.overflow = '';
      reportForm.reset();
    };

    // Close modal events
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Close on overlay click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeModal();
      }
    });

    // Handle form submission
    if (reportForm) {
      reportForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleReportSubmission(reportForm);
      });
    }
  }

  async handleReportSubmission(form) {
    const formData = new FormData(form);
    const productId = document.querySelector('.btn--report').dataset.productId;
    
    const reportData = {
      productId: productId,
      reason: formData.get('reason'),
      details: formData.get('details')
    };

    try {
      // In a real application, you would send this to your backend
      console.log('Report submitted:', reportData);
      
      // Show success message
      this.showNotification('Report submitted successfully. Thank you for helping keep our marketplace safe.', 'success');
      
      // Close modal
      document.getElementById('report-modal').style.display = 'none';
      document.body.style.overflow = '';
      form.reset();
      
    } catch (error) {
      console.error('Error submitting report:', error);
      this.showNotification('Failed to submit report. Please try again.', 'error');
    }
  }

  showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? 'var(--vote-active-bg)' : '#FEF2F2'};
      color: ${type === 'success' ? 'var(--vote-active)' : '#DC2626'};
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      z-index: 1001;
      max-width: 400px;
      font-size: 0.875rem;
      font-weight: 500;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove notification after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new ProductPage();
});