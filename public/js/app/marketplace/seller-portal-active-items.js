/**
 * Active Items Management
 * Simple functionality for viewing, editing, and archiving items
 */

class ActiveItemsManager {
  constructor() {
    this.itemsGrid = null;
    this.emptyState = null;
    this.init();
  }

  init() {
    this.itemsGrid = document.getElementById('items-grid');
    this.emptyState = document.getElementById('empty-state');
    this.setupEventListeners();
    this.loadItems();
  }

  // Setup basic event listeners
  setupEventListeners() {
    // Handle any dynamic content loading if needed
    document.addEventListener('click', (e) => {
      if (e.target.matches('.btn')) {
        e.target.classList.add('clicked');
        setTimeout(() => e.target.classList.remove('clicked'), 200);
      }
    });
  }

  async loadItems() {
    this.showLoading();
    try {
      const res = await fetch('/dashboard/marketplace/seller/active/get', { method: 'GET' });
      const data = await res.json();
      if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load items');
      this.renderItems(Array.isArray(data.items) ? data.items : []);
    } catch (err) {
      console.error(err);
      this.renderItems([]);
    } finally {
      this.hideLoading();
    }
  }

  renderItems(items) {
    if (!this.itemsGrid) return;
    this.itemsGrid.innerHTML = '';

    if (!items.length) {
      this.itemsGrid.style.display = 'none';
      if (this.emptyState) this.emptyState.style.display = 'block';
      return;
    }

    if (this.emptyState) this.emptyState.style.display = 'none';
    this.itemsGrid.style.display = 'grid';

    for (const item of items) {
      const img = item.image || '/images/placeholders/product.png';
      const condition = item.condition === 'brand_new' ? 'Brand New' : 'Used';
      const price = `Rs.${Number(item.price).toLocaleString('en-LK', { minimumFractionDigits: 0 })}`;

      const card = document.createElement('div');
      card.className = 'item-card';
      card.setAttribute('data-item-id', String(item.id));
      card.innerHTML = `
        <div class="item-content">
          <div class="item-info">
            <h3 class="item-title">${this.escape(item.title)}</h3>
            <div class="item-price">${price}</div>
            <div class="item-meta">
              <span class="item-condition">Condition: ${condition}</span>
            </div>
            <div class="item-actions">
              <button class="btn btn-primary btn-sm" data-action="view" data-id="${item.id}">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
                View
              </button>
              <button class="btn btn-primary btn-sm" data-action="edit" data-id="${item.id}">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                  <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Edit
              </button>
              <button class="btn btn-secondary btn-sm" data-action="archive" data-id="${item.id}">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                  <polyline points="21,8 21,21 3,21 3,8" stroke="currentColor" stroke-width="2"/>
                  <rect x="1" y="3" width="22" height="5" stroke="currentColor" stroke-width="2"/>
                  <line x1="10" y1="12" x2="14" y2="12" stroke="currentColor" stroke-width="2"/>
                </svg>
                Add to Archive
              </button>
            </div>
          </div>
          <div class="item-image">
            <img src="${this.escape(img)}" alt="${this.escape(item.title)}" onerror="this.src='/assets/images/placeholders/product.jpeg'">
          </div>
        </div>
      `;
      this.itemsGrid.appendChild(card);
    }

    // Wire action buttons
    this.itemsGrid.addEventListener('click', (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;
      const id = btn.getAttribute('data-id');
      const action = btn.getAttribute('data-action');
      if (!id || !action) return;

      if (action === 'view') {
        window.location.href = `/dashboard/marketplace/show-product?id=${id}`;
      } else if (action === 'edit') {
        window.location.href = `/dashboard/marketplace/seller/edit?id=${id}`;
      } else if (action === 'archive') {
        // Minimal popup flow
        const fd = new FormData();
        fd.append('id', id);
        fetch('/dashboard/marketplace/seller/active/archive', {
          method: 'POST',
          body: fd
        })
        .then(res => res.json().catch(() => ({})).then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
          if (!ok || !data?.success) {
            alert(data?.message || 'Failed to archive item');
            return;
          }
          alert('Item archived successfully');
          // Refresh to reflect changes
          window.location.reload();
        })
        .catch(() => alert('Network error while archiving'));
      }
    }, { once: true });
  }

  // Show loading overlay
  showLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }

  // Hide loading overlay
  hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'none';
  }

  escape(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
  }
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  window.activeItemsManager = new ActiveItemsManager();
});