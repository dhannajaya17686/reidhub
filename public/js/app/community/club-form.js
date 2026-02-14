class ClubFormManager {
  constructor() {
    this.form = document.querySelector('.blog-form');
    this.fileInput = document.getElementById('club-image');
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

    this.uploadTrigger.addEventListener('click', (e) => {
      e.preventDefault();
      this.fileInput.click();
    });

    this.fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) this.handleFileSelect(file);
    });

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

    if (this.removeButton) {
      this.removeButton.addEventListener('click', (e) => {
        e.preventDefault();
        this.clearFileSelection();
      });
    }
  }

  handleFileSelect(file) {
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
      this.showError('club-image-error', 'Please upload PNG, JPEG, or JPG');
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      this.showError('club-image-error', 'File must be less than 5MB');
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      this.previewImage.src = e.target.result;
      this.uploadArea.style.display = 'none';
      this.previewArea.style.display = 'block';
      this.clearError('club-image-error');
    };
    reader.readAsDataURL(file);
  }

  clearFileSelection() {
    this.fileInput.value = '';
    this.previewImage.src = '';
    this.uploadArea.style.display = 'block';
    this.previewArea.style.display = 'none';
  }

  setupFormValidation() {
    if (!this.form) return;
    this.form.addEventListener('submit', (e) => {
      if (!this.validateForm()) e.preventDefault();
    });
  }

  validateForm() {
    let isValid = true;
    const clubName = document.getElementById('club-name');
    const category = document.getElementById('category');
    const contactEmail = document.getElementById('contact-email');
    const description = document.getElementById('description');

    if (!clubName.value.trim()) {
      this.showError('club-name-error', 'Club name is required');
      isValid = false;
    } else this.clearError('club-name-error');

    if (!category.value) {
      this.showError('category-error', 'Please select a category');
      isValid = false;
    } else this.clearError('category-error');

    if (contactEmail) {
      if (!contactEmail.value.trim() || !this.isValidEmail(contactEmail.value)) {
        this.showError('contact-email-error', 'Valid email is required');
        isValid = false;
      } else this.clearError('contact-email-error');
    }

    if (!description.value.trim()) {
      this.showError('description-error', 'Description is required');
      isValid = false;
    } else this.clearError('description-error');

    if (this.form.id === 'create-club-form' && !this.fileInput.files.length) {
      this.showError('club-image-error', 'Club logo is required');
      isValid = false;
    }

    return isValid;
  }

  isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  showError(elementId, message) {
    const el = document.getElementById(elementId);
    if (el) {
      el.textContent = message;
      el.style.display = 'block';
    }
  }

  clearError(elementId) {
    const el = document.getElementById(elementId);
    if (el) {
      el.textContent = '';
      el.style.display = 'none';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new ClubFormManager();
});
