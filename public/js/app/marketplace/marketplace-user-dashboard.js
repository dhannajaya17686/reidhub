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
    this.setupViewProductButtons();
    this.setupSorting();
    this.loadBuyerStats(); 
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
   * Load and display buyer statistics from API
   * Fetches recent purchases, active orders, and cart items count
   */
  async loadBuyerStats() {
    try {
        const response = await fetch('/dashboard/marketplace/stats', {
    method: 'GET',
    credentials: 'include',  // ← change from 'same-origin' to 'include'
    headers: {
        'Accept': 'application/json'
    }
  });

        if (!response.ok) {
            console.error('API Error: Status', response.status);
            if (response.status === 401) {
                this.updateStatCards({ recent_purchases: 0, active_orders: 0, cart_items: 0 });
            }
            return;
        }

        const data = await response.json(); // ← parse AFTER checking ok

        if (data && data.success) {
            this.updateStatCards(data);
        } else {
            console.error('Failed to load buyer stats:', data?.message || 'Unknown error');
        }
    } catch (error) {
        console.error('Error loading buyer stats:', error);
    }
}

  /**
   * Update stat cards with fetched data
   * @param {Object} data - Stats data from API
   */
  updateStatCards(data) {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
      // Try BEM naming first, then simple class
      const labelElement = card.querySelector('.stat-card__label') || card.querySelector('.stat-label');
      const numberElement = card.querySelector('.stat-card__number') || card.querySelector('.stat-number');
      
      if (!labelElement || !numberElement) return; // Skip if elements not found
      
      const label = labelElement.textContent.toLowerCase();

      if (label.includes('recent') && label.includes('purchase')) {
        numberElement.textContent = data.recent_purchases || 0;
      } else if (label.includes('active') && label.includes('order')) {
        numberElement.textContent = data.active_orders || 0;
      } else if (label.includes('cart')) {
        numberElement.textContent = data.cart_items || 0;
      }
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
   * Set up sorting functionality for product grids
   * Listens to sort dropdown changes and reorders product cards
   * Supports combined filtering (by type) and sorting (by price)
   */
  setupSorting() {
    const sortDropdowns = document.querySelectorAll('.sort-dropdown');
    console.log('setupSorting initialized, found dropdowns:', sortDropdowns.length);
    
    sortDropdowns.forEach((dropdown, index) => {
      console.log(`Dropdown ${index}:`, dropdown.id, dropdown.getAttribute('data-sort'));
      
      dropdown.addEventListener('change', (e) => {
        const sortType = dropdown.getAttribute('data-sort');
        const sortValue = e.target.value;
        const section = dropdown.closest('.product-section');
        const productGrid = section ? section.querySelector('.product-grid') : null;
        
        console.log('Sort triggered:', { sortType, sortValue, gridFound: !!productGrid });
        
        if (!productGrid) {
          console.warn('Product grid not found for dropdown', dropdown.id);
          return;
        }
        
        const cards = Array.from(productGrid.querySelectorAll('.product-card'));
        console.log(`Found ${cards.length} cards to sort/filter`);
        
        // Get current type filter from the type dropdown
        const typeDropdown = section.querySelector('.sort-dropdown[data-sort="type"]');
        const currentTypeFilter = typeDropdown ? typeDropdown.value : '';
        
        // Get current price sort from the price dropdown
        const priceDropdown = section.querySelector('.sort-dropdown[data-sort="price"]');
        const currentPriceSort = priceDropdown ? priceDropdown.value : '';
        
        // Get visible cards (respect current type filter)
        let visibleCards = cards.filter(card => {
          if (currentTypeFilter === '') {
            return true; // Show all if no type filter
          }
          const cardType = card.getAttribute('data-type') || '';
          return cardType === currentTypeFilter;
        });
        
        // Apply price sorting to visible cards
        if (currentPriceSort !== '') {
          visibleCards.sort((a, b) => this.sortByPrice(a, b, currentPriceSort));
        }
        
        // Update visibility and order
        cards.forEach(card => {
          if (visibleCards.includes(card)) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
        
        // Reorder visible cards in the DOM
        visibleCards.forEach(card => productGrid.appendChild(card));
        console.log(`Applied: Type filter="${currentTypeFilter}", Price sort="${currentPriceSort}"`);
      });
    });
  }

  /**
   * Sort product cards by price
   * @param {Element} cardA - First product card
   * @param {Element} cardB - Second product card
   * @param {string} direction - 'low-to-high' or 'high-to-low'
   * @returns {number} Sort comparison value
   */
  sortByPrice(cardA, cardB, direction) {
    const priceA = parseInt(cardA.getAttribute('data-price')) || 0;
    const priceB = parseInt(cardB.getAttribute('data-price')) || 0;
    
    return direction === 'low-to-high' ? priceA - priceB : priceB - priceA;
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

}

/**
 * Initialize dashboard when DOM is ready
 * Ensures all elements are available before setup
 */
// Replace the bottom of your JS file:
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new MarketplaceDashboard());
} else {
    new MarketplaceDashboard(); // DOM already ready when module executes
}
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