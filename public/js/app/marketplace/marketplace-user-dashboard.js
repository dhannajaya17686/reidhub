/* ==========================================================================
   Enhanced Marketplace Dashboard Interactive Features
   --------------------------------------------------------------------------
   Comprehensive vanilla JavaScript for tab navigation, product interactions,
   and enhanced user experience with smooth animations and accessibility.
   ========================================================================== */

class MarketplaceDashboard {
  constructor() {
    this.activeTab = 'merchandise'; // Default active tab
    this.init();
  }

  /**
   * Initialize all dashboard interactions
   * Sets up event listeners and initial state
   */
  init() {
    this.setupTabNavigation();
    this.setupProductCardInteractions();
    this.setupViewProductButtons();
    this.setupStatCardInteractions();
    this.addAccessibilityEnhancements();
    this.initializeTabContent();
  }

  /**
   * Tab Navigation System
   * Handles switching between Merchandise and Second Hand Items
   * Manages URL state and content visibility
   */
  setupTabNavigation() {
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('[data-tab-content]');

    tabButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const targetTab = button.dataset.tab;
        this.switchTab(targetTab, tabButtons, tabContents);
      });

      // Keyboard navigation for tabs
      button.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
          e.preventDefault();
          const currentIndex = Array.from(tabButtons).indexOf(button);
          const nextIndex = e.key === 'ArrowRight' 
            ? (currentIndex + 1) % tabButtons.length
            : (currentIndex - 1 + tabButtons.length) % tabButtons.length;
          
          tabButtons[nextIndex].focus();
          tabButtons[nextIndex].click();
        }
      });
    });
  }

  /**
   * Switch active tab and update content visibility
   * @param {string} targetTab - Tab identifier to activate
   * @param {NodeList} tabButtons - All tab button elements
   * @param {NodeList} tabContents - All tab content elements
   */
  switchTab(targetTab, tabButtons, tabContents) {
    // Update active tab state
    this.activeTab = targetTab;

    // Update button states
    tabButtons.forEach(btn => {
      const isActive = btn.dataset.tab === targetTab;
      btn.classList.toggle('tab-button--active', isActive);
      btn.setAttribute('aria-selected', isActive);
      btn.setAttribute('tabindex', isActive ? '0' : '-1');
    });

    // Update content visibility with fade effect
    tabContents.forEach(content => {
      const isActive = content.dataset.tabContent === targetTab;
      
      if (isActive) {
        content.classList.remove('is-hidden');
        content.setAttribute('aria-hidden', 'false');
      } else {
        content.classList.add('is-hidden');
        content.setAttribute('aria-hidden', 'true');
      }
    });

    // Update URL without page reload
    this.updateUrlState(targetTab);
    
    // Announce tab change to screen readers
    this.announceToScreenReader(`Switched to ${targetTab} section`);
    
    // Track tab interaction
    this.trackTabSwitch(targetTab);
  }

  /**
   * Initialize tab content based on URL or default state
   * Checks URL parameters and sets initial active tab
   */
  initializeTabContent() {
    const urlParams = new URLSearchParams(window.location.search);
    const urlTab = urlParams.get('tab');
    
    if (urlTab && ['merchandise', 'second-hand'].includes(urlTab)) {
      this.activeTab = urlTab;
    }

    // Set initial tab state
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('[data-tab-content]');
    this.switchTab(this.activeTab, tabButtons, tabContents);
  }

  /**
   * Update browser URL to reflect current tab state
   * @param {string} tab - Current active tab
   */
  updateUrlState(tab) {
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
  }

  /**
   * Enhanced product card hover and focus interactions
   * Adds smooth animations and keyboard navigation support
   */
  setupProductCardInteractions() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
      // Add keyboard navigation support
      card.setAttribute('tabindex', '0');
      card.setAttribute('role', 'article');
      
      // Enhanced focus handling with smooth animations
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const viewButton = card.querySelector('.btn--primary');
          if (viewButton) {
            this.simulateButtonClick(viewButton);
          }
        }
      });

      // Add subtle interaction feedback
      card.addEventListener('mouseenter', () => {
        this.addCardHoverEffect(card);
      });

      card.addEventListener('mouseleave', () => {
        this.removeCardHoverEffect(card);
      });

      // Analytics tracking for card interactions
      card.addEventListener('click', (e) => {
        if (!e.target.classList.contains('btn')) {
          this.trackProductView(card);
        }
      });
    });
  }

  /**
   * Add visual hover effects to product cards
   * @param {Element} card - Product card element
   */
  addCardHoverEffect(card) {
    const image = card.querySelector('.product-card__image img');
    if (image) {
      image.style.transform = 'scale(1.05)';
    }
  }

  /**
   * Remove visual hover effects from product cards
   * @param {Element} card - Product card element
   */
  removeCardHoverEffect(card) {
    const image = card.querySelector('.product-card__image img');
    if (image) {
      image.style.transform = 'scale(1)';
    }
  }

  /**
   * Enhanced View Product button interactions
   * Handles product navigation with loading states and smooth transitions
   */
  setupViewProductButtons() {
    const viewButtons = document.querySelectorAll('[data-product-action="view"]');
    
    viewButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        this.handleProductView(button);
      });
    });
  }

  /**
   * Handle product view action with loading state
   * @param {Element} button - View product button
   */
  async handleProductView(button) {
    // Add loading state
    const originalText = button.textContent;
    button.textContent = 'Loading...';
    button.disabled = true;
    button.classList.add('btn--loading');
    
    // Get product data from card
    const productCard = button.closest('.product-card');
    const productData = this.extractProductData(productCard);
    
    try {
      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 800));
      
      // Navigate to product
      this.navigateToProduct(productData);
      
    } catch (error) {
      console.error('Error loading product:', error);
      this.announceToScreenReader('Error loading product. Please try again.');
      
    } finally {
      // Reset button state
      button.textContent = originalText;
      button.disabled = false;
      button.classList.remove('btn--loading');
    }
  }

  /**
   * Simulate button click for keyboard navigation
   * @param {Element} button - Button to simulate click on
   */
  simulateButtonClick(button) {
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
      button.style.transform = '';
      button.click();
    }, 100);
  }

  /**
   * Setup interactions for statistics cards
   * Makes stat cards clickable with smooth transitions
   */
  setupStatCardInteractions() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
      card.setAttribute('tabindex', '0');
      card.setAttribute('role', 'button');
      
      const label = card.querySelector('.stat-card__label').textContent.toLowerCase();
      card.setAttribute('aria-label', `View ${label}`);
      
      card.addEventListener('click', () => {
        this.handleStatCardClick(card, label);
      });

      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.handleStatCardClick(card, label);
        }
      });
    });
  }

  /**
   * Handle stat card interactions
   * @param {Element} card - Stat card element
   * @param {string} label - Card label for navigation
   */
  handleStatCardClick(card, label) {
    // Add click animation
    card.style.transform = 'scale(0.95)';
    setTimeout(() => {
      card.style.transform = '';
    }, 150);

    // Navigate based on card type
    if (label.includes('cart')) {
      this.announceToScreenReader('Navigating to cart');
      // Navigate to cart page
    } else if (label.includes('orders')) {
      this.announceToScreenReader('Navigating to orders');
      // Navigate to orders page
    } else if (label.includes('purchases')) {
      this.announceToScreenReader('Navigating to purchase history');
      // Navigate to purchase history
    }

    this.trackStatCardClick(label);
  }

  /**
   * Extract comprehensive product information from card element
   * @param {Element} card - Product card element
   * @returns {Object} Product data object
   */
  extractProductData(card) {
    const title = card.querySelector('.product-card__title')?.textContent || '';
    const price = card.querySelector('.product-card__price')?.textContent || '';
    const condition = card.querySelector('.product-card__condition')?.textContent || '';
    const image = card.querySelector('.product-card__image img')?.src || '';
    const isFeatured = card.classList.contains('product-card--featured');
    const isDeal = card.classList.contains('product-card--deal');
    
    return {
      title: title.trim(),
      price: price.trim(),
      condition: condition.trim(),
      image,
      isFeatured,
      isDeal,
      category: this.activeTab,
      cardElement: card
    };
  }

  /**
   * Navigate to product detail page with enhanced feedback
   * @param {Object} productData - Product information
   */
  navigateToProduct(productData) {
    // Track product view
    this.trackProductView(productData.cardElement);
    
    // Enhanced navigation feedback
    this.announceToScreenReader(`Loading ${productData.title}`);
    
    // In a real application, this would navigate to the product page
    console.log('Navigating to product:', productData);
    
    // Example navigation with category context
    const productSlug = this.generateProductSlug(productData.title);
    const url = `/product/${productSlug}?category=${productData.category}`;
    
    // For demo purposes, show enhanced alert with product info
    const alertMessage = `Viewing: ${productData.title}\n` +
                        `Price: ${productData.price}\n` +
                        `Condition: ${productData.condition}\n` +
                        `Category: ${productData.category}\n` +
                        `${productData.isFeatured ? '⭐ Featured Item' : ''}\n` +
                        `${productData.isDeal ? '🏷️ Special Deal' : ''}`;
    
    alert(alertMessage.trim());
  }

  /**
   * Generate URL-friendly slug from product title
   * @param {string} title - Product title
   * @returns {string} URL slug
   */
  generateProductSlug(title) {
    return title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
  }

  /**
   * Enhanced analytics tracking for product interactions
   * @param {Element} card - Product card element
   */
  trackProductView(card) {
    const productData = this.extractProductData(card);
    const section = card.closest('.product-section')?.querySelector('.section-title')?.textContent || 'Unknown';
    
    const trackingData = {
      event: 'product_view',
      product: productData.title,
      price: productData.price,
      condition: productData.condition,
      category: productData.category,
      section: section.trim(),
      is_featured: productData.isFeatured,
      is_deal: productData.isDeal,
      timestamp: new Date().toISOString(),
      user: 'AmashaRanasinghe'
    };
    
    console.log('Product interaction tracked:', trackingData);
    
    // In a real application, send to analytics service
    // analytics.track('product_view', trackingData);
  }

  /**
   * Track tab switching behavior
   * @param {string} tab - Tab identifier
   */
  trackTabSwitch(tab) {
    const trackingData = {
      event: 'tab_switch',
      tab: tab,
      timestamp: new Date().toISOString(),
      user: 'AmashaRanasinghe'
    };
    
    console.log('Tab switch tracked:', trackingData);
  }

  /**
   * Track stat card interactions
   * @param {string} cardType - Type of stat card clicked
   */
  trackStatCardClick(cardType) {
    const trackingData = {
      event: 'stat_card_click',
      card_type: cardType,
      timestamp: new Date().toISOString(),
      user: 'AmashaRanasinghe'
    };
    
    console.log('Stat card interaction tracked:', trackingData);
  }

  /**
   * Enhanced accessibility features
   * Improves screen reader support and keyboard navigation
   */
  addAccessibilityEnhancements() {
    // Add aria-labels to product cards
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
      const title = card.querySelector('.product-card__title')?.textContent || '';
      const price = card.querySelector('.product-card__price')?.textContent || '';
      const condition = card.querySelector('.product-card__condition')?.textContent || '';
      
      if (title && price) {
        card.setAttribute('aria-label', `${title}, ${price}, ${condition}`);
      }
    });

    // Setup tab navigation accessibility
    const tabList = document.querySelector('.tab-list');
    if (tabList) {
      tabList.setAttribute('role', 'tablist');
      tabList.setAttribute('aria-label', 'Product categories');
    }

    const tabButtons = document.querySelectorAll('[data-tab]');
    tabButtons.forEach(button => {
      button.setAttribute('role', 'tab');
      button.setAttribute('aria-controls', `tab-content-${button.dataset.tab}`);
    });

    // Add live region for dynamic content updates
    this.createLiveRegion();
  }

  /**
   * Create live region for screen reader announcements
   */
  createLiveRegion() {
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'visually-hidden';
    liveRegion.id = 'dashboard-live-region';
    document.body.appendChild(liveRegion);
  }

  /**
   * Announce changes to screen readers
   * @param {string} message - Message to announce
   */
  announceToScreenReader(message) {
    const liveRegion = document.getElementById('dashboard-live-region');
    if (liveRegion) {
      liveRegion.textContent = message;
      setTimeout(() => {
        liveRegion.textContent = '';
      }, 1000);
    }
  }
}

/**
 * Initialize dashboard when DOM is ready
 * Ensures all elements are available before setup
 */
document.addEventListener('DOMContentLoaded', () => {
  new MarketplaceDashboard();
});

/**
 * Handle browser back/forward navigation
 * Updates tab state when user navigates with browser controls
 */
window.addEventListener('popstate', () => {
  const dashboard = new MarketplaceDashboard();
  dashboard.initializeTabContent();
});

/**
 * Export for potential module usage
 * Allows dashboard to be imported in other scripts if needed
 */
export { MarketplaceDashboard };

document.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-product-action="add-to-cart"]');
  if (!btn) return;
  e.preventDefault();

  const productId = parseInt(btn.getAttribute('data-product-id') || '0', 10);
  if (!productId) { alert('Invalid product.'); return; }

  try {
    const fd = new FormData();
    fd.append('product_id', String(productId));
    fd.append('quantity', '1');

    const res = await fetch('/dashboard/marketplace/cart/add', { method: 'POST', body: fd });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data?.success) {
      alert(data?.message || 'Failed to add to cart');
      return;
    }
    alert('Added to cart');
  } catch {
    alert('Network error');
  }
});