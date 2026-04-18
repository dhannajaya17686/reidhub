<?php
class Forum_ForumUserController extends Controller
{
    public function showAllQuestions()
    {
        $this->viewApp('User/edu-forum/all-questions-view', [], 'All Questions - ReidHub');
    }
    public function showQuestion()
    {
        $this->viewApp('User/edu-forum/one-question-view', [], 'One Question - ReidHub');
    }
    public function addQuestion()
    {
        $this->viewApp('User/edu-forum/add-question-view', [], 'Add Question - ReidHub');
    }

    /**
     * API: Quick ask question from dashboard
     */
    public function quickAsk()
    {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check authentication
        $user = Auth_LoginController::getSessionUser(true);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $question = trim($input['question'] ?? '');

            // Validation
            if (empty($question)) {
                echo json_encode(['success' => false, 'message' => 'Question cannot be empty']);
                return;
            }

            if (strlen($question) < 10) {
                echo json_encode(['success' => false, 'message' => 'Question must be at least 10 characters']);
                return;
            }

            if (strlen($question) > 500) {
                echo json_encode(['success' => false, 'message' => 'Question cannot exceed 500 characters']);
                return;
            }

            // Create the question
            $forumQuestionModel = new ForumQuestion();
            $questionId = $forumQuestionModel->create($user['id'], $question, $question);

            if ($questionId) {
                Logger::info("Quick question created by user {$user['id']}: ID={$questionId}");
                echo json_encode([
                    'success' => true,
                    'message' => 'Question posted successfully',
                    'questionId' => $questionId
                ]);
            } else {
                Logger::error("Failed to create quick question for user {$user['id']}");
                echo json_encode(['success' => false, 'message' => 'Failed to post question']);
            }
        } catch (Throwable $e) {
            Logger::error("Error in quickAsk: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error occurred']);
        }
    }
}