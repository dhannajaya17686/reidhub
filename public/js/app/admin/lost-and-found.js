// Add these functions to your existing lost-and-found.js file

// Enhanced navigation functions
function switchNav(sectionId, event) {
  event.preventDefault();
  
  const sections = document.querySelectorAll('.lf-section');
  sections.forEach(section => section.classList.remove('active'));

  const navTabs = document.querySelectorAll('.nav-tab');
  navTabs.forEach(tab => tab.classList.remove('active'));

  const selectedSection = document.getElementById(sectionId);
  if (selectedSection) {
    selectedSection.classList.add('active');
  }

  event.target.classList.add('active');

  // Load data based on section
  if (sectionId === 'lf-lost-items') {
    loadLostItems();
  } else if (sectionId === 'lf-found-items') {
    loadFoundItems();
  } else if (sectionId === 'lf-reports') {
    loadLFReports('all');
  }
}

// Enhanced filter functions
function filterLostItems(filter) {
  const tabs = document.querySelectorAll('#lf-lost-items .filter-tab');
  tabs.forEach(tab => tab.classList.remove('active'));
  event.target.classList.add('active');

  const grids = document.querySelectorAll('#lf-lost-items .items-grid');
  grids.forEach(grid => grid.style.display = 'none');

  const targetGrid = document.getElementById(`lost-items-${filter}`);
  if (targetGrid) {
    targetGrid.style.display = 'grid';
  }
}

function filterFoundItems(filter) {
  const tabs = document.querySelectorAll('#lf-found-items .filter-tab');
  tabs.forEach(tab => tab.classList.remove('active'));
  event.target.classList.add('active');

  const grids = document.querySelectorAll('#lf-found-items .items-grid');
  grids.forEach(grid => grid.style.display = 'none');

  const targetGrid = document.getElementById(`found-items-${filter}`);
  if (targetGrid) {
    targetGrid.style.display = 'grid';
  }
}

// New report modal functions
function openNewReportModal() {
  document.getElementById('new-report-modal').classList.add('active');
}

function closeNewReportModal() {
  document.getElementById('new-report-modal').classList.remove('active');
}

// Enhanced item card creation with avatars
function createItemCard(item) {
  const card = document.createElement('div');
  card.className = 'item-card';
  card.onclick = () => openItemModal(item.id, item.type);
  
  // Create initials for avatar
  const initials = item.user.split(' ').map(name => name.charAt(0)).join('').substring(0, 2);
  
  card.innerHTML = `
    <div class="item-avatar">${initials}</div>
    <div class="item-info">
      <div class="item-title">${item.item}</div>
      <div class="item-description">${item.description}</div>
    </div>
    <div class="item-image"></div>
  `;
  
  return card;
}

// Enhanced modal handling
async function openItemModal(itemId, type) {
  const item = await fetchItemDetails(itemId, type);
  if (!item) return;

  currentItem = item;

  // Update modal content with avatars
  const userInitials = item.user.split(' ').map(name => name.charAt(0)).join('').substring(0, 2);
  document.querySelector('.modal-avatar').textContent = userInitials;
  
  document.getElementById('modal-user-name').textContent = item.user;
  document.getElementById('modal-user-year').textContent = item.userYear || '2nd Year Undergraduate';
  document.getElementById('modal-item-title').textContent = item.item;
  document.getElementById('modal-description').textContent = item.description;
  document.getElementById('modal-location').textContent = item.location;
  document.getElementById('modal-location-2').textContent = item.location;
  document.getElementById('modal-date').textContent = item.date;
  document.getElementById('modal-time').textContent = item.time;
  document.getElementById('modal-email').textContent = item.email;
  document.getElementById('modal-mobile').textContent = item.mobile;

  // Update modal actions based on status (keep your existing logic)
  const actionsContainer = document.getElementById('modal-actions');
  actionsContainer.innerHTML = '';

  if (item.status === 'pending') {
    actionsContainer.innerHTML = `
      <button class="modal-btn btn-success" onclick="approvePost()">Approve Post</button>
    `;
  } else if (item.status === 'missing' || item.status === 'active') {
    actionsContainer.innerHTML = `
      <button class="modal-btn btn-primary" onclick="pinPost()">Pin post</button>
      <button class="modal-btn btn-success" onclick="markAsResolved()">Mark as resolved</button>
      <button class="modal-btn btn-secondary" onclick="editPost()">Edit Post</button>
      <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
      <button class="modal-btn btn-primary" onclick="contactOwner()">Contact Owner</button>
    `;
  } else {
    actionsContainer.innerHTML = `
      <button class="modal-btn btn-primary" onclick="pinPost()">Pin post</button>
      <button class="modal-btn btn-secondary" onclick="editPost()">Edit Post</button>
      <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
      <button class="modal-btn btn-primary" onclick="contactOwner()">Contact Owner</button>
    `;
  }

  document.getElementById('item-modal').classList.add('active');
}

// Close modals when clicking outside
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    if (e.target.id === 'item-modal') {
      closeModal();
    } else if (e.target.id === 'new-report-modal') {
      closeNewReportModal();
    }
  }
});

// Add your existing JavaScript functions here...
// (All the existing functions from your lost-and-found.js file)