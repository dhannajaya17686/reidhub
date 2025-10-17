
// ===== DATABASE INTEGRATION TEMPLATE =====
// Replace these functions with actual API calls to your backend

// Dummy data - replace with database queries
const DUMMY_LOST_ITEMS = [
    {
        id: 'L001',
        item: 'BackPack',
        description: 'Lost on Bawana on October 26, 2024. Black backpack with a laptop and notebooks.',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-10-26',
        time: '08:00 P.M',
        location: 'Bawana(UCSC Canteen)',
        email: 'dhananjayamudalige@gmail.com',
        mobile: '+94 771234567',
        status: 'active',
        images: [],
        type: 'lost'
    },
    {
        id: 'L002',
        item: 'Umbrella',
        description: 'Found at Bawana yesterday',
        user: 'Amasha Ranasinghe',
        userYear: '3rd Year Undergraduate',
        date: '2024-10-25',
        time: '02:00 P.M',
        location: 'Library',
        email: 'amasha@gmail.com',
        mobile: '+94 771234568',
        status: 'resolved',
        images: [],
        type: 'lost'
    }
];

const DUMMY_FOUND_ITEMS = [
    {
        id: 'F001',
        item: 'Wallet',
        description: 'Found a black wallet near the library',
        user: 'John Smith',
        userYear: '1st Year Undergraduate',
        date: '2024-10-27',
        time: '10:00 A.M',
        location: 'Library Entrance',
        email: 'john@gmail.com',
        mobile: '+94 771234569',
        status: 'active',
        images: [],
        type: 'found'
    }
];

const DUMMY_REPORTS = [
    {
        id: 12345,
        item: 'Laptop',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-26',
        time: '10:00 A.M',
        location: 'Library',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'missing',
        description: 'Lost my laptop near the library. It is a Dell Inspiron with a blue case.',
        type: 'lost',
        images: []
    },
    {
        id: 12346,
        item: 'Wallet',
        user: 'Amasha',
        userYear: '3rd Year Undergraduate',
        date: '2024-07-25',
        time: '02:00 P.M',
        location: 'Canteen',
        email: 'amasha@gmail.com',
        mobile: '+94 771234568',
        status: 'returned',
        description: 'Found a black wallet near the canteen.',
        type: 'found',
        images: []
    },
    {
        id: 12347,
        item: 'Keys',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-24',
        time: '03:00 P.M',
        location: 'Parking Lot',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'collected',
        description: 'Found a set of keys with a blue keychain.',
        type: 'found',
        images: []
    },
    {
        id: 12348,
        item: 'Phone',
        user: 'Jonty Mischel',
        userYear: '1st Year Undergraduate',
        date: '2024-07-23',
        time: '11:00 A.M',
        location: 'Lab',
        email: 'jonty@gmail.com',
        mobile: '+94 771234569',
        status: 'missing',
        description: 'Lost my phone in the computer lab.',
        type: 'lost',
        images: []
    },
    {
        id: 12349,
        item: 'Backpack',
        user: 'Abhishek',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-22',
        time: '04:00 P.M',
        location: 'Sports Ground',
        email: 'abhishek@gmail.com',
        mobile: '+94 771234570',
        status: 'returned',
        description: 'Found a black backpack near the sports ground.',
        type: 'found',
        images: []
    },
    {
        id: 12350,
        item: 'ID Card',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-21',
        time: '09:00 A.M',
        location: 'Main Gate',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'collected',
        description: 'Found an ID card near the main gate.',
        type: 'found',
        images: []
    },
    {
        id: 12351,
        item: 'Textbooks',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-20',
        time: '01:00 P.M',
        location: 'Library',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'pending',
        description: 'Lost my Data Structures textbook in the library.',
        type: 'lost',
        images: []
    },
    {
        id: 12352,
        item: 'Charger',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-19',
        time: '05:00 P.M',
        location: 'Lecture Hall',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'returned',
        description: 'Found a laptop charger in lecture hall.',
        type: 'found',
        images: []
    },
    {
        id: 12353,
        item: 'Headphones',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-18',
        time: '12:00 P.M',
        location: 'Canteen',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'collected',
        description: 'Found wireless headphones in the canteen.',
        type: 'found',
        images: []
    },
    {
        id: 12354,
        item: 'Umbrella',
        user: 'Dhananjaya Mudalige',
        userYear: '2nd Year Undergraduate',
        date: '2024-07-17',
        time: '06:00 P.M',
        location: 'Bawana',
        email: 'dhananjaya@gmail.com',
        mobile: '+94 771234567',
        status: 'pending',
        description: 'Lost my blue umbrella at Bawana canteen.',
        type: 'lost',
        images: []
    }
];

// ===== DATABASE INTEGRATION FUNCTIONS =====
// TODO: Replace these with actual API calls

async function fetchLostItems(filter = 'all') {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/lost-items?filter=${filter}`);
    // return await response.json();
    
    if (filter === 'all') return DUMMY_LOST_ITEMS;
    return DUMMY_LOST_ITEMS.filter(item => item.status === filter);
}

async function fetchFoundItems(filter = 'all') {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/found-items?filter=${filter}`);
    // return await response.json();
    
    if (filter === 'all') return DUMMY_FOUND_ITEMS;
    return DUMMY_FOUND_ITEMS.filter(item => item.status === filter);
}

async function fetchLFReports(filter = 'all') {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/lf-reports?filter=${filter}`);
    // return await response.json();
    
    if (filter === 'all') return DUMMY_REPORTS;
    if (filter === 'lost') return DUMMY_REPORTS.filter(r => r.type === 'lost');
    if (filter === 'found') return DUMMY_REPORTS.filter(r => r.type === 'found');
    return DUMMY_REPORTS.filter(r => r.status === filter);
}

async function fetchItemDetails(itemId, type) {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/lf-items/${itemId}?type=${type}`);
    // return await response.json();
    
    // First check in reports (which have full details)
    const report = DUMMY_REPORTS.find(r => r.id == itemId);
    if (report) return report;
    
    // Then check in lost/found items
    const allItems = [...DUMMY_LOST_ITEMS, ...DUMMY_FOUND_ITEMS];
    return allItems.find(item => item.id === itemId);
}

async function updateItemStatus(itemId, status) {
    // TODO: Replace with actual API call
    // Example: await fetch(`/api/lf-items/${itemId}/status`, {
    //     method: 'PUT',
    //     body: JSON.stringify({ status }),
    //     headers: { 'Content-Type': 'application/json' }
    // });
    
    console.log(`Updating item ${itemId} status to ${status}`);
}

async function pinItemPost(itemId) {
    // TODO: Replace with actual API call
    console.log(`Pinning item ${itemId}`);
}

async function deleteItem(itemId) {
    // TODO: Replace with actual API call
    console.log(`Deleting item ${itemId}`);
}

// ===== UI RENDERING FUNCTIONS =====

let currentFilter = 'all';
let currentItem = null;

async function loadLostItems() {
    const items = await fetchLostItems();
    const activeContainer = document.getElementById('lost-items-active');
    const resolvedContainer = document.getElementById('lost-items-resolved');
    
    activeContainer.innerHTML = '';
    resolvedContainer.innerHTML = '';

    items.forEach(item => {
        const card = createItemCard(item);
        if (item.status === 'active') {
            activeContainer.appendChild(card);
        } else {
            resolvedContainer.appendChild(card);
        }
    });
}

async function loadFoundItems() {
    const items = await fetchFoundItems();
    const activeContainer = document.getElementById('found-items-active');
    const resolvedContainer = document.getElementById('found-items-resolved');
    
    activeContainer.innerHTML = '';
    resolvedContainer.innerHTML = '';

    items.forEach(item => {
        const card = createItemCard(item);
        if (item.status === 'active') {
            activeContainer.appendChild(card);
        } else {
            resolvedContainer.appendChild(card);
        }
    });
}

function createItemCard(item) {
    const card = document.createElement('div');
    card.className = 'item-card';
    card.onclick = () => openItemModal(item.id, item.type);
    
    card.innerHTML = `
        <div class="item-avatar"></div>
        <div class="item-info">
            <div class="item-title">${item.item}</div>
            <div class="item-description">${item.description}</div>
        </div>
        <div class="item-image"></div>
    `;
    
    return card;
}

async function loadLFReports(filter = 'all') {
    currentFilter = filter;
    const reports = await fetchLFReports(filter);
    const tbody = document.getElementById('lf-reports-table');
    tbody.innerHTML = '';

    reports.forEach(report => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><a class="item-link">#${report.id}</a></td>
            <td>${report.item}</td>
            <td><a class="item-link">${report.user}</a></td>
            <td>${report.date}</td>
            <td class="status-${report.status}">${report.status.charAt(0).toUpperCase() + report.status.slice(1)}</td>
            <td><a class="action-link" onclick="openItemModal('${report.id}', '${report.type}')">View</a></td>
        `;
        tbody.appendChild(row);
    });
}

async function openItemModal(itemId, type) {
    const item = await fetchItemDetails(itemId, type);
    if (!item) return;

    currentItem = item;

    // Update modal content
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

    // Load images
    const imagesContainer = document.getElementById('modal-images');
    imagesContainer.innerHTML = '';
    for (let i = 0; i < 3; i++) {
        const img = document.createElement('div');
        img.className = 'modal-image';
        imagesContainer.appendChild(img);
    }

    // Update modal actions based on status
    const actionsContainer = document.getElementById('modal-actions');
    actionsContainer.innerHTML = '';

    if (item.status === 'pending') {
        // Pending approval - show Approve Post button only
        actionsContainer.innerHTML = `
            <button class="modal-btn btn-success" onclick="approvePost()">Approve Post</button>
        `;
    } else if (item.status === 'missing' || item.status === 'active') {
        // Lost item (active/missing) - show full action buttons
        actionsContainer.innerHTML = `
            <button class="modal-btn btn-primary" onclick="pinPost()">Pin post</button>
            <button class="modal-btn btn-success" onclick="markAsResolved()">Mark as resolved</button>
            <button class="modal-btn btn-secondary" onclick="editPost()">Edit Post</button>
            <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
            <button class="modal-btn btn-primary" onclick="contactOwner()">Contact Owner</button>
        `;
    } else {
        // Other statuses (returned, collected, resolved)
        actionsContainer.innerHTML = `
            <button class="modal-btn btn-primary" onclick="pinPost()">Pin post</button>
            <button class="modal-btn btn-secondary" onclick="editPost()">Edit Post</button>
            <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
            <button class="modal-btn btn-primary" onclick="contactOwner()">Contact Owner</button>
        `;
    }

    // Show modal
    document.getElementById('item-modal').classList.add('active');
}

function closeModal() {
    document.getElementById('item-modal').classList.remove('active');
    currentItem = null;
}

function switchNav(sectionId, event) {
    event.preventDefault();
    
    const sections = document.querySelectorAll('.lf-section');
    sections.forEach(section => section.classList.remove('active'));

    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));

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

function switchModule(moduleName, event) {
    event.preventDefault();
    
    const navTabs = document.querySelectorAll('.nav-tab');
    navTabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    console.log('Switched to module:', moduleName);
}

function filterLFReports(filter) {
    const tabs = document.querySelectorAll('#lf-reports .filter-tabs .filter-tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    loadLFReports(filter);
}

// Modal action handlers
async function approvePost() {
    if (!currentItem) return;
    
    if (confirm(`Approve post for ${currentItem.item}?`)) {
        await updateItemStatus(currentItem.id, 'active');
        alert('Post approved successfully!');
        closeModal();
        loadLostItems();
        loadFoundItems();
        loadLFReports(currentFilter);
    }
}

async function pinPost() {
    if (!currentItem) return;
    
    if (confirm(`Pin post for ${currentItem.item}?`)) {
        await pinItemPost(currentItem.id);
        alert('Post pinned successfully!');
        closeModal();
    }
}

async function markAsResolved() {
    if (!currentItem) return;
    
    if (confirm(`Mark ${currentItem.item} as resolved?`)) {
        await updateItemStatus(currentItem.id, 'resolved');
        alert('Item marked as resolved!');
        closeModal();
        loadLostItems();
        loadFoundItems();
        loadLFReports(currentFilter);
    }
}

function editPost() {
    if (!currentItem) return;
    alert('Edit functionality would open an edit form here');
}

async function removePost() {
    if (!currentItem) return;
    
    if (confirm(`Are you sure you want to remove ${currentItem.item}?`)) {
        await deleteItem(currentItem.id);
        alert('Item removed successfully!');
        closeModal();
        loadLostItems();
        loadFoundItems();
        loadLFReports(currentFilter);
    }
}

function contactOwner() {
    if (!currentItem) return;
    alert(`Contact ${currentItem.user} at ${currentItem.email} or ${currentItem.mobile}`);
}

// Close modal when clicking outside
document.getElementById('item-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Search functionality
document.getElementById('lf-report-search')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#lf-reports-table tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    loadLostItems();
    loadFoundItems();
    loadLFReports('all');
});
