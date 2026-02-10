class BlogFormManager {
  constructor() {
    this.form = document.querySelector('.blog-form');
    this.fileInput = document.getElementById('blog-image');
    this.uploadArea = document.getElementById('file-upload-area');
    this.previewArea = document.getElementById('file-preview');
    this.previewImage = document.getElementById('preview-image');
    this.uploadTrigger = document.getElementById('upload-trigger');
    this.removeButton = document.getElementById('preview-remove');

    this.init();
  }

  init() {
    this.setupFileUpload();
    this.setupFormValidation();
  }

  setupFileUpload() {
    if (!this.uploadTrigger || !this.fileInput) return;

    // Trigger file input on button click
    this.uploadTrigger.addEventListener('click', (e) => {
      e.preventDefault();
      this.fileInput.click();
    });

    // Handle file selection
    this.fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        this.handleFileSelect(file);
      }
    });

    // Handle drag and drop
    if (this.uploadArea) {
      this.uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        this.uploadArea.style.borderColor = 'var(--secondary-color)';
      });

      this.uploadArea.addEventListener('dragleave', () => {
        this.uploadArea.style.borderColor = '';
      });

      this.uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        this.uploadArea.style.borderColor = '';
        const file = e.dataTransfer.files[0];
        if (file) {
          this.fileInput.files = e.dataTransfer.files;
          this.handleFileSelect(file);
        }
      });
    }

    // Handle remove button
    if (this.removeButton) {
      this.removeButton.addEventListener('click', (e) => {
        e.preventDefault();
        this.clearFileSelection();
      });
    }
  }

  handleFileSelect(file) {
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
      this.showError('blog-image-error', 'Please upload a valid image (PNG, JPEG, JPG)');
      return;
    }

    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      this.showError('blog-image-error', 'File size must be less than 5MB');
      return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
      if (this.previewImage && this.previewArea && this.uploadArea) {
        this.previewImage.src = e.target.result;
        this.uploadArea.style.display = 'none';
        this.previewArea.style.display = 'block';
        this.clearError('blog-image-error');
      }
    };
    reader.readAsDataURL(file);
  }

  clearFileSelection() {
    if (this.fileInput) this.fileInput.value = '';
    if (this.previewImage) this.previewImage.src = '';
    if (this.uploadArea) this.uploadArea.style.display = 'block';
    if (this.previewArea) this.previewArea.style.display = 'none';
  }

  setupFormValidation() {
    if (!this.form) return;

    this.form.addEventListener('submit', (e) => {
      console.log('🔵 Form submission started');
      e.preventDefault();
      
      // Clear any previous errors first
      this.clearError('blog-name-error');
      this.clearError('category-error');
      this.clearError('description-error');
      this.clearError('blog-image-error');

      // Validate form
      if (!this.validateForm()) {
        console.error('❌ Form validation failed');
        e.stopPropagation();
        // Scroll to first error
        const firstError = this.form.querySelector('.form-error[style*="display: block"]');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
      
      console.log('✓ Form validation passed, submitting via traditional form submission');
      
      // Add loading state to submit button
      const submitBtn = this.form.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        console.log('Submit button disabled, showing "Submitting..." state');
      }
      
      // Submit the form using traditional method (FormData with multipart for file upload)
      console.log('📤 Submitting form data...');
      console.log('Form action:', this.form.action);
      console.log('Form method:', this.form.method);
      
      // Use traditional form submission to handle file uploads
      this.form.submit();
    });
  }

  validateForm() {
    let isValid = true;

    // Validate blog name
    const blogName = document.getElementById('blog-name');
    if (blogName && !blogName.value.trim()) {
      this.showError('blog-name-error', 'Blog name is required');
      isValid = false;
    }

    // Validate category
    const category = document.getElementById('category');
    if (category && !category.value) {
      this.showError('category-error', 'Please select a category');
      isValid = false;
    }

    // Validate description
    const description = document.getElementById('description');
    if (description && !description.value.trim()) {
      this.showError('description-error', 'Description is required');
      isValid = false;
    }

    // Validate image (optional for both create and edit)
    // Images are not required
    
    return isValid;
  }

  showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = 'block';
    }
  }

  clearError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = '';
      errorElement.style.display = 'none';
    }
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  new BlogFormManager();
});
