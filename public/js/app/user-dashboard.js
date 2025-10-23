/**
 * User Dashboard - Minimal JavaScript
 */
class UserDashboard {
  constructor() {
    this.sidebarOpen = false;
    this.init();
  }

  init() {
    this.setupSearch();
    this.setupResponsiveSidebar();
    this.setupNotifications();
    this.setupImageLoading();
    this.setupAnimations();
    this.loadDynamicContent();
  }

  setupSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');

    if (searchInput) {
      searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          this.performSearch(searchInput.value);
        }
      });
    }

    if (searchBtn) {
      searchBtn.addEventListener('click', () => {
        this.performSearch(searchInput?.value || '');
      });
    }
  }

  performSearch(query) {
    if (!query.trim()) return;
    
    console.log('Searching for:', query);
    // Implement search functionality
    // window.location.href = `/search?q=${encodeURIComponent(query)}`;
  }

  setupResponsiveSidebar() {
    // Add mobile menu toggle if needed
    if (window.innerWidth <= 1024) {
      this.createMobileMenuToggle();
    }

    window.addEventListener('resize', () => {
      if (window.innerWidth > 1024) {
        this.closeMobileSidebar();
      }
    });
  }

  createMobileMenuToggle() {
    const header = document.querySelector('.dashboard-header');
    if (!header || document.querySelector('.mobile-menu-toggle')) return;

    const toggle = document.createElement('button');
    toggle.className = 'mobile-menu-toggle icon-btn';
    toggle.innerHTML = '☰';
    toggle.addEventListener('click', () => this.toggleMobileSidebar());

    const headerLeft = header.querySelector('.header-left');
    if (headerLeft) {
      headerLeft.insertBefore(toggle, headerLeft.firstChild);
    }
  }

  toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    this.sidebarOpen = !this.sidebarOpen;
    
    if (this.sidebarOpen) {
      sidebar.style.transform = 'translateX(0)';
      sidebar.style.zIndex = '1000';
      this.createOverlay();
    } else {
      this.closeMobileSidebar();
    }
  }

  closeMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar) {
      sidebar.style.transform = 'translateX(-100%)';
      sidebar.style.zIndex = '';
    }
    
    if (overlay) {
      overlay.remove();
    }
    
    this.sidebarOpen = false;
  }

  createOverlay() {
    if (document.querySelector('.sidebar-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    `;
    
    overlay.addEventListener('click', () => this.closeMobileSidebar());
    document.body.appendChild(overlay);
  }

  setupNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    
    if (notificationBtn) {
      notificationBtn.addEventListener('click', () => {
        this.showNotifications();
      });
    }
  }

  showNotifications() {
    // Simple notification indicator
    const btn = document.querySelector('.notification-btn');
    if (btn) {
      btn.style.background = 'var(--surface-hover)';
      setTimeout(() => {
        btn.style.background = '';
      }, 200);
    }
    
    console.log('Showing notifications...');
    // Implement notification dropdown/modal
  }

  setupImageLoading() {
    // Lazy loading for images
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.src;
            img.classList.remove('lazy');
            observer.unobserve(img);
          }
        });
      });

      images.forEach(img => {
        img.classList.add('lazy');
        imageObserver.observe(img);
      });
    }
  }

  setupAnimations() {
    // Add fade-in animation to dashboard sections
    const sections = document.querySelectorAll('.dashboard-section');
    sections.forEach((section, index) => {
      section.style.opacity = '0';
      section.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        section.style.transition = 'all 0.6s ease';
        section.style.opacity = '1';
        section.style.transform = 'translateY(0)';
      }, index * 150);
    });
  }

  loadDynamicContent() {
    // Simulate loading recent activity
    this.updateLastSeen();
    this.loadRecentEvents();
    this.updateWelcomeDate();
    this.loadNotificationCount();
    this.updateActivityStats();
  }

  updateLastSeen() {
    // Update timestamp displays
    const dateElements = document.querySelectorAll('.welcome-date');
    dateElements.forEach(el => {
      if (el.textContent.includes('>>')) {
        const now = new Date();
        el.textContent = `>> ${now.toLocaleDateString('en-US', { 
          month: 'short', 
          day: 'numeric', 
          year: 'numeric' 
        })}`;
      }
    });
  }

  loadRecentEvents() {
    // Add loading states and fetch recent events
    const eventsList = document.querySelector('.events-list');
    if (eventsList) {
      // Add subtle loading animation
      eventsList.style.opacity = '0.7';
      
      setTimeout(() => {
        eventsList.style.opacity = '1';
        // Here you would fetch and update events from API
      }, 500);
    }
  }

  updateWelcomeDate() {
    const dateElement = document.querySelector('.welcome-date');
    if (dateElement) {
      const now = new Date();
      dateElement.textContent = `>> ${now.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
      })}`;
    }
  }

  loadNotificationCount() {
    // Simulate loading notification count
    setTimeout(() => {
      const notificationBadge = document.querySelector('.notification-badge');
      if (notificationBadge) {
        notificationBadge.textContent = '3';
        notificationBadge.style.display = 'inline-block';
      }
    }, 1000);
  }

  updateActivityStats() {
    // Animate stat numbers
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
      const target = parseInt(stat.textContent);
      let current = 0;
      const increment = target / 20;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        stat.textContent = Math.floor(current);
      }, 50);
    });
  }

  // Handle card interactions
  handleCardClick(type, id) {
    console.log(`Clicked ${type} with ID: ${id}`);
    
    // Add click feedback
    const activeCard = event.currentTarget;
    if (activeCard) {
      activeCard.style.transform = 'scale(0.98)';
      setTimeout(() => {
        activeCard.style.transform = '';
      }, 150);
    }
  }

  // Utility method for navigation
  navigateTo(url) {
    console.log(`Navigating to: ${url}`);
    // window.location.href = url;
  }

  // Show loading state
  showLoading(element) {
    if (element) {
      element.style.opacity = '0.6';
      element.style.pointerEvents = 'none';
    }
  }

  hideLoading(element) {
    if (element) {
      element.style.opacity = '1';
      element.style.pointerEvents = 'auto';
    }
  }
}

// Global navigation function
function navigateTo(url) {
  console.log(`Navigating to: ${url}`);
  // Add loading state or navigation logic here
  // window.location.href = url;
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
  new UserDashboard();
});

// Expose global functions
window.navigateTo = navigateTo;