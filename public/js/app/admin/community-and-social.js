/**
 * Admin Community & Social Management - Minimal JavaScript
 */
class CommunityAdmin {
  constructor() {
    this.currentTab = 'blog-posts';
    this.currentContentTab = {};
    this.init();
  }

  init() {
    this.setupTabs();
    this.setupContentTabs();
    this.setupFilters();
    this.setupSearch();
    this.setupActions();
  }

  setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const targetTab = button.dataset.tab;
        
        // Update button states
        tabButtons.forEach(btn => {
          btn.classList.remove('tab-button--active');
          btn.setAttribute('aria-selected', 'false');
        });
        button.classList.add('tab-button--active');
        button.setAttribute('aria-selected', 'true');

        // Update content visibility
        tabContents.forEach(content => {
          content.classList.remove('active');
        });
        
        const targetContent = document.getElementById(`${targetTab}-content`);
        if (targetContent) {
          targetContent.classList.add('active');
        }

        this.currentTab = targetTab;
      });
    });
  }

  setupContentTabs() {
    const contentTabs = document.querySelectorAll('.content-tab');
    
    contentTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const parent = tab.closest('.tab-content');
        const parentTabs = parent.querySelectorAll('.content-tab');
        
        // Update tab states within the same parent
        parentTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Store current content tab for this section
        this.currentContentTab[this.currentTab] = tab.dataset.contentTab;
        
        // Apply any filtering based on content tab
        this.applyContentTabFilter(tab.dataset.contentTab);
      });
    });
  }

  applyContentTabFilter(contentTab) {
    // Simple visual feedback for content tab changes
    const tables = document.querySelectorAll('.data-table tbody');
    tables.forEach(tbody => {
      tbody.style.opacity = '0.7';
      setTimeout(() => {
        tbody.style.opacity = '1';
      }, 200);
    });
  }

  setupFilters() {
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
      select.addEventListener('change', () => {
        this.applyFilters();
      });
    });
  }

  setupSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    const searchButtons = document.querySelectorAll('.search-btn');
    
    searchInputs.forEach(input => {
      input.addEventListener('input', () => {
        this.debounceSearch(input.value);
      });
      
      input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          this.performSearch(input.value);
        }
      });
    });
    
    searchButtons.forEach(button => {
      button.addEventListener('click', () => {
        const input = button.previousElementSibling;
        if (input) {
          this.performSearch(input.value);
        }
      });
    });
  }

  debounceSearch(query) {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
      this.performSearch(query);
    }, 300);
  }

  performSearch(query) {
    const activeTable = document.querySelector('.tab-content.active .data-table');
    if (!activeTable) return;
    
    const rows = activeTable.querySelectorAll('tbody tr');
    const searchTerm = query.toLowerCase().trim();
    
    rows.forEach(row => {
      if (!searchTerm) {
        row.style.display = '';
        return;
      }
      
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
    
    this.updateEmptyState(activeTable, query);
  }

  applyFilters() {
    const activeTab = document.querySelector('.tab-content.active');
    if (!activeTab) return;
    
    const statusFilter = activeTab.querySelector('#status-filter, #club-status-filter, #event-status-filter')?.value || '';
    const dateFilter = activeTab.querySelector('#date-filter, #club-date-filter')?.value || '';
    const typeFilter = activeTab.querySelector('#event-type-filter')?.value || '';
    
    const rows = activeTab.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
      let visible = true;
      
      // Status filter
      if (statusFilter) {
        const statusBadge = row.querySelector('.status-badge');
        const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase().trim() : '';
        if (rowStatus !== statusFilter.toLowerCase()) {
          visible = false;
        }
      }
      
      // Date filter (simplified)
      if (dateFilter && visible) {
        const dateCell = row.querySelector('.date-placed, .event-date');
        if (dateCell) {
          // Simple date filtering logic would go here
          // For demo purposes, just show all
        }
      }
      
      row.style.display = visible ? '' : 'none';
    });
  }

  updateEmptyState(table, query) {
    const tbody = table.querySelector('tbody');
    const visibleRows = Array.from(tbody.children).filter(row => 
      row.style.display !== 'none'
    );
    
    // Remove existing empty state
    const existingEmpty = table.querySelector('.empty-state');
    if (existingEmpty) {
      existingEmpty.remove();
    }
    
    // Add empty state if no visible rows
    if (visibleRows.length === 0) {
      const emptyRow = document.createElement('tr');
      emptyRow.className = 'empty-state';
      emptyRow.innerHTML = `
        <td colspan="100%" style="text-align: center; padding: var(--space-2xl); color: var(--text-muted);">
          ${query ? `No results found for "${query}"` : 'No items found'}
        </td>
      `;
      tbody.appendChild(emptyRow);
    }
  }

  setupActions() {
    // View buttons
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('view-btn')) {
        this.handleView(e.target);
      } else if (e.target.classList.contains('edit-btn')) {
        this.handleEdit(e.target);
      } else if (e.target.classList.contains('remove-btn')) {
        this.handleRemove(e.target);
      } else if (e.target.classList.contains('pagination-btn') && !e.target.disabled) {
        this.handlePagination(e.target);
      }
    });
  }

  handleView(button) {
    const row = button.closest('tr');
    const id = row.querySelector('[class*="id"]')?.textContent || '';
    
    this.showToast(`Opening details for ${id}...`);
    
    // Add visual feedback
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
      button.style.transform = '';
    }, 150);
  }

  handleEdit(button) {
    const row = button.closest('tr');
    const id = row.querySelector('[class*="id"]')?.textContent || '';
    
    this.showToast(`Opening editor for ${id}...`);
    
    // Add visual feedback
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
      button.style.transform = '';
    }, 150);
  }

  handleRemove(button) {
    const row = button.closest('tr');
    const name = row.querySelector('[class*="name"]')?.textContent || 'item';
    
    if (confirm(`Are you sure you want to remove "${name}"?`)) {
      // Add removal animation
      row.style.transition = 'all 0.3s ease';
      row.style.opacity = '0';
      row.style.transform = 'translateX(-20px)';
      
      setTimeout(() => {
        row.remove();
        this.showToast(`${name} has been removed.`);
      }, 300);
    }
  }

  handlePagination(button) {
    const currentActive = button.parentElement.querySelector('.pagination-btn.active');
    if (currentActive) {
      currentActive.classList.remove('active');
    }
    
    if (!button.textContent.match(/[‹›]/)) {
      button.classList.add('active');
    }
    
    // Add loading effect
    const table = document.querySelector('.tab-content.active .data-table tbody');
    if (table) {
      table.style.opacity = '0.5';
      setTimeout(() => {
        table.style.opacity = '1';
      }, 300);
    }
    
    this.showToast(`Loading page ${button.textContent}...`);
  }

  showToast(message) {
    // Remove existing toasts
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
      existingToast.remove();
    }
    
    // Create toast
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: var(--secondary-color);
      color: white;
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      z-index: 1000;
      opacity: 0;
      transform: translateY(-10px);
      transition: all 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }, 3000);
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  new CommunityAdmin();
});