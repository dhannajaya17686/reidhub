<?php

class System_NotificationController extends Controller
{
    private array $typeToPrefix = [
        'EDU' => 'edh',
        'MAR' => 'mar',
        'LAF' => 'laf',
        'CAS' => 'cas',
        'SYS' => 'sys',
        'ADM' => 'adm',
    ];

    public function createNotification()
    {
        header('Content-Type: application/json');

        try {
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $actor = $this->resolveActor();
            if (!$actor) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $input = $this->getInput();

            $content = trim((string)($input['content'] ?? ''));
            $type = strtoupper(trim((string)($input['type'] ?? '')));
            $from = strtolower(trim((string)($input['from'] ?? $actor['from'])));
            $topic = isset($input['topic']) ? trim((string)$input['topic']) : null;
            $recipientId = isset($input['recipient_id']) ? (int)$input['recipient_id'] : $actor['id'];
            $recipientRole = isset($input['recipient_role']) ? strtolower(trim((string)$input['recipient_role'])) : $actor['role'];

            if ($content === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'content is required']);
                return;
            }

            if (!array_key_exists($type, $this->typeToPrefix)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid type']);
                return;
            }

            if (!in_array($from, ['system', 'admin', 'user'], true)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid from']);
                return;
            }

            if (!in_array($recipientRole, ['user', 'seller', 'buyer', 'admin'], true)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid recipient_role']);
                return;
            }

            if ($recipientId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid recipient_id']);
                return;
            }

            if ($topic !== null && $topic !== '' && !$this->isValidTopic($topic, $type)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid topic format for type']);
                return;
            }

            if (!$this->canEmitFrom($actor, $from)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Not allowed to emit this from origin']);
                return;
            }

            if (!$this->canWriteRecipient($actor, $recipientId, $recipientRole)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Not allowed to dispatch to this recipient']);
                return;
            }

            $model = new Notification();
            $id = $model->createNotification([
                'content' => $content,
                'type' => $type,
                'from' => $from,
                'topic' => $topic !== '' ? $topic : null,
                'recipient_id' => $recipientId,
                'recipient_role' => $recipientRole,
            ]);

            if (!$id) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create notification']);
                return;
            }

            echo json_encode([
                'success' => true,
                'notification' => [
                    'id' => $id,
                    'content' => $content,
                    'from' => $from,
                    'topic' => $topic,
                    'type' => $type,
                    'isRead' => false,
                    'recipient_id' => $recipientId,
                    'recipient_role' => $recipientRole,
                ]
            ]);
        } catch (Throwable $e) {
            Logger::error('createNotification error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function getNotifications()
    {
        header('Content-Type: application/json');

        try {
            $actor = $this->resolveActor();
            if (!$actor) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $filters = [
                'type' => isset($_GET['type']) ? strtoupper(trim((string)$_GET['type'])) : null,
                'from' => isset($_GET['from']) ? strtolower(trim((string)$_GET['from'])) : null,
                'topic' => isset($_GET['topic']) ? trim((string)$_GET['topic']) : null,
                'topicPrefix' => isset($_GET['topicPrefix']) ? trim((string)$_GET['topicPrefix']) : null,
                'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 50,
                'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0,
            ];

            if (isset($_GET['isRead']) && $_GET['isRead'] !== '') {
                $filters['isRead'] = ((string)$_GET['isRead'] === '1' || strtolower((string)$_GET['isRead']) === 'true') ? 1 : 0;
            }

            $requestedRole = isset($_GET['recipient_role']) ? strtolower(trim((string)$_GET['recipient_role'])) : $actor['role'];
            $requestedId = isset($_GET['recipient_id']) ? (int)$_GET['recipient_id'] : $actor['id'];

            if (!$this->canReadRecipient($actor, $requestedId, $requestedRole)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Not allowed to query this recipient feed']);
                return;
            }

            $filters['recipient_id'] = $requestedId;
            $filters['recipient_role'] = $requestedRole;

            if (!empty($filters['type']) && !array_key_exists($filters['type'], $this->typeToPrefix)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid type']);
                return;
            }

            $model = new Notification();
            $items = $model->getNotifications($filters);

            $unread = 0;
            foreach ($items as $item) {
                if (empty($item['isRead'])) {
                    $unread++;
                }
            }

            echo json_encode([
                'success' => true,
                'items' => $items,
                'count' => count($items),
                'unread' => $unread,
            ]);
        } catch (Throwable $e) {
            Logger::error('getNotifications error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function markAsRead()
    {
        header('Content-Type: application/json');

        try {
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                return;
            }

            $actor = $this->resolveActor();
            if (!$actor) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $input = $this->getInput();
            $notificationId = isset($input['id']) ? (int)$input['id'] : 0;
            if ($notificationId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid id']);
                return;
            }

            $model = new Notification();
            $ok = $model->markAsRead($notificationId, $actor['id'], $actor['role']);

            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Notification not found']);
                return;
            }

            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            Logger::error('markAsRead error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    private function isValidTopic(string $topic, string $type): bool
    {
        if (!preg_match('/^[a-z]{3}\.[a-z]+\.[a-z_]+$/', $topic)) {
            return false;
        }

        $parts = explode('.', $topic);
        if (count($parts) !== 3) {
            return false;
        }

        return $parts[0] === $this->typeToPrefix[$type];
    }

    private function getInput(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }

        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function resolveActor(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['admin_id'])) {
            return [
                'id' => (int)$_SESSION['admin_id'],
                'role' => 'admin',
                'from' => 'admin',
            ];
        }

        if (!empty($_SESSION['user_id'])) {
            return [
                'id' => (int)$_SESSION['user_id'],
                'role' => 'user',
                'from' => 'user',
            ];
        }

        return null;
    }

    private function canEmitFrom(array $actor, string $from): bool
    {
        if ($actor['role'] === 'admin') {
            return in_array($from, ['admin', 'system'], true);
        }

        return $from === 'user';
    }

    private function canWriteRecipient(array $actor, int $recipientId, string $recipientRole): bool
    {
        if ($actor['role'] === 'admin') {
            return true;
        }

        return $recipientId === $actor['id'] && in_array($recipientRole, ['user', 'seller', 'buyer'], true);
    }

    private function canReadRecipient(array $actor, int $recipientId, string $recipientRole): bool
    {
        if ($actor['role'] === 'admin') {
            return true;
        }

        return $recipientId === $actor['id'] && in_array($recipientRole, ['user', 'seller', 'buyer'], true);
    }
}
