<?php

/*
 * EDU FORUM USER CONTROLLER - DETAILED GUIDE
 * EDU FORUM USER CONTROLLER
 * ------------------------------------------
 * This controller handles the learner-facing forum features:
 * viewing questions, creating questions, answering, commenting, voting,
 * bookmarking, reporting, accepting answers, deleting/editing own content,
 * showing similar questions, and showing saved bookmarks.
 *
 * PHP syntax reminders used throughout this file:
 * - $name means "a variable named name". Example: $forumModel stores an object.
 * - -> is PHP's object operator. Example: $forumModel->getAllQuestions()
 *   means "call the getAllQuestions method that belongs to the $forumModel object".
 * - :: is PHP's static access operator. Example: Logger::error()
 *   means "call error directly on the Logger class, without creating an object".
 * - [] creates or reads arrays. Example: $_SESSION['user_id'] reads user_id
 *   from the session array.
 * - ?? is the null coalescing operator. Example: $_GET['tag'] ?? null means
 *   "use $_GET['tag'] if it exists; otherwise use null".
 * - => connects an array key to its value. Example: 'questions' => $questions
 *   stores $questions under the key named questions.
 * - (int)$value converts a value into an integer before using it.
 * - header('Location: ...') tells the browser to redirect to another page.
 * - exit stops the current PHP request immediately after a redirect or response.
 * Handles the learner-facing forum features: viewing, creating, answering, 
 * commenting, voting, bookmarking, reporting, editing, and deleting content.
 */
class Forum_ForumUserController extends Controller
{
    private function ensureUserCanPostToForum(): bool
    {
        // If the session does not contain user_id, the visitor is not logged in.
        if (!isset($_SESSION['user_id'])) {
            // Send guests to the login page before allowing any write action.
            header('Location: /login');
            // Stop this request so the protected action cannot continue.
            exit;
        }

        // Create the forum model object; $forumModel->method() calls database logic.
        $forumModel = new ForumModel();
        // Check active forum suspensions before allowing posts, answers, or comments.
        if ($forumModel->isUserSuspended((int)$_SESSION['user_id'])) {
            // Redirect suspended users back to the forum list with an error flag.
            header('Location: /dashboard/forum/all?error=suspended');
            exit;
        }

        // Returning true lets the calling method continue safely.
        return true;
    }

    // ==================================================================
    // 1. PAGE DISPLAY METHODS (GET Requests)
    // 1. PAGE DISPLAY METHODS
    // ==================================================================

    // Show the Main Feed (All Questions) with Search, Filter & Pagination
    public function showAllQuestions()
    {
        // The model is responsible for reading forum data from the database.
        $forumModel = new ForumModel();

        // 1. Get params from the URL query string, such as ?filter=trending.
        $filter = $_GET['filter'] ?? 'newest'; // newest, trending, unanswered
        $tag    = $_GET['tag'] ?? null;        // e.g. 'php'
        $search = $_GET['search'] ?? null;     // Search keyword
        // If page exists in the URL, cast it to an integer; otherwise default to page 1.
        // Read filters from URL
        $filter = $_GET['filter'] ?? 'newest';
        $tag    = $_GET['tag'] ?? null;
        $search = $_GET['search'] ?? null;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // 2. Pagination Settings
        $limit = 10; // Questions per page
        // Offset means "how many rows to skip" before fetching the current page.
        // Pagination Settings
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // 3. Fetch current-page questions from the model; -> calls the model method.
        // Model calls
        $questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
        
        // 4. Calculate total pages for pagination controls in the view.
        $totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search);
        // ceil rounds up, so 11 questions with limit 10 becomes 2 pages.
        $totalPages = ceil($totalQuestions / $limit);

        // 5. Pack all values into an array that the view can display.
        // Pass data to the view
        $data = [
            'questions'      => $questions,//Store the $questions variable inside the $data array using the name questions.”So later, the view can access the questions list.
            'questions'      => $questions,
            'current_page'   => $page,
            'total_pages'    => $totalPages,
            'current_filter' => $filter,
            'current_search' => $search,
            'current_tag'    => $tag
        ];

        // Render the all-questions view inside the app layout.
        $this->viewApp('User/edu-forum/all-questions-view', $data, 'All Questions - ReidHub');
    }

    // Show a Single Question Page
    public function showQuestion()
    {
        // 1. Get the question id from the URL, e.g. /dashboard/forum/question?id=5.
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        // Create the model object used for all forum database reads below.
        $forumModel = new ForumModel();
        
        // 2. Fetch the question details; -> means "call this method on this object".
        // Model calls
        $question = $forumModel->getQuestionById($id);

        // If the model returns false/null, there is no active question with that id.
        if (!$question) {
            echo "Question not found.";
            return;
        }
        
        // 3. Fetch all active answers that belong to this question.
        $answers = $forumModel->getAnswers($id);

        // =========================================================
        // NEW: Fetch Comments for the Question
        // =========================================================
        $questionComments = $forumModel->getComments('question', $id);

        // =========================================================
        // NEW: Fetch Comments for each Answer
        // =========================================================
        // The & before $answer means "use a reference", so changes affect the array item.
        // We attach comments to each answer so the view can render nested discussions.
        // Fetch Comments for each Answer
        foreach ($answers as &$answer) {
            // Each answer gets a new comments key containing its own active comments.
            $answer['comments'] = $forumModel->getComments('answer', $answer['id']);
        }
        // Break the reference so later use of $answer cannot accidentally edit the last row.
        unset($answer);

        // 4. Pass question, answers, and question-level comments to the detail view.
        // Pass data to the view
        $this->viewApp('User/edu-forum/one-question-view', [
            'question'          => $question,
            'answers'           => $answers,
            'question_comments' => $questionComments 
        ], 'Question Details - ReidHub');
    }

    // Show the "Ask Question" page
    public function addQuestion()
    {
        // Only logged-in users should see the ask-question form.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Render the add question page. Empty [] means no extra view data is needed.
        $this->viewApp('User/edu-forum/add-question-view', [], 'Ask a Question - ReidHub');
    }

    // ==================================================================
    // 2. FORM SUBMISSION METHODS (POST Requests)
    // 2. FORM SUBMISSION METHODS
    // ==================================================================

    // Handle "Post Answer" Form Submission
    public function createAnswer()
    {
        // Reuse the helper so guests and suspended users cannot answer.
        $this->ensureUserCanPostToForum();

        // Only process real form submissions; GET requests should fall through to redirect.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Read the hidden question_id field submitted by the answer form.
            // Read fields
            $questionId = $_POST['question_id'] ?? null;
            // trim removes extra spaces/newlines from the answer content.
            $content    = trim($_POST['content'] ?? '');

            // Validate that both the question id and answer body are present.
            if (!empty($questionId) && !empty($content)) {
                
                // Create the model object that will insert the answer into the database.
                $forumModel = new ForumModel();
                
                // Save to database. The logged-in user's id becomes the answer author.
                    $saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content);
                    if ($saved) {
                        
                        // --- NOTIFICATION START: Notify Question Owner ---
                        $question = $forumModel->getQuestionById($questionId);
                        // Only notify if the person answering is NOT the question owner.
                        if ($question && $question['user_id'] != $_SESSION['user_id']) {
                            // Build a short notification message from the question title.
                            $msg = "Someone answered your question: " . substr($question['title'], 0, 30) . "...";
                            // This link takes the owner directly back to the question page.
                            $link = "/dashboard/forum/question?id=" . $questionId;
                            // Insert the notification row for the question owner.
                            $forumModel->createNotification($question['user_id'], 'answer', $msg, $link);
                        }
                        // --- NOTIFICATION END ---
                // Model call
                $saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content);
                
                if ($saved) {
                    // Notification Logic
                    $question = $forumModel->getQuestionById($questionId);
                    if ($question && $question['user_id'] != $_SESSION['user_id']) {
                        $msg = "Someone answered your question: " . substr($question['title'], 0, 30) . "...";
                        $link = "/dashboard/forum/question?id=" . $questionId;
                        $forumModel->createNotification($question['user_id'], 'answer', $msg, $link);
                    }

                        // Success! Redirect back to the question so they see their answer
                        header("Location: /dashboard/forum/question?id=" . $questionId . "&success=answered");
                        exit;
                    } else {
                        // Logger::error uses :: because error is called statically on Logger.
                        Logger::error('Failed to save forum answer', [
                            'user_id' => $_SESSION['user_id'] ?? null,
                            'question_id' => $questionId,
                            'content_length' => strlen($content)
                        ]);
                    }
                    header("Location: /dashboard/forum/question?id=" . $questionId . "&success=answered");
                    exit;
                } else {
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
        // Reuse the helper so guests and suspended users cannot create questions.
        $this->ensureUserCanPostToForum();

        // Save data only when the ask-question form sends a POST request.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Create a clean data array from posted form fields.
            
            // Read fields
            $data = [
                'title'    => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'content'  => trim($_POST['description']),
                'tags'     => trim($_POST['tags'] ?? '') // Captures the hidden input from add-question.js
                'tags'     => trim($_POST['tags'] ?? '')
            ];

            // Title and content are required; tags/category can be handled separately.
            if (!empty($data['title']) && !empty($data['content'])) {
                $forumModel = new ForumModel();
                
                // Pass data to the model; the model creates the SQL INSERT query.
                // Model call
                if ($forumModel->createQuestion($_SESSION['user_id'], $data)) {
                    // Success: redirect to the feed with a URL flag for a toast/message.
                    header("Location: /dashboard/forum/all?success=created");
                    exit;
                }
            }
        }
        // Failure: return to the add form with an error flag.
        header("Location: /dashboard/forum/add?error=failed");
        exit;
    }

    // ==================================================================
    // NEW: Handle "Mark as Solved" Action
    // Handle "Mark as Solved" Action
    // ==================================================================
    public function acceptAnswer()
    {
        // 1. Security check: only logged-in users can mark a solution.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Accept-answer buttons submit by POST so users cannot change state by just visiting a URL.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Read ids from hidden form fields.
            // Read fields
            $answerId   = $_POST['answer_id'] ?? null;
            $questionId = $_POST['question_id'] ?? null;

            // Continue only if both ids were submitted.
            if ($answerId && $questionId) {
                $forumModel = new ForumModel();

                // 2. Verify ownership: fetch the question to see who owns it.
                $question = $forumModel->getQuestionById($questionId);
                
                // Only the question owner or an admin can mark an answer as solved.
                // Security check
                if ($question && ($question['user_id'] == $_SESSION['user_id'] || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'))) {
                    
                    // 3. Update the database so only this answer is accepted.
                    // Model call
                    $forumModel->markAnswerAsAccepted($questionId, $answerId);
                    
                    // --- NOTIFICATION START: Notify Answer Author ---
                    // Fetch the answer to find out who wrote it.
                    // Notification Logic
                    $answer = $forumModel->getAnswersByAnswerId($answerId);
                    // Notify the answer author unless they accepted their own answer.
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
    // 3. AJAX ACTIONS
    // ==================================================================

    // Handle Upvote/Downvote clicks
    public function vote()
    {
        // AJAX requests still need authentication; guests receive a JSON error.
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to vote']);
            return;
        }

        // Read JSON input sent by fetch(). php://input is the raw request body.
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Vote requests must include the target type and id.
        if (!isset($input['type']) || !isset($input['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        $forumModel = new ForumModel();
        
        // Toggle the vote: add it, remove it, or update it depending on current state.
        // Model call
        $action = $forumModel->toggleVote($_SESSION['user_id'], $input['type'], $input['id']);
        
        // Get the new score after the vote change so the browser can update the UI.
        $newCount = $forumModel->getVoteCount($input['type'], $input['id']);

        // Return JSON because JavaScript is waiting for a structured response.
        echo json_encode([
            'status'    => 'success', 
            'action'    => $action, 
            'new_count' => $newCount
        ]);
    }

    // Handle Bookmark clicks
    public function bookmark()
    {
        // Bookmarks belong to a user account, so login is required.
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to bookmark']);
            return;
        }

        // Decode JSON request body sent by the bookmark button JavaScript.
        $input = json_decode(file_get_contents('php://input'), true);
        
        // The request must include the question id.
        if (!isset($input['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            return;
        }

        $forumModel = new ForumModel();
        
        // Toggle bookmark: insert if missing, delete if already saved.
        // Model call
        $action = $forumModel->toggleBookmark($_SESSION['user_id'], $input['id']);
        
        echo json_encode([
            'status' => 'success', 
            'action' => $action
        ]);
    }

    // Handle Report clicks
    public function report()
    {
        // Reports are tied to the reporting user, so login is required.
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Please login to report']);
            return;
        }

        // Decode the JSON body containing type, id, and reason.
        $input = json_decode(file_get_contents('php://input'), true);
        $forumModel = new ForumModel();
        
        // Store the report and return a JSON success/error message for JavaScript.
        // Model call
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
        // Guests cannot delete content; send them to login.
        if (!isset($_SESSION['user_id'])) header('Location: /login');

        // Delete forms submit POST to prevent accidental deletion by URL visit.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type']; // 'question' or 'answer'
            // Read fields
            $type = $_POST['type'];
            $id = $_POST['id'];

            $forumModel = new ForumModel();
            // The model checks ownership before deleting.
            
            // Model call
            if ($forumModel->deleteContent($type, $id, $_SESSION['user_id'])) {
                if ($type === 'question') {
                    // Deleted questions return to the list because the detail page no longer exists.
                    header("Location: /dashboard/forum/all?msg=deleted");
                } else {
                    // Deleted answers return to the referring question page.
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                }
                exit;
            }
        }
        header("Location: /dashboard/forum/all?error=unauthorized");
    }

    public function updateContent() {
        // Guests cannot edit content; send them to login.
        if (!isset($_SESSION['user_id'])) header('Location: /login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Read submitted content metadata from the edit modal/form.
            // Read fields
            $type = $_POST['type'];
            $id = $_POST['id'];
            $content = trim($_POST['content']);
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;

            $forumModel = new ForumModel();

            $success = false;
            
            if ($type === 'question') {
                // Update title and body for a question owned by the current user.
                // Model call
                $success = $forumModel->updateQuestion($id, $_SESSION['user_id'], $title, $content);
                $redirect = "/dashboard/forum/question?id=" . $id;
            } else {
                // Update body only for an answer owned by the current user.
                // Model call
                $success = $forumModel->updateAnswer($id, $_SESSION['user_id'], $content);
                
                // Use posted question_id if available; fallback to HTTP_REFERER.
                if (isset($_POST['question_id'])) {
                    $redirect = "/dashboard/forum/question?id=" . $_POST['question_id'];
                } else {
                    $redirect = $_SERVER['HTTP_REFERER']; 
                }
            }

            if ($success) {
                // Safely append success=updated whether the URL already has ? or not.
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
        // Reuse shared login/suspension protection for comment creation.
        $this->ensureUserCanPostToForum();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // parent_type tells us whether the comment belongs to a question or answer.
            $parentType = $_POST['parent_type']; // 'question' or 'answer'
            // parent_id is the id of that question or answer.
            // Read fields
            $parentType = $_POST['parent_type'];
            $parentId   = $_POST['parent_id'];
            // Comment text is trimmed before being stored.
            $content    = trim($_POST['content']);
            $questionId = $_POST['redirect_id']; // The main question ID to redirect back to
            $questionId = $_POST['redirect_id']; 

            // Empty comments are ignored, but the user still returns to the question page.
            if (!empty($content)) {
                $forumModel = new ForumModel();
                // Insert the comment row into forum_comments.
                
                // Model call
                $forumModel->addComment($_SESSION['user_id'], $parentType, $parentId, $content);

                // --- NOTIFICATION START: Notify Post Owner ---
                // Notification Logic
                $targetUserId = null;
                $targetTitle = "your post";

                if ($parentType === 'question') {
                    // Fetch question owner so we can notify them.
                    $q = $forumModel->getQuestionById($parentId);
                    if ($q) {
                        $targetUserId = $q['user_id'];
                        $targetTitle = $q['title'];
                    }
                } else {
                    // Fetch answer owner so we can notify them.
                    $ans = $forumModel->getAnswersByAnswerId($parentId);
                    if ($ans) {
                        $targetUserId = $ans['user_id'];
                        $targetTitle = "your answer";
                    }
                }

                // If owner exists and is not the commenter, send a notification.
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
        // Only logged-in users can delete their own comments.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Read the comment id and redirect question id from hidden form fields.
            // Read fields
            $commentId  = $_POST['comment_id'];
            $questionId = $_POST['redirect_id']; // To send them back to the right page
            $questionId = $_POST['redirect_id'];

            $forumModel = new ForumModel();
            
            // The model deletes only if the current user owns the comment.
            // Model call
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
        // This endpoint supports live similar-question suggestions while typing a title.
        // Allow public access or restrict to logged in users (optional).
        // if (!isset($_SESSION['user_id'])) return; 

        // q is the search text from /dashboard/forum/search-similar?q=...
        // Read query
        $query = $_GET['q'] ?? '';
        
        // Avoid expensive/low-quality searches for very short text.
        if (strlen($query) < 3) {
            echo json_encode([]);
            return;
        }

        $forumModel = new ForumModel();
        // Ask the model to find matching active questions.
        
        // Model call
        $results = $forumModel->findSimilarQuestions($query);
        
        // Return suggestions as JSON to the add-question JavaScript.
        echo json_encode(['status' => 'success', 'results' => $results]);
    }

    // ... inside ForumUserController class ...

    // Show "My Bookmarks" Page
    public function showBookmarks()
    {
        // 1. Security check: saved items are personal to the logged-in user.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // ForumModel reads saved forum questions.
        $forumModel = new ForumModel();
        // EduResourceModel reads saved education resources, so this page can show both.
        $eduModel = new EduResourceModel();
        
        // 2. Fetch saved forum questions and saved archive resources.
        // Model calls
        $bookmarks = $forumModel->getBookmarkedQuestions($_SESSION['user_id']);
        $resourceBookmarks = $eduModel->getBookmarkedResources($_SESSION['user_id']);

        // 3. Pass both lists to the bookmarks view.
        // Pass data to the view
        $this->viewApp('User/edu-forum/bookmarks-view', [
            'questions' => $bookmarks,
            'resources' => $resourceBookmarks
        ], 'My Bookmarks - ReidHub');
    }
}
