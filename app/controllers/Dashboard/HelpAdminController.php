<?php
class Dashboard_HelpAdminController extends Controller
{
    /**
     * Show admin help dashboard
     */
    public function showAdminHelpDashboard()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        $userQuestion = new UserQuestion();
        $totalQuestions = $userQuestion->getTotalCount();
        $pendingCount = $userQuestion->getPendingCount();
        $bugReportCount = $userQuestion->getCountByCategory('bug_report');
        $featureRequestCount = $userQuestion->getCountByCategory('feature_request');

        $this->viewApp('Admin/help-admin-dashboard-view', [
            'totalQuestions' => $totalQuestions,
            'pendingCount' => $pendingCount,
            'bugReportCount' => $bugReportCount,
            'featureRequestCount' => $featureRequestCount
        ], 'Help & Feedback Admin');
    }

    /**
     * Get admin questions API
     */
    public function getAdminQuestionsApi()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');

        $userQuestion = new UserQuestion();
        $status = $_GET['status'] ?? null;
        $category = $_GET['category'] ?? null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $questions = $userQuestion->getFiltered($status, $category, $limit, $offset);

        echo json_encode([
            'success' => true,
            'data' => $questions,
            'page' => $page
        ]);
    }

    /**
     * Show question details
     */
    public function showQuestionDetails()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        $questionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$questionId) {
            header('Location: /dashboard/admin/help');
            return;
        }

        $userQuestion = new UserQuestion();
        $question = $userQuestion->findById($questionId);

        if (!$question) {
            $_SESSION['error'] = 'Question not found.';
            header('Location: /dashboard/admin/help');
            return;
        }

        $replies = $userQuestion->getReplies($questionId);

        $this->viewApp('Admin/help-question-detail-view', [
            'question' => $question,
            'replies' => $replies
        ], 'Question Details');
    }

    /**
     * Submit reply
     */
    public function submitReply()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        header('Content-Type: application/json');

        $questionId = $_POST['question_id'] ?? 0;
        $replyMessage = $_POST['reply_message'] ?? '';
        $adminId = $_SESSION['admin_id'];

        // Validation
        if (empty($questionId) || empty($replyMessage)) {
            echo json_encode(['error' => 'Question ID and reply message are required']);
            return;
        }

        $userQuestion = new UserQuestion();
        $question = $userQuestion->findById($questionId);
        if (!$question) {
            echo json_encode(['error' => 'Question not found']);
            return;
        }

        if ($userQuestion->addReply($questionId, $adminId, $replyMessage)) {
            // Send email notification
            $this->sendReplyNotificationEmail($question, $replyMessage);

            echo json_encode([
                'success' => true,
                'message' => 'Reply submitted successfully'
            ]);
        } else {
            echo json_encode(['error' => 'Failed to submit reply']);
        }
    }

    /**
     * Resolve question
     */
    public function resolveQuestion()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request method']);
            return;
        }

        header('Content-Type: application/json');

        $questionId = $_POST['question_id'] ?? 0;

        if (empty($questionId)) {
            echo json_encode(['error' => 'Question ID is required']);
            return;
        }

        $userQuestion = new UserQuestion();
        if ($userQuestion->updateStatus($questionId, 'resolved')) {
            echo json_encode([
                'success' => true,
                'message' => 'Question marked as resolved'
            ]);
        } else {
            echo json_encode(['error' => 'Failed to resolve question']);
        }
    }

    /**
     * Send reply notification email
     */
    private function sendReplyNotificationEmail($question, $replyMessage)
    {
        try {
            $user = new User();
            $userData = $user->getUserById($question['user_id']);

            if (!$userData || empty($userData['email'])) {
                return;
            }

            $to = $userData['email'];
            $subject = "Reply to your question: " . htmlspecialchars($question['subject']);
            
            $emailBody = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2>You have a new reply to your question</h2>
                <p><strong>Your Question:</strong></p>
                <p>" . htmlspecialchars($question['subject']) . "</p>
                
                <p><strong>Admin Reply:</strong></p>
                <p>" . nl2br(htmlspecialchars($replyMessage)) . "</p>
                
                <p><a href='http://localhost/dashboard/help/my-questions'>View your question</a></p>
                
                <hr>
                <p style='color: #666; font-size: 12px;'>This is an automated message from ReidHub Help & Feedback system.</p>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@reidhub.com\r\n";

            mail($to, $subject, $emailBody, $headers);
        } catch (Exception $e) {
            Logger::error("Error sending email: " . $e->getMessage());
        }
    }
}
?>
