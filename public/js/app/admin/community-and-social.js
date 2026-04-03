class CommunityAdminPage {
  constructor() {
    this.selectedUserId = null;
    this.userSearchResults = [];
    this.currentTab = 'community-admins';
    this.init();
  }

  init() {
    this.bindTabNavigation();
    this.bindAdminActions();
    this.bindSearchInputs();
    this.loadCurrentTabData();
  }

  bindTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const tab = button.dataset.tab;
        this.currentTab = tab;

        tabButtons.forEach((btn) => {
          const isActive = btn === button;
          btn.classList.toggle('tab-button--active', isActive);
          btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        tabContents.forEach((content) => {
          content.classList.toggle('active', content.id === `${tab}-content`);
        });

        this.loadCurrentTabData();
      });
    });
  }

  bindAdminActions() {
    const addBtn = document.getElementById('add-community-admin-btn');
    if (addBtn) {
      addBtn.addEventListener('click', () => this.handleAddAdmin());
    }

    const userSearchInput = document.getElementById('admin-user-search');
    if (userSearchInput) {
      userSearchInput.addEventListener('input', () => this.handleUserSearchInput());
      userSearchInput.addEventListener('change', () => this.resolveSelectedUser());
      userSearchInput.addEventListener('blur', () => this.resolveSelectedUser());
    }

    document.addEventListener('click', (event) => {
      const removeAdminBtn = event.target.closest('[data-remove-admin-id]');
      if (removeAdminBtn) {
        const id = parseInt(removeAdminBtn.getAttribute('data-remove-admin-id'), 10);
        this.handleRemoveAdmin(id);
        return;
      }

      const deleteBlogBtn = event.target.closest('[data-delete-blog-id]');
      if (deleteBlogBtn) {
        const id = parseInt(deleteBlogBtn.getAttribute('data-delete-blog-id'), 10);
        this.handleDeleteBlog(id);
        return;
      }

      const deleteClubBtn = event.target.closest('[data-delete-club-id]');
      if (deleteClubBtn) {
        const id = parseInt(deleteClubBtn.getAttribute('data-delete-club-id'), 10);
        this.handleDeleteClub(id);
        return;
      }

      const deleteEventBtn = event.target.closest('[data-delete-event-id]');
      if (deleteEventBtn) {
        const id = parseInt(deleteEventBtn.getAttribute('data-delete-event-id'), 10);
        this.handleDeleteEvent(id);
      }
    });
  }

  bindSearchInputs() {
    this.bindTableSearch('reported-blogs-search', 'reported-blogs-tbody');
    this.bindTableSearch('clubs-search', 'clubs-tbody');
    this.bindTableSearch('events-search', 'events-tbody');
  }

  bindTableSearch(inputId, tbodyId) {
    const input = document.getElementById(inputId);
    const tbody = document.getElementById(tbodyId);
    if (!input || !tbody) {
      return;
    }

    input.addEventListener('input', () => {
      const term = input.value.trim().toLowerCase();
      const rows = tbody.querySelectorAll('tr');

      rows.forEach((row) => {
        if (row.children.length === 1) {
          row.style.display = '';
          return;
        }

        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }

  async loadCurrentTabData() {
    if (this.currentTab === 'community-admins') {
      await this.loadCommunityAdmins();
      return;
    }

    if (this.currentTab === 'blog-posts') {
      await this.loadReportedBlogs();
      return;
    }

    if (this.currentTab === 'clubs-societies') {
      await this.loadClubs();
      return;
    }

    if (this.currentTab === 'events') {
      await this.loadEvents();
    }
  }

  async apiGet(url) {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    });

    const payload = await response.json();
    if (!response.ok || !payload.success) {
      throw new Error(payload.message || 'Request failed');
    }

    return payload.data || [];
  }

  async apiPost(url, body) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(body)
    });

    const payload = await response.json();
    if (!response.ok || !payload.success) {
      throw new Error(payload.message || 'Request failed');
    }

    return payload;
  }

  async handleUserSearchInput() {
    const input = document.getElementById('admin-user-search');
    const datalist = document.getElementById('admin-user-results');
    if (!input || !datalist) {
      return;
    }

    const query = input.value.trim();
    this.selectedUserId = null;

    if (query.length < 2) {
      datalist.innerHTML = '';
      this.userSearchResults = [];
      return;
    }

    try {
      const users = await this.apiGet(`/api/admin/community/users/search?q=${encodeURIComponent(query)}`);
      this.userSearchResults = users;

      datalist.innerHTML = users.map((user) => {
        const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
        const label = `${fullName} (${user.email})${user.is_community_admin === 1 ? ' - already admin' : ''}`;
        return `<option value="${this.escapeHtml(label)}"></option>`;
      }).join('');
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  resolveSelectedUser() {
    const input = document.getElementById('admin-user-search');
    if (!input) {
      return;
    }

    const value = input.value.trim();
    const match = this.userSearchResults.find((user) => {
      const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
      const label = `${fullName} (${user.email})${user.is_community_admin === 1 ? ' - already admin' : ''}`;
      return label === value;
    });

    this.selectedUserId = match ? parseInt(match.id, 10) : null;
  }

  async handleAddAdmin() {
    this.resolveSelectedUser();

    const roleSelect = document.getElementById('admin-role-select');
    const userInput = document.getElementById('admin-user-search');

    const roleType = roleSelect ? roleSelect.value : '';
    if (!this.selectedUserId || !roleType) {
      this.showToast('Select a user and permission first', 'error');
      return;
    }

    try {
      await this.apiPost('/api/admin/community/admins/add', {
        user_id: this.selectedUserId,
        role_type: roleType
      });

      if (userInput) {
        userInput.value = '';
      }
      if (roleSelect) {
        roleSelect.value = '';
      }
      this.selectedUserId = null;

      this.showToast('Community admin added', 'success');
      await this.loadCommunityAdmins();
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  async handleRemoveAdmin(communityAdminId) {
    if (!communityAdminId) {
      return;
    }

    if (!confirm('Remove this community admin permission?')) {
      return;
    }

    try {
      await this.apiPost('/api/admin/community/admins/remove', {
        community_admin_id: communityAdminId
      });
      this.showToast('Community admin removed', 'success');
      await this.loadCommunityAdmins();
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  async handleDeleteBlog(blogId) {
    if (!blogId) {
      return;
    }

    if (!confirm('Delete this reported blog?')) {
      return;
    }

    try {
      await this.apiPost('/api/admin/community/blogs/delete', { blog_id: blogId });
      this.showToast('Blog deleted', 'success');
      await this.loadReportedBlogs();
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  async handleDeleteClub(clubId) {
    if (!clubId) {
      return;
    }

    if (!confirm('Delete this club? This removes related records too.')) {
      return;
    }

    try {
      await this.apiPost('/api/admin/community/clubs/delete', { club_id: clubId });
      this.showToast('Club deleted', 'success');
      await this.loadClubs();
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  async handleDeleteEvent(eventId) {
    if (!eventId) {
      return;
    }

    if (!confirm('Delete this event?')) {
      return;
    }

    try {
      await this.apiPost('/api/admin/community/events/delete', { event_id: eventId });
      this.showToast('Event deleted', 'success');
      await this.loadEvents();
    } catch (error) {
      this.showToast(error.message, 'error');
    }
  }

  async loadCommunityAdmins() {
    const tbody = document.getElementById('community-admins-tbody');
    if (!tbody) {
      return;
    }

    try {
      const admins = await this.apiGet('/api/admin/community/admins/list');
      if (!admins.length) {
        tbody.innerHTML = '<tr class="table-row"><td colspan="6">No community admins found</td></tr>';
        return;
      }

      tbody.innerHTML = admins.map((admin) => {
        const addedAt = admin.created_at ? new Date(admin.created_at).toLocaleDateString() : '-';
        const fullName = `${admin.first_name || ''} ${admin.last_name || ''}`.trim();

        return `
          <tr class="table-row">
            <td class="blog-id">#${admin.id}</td>
            <td class="blog-name">${this.escapeHtml(fullName)}</td>
            <td>${this.escapeHtml(admin.email || '-')}</td>
            <td><span class="status-badge active">${this.escapeHtml((admin.role_type || '').replaceAll('_', ' '))}</span></td>
            <td class="date-placed">${this.escapeHtml(addedAt)}</td>
            <td class="actions">
              <button class="action-btn remove-btn" data-remove-admin-id="${admin.id}" type="button">Remove</button>
            </td>
          </tr>
        `;
      }).join('');
    } catch (error) {
      tbody.innerHTML = '<tr class="table-row"><td colspan="6">Failed to load admins</td></tr>';
      this.showToast(error.message, 'error');
    }
  }

  async loadReportedBlogs() {
    const tbody = document.getElementById('reported-blogs-tbody');
    if (!tbody) {
      return;
    }

    try {
      const blogs = await this.apiGet('/api/admin/community/blogs/reported');
      if (!blogs.length) {
        tbody.innerHTML = '<tr class="table-row"><td colspan="7">No reported blogs found</td></tr>';
        return;
      }

      tbody.innerHTML = blogs.map((blog) => {
        const date = blog.created_at ? new Date(blog.created_at).toLocaleDateString() : '-';
        const author = `${blog.first_name || ''} ${blog.last_name || ''}`.trim();

        return `
          <tr class="table-row">
            <td class="blog-id">#${blog.id}</td>
            <td class="blog-name">${this.escapeHtml(blog.title || '-')}</td>
            <td class="user-info"><div class="user-name">${this.escapeHtml(author)}</div></td>
            <td class="date-placed">${this.escapeHtml(date)}</td>
            <td><span class="status-badge reported">${this.escapeHtml(blog.status || '-')}</span></td>
            <td>${blog.report_count || 0}</td>
            <td class="actions">
              <button class="action-btn remove-btn" data-delete-blog-id="${blog.id}" type="button">Delete</button>
            </td>
          </tr>
        `;
      }).join('');
    } catch (error) {
      tbody.innerHTML = '<tr class="table-row"><td colspan="7">Failed to load reported blogs</td></tr>';
      this.showToast(error.message, 'error');
    }
  }

  async loadClubs() {
    const tbody = document.getElementById('clubs-tbody');
    if (!tbody) {
      return;
    }

    try {
      const clubs = await this.apiGet('/api/admin/community/clubs/list');
      if (!clubs.length) {
        tbody.innerHTML = '<tr class="table-row"><td colspan="6">No clubs found</td></tr>';
        return;
      }

      tbody.innerHTML = clubs.map((club) => {
        const creator = `${club.creator_first_name || ''} ${club.creator_last_name || ''}`.trim();

        return `
          <tr class="table-row">
            <td class="post-id">#${club.id}</td>
            <td class="post-name">${this.escapeHtml(club.name || '-')}</td>
            <td>${this.escapeHtml(creator)}</td>
            <td>${club.member_count || 0}</td>
            <td><span class="status-badge active">${this.escapeHtml(club.status || '-')}</span></td>
            <td class="actions">
              <button class="action-btn remove-btn" data-delete-club-id="${club.id}" type="button">Delete</button>
            </td>
          </tr>
        `;
      }).join('');
    } catch (error) {
      tbody.innerHTML = '<tr class="table-row"><td colspan="6">Failed to load clubs</td></tr>';
      this.showToast(error.message, 'error');
    }
  }

  async loadEvents() {
    const tbody = document.getElementById('events-tbody');
    if (!tbody) {
      return;
    }

    try {
      const events = await this.apiGet('/api/admin/community/events/list');
      if (!events.length) {
        tbody.innerHTML = '<tr class="table-row"><td colspan="6">No events found</td></tr>';
        return;
      }

      tbody.innerHTML = events.map((eventItem) => {
        const organizer = eventItem.club_name
          ? eventItem.club_name
          : `${eventItem.creator_first_name || ''} ${eventItem.creator_last_name || ''}`.trim();
        const date = eventItem.event_date ? new Date(eventItem.event_date).toLocaleDateString() : '-';

        return `
          <tr class="table-row">
            <td class="event-id">#${eventItem.id}</td>
            <td class="event-name">${this.escapeHtml(eventItem.title || '-')}</td>
            <td class="organizer">${this.escapeHtml(organizer)}</td>
            <td class="event-date">${this.escapeHtml(date)}</td>
            <td><span class="status-badge upcoming">${this.escapeHtml(eventItem.status || '-')}</span></td>
            <td class="actions">
              <button class="action-btn remove-btn" data-delete-event-id="${eventItem.id}" type="button">Delete</button>
            </td>
          </tr>
        `;
      }).join('');
    } catch (error) {
      tbody.innerHTML = '<tr class="table-row"><td colspan="6">Failed to load events</td></tr>';
      this.showToast(error.message, 'error');
    }
  }

  escapeHtml(value) {
    if (value === null || value === undefined) {
      return '';
    }

    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  showToast(message, type = 'info') {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
      existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = 'toast';

    let bg = 'var(--secondary-color)';
    if (type === 'success') {
      bg = '#059669';
    } else if (type === 'error') {
      bg = '#DC2626';
    }

    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${bg};
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

    setTimeout(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    }, 10);

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

document.addEventListener('DOMContentLoaded', () => {
  new CommunityAdminPage();
});
