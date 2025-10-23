/**
 * Admin Dashboard - Minimal JavaScript
 */
class AdminDashboard {
  constructor() {
    this.init();
  }

  init() {
    this.updateTime();
    this.setupInteractions();
    this.animateStats();
  }

  updateTime() {
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
      setInterval(() => {
        const now = new Date();
        timeElement.textContent = now.toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        }) + ' - ' + now.toLocaleTimeString('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        });
      }, 1000);
    }
  }

  setupInteractions() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.stat-card, .dashboard-card, .quick-action-btn');
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-2px)';
      });
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = '';
      });
    });

    // Simple click handlers for demonstration
    const actionBtns = document.querySelectorAll('.quick-action-btn, .view-all-btn, .action-btn');
    actionBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        this.showToast('Feature coming soon!');
      });
    });
  }

  animateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
      const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
      this.animateNumber(stat, 0, finalValue, 1500);
    });
  }

  animateNumber(element, start, end, duration) {
    const startTime = performance.now();
    
    function update(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      
      const current = Math.floor(start + (end - start) * progress);
      element.textContent = current.toLocaleString();
      
      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }
    
    requestAnimationFrame(update);
  }

  showToast(message) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: var(--admin-primary);
      color: white;
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
      toast.style.opacity = '1';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3000);
  }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
  new AdminDashboard();
});