const DUMMY_SELLER_REQUESTS = [
    {
        id: 12345,
        item: 'UCSC Tshirt',
        user: 'Dhananjaya Mudalige',
        date: '2025-07-26',
        status: 'pending',
        price: 2000,
        stock: 'In Stock',
        description: 'University of Colombo School of Computing printed tshirt. Made from baby peaque material. Dark blue. Front with curved white lines, collar white borders and hand borders. Premium quality.',
        images: [],
        reviews: [
            {
                author: "Student's Union - Nimesha Madushan",
                rating: 5,
                text: 'This tshirt is really amazing, durable and premium quality made.'
            }
        ],
        avgRating: 4.0,
        reviewCount: 10
    },
    {
        id: 12346,
        item: 'UCSC Wrist band',
        user: 'Amasha',
        date: '2025-07-25',
        status: 'pending',
        price: 500,
        stock: 'In Stock',
        description: 'Official UCSC wrist band for events and identification.',
        images: [],
        reviews: [],
        avgRating: 0,
        reviewCount: 0
    },
    {
        id: 12347,
        item: 'DSA Book',
        user: 'Dhananjaya Mudalige',
        date: '2025-07-26',
        status: 'approved',
        price: 1500,
        stock: 'In Stock',
        description: 'Data Structures and Algorithms textbook in excellent condition.',
        images: [],
        reviews: [],
        avgRating: 0,
        reviewCount: 0
    },
    {
        id: 12348,
        item: 'Laptop Charger',
        user: 'Amasha',
        date: '2025-07-25',
        status: 'approved',
        price: 3000,
        stock: 'In Stock',
        description: 'Universal laptop charger compatible with most brands.',
        images: [],
        reviews: [],
        avgRating: 0,
        reviewCount: 0
    }
];

const DUMMY_BUYER_REQUESTS = [
    {
        id: 12345,
        item: 'UCSC Tshirt',
        user: 'Dhananjaya Mudalige',
        date: '2025-07-26',
        status: 'reported',
        reportReason: 'Suspicious listing'
    },
    {
        id: 12346,
        item: 'UCSC Wrist band',
        user: 'Amasha',
        date: '2025-07-25',
        status: 'reported',
        reportReason: 'Price manipulation'
    }
];

const DUMMY_CHAT_MESSAGES = [
    {
        id: 1,
        userId: 'dhananjaya',
        userName: 'Dhananjaya Mudalige',
        preview: 'Hey, Did you dispatch my order?',
        timestamp: 'Today | 08:00 AM',
        messages: [
            { from: 'user', text: 'Hey, Did you dispatch my order?' },
            { from: 'admin', text: 'Let me check that for you right away.' }
        ]
    },
    {
        id: 2,
        userId: 'amasha',
        userName: 'Amasha Ranasinghe',
        preview: 'Thanks for the update!',
        timestamp: 'Today | 09:30 AM',
        messages: [
            { from: 'user', text: 'Is my item approved?' },
            { from: 'admin', text: 'Oh, hello! All perfectly. I will check it and get back to you soon' },
            { from: 'user', text: 'Thanks for the update!' }
        ]
    }
];

// ===== DATABASE INTEGRATION FUNCTIONS =====
// TODO: Replace these with actual API calls

async function fetchSellerRequests(filter = 'all') {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/seller-requests?filter=${filter}`);
    // return await response.json();
    
    if (filter === 'all') return DUMMY_SELLER_REQUESTS;
    return DUMMY_SELLER_REQUESTS.filter(req => req.status === filter);
}

async function fetchBuyerRequests() {
    // TODO: Replace with actual API call
    // Example: const response = await fetch('/api/buyer-requests');
    // return await response.json();
    
    return DUMMY_BUYER_REQUESTS;
}

async function fetchProductDetails(itemId) {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/products/${itemId}`);
    // return await response.json();
    
    return DUMMY_SELLER_REQUESTS.find(req => req.id === itemId);
}

async function fetchChatList() {
    // TODO: Replace with actual API call
    // Example: const response = await fetch('/api/chats');
    // return await response.json();
    
    return DUMMY_CHAT_MESSAGES;
}

async function fetchChatMessages(userId) {
    // TODO: Replace with actual API call
    // Example: const response = await fetch(`/api/chats/${userId}`);
    // return await response.json();
    
    const chat = DUMMY_CHAT_MESSAGES.find(c => c.userId === userId);
    return chat ? chat.messages : [];
}

async function updateRequestStatus(itemId, status) {
    // TODO: Replace with actual API call
    // Example: await fetch(`/api/seller-requests/${itemId}/status`, {
    //     method: 'PUT',
    //     body: JSON.stringify({ status }),
    //     headers: { 'Content-Type': 'application/json' }
    // });
    
    console.log(`Updating item ${itemId} status to ${status}`);
    const item = DUMMY_SELLER_REQUESTS.find(req => req.id === itemId);
    if (item) item.status = status;
}

async function sendChatMessage(userId, message) {
    // TODO: Replace with actual API call
    // Example: await fetch(`/api/chats/${userId}/messages`, {
    //     method: 'POST',
    //     body: JSON.stringify({ message }),
    //     headers: { 'Content-Type': 'application/json' }
    // });
    
    console.log(`Sending message to ${userId}: ${message}`);
}

// ===== UI RENDERING FUNCTIONS =====

let currentFilter = 'all';
let currentProduct = null;

async function loadSellerRequests(filter = 'all') {
    currentFilter = filter;
    const requests = await fetchSellerRequests(filter);
    const tbody = document.getElementById('seller-requests-table');
    tbody.innerHTML = '';

    requests.forEach(req => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><a class="item-link">#${req.id}</a></td>
            <td>${req.item}</td>
            <td><a class="item-link">${req.user}</a></td>
            <td>${req.date}</td>
            <td class="status-${req.status}">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</td>
            <td><a class="action-link" onclick="viewProduct(${req.id})">View</a></td>
        `;
        tbody.appendChild(row);
    });
}

async function loadBuyerRequests() {
    const requests = await fetchBuyerRequests();
    const tbody = document.getElementById('buyer-requests-table');
    tbody.innerHTML = '';

    requests.forEach(req => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><a class="item-link">#${req.id}</a></td>
            <td>${req.item}</td>
            <td><a class="item-link">${req.user}</a></td>
            <td>${req.date}</td>
            <td class="status-${req.status}">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</td>
            <td><a class="action-link" onclick="viewProduct(${req.id})">View</a></td>
        `;
        tbody.appendChild(row);
    });
}

async function viewProduct(itemId) {
    const product = await fetchProductDetails(itemId);
    if (!product) return;

    currentProduct = product;

    // Update product details
    document.getElementById('product-title').textContent = product.item;
    document.getElementById('product-price').textContent = `Rs.${product.price}`;
    document.getElementById('product-stock').textContent = product.stock;
    document.getElementById('product-description').textContent = product.description;
    document.getElementById('product-breadcrumb').textContent = product.item;
    document.getElementById('avg-rating').textContent = product.avgRating.toFixed(1);
    document.getElementById('review-count').textContent = product.reviewCount;

    // Load reviews
    const reviewsList = document.getElementById('reviews-list');
    reviewsList.innerHTML = '';
    product.reviews.forEach(review => {
        const stars = '⭐'.repeat(review.rating);
        const reviewDiv = document.createElement('div');
        reviewDiv.className = 'review-item';
        reviewDiv.innerHTML = `
            <div class="review-author">👤 ${review.author}</div>
            <div class="review-rating">${stars}</div>
            <div class="review-text">${review.text}</div>
            <div class="action-buttons">
                <button class="action-btn">👍</button>
                <button class="action-btn">👎</button>
            </div>
        `;
        reviewsList.appendChild(reviewDiv);
    });

    // Switch to product detail view
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.getElementById('product-detail').classList.add('active');
}

async function loadChatList() {
    const chats = await fetchChatList();
    const container = document.getElementById('messages-list-container');
    container.innerHTML = '';

    chats.forEach((chat, index) => {
        const chatItem = document.createElement('div');
        chatItem.className = `message-item ${index === 0 ? 'active' : ''}`;
        chatItem.onclick = () => loadChatMessages(chat.userId, chat.userName);
        chatItem.innerHTML = `
            <div class="message-avatar">👤</div>
            <div class="message-name">${chat.userName}</div>
            <div class="message-preview">${chat.preview}</div>
            <div class="message-time">${chat.timestamp}</div>
        `;
        container.appendChild(chatItem);
    });

    // Load first chat by default
    if (chats.length > 0) {
        loadChatMessages(chats[0].userId, chats[0].userName);
    }
}

async function loadChatMessages(userId, userName) {
    const messages = await fetchChatMessages(userId);
    const container = document.getElementById('chat-messages-container');
    document.getElementById('chat-user-name').textContent = `👤 ${userName}`;
    
    container.innerHTML = '';
    messages.forEach(msg => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${msg.from === 'admin' ? 'own' : ''}`;
        msgDiv.innerHTML = `<div class="message-bubble">${msg.text}</div>`;
        container.appendChild(msgDiv);
    });

    // Scroll to bottom
    container.scrollTop = container.scrollHeight;

    // Update active state
    document.querySelectorAll('.message-item').forEach(item => {
        item.classList.remove('active');
        if (item.textContent.includes(userName)) {
            item.classList.add('active');
        }
    });
}

// ===== EVENT HANDLERS =====

function switchNav(navId, event) {
    event.preventDefault();
    
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.remove('active'));

    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));

    const selectedSection = document.getElementById(navId);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }

    event.target.classList.add('active');

    // Load data based on section
    if (navId === 'seller-requests') {
        loadSellerRequests(currentFilter);
    } else if (navId === 'buyer-requests') {
        loadBuyerRequests();
    } else if (navId === 'seller-chat') {
        loadChatList();
    }
}

function switchModule(moduleName, event) {
    event.preventDefault();
    
    const navTabs = document.querySelectorAll('.nav-tab');
    navTabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    console.log('Switched to module:', moduleName);
}

function filterRequests(filter) {
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    loadSellerRequests(filter);
}

function backToSellerRequests() {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.getElementById('seller-requests').classList.add('active');
}

async function handleApproval() {
    if (!currentProduct) return;
    
    if (confirm(`Approve request for ${currentProduct.item}?`)) {
        await updateRequestStatus(currentProduct.id, 'approved');
        alert('Request approved successfully!');
        backToSellerRequests();
        loadSellerRequests(currentFilter);
    }
}

async function handleRejection() {
    if (!currentProduct) return;
    
    if (confirm(`Reject request for ${currentProduct.item}?`)) {
        await updateRequestStatus(currentProduct.id, 'rejected');
        alert('Request rejected.');
        backToSellerRequests();
        loadSellerRequests(currentFilter);
    }
}

function contactSeller() {
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    document.querySelector('.nav-link:nth-child(3)').classList.add('active');
    
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.getElementById('seller-chat').classList.add('active');
    
    loadChatList();
}

async function sendMessage() {
    const input = document.getElementById('chat-message-input');
    const message = input.value.trim();
    
    if (!message) return;

    // Get current user from chat header
    const userName = document.getElementById('chat-user-name').textContent.replace('👤 ', '');
    const userId = userName.toLowerCase().replace(' ', '');

    await sendChatMessage(userId, message);

    // Add message to UI
    const container = document.getElementById('chat-messages-container');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-message own';
    msgDiv.innerHTML = `<div class="message-bubble">${message}</div>`;
    container.appendChild(msgDiv);

    input.value = '';
    container.scrollTop = container.scrollHeight;
}

// Allow Enter key to send messages
document.addEventListener('DOMContentLoaded', () => {
    const chatInput = document.getElementById('chat-message-input');
    if (chatInput) {
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    loadSellerRequests('all');
});