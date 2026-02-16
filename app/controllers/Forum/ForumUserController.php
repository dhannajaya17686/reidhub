<?php

class Forum_ForumUserController extends Controller
{
    // ==================================================================
    // 1. PAGE DISPLAY METHODS (GET Requests)
    // ==================================================================

    // Show the Main Feed (All Questions) with Search, Filter & Pagination
    public function showAllQuestions()
    {
        $forumModel = new ForumModel();

        // 1. Get Params from URL
        $filter = $_GET['filter'] ?? 'newest'; // newest, trending, unanswered
        $tag    = $_GET['tag'] ?? null;        // e.g. 'php'
        $search = $_GET['search'] ?? null;     // Search keyword
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // 2. Pagination Settings
        $limit = 10; // Questions per page
        $offset = ($page - 1) * $limit;

        // 3. Fetch Data from Model
        $questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
        
        // 4. Calculate Total Pages (for Pagination controls)
        $totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search);
        $totalPages = ceil($totalQuestions / $limit);

        // 5. Pass data to the view
        $data = [
            'questions'      => $questions,
            'current_page'   => $page,
            'total_pages'    => $totalPages,
            'current_filter' => $filter,
            'current_search' => $search,
            'current_tag'    => $tag
        ];

        $this->viewApp('User/edu-forum/all-questions-view', $data, 'All Questions - ReidHub');
    }

    // Show a Single Question Page
    public function showQuestion()
    {
        // 1. Get 'id' from URL
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        $forumModel = new ForumModel();
        
        // 2. Fetch the Question details
        $question = $forumModel->getQuestionById($id);

        if (!$question) {
            echo "Question not found.";
            return;
        }
        
        // 3. Fetch all Answers for this question
        $answers = $forumModel->getAnswers($id);

        // =========================================================
        // NEW: Fetch Comments for the Question
        // =========================================================
        $questionComments = $forumModel->getComments('question', $id);

        // =========================================================
        // NEW: Fetch Comments for each Answer
        // =========================================================
        // We loop through the answers and attach their specific comments
        foreach ($answers as &$answer) {
            $answer['comments'] = $forumModel->getComments('answer', $answer['id']);
        }
        unset($answer); // Break reference to avoid bugs

        // 4. Pass EVERYTHING to the view
        $this->viewApp('User/edu-forum/one-question-view', [
            'question'          => $question,
            'answers'           => $answers,
            'question_comments' => $questionComments 
        ], 'Question Details - ReidHub');
    }

    // Show the "Ask Question" page
    public function addQuestion()
    {
        // Require login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Render the add question view
        $this->viewApp('User/edu-forum/add-question-view', [], 'Ask a Question - ReidHub');
    }

    // ==================================================================
    // 2. FORM SUBMISSION METHODS (POST Requests)
    // ==================================================================

    // Handle "Post Answer" Form Submission
    public function createAnswer()
    {
        // Security: Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $questionId = $_POST['question_id'] ?? null;
            $content    = trim($_POST['content'] ?? '');

            // Validate inputs
            if (!empty($questionId) && !empty($content)) {
                
                $forumModel = new ForumModel();
                
                // Save to Database
                    $saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content);
                    if ($saved) {
                        
                        // --- NOTIFICATION START: Notify Question Owner ---
                        $question = $forumModel->getQuestionById($questionId);
                        // Only notify if the person answering is NOT the owner
                        if ($question && $question['user_id'] != $_SESSION['user_id']) {
                            $msg = "Someone answered your question: " . substr($question['title'], 0, 30) . "...";
                            $link = "/dashboard/forum/question?id=" . $questionId;
                            $forumModel->createNotification($question['user_id'], 'answer', $msg, $link);
                        }
                        // --- NOTIFICATION END ---

                        // Success! Redirect back to the question so they see their answer
                        header("Location: /dashboard/forum/question?id=" . $questionId . "&success=answered");
                        exit;
                    } else {
                        // Log failure for easier debugging
                        Logger::error('Failed to save forum answer', [
                            'user_id' => $_SESSION['user_id'] ?? null,
                            'question_id' => $questionId,
                            'content_length' => strlen($content)
                        ]);
                    }
            }
        }

        // If validation failed or error
        if(isset($_POST['question_id'])) {
             header("Location: /dashboard/forum/question?id=" . $_POST['question_id'] . "&error=empty");
        } else {
             header("Location: /dashboard/forum/all");
        }
        exit;
    }

    // Handle "Post Question" Submission
    public function createQuestion()
    {
        // 1. Security Check
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2. Save Data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'    => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'content'  => trim($_POST['description']),
                'tags'     => trim($_POST['tags'] ?? '') // Captures the hidden input from add-question.js
            ];

            if (!empty($data['title']) && !empty($data['content'])) {
                $forumModel = new ForumModel();
                
                // Pass data to Model
                if ($forumModel->createQuestion($_SESSION['user_id'], $data)) {
                    // Success! Go to the feed
                    header("Location: /dashboard/forum/all?success=created");
                    exit;
                }
            }
        }
        // Failure
        header("Location: /dashboard/forum/add?error=failed");
        exit;
    }

    // ==================================================================
    // NEW: Handle "Mark as Solved" Action
    // ==================================================================
    public function acceptAnswer()
    {
        // 1. Security Check: User must be logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answerId   = $_POST['answer_id'] ?? null;
            $questionId = $_POST['question_id'] ?? null;

            if ($answerId && $questionId) {
                $forumModel = new ForumModel();

                // 2. Verify Ownership: Fetch the question to see who owns it
                $question = $forumModel->getQuestionById($questionId);
                
                // Only the Question Owner (or an Admin) can mark it as solved
                if ($question && ($question['user_id'] == $_SESSION['user_id'] || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'))) {
                    
                    // 3. Call Model to update DB
                    $forumModel->markAnswerAsAccepted($questionId, $answerId);
                    
                    // --- NOTIFICATION START: Notify Answer Author ---
                    // Fetch the answer to find out who wrote it
                    $answer = $forumModel->getAnswersByAnswerId($answerId);
                    if ($answer && $answer['user_id'] != $_SESSION['user_id']) {
                        $msg = "Your answer was marked as the solution! (+15 pts)";
                        $link = "/dashboard/forum/question?id=" . $questionId;
                        $forumModel->createNotification($answer['user_id'], 'solution', $msg, $link);
                    }
                    // --- NOTIFICATION END ---

                    header("Location: /dashboard/forum/question?id=" . $questionId . "&success=solved");
                    exit;
                } else {
                    // User does not own this question
                    header("Location: /dashboard/forum/question?id=" . $questionId . "&error=unauthorized");
                    exit;
                }
            }
        }
        
        // Fallback if something was missing
        header("Location: /dashboard/forum/all?error=failed");
        exit;
    }

    // ==================================================================
    // 3. AJAX ACTIONS (Hidden Background Requests)
    // ==================================================================

    // Handle Upvote/Downvote clicks
    public function vote()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to vote']);
            return;
        }

        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['type']) || !isset($input['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        $forumModel = new ForumModel();
        
        // Toggle the vote
        $action = $forumModel->toggleVote($_SESSION['user_id'], $input['type'], $input['id']);
        
        // Get the new total count
        $newCount = $forumModel->getVoteCount($input['type'], $input['id']);

        echo json_encode([
            'status'    => 'success', 
            'action'    => $action, 
            'new_count' => $newCount
        ]);
    }

    // Handle Bookmark clicks
    public function bookmark()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to bookmark']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            return;
        }

        $forumModel = new ForumModel();
        
        // Toggle Bookmark
        $action = $forumModel->toggleBookmark($_SESSION['user_id'], $input['id']);
        
        echo json_encode([
            'status' => 'success', 
            'action' => $action
        ]);
    }

    // Handle Report clicks
    public function report()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to report']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $forumModel = new ForumModel();
        
        if ($forumModel->createReport($_SESSION['user_id'], $input['type'], $input['id'], $input['reason'])) {
            echo json_encode(['status' => 'success', 'message' => 'Report submitted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    }


    // ==================================================================
    // 4. EDIT & DELETE ACTIONS
    // ==================================================================

    public function deleteContent() {
        if (!isset($_SESSION['user_id'])) header('Location: /login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type']; // 'question' or 'answer'
            $id = $_POST['id'];

            $forumModel = new ForumModel();
            if ($forumModel->deleteContent($type, $id, $_SESSION['user_id'])) {
                if ($type === 'question') {
                    header("Location: /dashboard/forum/all?msg=deleted");
                } else {
                    // If answer deleted, go back to question
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                }
                exit;
            }
        }
        header("Location: /dashboard/forum/all?error=unauthorized");
    }

    public function updateContent() {
        if (!isset($_SESSION['user_id'])) header('Location: /login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $content = trim($_POST['content']);
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;

            $forumModel = new ForumModel();

            $success = false;
            if ($type === 'question') {
                $success = $forumModel->updateQuestion($id, $_SESSION['user_id'], $title, $content);
                $redirect = "/dashboard/forum/question?id=" . $id;
            } else {
                $success = $forumModel->updateAnswer($id, $_SESSION['user_id'], $content);
                
                // BETTER: Use a posted question_id if available, fallback to referer
                if (isset($_POST['question_id'])) {
                    $redirect = "/dashboard/forum/question?id=" . $_POST['question_id'];
                } else {
                    $redirect = $_SERVER['HTTP_REFERER']; 
                }
            }

            if ($success) {
                // Safely append the success message
                $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
                header("Location: " . $redirect . $separator . "success=updated");
                exit;
            }
        }
        header("Location: /dashboard/forum/all?error=failed");
    }

    // ==================================================================
    // 5. COMMENT ACTIONS
    // ==================================================================

    public function createComment() {
        if (!isset($_SESSION['user_id'])) header('Location: /login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $parentType = $_POST['parent_type']; // 'question' or 'answer'
            $parentId   = $_POST['parent_id'];
            $content    = trim($_POST['content']);
            $questionId = $_POST['redirect_id']; // The main question ID to redirect back to

            if (!empty($content)) {
                $forumModel = new ForumModel();
                $forumModel->addComment($_SESSION['user_id'], $parentType, $parentId, $content);

                // --- NOTIFICATION START: Notify Post Owner ---
                $targetUserId = null;
                $targetTitle = "your post";

                if ($parentType === 'question') {
                    // Fetch Question Owner
                    $q = $forumModel->getQuestionById($parentId);
                    if ($q) {
                        $targetUserId = $q['user_id'];
                        $targetTitle = $q['title'];
                    }
                } else {
                    // Fetch Answer Owner
                    $ans = $forumModel->getAnswersByAnswerId($parentId);
                    if ($ans) {
                        $targetUserId = $ans['user_id'];
                        $targetTitle = "your answer";
                    }
                }

                // If owner exists and isn't the commenter, send notification
                if ($targetUserId && $targetUserId != $_SESSION['user_id']) {
                    $msg = "New comment on " . substr($targetTitle, 0, 20) . "...";
                    $link = "/dashboard/forum/question?id=" . $questionId;
                    $forumModel->createNotification($targetUserId, 'comment', $msg, $link);
                }
                // --- NOTIFICATION END ---
            }
            
            header("Location: /dashboard/forum/question?id=" . $questionId . "&success=commented");
            exit;
        }
    }

    // Handle Comment Deletion
    public function deleteComment() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentId  = $_POST['comment_id'];
            $questionId = $_POST['redirect_id']; // To send them back to the right page

            $forumModel = new ForumModel();
            
            if ($forumModel->deleteComment($commentId, $_SESSION['user_id'])) {
                header("Location: /dashboard/forum/question?id=" . $questionId . "&msg=comment_deleted");
            } else {
                header("Location: /dashboard/forum/question?id=" . $questionId . "&error=unauthorized");
            }
            exit;
        }
    }

    // ==================================================================
    // 10. SEARCH SUGGESTIONS API
    // ==================================================================

    public function searchSimilar() {
        // Allow public access or restrict to logged in users (optional)
        // if (!isset($_SESSION['user_id'])) return; 

        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 3) {
            echo json_encode([]);
            return;
        }

        $forumModel = new ForumModel();
        $results = $forumModel->findSimilarQuestions($query);
        
        echo json_encode(['status' => 'success', 'results' => $results]);
    }
}