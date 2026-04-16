# Marketplace Order Chat Implementation

## Overview
A real-time chat system for buyers and sellers to communicate within orders. Messages are stored in JSON files per order with automatic access control and comprehensive security measures.

## Files Created/Modified

### 1. **Controller: `app/controllers/Marketplace/MarketplaceChatController.php`**
- **`showOrderChat()`** - Display chat interface for an order (GET)
- **`sendMessage()`** - Handle new message submission (POST)
- **`getMessages()`** - Polling endpoint for real-time message retrieval (POST)
- **`getOrderDetails()`** - Private helper to fetch order with buyer/seller info
- **`getChatMessages()`** - Private helper to read messages from JSON file
- **`addMessageToChat()`** - Private helper to append message to JSON file with file locking
- **`cleanupOldChats()`** - Optional cleanup for cancelled orders older than 90 days

**Key Features:**
- Full access control: Users can only view/send messages for their own orders
- File locking for thread-safe message writes
- Message validation: 5000 character limit, non-empty content
- Automatic chat file creation with proper permissions
- Comprehensive error handling and logging

### 2. **View: `app/views/User/marketplace/order-chat-view.php`**
**Split-panel layout:**
- **Left sidebar** (340px fixed on desktop, responsive on mobile):
  - Order ID and status with colored badges
  - Product thumbnail and title
  - Buyer/seller names with "You" badge
  - Order date
  
- **Right chat panel** (flexible):
  - Messages container with auto-scroll
  - Real-time polling (every 2 seconds)
  - Message template with sender name and timestamp
  - Input textarea with character counter (max 5000)
  - Send button with icon

**JavaScript Features:**
- `pollMessages()` - Fetches new messages every 2 seconds
- `sendMessage()` - Submits message via AJAX without page reload
- Smart auto-scroll: Only scrolls to bottom if user is already near bottom
- Keeps only last 1000 messages in memory
- Character count live update
- Loading state management

### 3. **Styles: `public/css/app/user/marketplace/order-chat.css`**
- Grid layout (340px sidebar + 1fr content on desktop)
- Responsive design: Stacks on tablets, single column on mobile
- Message bubbles with timestamps
- Status badge colors matching marketplace theme
- Smooth animations for new messages (slideUp)
- Custom scrollbar styling
- Accessible form elements with focus states
- Touch-friendly buttons and inputs on mobile (44px minimum)

**CSS Variables Used:**
- `--secondary-color` - Primary action color (0466C8)
- `--surface`, `--surface-subtle` - Background colors
- `--border-color`, `--text-primary`, `--text-secondary` - Text colors
- `--space-*` - Spacing scale
- `--radius-*` - Border radius scale
- `--shadow-*` - Shadow effects
- `--transition-smooth`, `--transition-fast` - Animation timing

### 4. **Routes: `app/routes/web.php`**
```php
$routes['/dashboard/marketplace/orders/{id}/chat'] = [
    'GET' => 'Marketplace_MarketplaceChatController@showOrderChat'
];
$routes['/dashboard/marketplace/orders/{id}/chat/send'] = [
    'POST' => 'Marketplace_MarketplaceChatController@sendMessage'
];
$routes['/dashboard/marketplace/orders/{id}/chat/get'] = [
    'POST' => 'Marketplace_MarketplaceChatController@getMessages'
];
```

### 5. **Storage: `storage/filestore/chats/`**
- Directory for storing order chat JSON files
- Naming convention: `order-{id}-chat.json`
- Files are auto-created with proper permissions
- File locking prevents concurrent write issues

## Message JSON Schema

```json
{
  "id": 0,
  "sender_id": 123,
  "sender_name": "John Doe",
  "content": "Is this item still available?",
  "timestamp": 1713264000
}
```

## Access Control Features

1. **Order Verification**: Only participants (buyer or seller) can access chat
2. **Session Authentication**: Requires user login via `Auth_LoginController`
3. **User Role Detection**: Determines if user is buyer or seller
4. **Message Ownership**: Sender ID is automatically set from session
5. **File-level Protection**: JSON files contain only accessible messages

## Further Considerations Implemented

### 1. Access Control ✓
- Verifies user is `buyer_id` OR `seller_id` on order
- Returns 403 Forbidden for unauthorized access
- Validates on both view and message endpoints

### 2. File Permissions ✓
- Automatic directory creation with 0755 permissions
- Chat files created with 0666 permissions (writable by webserver)
- File locking during write operations (LOCK_EX) prevents race conditions
- Graceful handling if files can't be created

### 3. Message Retention ✓
- **Option 1**: Unlimited chat history (default)
  - All messages stored permanently
  - Full audit trail for disputes
  
- **Option 2**: Automatic cleanup available
  - `cleanupOldChats()` removes chats for cancelled orders
  - Only deletes if order cancelled >90 days ago
  - Can be called via cron job for maintenance

### 4. Security & Validation ✓
- HTML escaping in views (`htmlspecialchars()`)
- 5000 character limit per message
- Non-empty content validation
- SQL injection prevention via prepared statements
- CSRF protection via form structure
- Timestamps stored as Unix timestamps (immutable)

### 5. Performance ✓
- Real-time polling every 2 seconds (configurable)
- Last 1000 messages kept in memory (limit to prevent slowdown)
- Efficient JSON file I/O with locks
- Lazy loading: Only fetches new messages since last poll
- File size management: Keeps growing but manageable

### 6. UX Features ✓
- Auto-scroll to latest messages (smart, doesn't jump while reading)
- Character counter with max limit
- Loading indicators
- Responsive design for all devices
- Status badges with color coding
- Order context always visible (sidebar)
- Breadcrumb navigation for clarity
- "You" badge to identify self in participant list

## Usage

### For Buyers/Sellers to Chat:
1. Navigate to "My Orders" page
2. Click on an order
3. Click "Chat" button (would be added to orders page UI)
4. Start messaging in real-time

### API Endpoints:

**View Chat:**
```
GET /dashboard/marketplace/orders/{id}/chat?id={order_id}
```

**Send Message:**
```
POST /dashboard/marketplace/orders/{id}/chat/send
Parameters: order_id, content
```

**Poll Messages:**
```
POST /dashboard/marketplace/orders/{id}/chat/get
Parameters: order_id, last_message_id
Returns: JSON with new messages since last_message_id
```

## Next Steps (Optional Enhancement)

1. Add "Chat" button/link to My Orders page in `Marketplace_MarketplaceUserController`
2. Add message notification/badge count on orders list
3. Add typing indicators ("User is typing...")
4. Add message read receipts
5. Add attachment/image support
6. Implement WebSocket for true real-time instead of polling
7. Add chat search/filter functionality
8. Add automatic message archival after order completion

## Testing Checklist

- [ ] View chat as buyer on owned order
- [ ] View chat as seller on owned order
- [ ] Cannot view chat as unrelated user (403)
- [ ] Send message and see it appear immediately
- [ ] Poll updates show new messages from other user
- [ ] Message persists across page reloads
- [ ] Character counter works and prevents overflow
- [ ] Responsive layout on mobile/tablet
- [ ] File locks prevent message corruption
- [ ] Old cancelled order chats can be cleaned up
- [ ] Access logs show all operations
