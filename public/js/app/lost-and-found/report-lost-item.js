/**
 * Report Lost Item Form JavaScript
 * Minimal implementation with essential functionality
 */

class ReportLostItemForm {
  constructor() {
    this.maxImages = 3;
    this.init();
  }

  init() {
    this.setupImageUploads();
    this.setupCharacterCounters();
    this.setupRewardToggle();
    this.setupFormValidation();
    this.setupDateValidation();
  }

  // Setup image upload functionality
  setupImageUploads() {
    for (let i = 0; i < this.maxImages; i++) {
      const slot = document.querySelector(`[data-slot="${i}"]`);
      const input = document.getElementById(`image-${i}`);
      const preview = document.getElementById(`preview-${i}`);
      const removeBtn = slot.querySelector('.remove-image');

      if (!slot || !input || !preview) continue;

      // Click to upload
      slot.addEventListener('click', (e) => {
        if (!e.target.closest('.remove-image')) {
          input.click();
        }
      });

      // Handle file selection
      input.addEventListener('change', (e) => {
        this.handleImageUpload(e, i, preview, removeBtn);
      });

      // Remove image
      if (removeBtn) {
        removeBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.removeImage(i, input, preview, removeBtn);
        });
      }
    }
  }

  // Handle image upload
  handleImageUpload(event, slotIndex, preview, removeBtn) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file
    if (!this.validateImage(file)) {
      event.target.value = '';
      return;
    }

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
      if (removeBtn) removeBtn.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }

  // Validate image file
  validateImage(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

    if (file.size > maxSize) {
      alert('File size must be less than 5MB');
      return false;
    }

    if (!allowedTypes.includes(file.type)) {
      alert('Only JPG, PNG, and WebP files are allowed');
      return false;
    }

    return true;
  }

  // Remove image
  removeImage(slotIndex, input, preview, removeBtn) {
    input.value = '';
    preview.innerHTML = this.getPlaceholderHTML(slotIndex);
    if (removeBtn) removeBtn.style.display = 'none';
  }

  // Get placeholder HTML
  getPlaceholderHTML(slotIndex) {
    const isMain = slotIndex === 0;
    const text = isMain ? 'Main Photo' : `Photo ${slotIndex + 1}`;
    const hint = isMain ? 'Click to upload (Optional)' : 'Optional';

    return `
      <div class="upload-placeholder">
        <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
          <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
          <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span class="upload-text">${text}</span>
        <span class="upload-hint">${hint}</span>
      </div>
    `;
  }

  // Setup character counters
  setupCharacterCounters() {
    const description = document.getElementById('description');
    const counter = document.getElementById('desc-count');

    if (description && counter) {
      description.addEventListener('input', () => {
        const length = description.value.length;
        counter.textContent = length;
        
        if (length > 450) {
          counter.style.color = '#F59E0B';
        } else if (length > 500) {
          counter.style.color = '#EF4444';
        } else {
          counter.style.color = '#6B7280';
        }
      });
    }
  }

  // Setup reward toggle
  setupRewardToggle() {
    const rewardCheckbox = document.getElementById('reward-offered');
    const rewardDetails = document.getElementById('reward-details');

    if (rewardCheckbox && rewardDetails) {
      rewardCheckbox.addEventListener('change', () => {
        if (rewardCheckbox.checked) {
          rewardDetails.style.display = 'block';
          rewardDetails.classList.add('show');
        } else {
          rewardDetails.style.display = 'none';
          // Clear reward fields
          const rewardAmount = document.getElementById('reward-amount');
          const rewardDetailsText = document.getElementById('reward-details-text');
          if (rewardAmount) rewardAmount.value = '';
          if (rewardDetailsText) rewardDetailsText.value = '';
        }
      });
    }
  }

  // Setup date validation
  setupDateValidation() {
    const dateLost = document.getElementById('date-lost');
    if (dateLost) {
      // Set max date to today
      const today = new Date().toISOString().split('T')[0];
      dateLost.setAttribute('max', today);
      
      dateLost.addEventListener('change', () => {
        const selectedDate = new Date(dateLost.value);
        const today = new Date();
        
        if (selectedDate > today) {
          alert('Date cannot be in the future');
          dateLost.value = '';
        }
      });
    }
  }

  // Setup form validation
  setupFormValidation() {
    const form = document.getElementById('report-lost-form');
    
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (this.validateForm()) {
          this.submitForm();
        }
      });
    }
  }

  // Validate form
  validateForm() {
    let isValid = true;
    this.clearErrors();

    // Required field validation
    const requiredFields = [
      { id: 'item-name', name: 'Item name' },
      { id: 'category', name: 'Category' },
      { id: 'description', name: 'Description' },
      { id: 'location', name: 'Last known location' },
      { id: 'date-lost', name: 'Date lost' },
      { id: 'mobile', name: 'Mobile number' },
      { id: 'email', name: 'Email address' }
    ];

    requiredFields.forEach(field => {
      const element = document.getElementById(field.id);
      if (element && !element.value.trim()) {
        this.showError(`${field.id}-error`, `${field.name} is required`);
        isValid = false;
      }
    });

    // Priority validation
    const priority = document.querySelector('input[name="priority"]:checked');
    if (!priority) {
      this.showError('priority-error', 'Please select a priority level');
      isValid = false;
    }

    // Email validation
    const email = document.getElementById('email');
    if (email && email.value && !this.isValidEmail(email.value)) {
      this.showError('email-error', 'Please enter a valid email address');
      isValid = false;
    }

    // Mobile validation
    const mobile = document.getElementById('mobile');
    if (mobile && mobile.value && !this.isValidMobile(mobile.value)) {
      this.showError('mobile-error', 'Please enter a valid mobile number');
      isValid = false;
    }

    return isValid;
  }

  // Email validation
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Mobile validation
  isValidMobile(mobile) {
    const mobileRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
    return mobileRegex.test(mobile.replace(/\s/g, ''));
  }

  // Show error message
  showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add('show');
      
      // Scroll to first error
      if (document.querySelectorAll('.form-error.show').length === 1) {
        errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  }

  // Clear all errors
  clearErrors() {
    document.querySelectorAll('.form-error').forEach(error => {
      error.classList.remove('show');
    });
  }

  // Submit form
  submitForm() {
    const form = document.getElementById('report-lost-form');
    const loadingOverlay = document.getElementById('loading-overlay');
    const submitBtn = document.getElementById('submit-btn');

    if (loadingOverlay) loadingOverlay.style.display = 'flex';
    if (submitBtn) submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Failed to submit report');
        }
        alert('Lost item report submitted successfully!');
        window.location.href = '/dashboard/lost-found';
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
      })
      .finally(() => {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;
      });
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new ReportLostItemForm();
});