/**
 * Archived Items Management
 * Minimal functionality extending active items behavior
 */

class ArchivedItemsManager {
  constructor() {
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.checkItemsCount();
  }

  // Setup basic event listeners
  setupEventListeners() {
    // Handle button click animations
    document.addEventListener('click', (e) => {
      if (e.target.matches('.btn')) {
        e.target.classList.add('clicked');
        setTimeout(() => {
          e.target.classList.remove('clicked');
        }, 200);
      }
    });
  }

  // Check if there are items to show empty state
  checkItemsCount() {
    const itemsGrid = document.getElementById('items-grid');
    const emptyState = document.getElementById('empty-state');
    const items = itemsGrid.querySelectorAll('.item-card');

    if (items.length === 0) {
      itemsGrid.style.display = 'none';
      emptyState.style.display = 'block';
    } else {
      itemsGrid.style.display = 'grid';
      emptyState.style.display = 'none';
    }
  }

  // Show loading overlay
  showLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.style.display = 'flex';
    }
  }

  // Hide loading overlay
  hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.style.display = 'none';
    }
  }

  // Show success message (kept)
  showMessage(message, type = 'success') {
    alert(message); // simple popup
  }

  // Remove item from grid
  removeItemFromGrid(itemId) {
    const itemCard = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemCard) {
      itemCard.classList.add('fade-out');
      setTimeout(() => {
        itemCard.remove();
        this.checkItemsCount();
      }, 300);
    }
  }
}

// Update actions to match real routes
function viewItem(itemId) {
  window.location.href = `/dashboard/marketplace/show-product?id=${itemId}`;
}

function editItem(itemId) {
  window.location.href = `/dashboard/marketplace/seller/edit?id=${itemId}`;
}

// Unarchive with simple popup + refresh UI (POST only)
function unarchiveItem(itemId) {
  if (!confirm('Unarchive this item?')) return;

  const manager = window.archivedItemsManager;
  manager && manager.showLoading();

  const fd = new FormData();
  fd.append('id', String(itemId));

  fetch('/dashboard/marketplace/seller/archived/update', {
    method: 'POST',
    body: fd
  })
    .then(res => res.json().catch(() => ({})).then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      if (!ok || !data?.success) {
        alert(data?.message || 'Failed to unarchive item');
        return;
      }
      alert('Item unarchived successfully');
      const card = document.querySelector(`.item-card[data-item-id="${itemId}"]`);
      if (card) card.remove();
      manager && manager.checkItemsCount();
    })
    .catch(() => alert('Network error'))
    .finally(() => manager && manager.hideLoading());
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  window.archivedItemsManager = new ArchivedItemsManager();
});