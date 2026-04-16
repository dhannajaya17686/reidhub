<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Marketplace_MarketplaceChatController extends Controller
{
    // Chat file storage path
    private $chats_dir;
    private $db;
    
    public function __construct()
    {
        $this->chats_dir = __DIR__ . '/../../..' . '/storage/filestore/chats';
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Display chat page for a specific order
     * Accessible only by order participants (buyer or seller)
     */
    public function showOrderChat()
    {
        $user = Auth_LoginController::getSessionUser(true);
        // Get order ID from route parameter
        $order_id = (int)($_GET['id'] ?? 0);
        
        Logger::info("showOrderChat: user_id={$user['id']}, order_id={$order_id}");
        
        if ($order_id <= 0) {
            Logger::warning("showOrderChat: Invalid order_id={$order_id}");
            http_response_code(404);
            exit('Invalid order ID');
        }
        
        // Fetch order with buyer and seller info
        $order = $this->getOrderDetails($order_id);
        
        if (!$order) {
            Logger::warning("showOrderChat: Order not found for order_id={$order_id}");
            http_response_code(404);
            exit('Order not found');
        }
        
        // Verify user is participant (buyer or seller)
        if ($user['id'] != $order['buyer_id'] && $user['id'] != $order['seller_id']) {
            Logger::warning("showOrderChat: Access denied - user_id={$user['id']} not participant of order_id={$order_id}");
            http_response_code(403);
            exit('Access denied: You are not a participant in this order');
        }
        
        // Determine user role in this chat
        $user_role = ($user['id'] == $order['buyer_id']) ? 'buyer' : 'seller';
        Logger::info("showOrderChat: User role determined as {$user_role}");
        
        // Get chat history
        $messages = $this->getChatMessages($order_id);
        
        Logger::info("showOrderChat: Rendering chat view with " . count($messages) . " existing messages");
        
        $this->viewApp(
            '/User/marketplace/order-chat-view',
            [
                'user' => $user,
                'order' => $order,
                'messages' => $messages,
                'user_role' => $user_role,
            ],
            'Order Chat - ReidHub Marketplace'
        );
    }
    
    /**
     * Handle message submission
     * POST request with message content
     * Returns JSON response
     */
    public function sendMessage()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $user = Auth_LoginController::getSessionUser(true);
            $order_id = (int)($_POST['order_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            
            Logger::info("sendMessage: order_id={$order_id}, user_id={$user['id']}, content_length=" . strlen($content));
            
            // Validate input
            if ($order_id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
                exit();
            }
            
            if (empty($content)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                exit();
            }
            
            // Limit message length
            if (strlen($content) > 5000) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Message too long (max 5000 characters)']);
                exit();
            }
            
            // Verify user is order participant
            $order = $this->getOrderDetails($order_id);
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                exit();
            }
            
            if ($user['id'] != $order['buyer_id'] && $user['id'] != $order['seller_id']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit();
            }
            
            // Add message to chat file
            $message = $this->addMessageToChat($order_id, $user['id'], $user['first_name'], $content);
            
            if ($message) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save message']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
        exit();
    }
    
    /**
     * Poll for new messages (AJAX for real-time updates)
     * Returns JSON list of messages since last_message_id
     */
    public function getMessages()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $user = Auth_LoginController::getSessionUser(true);
            $order_id = (int)($_POST['order_id'] ?? 0);
            $last_message_id = (int)($_POST['last_message_id'] ?? -1);
            
            Logger::info("getMessages: order_id={$order_id}, user_id={$user['id']}, last_message_id={$last_message_id}");
            
            if ($order_id <= 0) {
                Logger::warning("getMessages: Invalid order_id={$order_id}");
                http_response_code(400);
                echo json_encode(['success' => false, 'messages' => []]);
                exit();
            }
            
            // Verify user is order participant
            $order = $this->getOrderDetails($order_id);
            
            if (!$order) {
                Logger::warning("getMessages: Order not found for order_id={$order_id}");
                http_response_code(404);
                echo json_encode(['success' => false, 'messages' => []]);
                exit();
            }
            
            if ($user['id'] != $order['buyer_id'] && $user['id'] != $order['seller_id']) {
                Logger::warning("getMessages: Access denied for user_id={$user['id']} on order_id={$order_id}");
                http_response_code(403);
                echo json_encode(['success' => false, 'messages' => []]);
                exit();
            }
            
            // Get all messages and filter by ID
            $all_messages = $this->getChatMessages($order_id);
            Logger::info("getMessages: Retrieved " . count($all_messages) . " total messages for order_id={$order_id}");
            
            $new_messages = array_filter($all_messages, function ($msg) use ($last_message_id) {
                return $msg['id'] > $last_message_id;
            });
            
            Logger::info("getMessages: Found " . count($new_messages) . " new messages for order_id={$order_id} since last_message_id={$last_message_id}");
            
            echo json_encode(['success' => true, 'messages' => array_values($new_messages)]);
        } catch (Throwable $e) {
            Logger::error("getMessages: Exception - " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            http_response_code(500);
            echo json_encode(['success' => false, 'messages' => []]);
        }
        exit();
    }
    
    /**
     * Get order details with buyer and seller information
     * Private helper method
     */
    private function getOrderDetails($order_id)
    {
        try {
            Logger::info("getOrderDetails: Fetching order_id={$order_id}");
            
            $sql = "SELECT 
                        o.id, o.product_id, o.quantity, o.unit_price, o.status, 
                        o.payment_method, o.created_at, o.buyer_id, o.seller_id,
                        p.title as product_title, p.images,
                        b.first_name as buyer_name, b.last_name as buyer_last_name,
                        s.first_name as seller_name, s.last_name as seller_last_name
                    FROM orders o
                    INNER JOIN products p ON p.id = o.product_id
                    INNER JOIN users b ON b.id = o.buyer_id
                    INNER JOIN users s ON s.id = o.seller_id
                    WHERE o.id = ?
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$order_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$row) {
                Logger::warning("getOrderDetails: Order not found for order_id={$order_id}");
                return null;
            }
            
            Logger::info("getOrderDetails: Order found - buyer_id={$row['buyer_id']}, seller_id={$row['seller_id']}");
            
            if ($row) {
                $images = json_decode($row['images'] ?? '[]', true);
                $row['product_image'] = (is_array($images) && !empty($images)) ? $images[0] : null;
            }
            
            return $row ?: null;
        } catch (Throwable $e) {
            Logger::error('getOrderDetails error: ' . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return null;
        }
    }
    
    /**
     * Get all messages for an order from JSON file
     * Returns array of messages sorted by timestamp
     */
    private function getChatMessages($order_id)
    {
        try {
            $chat_file = $this->getChatFilePath($order_id);
            Logger::info("getChatMessages: Reading from {$chat_file}");
            
            if (!file_exists($chat_file)) {
                Logger::info("getChatMessages: Chat file does not exist, returning empty array");
                return [];
            }
            
            $content = file_get_contents($chat_file);
            if ($content === false) {
                Logger::error("getChatMessages: Failed to read chat file: {$chat_file}");
                return [];
            }
            
            Logger::info("getChatMessages: File size: " . strlen($content) . " bytes");
            
            $messages = $content ? json_decode($content, true) : [];
            
            if (!is_array($messages)) {
                Logger::warning("getChatMessages: JSON decode failed or not array, json_last_error: " . json_last_error_msg());
                return [];
            }
            
            Logger::info("getChatMessages: Retrieved " . count($messages) . " messages for order_id={$order_id}");
            return $messages;
        } catch (Throwable $e) {
            Logger::error('getChatMessages error: ' . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return [];
        }
    }
    
    /**
     * Add a message to the chat file for an order
     * Creates file if it doesn't exist
     * Returns the message array on success, false on failure
     */
    private function addMessageToChat($order_id, $user_id, $user_name, $content)
    {
        try {
            $chat_file = $this->getChatFilePath($order_id);
            
            // Create chat directory if it doesn't exist
            if (!is_dir($this->chats_dir)) {
                if (!mkdir($this->chats_dir, 0755, true)) {
                    return false;
                }
            }
            
            // Create file if it doesn't exist
            if (!file_exists($chat_file)) {
                touch($chat_file);
                chmod($chat_file, 0666);
            }
            
            // Lock and read existing messages
            $handle = fopen($chat_file, 'r+b');
            if (!$handle) {
                $handle = fopen($chat_file, 'c+b');
                if (!$handle) {
                    return false;
                }
            }
            
            flock($handle, LOCK_EX);
            
            $file_content = stream_get_contents($handle);
            $messages = $file_content ? json_decode($file_content, true) : [];
            
            if (!is_array($messages)) {
                $messages = [];
            }
            
            // Generate next message ID
            $next_id = (count($messages) > 0) ? $messages[count($messages) - 1]['id'] + 1 : 0;
            
            // Create new message
            $new_message = [
                'id' => $next_id,
                'sender_id' => $user_id,
                'sender_name' => $user_name,
                'content' => $content,
                'timestamp' => time(),
            ];
            
            $messages[] = $new_message;
            
            // Prepare JSON
            $json_content = json_encode($messages);
            if ($json_content === false) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }
            
            // Rewrite file
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $json_content);
            
            flock($handle, LOCK_UN);
            fclose($handle);
            
            Logger::info("addMessageToChat: Message saved for order_id={$order_id}, message_id={$next_id}");
            return $new_message;
            
        } catch (Throwable $e) {
            Logger::error("addMessageToChat: Exception - " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return false;
        }
    }
    
    /**
     * Get the file path for a specific order's chat
     */
    private function getChatFilePath($order_id)
    {
        $path = $this->chats_dir . '/order-' . (int)$order_id . '-chat.json';
        Logger::debug("getChatFilePath: order_id={$order_id}, path={$path}");
        return $path;
    }
    
    /**
     * Clean up old cancelled order chats (optional cleanup)
     * Called periodically to maintain storage
     * Deletes chats for orders cancelled more than 90 days ago
     */
    public function cleanupOldChats()
    {
        try {
            $cutoff_date = strtotime('-90 days');
            
            // Get all chat files
            if (!is_dir($this->chats_dir)) {
                return;
            }
            
            $files = glob($this->chats_dir . '/order-*-chat.json');
            
            foreach ($files as $file) {
                // Extract order ID from filename
                preg_match('/order-(\d+)-chat\.json/', basename($file), $matches);
                if (empty($matches[1])) continue;
                
                $order_id = (int)$matches[1];
                $order = $this->getOrderDetails($order_id);
                
                // Delete if order is cancelled and older than 90 days
                if ($order && $order['status'] === 'cancelled' && strtotime($order['created_at']) < $cutoff_date) {
                    if (unlink($file)) {
                        Logger::info("Deleted old chat file for cancelled order_id={$order_id}");
                    }
                }
            }
        } catch (Throwable $e) {
            Logger::error('cleanupOldChats error: ' . $e->getMessage());
        }
    }
}
?>
