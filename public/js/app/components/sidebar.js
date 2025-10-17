/**
 * Forum Page Interactive Behaviors (Simplified)
 * ============================================
 * 
 * Provides basic progressive enhancement for the ReidHub forum interface.
 * Focus on core functionality without heavy performance optimizations.
 */


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
<<<<<<< HEAD
 * Sidebar Dropdown Navigation
 * ---------------------------
 * Handles expandable/collapsible navigation sections
 */
class SidebarDropdown {
  constructor() {
    this.init();
  }

  init() {
    // Add click handlers to all dropdown toggles
    document.querySelectorAll('[data-toggle="dropdown"]').forEach(button => {
      button.addEventListener('click', (e) => this.handleToggle(e));
    });
  }

  handleToggle(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    const chevron = button.querySelector('.sidebar-nav-chevron');
    const submenu = button.parentElement.querySelector('.sidebar-submenu');

    // Toggle states
    button.setAttribute('aria-expanded', !isExpanded);
    
    if (chevron) {
      chevron.classList.toggle('is-expanded', !isExpanded);
    }
    
    if (submenu) {
      submenu.classList.toggle('is-expanded', !isExpanded);
    }
  }
}

/**
=======
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
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
<<<<<<< HEAD

  // Initialize sidebar dropdowns
  new SidebarDropdown();
=======
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
});

/**
 * Export modules for potential external use
 */
export {MobileSidebar, SearchEnhancement };
