/**
 * Clubs & Societies JavaScript
 */

class ClubsManager {
  constructor() {
    this.init();
  }

  async init() {
    this.setupTabs();
    this.setupSearch();
    this.setupCategoryFilters();
  }

  setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        const tabName = button.getAttribute('data-tab');
        this.switchTab(tabName);
      });
    });
  }

  switchTab(tabName) {
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
    });
    document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('tab-button--active');

    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('is-hidden');
    });
    document.querySelector(`[data-tab-content="${tabName}"]`)?.classList.remove('is-hidden');
  }

  setupSearch() {
    const searchInput = document.getElementById('club-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.searchClubs(e.target.value);
      });
    }
  }

  searchClubs(query) {
    const clubCards = document.querySelectorAll('.blog-card');
    clubCards.forEach(card => {
      const title = card.querySelector('.blog-card__title')?.textContent.toLowerCase() || '';
      card.style.display = title.includes(query.toLowerCase()) ? 'block' : 'none';
    });
  }

  setupCategoryFilters() {
    const pills = document.querySelectorAll('.pill');
    pills.forEach(pill => {
      pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('pill--active'));
        pill.classList.add('pill--active');
        this.filterByCategory(pill.getAttribute('data-category'));
      });
    });
  }

  filterByCategory(category) {
    const clubCards = document.querySelectorAll('.blog-card');
    clubCards.forEach(card => {
      const cardCategory = card.getAttribute('data-category');
      card.style.display = (category === 'all' || cardCategory === category) ? 'block' : 'none';
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new ClubsManager();
});