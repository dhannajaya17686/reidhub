/**
 * Enhanced Add Items Form JavaScript
 * Handles image uploads, quantity controls, payment options, and form validation
 */

class AddItemsForm {
  constructor() {
    this.maxImages = 4;
    this.uploadedImages = new Set();
    this.init();
  }

  init() {
    this.setupImageUploads();
    this.setupQuantityControls();
    this.setupPaymentOptions();
    this.setupBankAccountValidation();
    this.setupFormValidation();
    this.setupCharacterCounter();
  }

  // Setup payment options functionality
  setupPaymentOptions() {
    const codOption = document.getElementById('cod-option');
    const preorderOption = document.getElementById('preorder-option');
    const bankDetailsSection = document.getElementById('bank-details-section');

    // Show/hide bank details based on preorder selection
    preorderOption.addEventListener('change', () => {
      if (preorderOption.checked) {
        bankDetailsSection.style.display = 'block';
        bankDetailsSection.classList.add('show');
        this.makeBankFieldsRequired(true);
      } else if (!preorderOption.checked && !codOption.checked) {
        bankDetailsSection.style.display = 'none';
        this.makeBankFieldsRequired(false);
      }
    });

    // Ensure at least one payment method is selected
    [codOption, preorderOption].forEach(option => {
      option.addEventListener('change', () => {
        this.validatePaymentMethods();
      });
    });
  }

  // Make bank fields required/optional
  makeBankFieldsRequired(required) {
    const bankFields = ['bank-name', 'bank-branch', 'account-name', 'account-number'];
    
    bankFields.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      if (field) {
        field.required = required;
        const label = document.querySelector(`label[for="${fieldId}"]`);
        if (label) {
          if (required && !label.classList.contains('required')) {
            label.classList.add('required');
          } else if (!required) {
            label.classList.remove('required');
          }
        }
      }
    });
  }

  // Setup bank account validation and preview
  setupBankAccountValidation() {
    const bankName = document.getElementById('bank-name');
    const bankBranch = document.getElementById('bank-branch');
    const accountName = document.getElementById('account-name');
    const accountNumber = document.getElementById('account-number');
    const bankPreview = document.getElementById('bank-preview');

    // Update preview when fields change
    [bankName, bankBranch, accountName, accountNumber].forEach(field => {
      if (field) {
        field.addEventListener('input', () => {
          this.updateBankPreview();
          this.validateBankField(field);
        });
      }
    });

    // Format account number as user types
    if (accountNumber) {
      accountNumber.addEventListener('input', (e) => {
        // Remove non-digits
        let value = e.target.value.replace(/\D/g, '');
        // Limit to 18 digits
        if (value.length > 18) {
          value = value.substring(0, 18);
        }
        e.target.value = value;
        this.updateBankPreview();
      });
    }
  }

  // Update bank account preview
  updateBankPreview() {
    const bankName = document.getElementById('bank-name');
    const bankBranch = document.getElementById('bank-branch');
    const accountName = document.getElementById('account-name');
    const accountNumber = document.getElementById('account-number');
    const bankPreview = document.getElementById('bank-preview');

    const hasAnyBankData = [bankName, bankBranch, accountName, accountNumber]
      .some(field => field && field.value.trim());

    if (hasAnyBankData) {
      bankPreview.style.display = 'block';
      
      // Update preview values
      document.getElementById('preview-bank').textContent = 
        bankName.value || '-';
      document.getElementById('preview-branch').textContent = 
        bankBranch.value || '-';
      document.getElementById('preview-account-name').textContent = 
        accountName.value || '-';
      document.getElementById('preview-account-number').textContent = 
        accountNumber.value || '-';
    } else {
      bankPreview.style.display = 'none';
    }
  }

  // Validate individual bank field
  validateBankField(field) {
    const value = field.value.trim();
    const isValid = field.checkValidity() && value.length > 0;

    // Remove previous validation classes
    field.classList.remove('valid', 'invalid');

    if (value.length > 0) {
      if (isValid) {
        field.classList.add('valid');
      } else {
        field.classList.add('invalid');
      }
    }

    return isValid;
  }

  // Validate payment methods selection
  validatePaymentMethods() {
    const codOption = document.getElementById('cod-option');
    const preorderOption = document.getElementById('preorder-option');
    const errorElement = document.getElementById('payment-methods-error');

    const isValid = codOption.checked || preorderOption.checked;

    if (!isValid) {
      this.showError('payment-methods-error', 'Please select at least one payment method');
    } else {
      errorElement.classList.remove('show');
    }

    return isValid;
  }

  // Setup image upload functionality
  setupImageUploads() {
    for (let i = 0; i < this.maxImages; i++) {
      const slot = document.querySelector(`[data-slot="${i}"]`);
      const input = document.getElementById(`image-${i}`);
      const preview = document.getElementById(`preview-${i}`);
      const removeBtn = slot.querySelector('.remove-image');

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
      removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        this.removeImage(i, input, preview, removeBtn);
      });
    }
  }

  // Handle image upload
  handleImageUpload(event, slotIndex, preview, removeBtn) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file
    if (!this.validateImage(file)) return;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
      removeBtn.style.display = 'block';
      this.uploadedImages.add(slotIndex);
    };
    reader.readAsDataURL(file);
  }

  // Validate image file
  validateImage(file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

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
    removeBtn.style.display = 'none';
    this.uploadedImages.delete(slotIndex);
  }

  // Get placeholder HTML
  getPlaceholderHTML(slotIndex) {
    const isMain = slotIndex === 0;
    const text = isMain ? 'Main Image' : `Image ${slotIndex + 1}`;
    const hint = isMain ? 'Click to upload' : 'Optional';

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

  // Setup quantity controls
  setupQuantityControls() {
    const decreaseBtn = document.querySelector('.qty-decrease');
    const increaseBtn = document.querySelector('.qty-increase');
    const qtyInput = document.getElementById('item-quantity');

    decreaseBtn.addEventListener('click', () => {
      const currentValue = parseInt(qtyInput.value) || 1;
      if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
      }
    });

    increaseBtn.addEventListener('click', () => {
      const currentValue = parseInt(qtyInput.value) || 1;
      if (currentValue < 999) {
        qtyInput.value = currentValue + 1;
      }
    });

    // Validate manual input
    qtyInput.addEventListener('input', () => {
      const value = parseInt(qtyInput.value);
      if (isNaN(value) || value < 1) {
        qtyInput.value = 1;
      } else if (value > 999) {
        qtyInput.value = 999;
      }
    });
  }

  // Setup character counter for description
  setupCharacterCounter() {
    const description = document.getElementById('description');
    const counter = document.getElementById('desc-count');

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

  // Setup form validation
  setupFormValidation() {
    const form = document.getElementById('add-item-form');
    
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      
      if (this.validateForm()) {
        this.submitForm();
      }
    });
  }

  // Validate form
  validateForm() {
    let isValid = true;
    this.clearErrors();

    // Basic validation
    const itemName = document.getElementById('item-name');
    if (!itemName.value.trim()) {
      this.showError('item-name-error', 'Item name is required');
      isValid = false;
    }

    const category = document.getElementById('category');
    if (!category.value) {
      this.showError('category-error', 'Please select a category');
      isValid = false;
    }

    const condition = document.querySelector('input[name="condition"]:checked');
    if (!condition) {
      this.showError('condition-error', 'Please select item condition');
      isValid = false;
    }

    const description = document.getElementById('description');
    if (!description.value.trim()) {
      this.showError('description-error', 'Description is required');
      isValid = false;
    }

    const price = document.getElementById('item-price');
    if (!price.value || parseFloat(price.value) <= 0) {
      this.showError('item-price-error', 'Please enter a valid price');
      isValid = false;
    }

    const quantity = document.getElementById('item-quantity');
    if (!quantity.value || parseInt(quantity.value) <= 0) {
      this.showError('item-quantity-error', 'Please enter a valid quantity');
      isValid = false;
    }

    // Payment methods validation
    if (!this.validatePaymentMethods()) {
      isValid = false;
    }

    // Bank details validation (if preorder is selected)
    const preorderOption = document.getElementById('preorder-option');
    if (preorderOption.checked) {
      const bankFields = [
        { id: 'bank-name', name: 'Bank name' },
        { id: 'bank-branch', name: 'Branch' },
        { id: 'account-name', name: 'Account holder name' },
        { id: 'account-number', name: 'Account number' }
      ];

      bankFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element.value.trim()) {
          this.showError(`${field.id}-error`, `${field.name} is required`);
          isValid = false;
        }
      });

      // Validate account number format
      const accountNumber = document.getElementById('account-number');
      if (accountNumber.value && !/^\d{10,18}$/.test(accountNumber.value)) {
        this.showError('account-number-error', 'Account number must be 10-18 digits');
        isValid = false;
      }
    }

    // At least main image required
    if (!this.uploadedImages.has(0)) {
      alert('Please upload at least a main image');
      isValid = false;
    }

    return isValid;
  }

  // Show error message
  showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add('show');
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
    const form = document.getElementById('add-item-form');
    const loadingOverlay = document.getElementById('loading-overlay');
    const submitBtn = document.getElementById('submit-btn');

    loadingOverlay.style.display = 'flex';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Failed to submit item');
        }
        alert('Item added successfully!');
        window.location.href = '/dashboard/marketplace/seller/active'; // updated redirect
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
      })
      .finally(() => {
        loadingOverlay.style.display = 'none';
        submitBtn.disabled = false;
      });
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  // Wire image slots (0..3)
  const slots = document.querySelectorAll('.image-slot');
  const OK_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

  slots.forEach((slot) => {
    const idx = slot.getAttribute('data-slot');
    const input = document.getElementById(`image-${idx}`);
    const preview = document.getElementById(`preview-${idx}`);
    const removeBtn = slot.querySelector('.remove-image');

    if (!input || !preview) return;

    let objectUrl = null;

    // Click preview area to open file chooser
    preview.addEventListener('click', () => input.click());

    // Also allow clicking anywhere on the slot (except the remove button)
    slot.addEventListener('click', (e) => {
      if (e.target.closest('.remove-image')) return;
      input.click();
    });

    // Handle file selection + preview
    input.addEventListener('change', () => {
      const file = input.files && input.files[0];
      if (!file) return;

      if (!OK_TYPES.includes(file.type)) {
        alert('Invalid image type. Use JPG, PNG, or WebP.');
        input.value = '';
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        alert('Image too large. Max 5MB.');
        input.value = '';
        return;
      }

      // Revoke old URL if user re-selects
      if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
      }

      objectUrl = URL.createObjectURL(file);

      // Hide placeholder and show background preview
      const placeholder = preview.querySelector('.upload-placeholder');
      if (placeholder) placeholder.style.display = 'none';

      preview.style.backgroundImage = `url("${objectUrl}")`;
      preview.style.backgroundSize = 'cover';
      preview.style.backgroundPosition = 'center';

      if (removeBtn) removeBtn.style.display = 'block';
    });

    // Remove selected image and reset preview
    if (removeBtn) {
      removeBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // avoid triggering file picker
        input.value = '';
        if (objectUrl) {
          URL.revokeObjectURL(objectUrl);
          objectUrl = null;
        }
        preview.style.backgroundImage = '';
        const placeholder = preview.querySelector('.upload-placeholder');
        if (placeholder) placeholder.style.display = '';
        removeBtn.style.display = 'none';
      });
    }
  });
});

(() => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    const preorder = document.getElementById('preorder-option');
    const cod = document.getElementById('cod-option');
    const bankSection = document.getElementById('bank-details-section');
    const bankPreview = document.getElementById('bank-preview');

    const bankName = document.getElementById('bank-name');
    const bankBranch = document.getElementById('bank-branch');
    const accountName = document.getElementById('account-name');
    const accountNumber = document.getElementById('account-number');

    const errPayment = document.getElementById('payment-methods-error');
    const errBankName = document.getElementById('bank-name-error');
    const errBankBranch = document.getElementById('bank-branch-error');
    const errAccName = document.getElementById('account-name-error');
    const errAccNumber = document.getElementById('account-number-error');

    const prevBank = document.getElementById('preview-bank');
    const prevBranch = document.getElementById('preview-branch');
    const prevAccName = document.getElementById('preview-account-name');
    const prevAccNumber = document.getElementById('preview-account-number');

    const form = document.getElementById('add-item-form');
    const submitBtn = document.getElementById('submit-btn');
    const overlay = document.getElementById('loading-overlay');

    if (!form || !preorder || !bankSection) return;

    function makeBankFieldsRequired(required) {
      [bankName, bankBranch, accountName, accountNumber].forEach(el => {
        if (!el) return;
        if (required) {
          el.setAttribute('required', 'required');
          el.setAttribute('aria-required', 'true');
        } else {
          el.removeAttribute('required');
          el.removeAttribute('aria-required');
          el.value = '';
        }
      });
      clearBankErrors();
      updateBankPreview();
    }

    function clearBankErrors() {
      [errBankName, errBankBranch, errAccName, errAccNumber].forEach(el => el && (el.textContent = ''));
    }

    function updateBankPreview() {
      if (!bankPreview) return;
      const show =
        preorder.checked &&
        (bankName?.value || bankBranch?.value || accountName?.value || accountNumber?.value);

      bankPreview.style.display = show ? 'block' : 'none';
      if (prevBank) prevBank.textContent = displayBankName(bankName?.value || '');
      if (prevBranch) prevBranch.textContent = bankBranch?.value || '-';
      if (prevAccName) prevAccName.textContent = accountName?.value || '-';
      if (prevAccNumber) prevAccNumber.textContent = accountNumber?.value || '-';
    }

    function displayBankName(val) {
      const map = {
        commercial_bank: 'Commercial Bank of Ceylon',
        peoples_bank: "People's Bank",
        bank_of_ceylon: 'Bank of Ceylon',
        hatton_national: 'Hatton National Bank',
        sampath_bank: 'Sampath Bank',
        seylan_bank: 'Seylan Bank',
        dfcc_bank: 'DFCC Bank',
        ndb_bank: 'National Development Bank',
        nations_trust: 'Nations Trust Bank',
        union_bank: 'Union Bank',
        other: 'Other'
      };
      return map[val] || '-';
    }

    function toggleBankSection() {
      if (preorder.checked) {
        bankSection.style.display = 'block';
        makeBankFieldsRequired(true);
      } else {
        bankSection.style.display = 'none';
        makeBankFieldsRequired(false);
      }
    }

    function validatePaymentMethods() {
      if (!errPayment) return true;
      const anyChecked = (preorder.checked || (cod && cod.checked));
      if (!anyChecked) {
        errPayment.textContent = 'Select at least one payment method.';
        return false;
      }
      errPayment.textContent = '';
      return true;
    }

    function validateBankFieldsIfNeeded() {
      if (!preorder.checked) return true;

      let ok = true;
      if (bankName && !bankName.value) {
        errBankName && (errBankName.textContent = 'Please select a bank.');
        ok = false;
      } else errBankName && (errBankName.textContent = '');

      if (bankBranch && !bankBranch.value.trim()) {
        errBankBranch && (errBankBranch.textContent = 'Branch is required.');
        ok = false;
      } else errBankBranch && (errBankBranch.textContent = '');

      if (accountName && !accountName.value.trim()) {
        errAccName && (errAccName.textContent = 'Account holder name is required.');
        ok = false;
      } else errAccName && (errAccName.textContent = '');

      if (accountNumber) {
        const v = (accountNumber.value || '').replace(/\D/g, '');
        if (!/^\d{10,18}$/.test(v)) {
          errAccNumber && (errAccNumber.textContent = 'Enter 10–18 digit account number.');
          ok = false;
        } else errAccNumber && (errAccNumber.textContent = '');
      }

      return ok;
    }

    // Events
    preorder.addEventListener('change', () => {
      toggleBankSection();
      validatePaymentMethods();
    });
    cod && cod.addEventListener('change', validatePaymentMethods);

    [bankName, bankBranch, accountName, accountNumber].forEach(el => {
      if (!el) return;
      el.addEventListener('input', () => {
        clearBankErrors();
        updateBankPreview();
      });
      el.addEventListener('blur', () => {
        if (preorder.checked) validateBankFieldsIfNeeded();
      });
    });

    // Init state on load (in case browser restores state)
    toggleBankSection();
    updateBankPreview();
    validatePaymentMethods();

    // Form submit: run validations; let normal POST proceed if valid
    form.addEventListener('submit', (e) => {
      const okPm = validatePaymentMethods();
      const okBank = validateBankFieldsIfNeeded();
      if (!okPm || !okBank) {
        e.preventDefault();
        return;
      }
      // optional: show overlay while submitting
      if (overlay) overlay.style.display = 'flex';
      if (submitBtn) submitBtn.disabled = true;
    });
  });
})();

// Minimal submit hook (does not alter existing code)
document.addEventListener('DOMContentLoaded', () => {
  if (window.__addItemSubmitHookInstalled) return;
  window.__addItemSubmitHookInstalled = true;

  const form = document.getElementById('add-item-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    // If another handler already prevented default and will handle, do nothing
    if (e.defaultPrevented) return;

    e.preventDefault();
    const fd = new FormData(form);

    try {
      const res = await fetch(form.action, { method: 'POST', body: fd });
      let data = {};
      try { data = await res.json(); } catch (_) {}

      if (!res.ok || !data?.success) {
        const msg =
          data?.message ||
          (res.status === 422 ? 'Validation failed. Check your inputs.' :
           res.status === 401 ? 'Unauthorized. Please log in.' :
           'Failed to submit item.');
        alert(msg);
        return;
      }

      alert('Item added successfully!');
      window.location.href = '/dashboard/marketplace/seller/active';
    } catch (err) {
      alert(err?.message || 'Network error. Please try again.');
    }
  });
});