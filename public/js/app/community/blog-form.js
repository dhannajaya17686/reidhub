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
      if (!this.validateForm()) {
        e.preventDefault();
      }
    });
  }

  validateForm() {
    let isValid = true;

    // Validate blog name
    const blogName = document.getElementById('blog-name');
    if (blogName && !blogName.value.trim()) {
      this.showError('blog-name-error', 'Blog name is required');
      isValid = false;
    } else if (blogName) {
      this.clearError('blog-name-error');
    }

    // Validate category
    const category = document.getElementById('category');
    if (category && !category.value) {
      this.showError('category-error', 'Please select a category');
      isValid = false;
    } else if (category) {
      this.clearError('category-error');
    }

    // Validate description
    const description = document.getElementById('description');
    if (description && !description.value.trim()) {
      this.showError('description-error', 'Description is required');
      isValid = false;
    } else if (description) {
      this.clearError('description-error');
    }

    // Validate image (only for create form)
    if (this.form.id === 'create-blog-form' && this.fileInput && !this.fileInput.files.length) {
      this.showError('blog-image-error', 'Blog image is required');
      isValid = false;
    }

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
