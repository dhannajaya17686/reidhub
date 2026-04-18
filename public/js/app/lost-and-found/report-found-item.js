/**
 * Report Found Item Form JavaScript
 * Minimal implementation with essential functionality
 */

class ReportFoundItemForm {
  constructor() {
    this.maxImages = 3;
    this.init();
  }

  init() {
    this.setupImageUploads();
    this.setupCharacterCounters();
    this.setupOtherLocationToggle();
    this.setupFormValidation();
    this.setupDateValidation();
  }

  // Setup image upload functionality (same as lost item)
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
      
      // Update required image styling for main image
      if (slotIndex === 0) {
        const slot = document.querySelector(`[data-slot="0"]`);
        if (slot) slot.classList.remove('required-image');
      }
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
    
    // Re-add required styling for main image
    if (slotIndex === 0) {
      const slot = document.querySelector(`[data-slot="0"]`);
      if (slot) slot.classList.add('required-image');
    }
  }

  // Get placeholder HTML
  getPlaceholderHTML(slotIndex) {
    const isMain = slotIndex === 0;
    const text = isMain ? 'Main Photo *' : `Photo ${slotIndex + 1}`;
    const hint = isMain ? 'Click to upload (Required)' : 'Optional';

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

  // Setup other location toggle
  setupOtherLocationToggle() {
    const currentLocationSelect = document.getElementById('current-location');
    const otherLocationDetails = document.getElementById('other-location-details');

    if (currentLocationSelect && otherLocationDetails) {
      currentLocationSelect.addEventListener('change', () => {
        if (currentLocationSelect.value === 'other') {
          otherLocationDetails.style.display = 'block';
          otherLocationDetails.classList.add('show');
          document.getElementById('other-location').required = true;
        } else {
          otherLocationDetails.style.display = 'none';
          document.getElementById('other-location').required = false;
          document.getElementById('other-location').value = '';
        }
      });
    }
  }

  // Setup date validation
  setupDateValidation() {
    const dateFound = document.getElementById('date-found');
    if (dateFound) {
      // Set max date to today
      const today = new Date().toISOString().split('T')[0];
      dateFound.setAttribute('max', today);
      
      dateFound.addEventListener('change', () => {
        const selectedDate = new Date(dateFound.value);
        const today = new Date();
        
        if (selectedDate > today) {
          alert('Date cannot be in the future');
          dateFound.value = '';
        }
      });
    }
  }

  // Setup form validation
  setupFormValidation() {
    const form = document.getElementById('report-found-form');
    
    if (form) {
      form.addEventListener('submit', (e) => {
        console.log('Form submission initiated');
        
        if (!this.validateForm()) {
          console.log('Form validation failed');
          e.preventDefault();
          return;
        }
        
        console.log('Form validation passed, submitting...');
        
        // Show loading overlay before natural form submission
        const loadingOverlay = document.getElementById('loading-overlay');
        const submitBtn = document.getElementById('submit-btn');
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
        if (submitBtn) submitBtn.disabled = true;
        
        // Let the form submit naturally (POST with redirect)
      });
    }
  }

  // Validate form
  validateForm() {
    let isValid = true;
    this.clearErrors();
    
    console.log('=== Form Validation Starting ===');

    // Required field validation
    const requiredFields = [
      { id: 'item-name', name: 'Item name' },
      { id: 'category', name: 'Category' },
      { id: 'description', name: 'Description' },
      { id: 'location', name: 'Found location' },
      { id: 'date-found', name: 'Date found' },
      { id: 'mobile', name: 'Mobile number' },
      { id: 'email', name: 'Email address' },
      { id: 'current-location', name: 'Current location' }
    ];

    requiredFields.forEach(field => {
      const element = document.getElementById(field.id);
      if (element && !element.value.trim()) {
        console.log(`❌ Validation failed: ${field.name} is empty`);
        this.showError(`${field.id}-error`, `${field.name} is required`);
        isValid = false;
      } else {
        console.log(`✓ ${field.name} is valid`);
      }
    });

    // Condition validation
    const condition = document.querySelector('input[name="condition"]:checked');
    if (!condition) {
      console.log('❌ Validation failed: Item condition not selected');
      this.showError('condition-error', 'Please select item condition');
      isValid = false;
    } else {
      console.log('✓ Item condition selected:', condition.value);
    }

    // Image validation - at least one image required
    const mainImage = document.getElementById('image-0');
    if (!mainImage || !mainImage.files || !mainImage.files[0]) {
      console.log('❌ Validation failed: No main image uploaded');
      this.showError('images-error', 'At least one photo is required');
      isValid = false;
    } else {
      console.log('✓ Main image uploaded:', mainImage.files[0].name);
    }

    // Other location validation
    const currentLocation = document.getElementById('current-location');
    const otherLocation = document.getElementById('other-location');
    if (currentLocation && currentLocation.value === 'other') {
      if (!otherLocation || !otherLocation.value.trim()) {
        console.log('❌ Validation failed: Other location not specified');
        this.showError('current-location-error', 'Please specify the current location');
        isValid = false;
      } else {
        console.log('✓ Other location specified:', otherLocation.value);
      }
    }

    // Email validation
    const email = document.getElementById('email');
    if (email && email.value && !this.isValidEmail(email.value)) {
      console.log('❌ Validation failed: Invalid email format');
      this.showError('email-error', 'Please enter a valid email address');
      isValid = false;
    } else if (email && email.value) {
      console.log('✓ Email format valid');
    }

    // Mobile validation
    const mobile = document.getElementById('mobile');
    if (mobile && mobile.value && !this.isValidMobile(mobile.value)) {
      console.log('❌ Validation failed: Invalid mobile number');
      this.showError('mobile-error', 'Please enter a valid mobile number');
      isValid = false;
    } else if (mobile && mobile.value) {
      console.log('✓ Mobile number valid');
    }

    console.log('=== Validation Result:', isValid ? '✓ PASSED' : '❌ FAILED', '===');
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
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new ReportFoundItemForm();
});