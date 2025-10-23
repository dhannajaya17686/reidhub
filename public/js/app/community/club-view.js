/**
 * Club View JavaScript
 */

console.log('Club view page loaded');

class ClubViewManager {
  constructor() {
    this.clubId = new URLSearchParams(window.location.search).get('id');
    this.init();
  }

  init() {
    const deleteBtn = document.getElementById('delete-club-btn');
    if (deleteBtn) {
      deleteBtn.addEventListener('click', () => {
        if (confirm('Delete this club?')) this.deleteClub();
      });
    }
  }

  async deleteClub() {
    try {
      const response = await fetch(`/api/community/clubs/${this.clubId}`, { method: 'DELETE' });
      const data = await response.json();
      if (data.success) {
        window.location.href = '/dashboard/community/clubs';
      } else {
        alert('Failed to delete club');
      }
    } catch (error) {
      console.error('Delete failed:', error);
      alert('An error occurred');
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new ClubViewManager();
});