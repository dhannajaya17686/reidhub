<?php
class Dashboard_HelpController extends Controller
{
    /**
     * Show help form
     */
    public function showHelpForm()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        $this->viewApp('User/help/help-form-view', [], 'Ask a Question');
    }

    /**
     * Submit question
     */
    public function submitQuestion()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/help');
            return;
        }

        $userQuestion = new UserQuestion();
        $userId = $_SESSION['user_id'];
        $category = $_POST['category'] ?? 'general_question';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validation
        if (empty($subject) || empty($message)) {
            $_SESSION['error'] = 'Subject and message are required.';
            header('Location: /dashboard/help');
            return;
        }

        if (strlen($subject) > 255 || strlen($message) > 5000) {
            $_SESSION['error'] = 'Subject or message is too long.';
            header('Location: /dashboard/help');
            return;
        }

        // Validate category
        $validCategories = ['bug_report', 'feature_request', 'general_question', 'feedback'];
        if (!in_array($category, $validCategories)) {
            $category = 'general_question';
        }

        $questionId = $userQuestion->create($userId, $category, $subject, $message);

        if ($questionId) {
            $_SESSION['success'] = 'Your question has been submitted successfully!';
            header('Location: /dashboard/help/my-questions');
        } else {
            $_SESSION['error'] = 'Failed to submit question. Please try again.';
            header('Location: /dashboard/help');
        }
    }

    /**
     * Show user's questions
     */
    public function showMyQuestions()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        $userQuestion = new UserQuestion();
        $userId = $_SESSION['user_id'];
        $questions = $userQuestion->getByUserId($userId);

        $this->viewApp('User/help/my-questions-view', [
            'questions' => $questions
        ], 'My Questions');
    }

    /**
     * Get questions API (for AJAX)
     */
    public function getQuestionsApi()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');
        $userQuestion = new UserQuestion();
        $userId = $_SESSION['user_id'];
        $questions = $userQuestion->getByUserId($userId);

        echo json_encode([
            'success' => true,
            'data' => $questions
        ]);
    }
}
?>
