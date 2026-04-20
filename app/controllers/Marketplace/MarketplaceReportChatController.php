<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Marketplace_MarketplaceReportChatController extends Controller
{
    private $chatsDir;

    public function __construct()
    {
        $this->chatsDir = __DIR__ . '/../../..' . '/storage/filestore/chats/report-chats';
    }

    public function showAdminReportChat()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $reportId = (int)($_GET['id'] ?? 0);

        if ($reportId <= 0) {
            http_response_code(404);
            exit('Invalid report ID');
        }

        $reportModel = new MarketplaceReport();
        $report = $reportModel->getByIdWithDetails($reportId);

        if (!$report) {
            http_response_code(404);
            exit('Report not found');
        }

        $messages = $this->getChatMessages($reportId);

        $this->viewApp(
            '/Admin/marketplace/report-chat-view',
            [
                'admin' => $admin,
                'report' => $report,
                'messages' => $messages,
                'actor_role' => 'admin',
            ],
            'Marketplace Report Chat - ReidHub'
        );
    }

    public function showSellerReportChat()
    {
        $seller = Auth_LoginController::getSessionUser(true);
        $reportId = (int)($_GET['id'] ?? 0);

        if ($reportId <= 0) {
            http_response_code(404);
            exit('Invalid report ID');
        }

        $reportModel = new MarketplaceReport();
        $report = $reportModel->getByIdWithDetails($reportId);

        if (!$report) {
            http_response_code(404);
            exit('Report not found');
        }

        if ((int)$report['seller_id'] !== (int)$seller['id']) {
            http_response_code(403);
            exit('Access denied');
        }

        $messages = $this->getChatMessages($reportId);

        $this->viewApp(
            '/User/marketplace/seller-report-chat-view',
            [
                'user' => $seller,
                'report' => $report,
                'messages' => $messages,
                'actor_role' => 'seller',
            ],
            'Marketplace Report Chat - ReidHub'
        );
    }

    public function sendMessage()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $reportId = (int)($_POST['report_id'] ?? $_GET['id'] ?? 0);
            $content = trim((string)($_POST['content'] ?? ''));

            if ($reportId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid report ID']);
                return;
            }

            if ($content === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
                return;
            }

            if (strlen($content) > 5000) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Message too long (max 5000 characters)']);
                return;
            }

            $resolved = $this->resolveActorAndReport($reportId);
            if (!$resolved['success']) {
                http_response_code($resolved['code']);
                echo json_encode(['success' => false, 'message' => $resolved['message']]);
                return;
            }

            $message = $this->addMessageToChat(
                $reportId,
                (int)$resolved['actor_id'],
                (string)$resolved['actor_name'],
                (string)$resolved['actor_role'],
                $content
            );

            if (!$message) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save message']);
                return;
            }

            echo json_encode(['success' => true, 'message' => $message]);
        } catch (Throwable $e) {
            Logger::error('MarketplaceReportChat sendMessage error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function getMessages()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $reportId = (int)($_POST['report_id'] ?? $_GET['id'] ?? 0);
            $lastMessageId = (int)($_POST['last_message_id'] ?? -1);

            if ($reportId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'messages' => []]);
                return;
            }

            $resolved = $this->resolveActorAndReport($reportId);
            if (!$resolved['success']) {
                http_response_code($resolved['code']);
                echo json_encode(['success' => false, 'messages' => []]);
                return;
            }

            $allMessages = $this->getChatMessages($reportId);
            $newMessages = array_values(array_filter($allMessages, function ($msg) use ($lastMessageId) {
                return (int)($msg['id'] ?? -1) > $lastMessageId;
            }));

            echo json_encode(['success' => true, 'messages' => $newMessages]);
        } catch (Throwable $e) {
            Logger::error('MarketplaceReportChat getMessages error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'messages' => []]);
        }
    }

    private function resolveActorAndReport(int $reportId): array
    {
        $reportModel = new MarketplaceReport();
        $report = $reportModel->getByIdWithDetails($reportId);

        if (!$report) {
            return ['success' => false, 'code' => 404, 'message' => 'Report not found'];
        }

        $admin = Auth_LoginController::getSessionAdmin(false);
        if ($admin) {
            $actorName = trim((string)($admin['first_name'] ?? '') . ' ' . (string)($admin['last_name'] ?? ''));
            if ($actorName === '') {
                $actorName = (string)($admin['email'] ?? 'Admin');
            }

            return [
                'success' => true,
                'actor_role' => 'admin',
                'actor_id' => (int)$admin['id'],
                'actor_name' => $actorName,
                'report' => $report,
            ];
        }

        $seller = Auth_LoginController::getSessionUser(false);
        if ($seller && (int)$seller['id'] === (int)$report['seller_id']) {
            $actorName = trim((string)($seller['first_name'] ?? '') . ' ' . (string)($seller['last_name'] ?? ''));
            if ($actorName === '') {
                $actorName = (string)($seller['email'] ?? 'Seller');
            }

            return [
                'success' => true,
                'actor_role' => 'seller',
                'actor_id' => (int)$seller['id'],
                'actor_name' => $actorName,
                'report' => $report,
            ];
        }

        return ['success' => false, 'code' => 403, 'message' => 'Access denied'];
    }

    private function getChatMessages(int $reportId): array
    {
        try {
            $chatFile = $this->getChatFilePath($reportId);
            if (!file_exists($chatFile)) {
                return [];
            }

            $content = file_get_contents($chatFile);
            if ($content === false || trim($content) === '') {
                return [];
            }

            $decoded = json_decode($content, true);
            return is_array($decoded) ? $decoded : [];
        } catch (Throwable $e) {
            Logger::error('MarketplaceReportChat getChatMessages error: ' . $e->getMessage());
            return [];
        }
    }

    private function addMessageToChat(int $reportId, int $actorId, string $actorName, string $actorRole, string $content)
    {
        try {
            if (!is_dir($this->chatsDir) && !mkdir($this->chatsDir, 0755, true) && !is_dir($this->chatsDir)) {
                return false;
            }

            $chatFile = $this->getChatFilePath($reportId);
            if (!file_exists($chatFile)) {
                file_put_contents($chatFile, '[]');
                chmod($chatFile, 0666);
            }

            $handle = fopen($chatFile, 'c+b');
            if (!$handle) {
                return false;
            }

            flock($handle, LOCK_EX);
            $raw = stream_get_contents($handle);
            $messages = $raw ? json_decode($raw, true) : [];
            if (!is_array($messages)) {
                $messages = [];
            }

            $nextId = !empty($messages) ? ((int)$messages[count($messages) - 1]['id'] + 1) : 0;

            $newMessage = [
                'id' => $nextId,
                'sender_id' => $actorId,
                'sender_name' => $actorName,
                'sender_role' => $actorRole,
                'content' => $content,
                'timestamp' => time(),
            ];

            $messages[] = $newMessage;

            $encoded = json_encode($messages);
            if ($encoded === false) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $encoded);
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);

            return $newMessage;
        } catch (Throwable $e) {
            Logger::error('MarketplaceReportChat addMessageToChat error: ' . $e->getMessage());
            return false;
        }
    }

    private function getChatFilePath(int $reportId): string
    {
        return $this->chatsDir . '/report-' . $reportId . '-chat.json';
    }
}
