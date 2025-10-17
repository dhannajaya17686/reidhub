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
});

/**
 * Export modules for potential external use
 */
export { TabNavigation};
