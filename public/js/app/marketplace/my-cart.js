class CartManager {
  constructor() {
    this.cartItems = new Map();
    this.init();
  }

  init() {
    this.loadCartData();
    this.setupEventListeners();
    this.updateCartSummary();
  }

  loadCartData() {
    // Sample cart data - in real app, this would come from API
    this.cartItems.set('1', {
      id: '1',
      name: 'UCSC Tshirt',
      price: 2000,
      quantity: 2,
      condition: 'new',
      seller: 'Students Union of UCSC',
      image: 'https://via.placeholder.com/120x120/1e3a8a/ffffff?text=UCSC+Tshirt',
      paymentType: 'cod',
      allowsCOD: true,
      isPreorder: false
    });

    this.cartItems.set('2', {
      id: '2',
      name: 'UCSC Wrist Band',
      price: 600,
      quantity: 1,
      condition: 'new',
      seller: 'Students Union of UCSC',
      image: 'https://via.placeholder.com/120x120/374151/ffffff?text=Wrist+Band',
      paymentType: 'prepaid',
      allowsCOD: false,
      isPreorder: true
    });
  }

  setupEventListeners() {
    // Quantity controls
    document.addEventListener('click', (e) => {
      if (e.target.matches('.quantity-btn')) {
        this.handleQuantityChange(e.target);
      }
    });

    // Quantity input changes
    document.addEventListener('change', (e) => {
      if (e.target.matches('.quantity-input')) {
        this.handleQuantityInput(e.target);
      }
    });

    // Payment option changes
    document.addEventListener('change', (e) => {
      if (e.target.matches('.payment-radio')) {
        this.handlePaymentOptionChange(e.target);
      }
    });

    // Item actions
    document.addEventListener('click', (e) => {
      const action = e.target.dataset.action;
      const itemId = e.target.dataset.itemId;

      switch (action) {
        case 'save-later':
          this.saveForLater(itemId);
          break;
        case 'remove':
          this.removeItem(itemId);
          break;
      }
    });

    // Checkout button
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', () => this.handleCheckout());
    }

    // Payment modal
    this.setupPaymentModal();

    // File upload
    this.setupFileUpload();
  }

  handleQuantityChange(button) {
    const action = button.dataset.action;
    const itemId = button.dataset.itemId;
    const quantityInput = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
    
    if (!quantityInput) return;

    let newQuantity = parseInt(quantityInput.value);
    const min = parseInt(quantityInput.min) || 1;
    const max = parseInt(quantityInput.max) || 99;

    if (action === 'increase' && newQuantity < max) {
      newQuantity++;
    } else if (action === 'decrease' && newQuantity > min) {
      newQuantity--;
    }

    quantityInput.value = newQuantity;
    this.updateItemQuantity(itemId, newQuantity);
  }

  handleQuantityInput(input) {
    const itemId = input.dataset.itemId;
    const quantity = parseInt(input.value);
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 99;

    if (quantity < min) {
      input.value = min;
      this.updateItemQuantity(itemId, min);
    } else if (quantity > max) {
      input.value = max;
      this.updateItemQuantity(itemId, max);
    } else {
      this.updateItemQuantity(itemId, quantity);
    }
  }

  updateItemQuantity(itemId, quantity) {
    const item = this.cartItems.get(itemId);
    if (item) {
      item.quantity = quantity;
      this.updateCartSummary();
    }
  }

  handlePaymentOptionChange(radio) {
    const itemId = radio.name.split('-')[1]; // Extract item ID from name like "payment-1"
    const paymentType = radio.value;
    
    const item = this.cartItems.get(itemId);
    if (item) {
      item.paymentType = paymentType;
      this.updateCartSummary();
    }
  }

  updateCartSummary() {
    let subtotal = 0;
    let codAmount = 0;
    let prepaidAmount = 0;
    let hasDiscountEligibleItems = false;

    this.cartItems.forEach(item => {
      const itemTotal = item.price * item.quantity;
      subtotal += itemTotal;

      if (item.paymentType === 'cod') {
        codAmount += itemTotal;
      } else {
        prepaidAmount += itemTotal;
        if (!item.isPreorder) {
          hasDiscountEligibleItems = true;
        }
      }
    });

    const shipping = 300;
    const taxes = 100;
    let discount = 0;

    // Calculate discount for prepaid items (5% off, excluding preorders)
    if (hasDiscountEligibleItems) {
      const discountEligibleAmount = Array.from(this.cartItems.values())
        .filter(item => item.paymentType === 'prepaid' && !item.isPreorder)
        .reduce((sum, item) => sum + (item.price * item.quantity), 0);
      discount = Math.round(discountEligibleAmount * 0.05);
    }

    const total = subtotal + shipping + taxes - discount;

    // Update UI
    document.getElementById('subtotal').textContent = `Rs. ${subtotal.toLocaleString()}`;
    document.getElementById('shipping').textContent = `Rs. ${shipping.toLocaleString()}`;
    document.getElementById('taxes').textContent = `Rs. ${taxes.toLocaleString()}`;
    document.getElementById('total').textContent = `Rs. ${total.toLocaleString()}`;

    // Show/hide discount line
    const discountLine = document.getElementById('discount-line');
    if (discount > 0) {
      document.getElementById('discount').textContent = `-Rs. ${discount.toLocaleString()}`;
      discountLine.style.display = 'flex';
    } else {
      discountLine.style.display = 'none';
    }

    // Update payment summary
    document.getElementById('cod-amount').textContent = `Rs. ${codAmount.toLocaleString()}`;
    document.getElementById('prepaid-amount').textContent = `Rs. ${prepaidAmount.toLocaleString()}`;

    // Update checkout button text
    const checkoutBtn = document.getElementById('checkout-btn');
    if (prepaidAmount > 0) {
      checkoutBtn.textContent = 'Proceed to Payment';
    } else {
      checkoutBtn.textContent = 'Place Order';
    }
  }

  saveForLater(itemId) {
    // In real app, this would save to wishlist/saved items
    this.showNotification('Item saved for later', 'success');
  }

  removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
      this.cartItems.delete(itemId);
      
      // Remove from DOM
      const itemElement = document.querySelector(`[data-item-id="${itemId}"]`).closest('.cart-item');
      if (itemElement) {
        itemElement.remove();
      }
      
      this.updateCartSummary();
      this.updateCartCount();
      this.showNotification('Item removed from cart', 'info');
    }
  }

  updateCartCount() {
    const totalItems = Array.from(this.cartItems.values())
      .reduce((sum, item) => sum + item.quantity, 0);
    
    const countElement = document.querySelector('.cart-count');
    if (countElement) {
      countElement.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''} in your cart`;
    }
  }

  handleCheckout() {
    const prepaidItems = Array.from(this.cartItems.values())
      .filter(item => item.paymentType === 'prepaid');

    if (prepaidItems.length > 0) {
      // Calculate total prepaid amount
      const prepaidTotal = prepaidItems.reduce((sum, item) => 
        sum + (item.price * item.quantity), 0);
      
      // Show payment modal
      this.showPaymentModal(prepaidTotal);
    } else {
      // All items are COD, proceed directly to order placement
      this.placeOrder();
    }
  }

  showPaymentModal(amount) {
    const modal = document.getElementById('payment-modal');
    const transferAmount = document.getElementById('transfer-amount');
    
    if (modal && transferAmount) {
      transferAmount.textContent = `Rs. ${amount.toLocaleString()}`;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  setupPaymentModal() {
    const modal = document.getElementById('payment-modal');
    const closeBtn = document.querySelector('.modal-close');
    const cancelBtn = document.getElementById('cancel-payment');
    const paymentForm = document.getElementById('payment-form');

    // Close modal function
    const closeModal = () => {
      modal.style.display = 'none';
      document.body.style.overflow = '';
      paymentForm.reset();
      this.clearFilePreview();
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

    // Copy account number
    document.addEventListener('click', (e) => {
      if (e.target.closest('.copy-btn')) {
        const copyText = e.target.closest('.copy-btn').dataset.copy;
        navigator.clipboard.writeText(copyText).then(() => {
          this.showNotification('Account number copied to clipboard', 'success');
        });
      }
    });

    // Handle form submission
    if (paymentForm) {
      paymentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handlePaymentSubmission(paymentForm);
      });
    }
  }

  setupFileUpload() {
    const fileInput = document.getElementById('payment-slip');
    const uploadArea = document.getElementById('file-upload-area');
    const preview = document.getElementById('file-preview');
    const removeBtn = document.getElementById('preview-remove');

    if (!fileInput || !uploadArea) return;

    // Handle file selection
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        this.showFilePreview(file);
      }
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--secondary-color)';
      uploadArea.style.background = 'var(--surface-hover)';
    });

    uploadArea.addEventListener('dragleave', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--border-color)';
      uploadArea.style.background = '';
    });

    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--border-color)';
      uploadArea.style.background = '';
      
      const file = e.dataTransfer.files[0];
      if (file) {
        fileInput.files = e.dataTransfer.files;
        this.showFilePreview(file);
      }
    });

    // Remove file
    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        this.clearFilePreview();
      });
    }
  }

  showFilePreview(file) {
    const preview = document.getElementById('file-preview');
    const uploadArea = document.getElementById('file-upload-area');
    const nameElement = document.getElementById('preview-name');
    const sizeElement = document.getElementById('preview-size');

    if (preview && uploadArea && nameElement && sizeElement) {
      nameElement.textContent = file.name;
      sizeElement.textContent = this.formatFileSize(file.size);
      
      uploadArea.style.display = 'none';
      preview.style.display = 'block';
    }
  }

  clearFilePreview() {
    const fileInput = document.getElementById('payment-slip');
    const preview = document.getElementById('file-preview');
    const uploadArea = document.getElementById('file-upload-area');

    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
  }

  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  async handlePaymentSubmission(form) {
    const formData = new FormData(form);
    
    // Validate required fields
    const paymentSlip = formData.get('payment-slip');
    if (!paymentSlip || paymentSlip.size === 0) {
      this.showNotification('Please upload a payment slip', 'error');
      return;
    }

    try {
      // In real app, upload payment slip and create order
      console.log('Payment submission:', {
        paymentSlip: paymentSlip.name,
        referenceNumber: formData.get('reference-number'),
        notes: formData.get('payment-notes')
      });

      // Show success and close modal
      this.showNotification('Payment submitted successfully! Your order will be processed once payment is verified.', 'success');
      
      document.getElementById('payment-modal').style.display = 'none';
      document.body.style.overflow = '';
      form.reset();
      this.clearFilePreview();
      
      // Redirect to orders page or show order confirmation
      setTimeout(() => {
        // window.location.href = '/orders';
        console.log('Redirecting to orders page...');
      }, 2000);
      
    } catch (error) {
      console.error('Payment submission error:', error);
      this.showNotification('Failed to submit payment. Please try again.', 'error');
    }
  }

  placeOrder() {
    // Handle COD order placement
    console.log('Placing COD order...');
    this.showNotification('Order placed successfully!', 'success');
    
    setTimeout(() => {
      // window.location.href = '/orders';
      console.log('Redirecting to orders page...');
    }, 2000);
  }

  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? 'var(--vote-active-bg)' : type === 'error' ? '#FEF2F2' : '#F3F4F6'};
      color: ${type === 'success' ? 'var(--vote-active)' : type === 'error' ? '#DC2626' : '#374151'};
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      z-index: 1002;
      max-width: 400px;
      font-size: 0.875rem;
      font-weight: 500;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new CartManager();
});