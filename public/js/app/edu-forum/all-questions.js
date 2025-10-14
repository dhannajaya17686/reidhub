/**
 * Forum Page Interactive Behaviors (Simplified)
 * ============================================
 * 
 * Provides basic progressive enhancement for the ReidHub forum interface.
 * Focus on core functionality without heavy performance optimizations.
 */

/**
 * Tab Navigation Controller
 * -------------------------
 * Handles switching between content filter tabs (Newest, Trending, etc.)
 * Updates active states and manages keyboard navigation.
 */
class TabNavigation {
  constructor(tabContainer) {
    this.container = tabContainer;
    this.tabs = tabContainer.querySelectorAll('[data-tab]');
    this.init();
  }

  init() {
    this.tabs.forEach(tab => {
      tab.addEventListener('click', (e) => this.handleTabClick(e));
      tab.addEventListener('keydown', (e) => this.handleTabKeydown(e));
    });
  }

  handleTabClick(event) {
    event.preventDefault();
    const tab = event.target;
    this.setActiveTab(tab);
  }

  handleTabKeydown(event) {
    const { key } = event;
    const currentIndex = Array.from(this.tabs).indexOf(event.target);

    if (key === 'ArrowRight' || key === 'ArrowLeft') {
      event.preventDefault();
      const direction = key === 'ArrowRight' ? 1 : -1;
      const nextIndex = (currentIndex + direction + this.tabs.length) % this.tabs.length;
      this.tabs[nextIndex].focus();
      this.setActiveTab(this.tabs[nextIndex]);
    }
  }

  setActiveTab(activeTab) {
    // Remove active state from all tabs
    this.tabs.forEach(tab => {
      tab.classList.remove('is-active');
      tab.setAttribute('aria-selected', 'false');
    });

    // Set active state on selected tab
    activeTab.classList.add('is-active');
    activeTab.setAttribute('aria-selected', 'true');

    // Emit custom event for content updates (if needed)
    const tabChangeEvent = new CustomEvent('tabchange', {
      detail: { activeTab: activeTab.dataset.tab }
    });
    this.container.dispatchEvent(tabChangeEvent);
  }
}

/**
 * Mobile Sidebar Controller
 * -------------------------
 * Handles mobile sidebar toggle functionality with focus management
 * and escape key handling for accessibility.
 */
class MobileSidebar {
  constructor() {
    this.sidebar = document.querySelector('.forum-sidebar');
    this.toggleBtn = document.querySelector('[data-sidebar-toggle]');
    this.overlay = document.querySelector('[data-sidebar-overlay]');
    this.isOpen = false;
    this.init();
  }

  init() {
    if (!this.sidebar) return;

    // Create mobile toggle button if not exists
    if (!this.toggleBtn) {
      this.createToggleButton();
    }

    // Create overlay for mobile
    if (!this.overlay) {
      this.createOverlay();
    }

    // Event listeners
    this.toggleBtn?.addEventListener('click', () => this.toggle());
    this.overlay?.addEventListener('click', () => this.close());
    document.addEventListener('keydown', (e) => this.handleKeydown(e));
  }

  createToggleButton() {
    const button = document.createElement('button');
    button.setAttribute('data-sidebar-toggle', '');
    button.setAttribute('aria-label', 'Toggle navigation menu');
    button.className = 'sidebar-toggle-btn';
    button.innerHTML = '☰';
    
    const header = document.querySelector('.forum-header .header-content');
    if (header) {
      header.prepend(button);
      this.toggleBtn = button;
    }
  }

  createOverlay() {
    const overlay = document.createElement('div');
    overlay.setAttribute('data-sidebar-overlay', '');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    this.overlay = overlay;
  }

  toggle() {
    this.isOpen ? this.close() : this.open();
  }

  open() {
    this.isOpen = true;
    this.sidebar.classList.add('is-open');
    this.overlay?.classList.add('is-visible');
    document.body.style.overflow = 'hidden';
    
    // Focus first focusable element in sidebar
    const firstFocusable = this.sidebar.querySelector('a, button');
    firstFocusable?.focus();
  }

  close() {
    this.isOpen = false;
    this.sidebar.classList.remove('is-open');
    this.overlay?.classList.remove('is-visible');
    document.body.style.overflow = '';
    
    // Return focus to toggle button
    this.toggleBtn?.focus();
  }

  handleKeydown(event) {
    if (event.key === 'Escape' && this.isOpen) {
      this.close();
    }
  }
}

/**
 * Search Enhancement (Basic)
 * --------------------------
 * Provides basic search input enhancements like clear functionality.
 */
class SearchEnhancement {
  constructor(searchInput) {
    this.input = searchInput;
    this.init();
  }

  init() {
    if (!this.input) return;

    // Add clear button functionality
    this.input.addEventListener('input', () => this.handleInput());
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => this.handleGlobalKeydown(e));
  }

  handleInput() {
    const value = this.input.value.trim();
    
    // Add/remove clear button based on input value
    if (value && !this.clearBtn) {
      this.addClearButton();
    } else if (!value && this.clearBtn) {
      this.removeClearButton();
    }
  }

  handleGlobalKeydown(event) {
    // Focus search with Ctrl/Cmd + K
    if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
      event.preventDefault();
      this.input.focus();
    }
  }

  addClearButton() {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'search-clear-btn';
    button.setAttribute('aria-label', 'Clear search');
    button.innerHTML = '×';
    button.addEventListener('click', () => this.clearSearch());
    
    this.input.parentElement.appendChild(button);
    this.clearBtn = button;
  }

  removeClearButton() {
    if (this.clearBtn) {
      this.clearBtn.remove();
      this.clearBtn = null;
    }
  }

  clearSearch() {
    this.input.value = '';
    this.input.focus();
    this.removeClearButton();
    
    // Emit clear event for any listeners
    const clearEvent = new CustomEvent('searchclear');
    this.input.dispatchEvent(clearEvent);
  }
}

/**
 * Basic Initialization
 * -------------------
 * Initialize core interactive components when DOM is ready.
 */
document.addEventListener('DOMContentLoaded', () => {
  // Initialize tab navigation
  const tabContainer = document.querySelector('.content-tabs');
  if (tabContainer) {
    new TabNavigation(tabContainer);
  }

  // Initialize mobile sidebar
  new MobileSidebar();

  // Initialize search enhancements
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    new SearchEnhancement(searchInput);
  }
});

/**
 * Export modules for potential external use
 */
export { TabNavigation, MobileSidebar, SearchEnhancement };