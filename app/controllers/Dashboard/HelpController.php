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
        $category = $_POST['category'] ?? 'academic_issues';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        $imagePath = null;

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
        $validCategories = ['academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks'];
        if (!in_array($category, $validCategories)) {
            $category = 'academic_issues';
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->handleImageUpload($_FILES['image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                header('Location: /dashboard/help');
                return;
            }
        }

        $questionId = $userQuestion->create($userId, $category, $subject, $message, $imagePath);

        if ($questionId) {
            $_SESSION['success'] = 'Your question has been submitted successfully!';
            header('Location: /dashboard/help/my-questions');
        } else {
            $_SESSION['error'] = 'Failed to submit question. Please try again.';
            header('Location: /dashboard/help');
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file)
    {
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
        $uploadDir = __DIR__ . '/../../public/storage/complaints/';

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size exceeds 5MB limit.'];
        }

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Only PNG, JPG, JPEG, and GIF are allowed.'];
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload failed.'];
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'complaint_' . time() . '_' . uniqid() . '.' . $ext;
        $filePath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'path' => '/storage/complaints/' . $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to save uploaded file.'];
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
        ], 'My Complains');
    }

    /**
     * Show edit complaint form
     */
    public function showEditForm()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        $questionId = $_GET['id'] ?? null;
        
        if (!$questionId) {
            $_SESSION['error'] = 'Invalid question ID';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        $userQuestion = new UserQuestion();
        $question = $userQuestion->findById($questionId);

        // Verify the question belongs to the current user
        if (!$question || $question['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'You do not have permission to edit this complaint';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        // Check if complaint can still be edited (only if status is 'open' or 'pending')
        if ($question['status'] !== 'open' && $question['status'] !== 'pending') {
            $_SESSION['error'] = 'This complaint cannot be edited as it has already been replied to.';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        $this->viewApp('User/help/edit-complain-view', [
            'question' => $question
        ], 'Edit Complaint');
    }

    /**
     * Save edited complaint
     */
    public function saveEdit()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/help/my-questions');
            return;
        }

        $userQuestion = new UserQuestion();
        $userId = $_SESSION['user_id'];
        $questionId = $_POST['question_id'] ?? null;
        $category = $_POST['category'] ?? 'academic_issues';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        $removeImage = isset($_POST['remove_image']) ? (bool)$_POST['remove_image'] : false;
        $imagePath = null;

        // Validation
        if (empty($questionId) || empty($subject) || empty($message)) {
            $_SESSION['error'] = 'Subject and message are required.';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        if (strlen($subject) > 255 || strlen($message) > 5000) {
            $_SESSION['error'] = 'Subject or message is too long.';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        // Get the existing question
        $question = $userQuestion->findById($questionId);
        
        if (!$question || $question['user_id'] != $userId) {
            $_SESSION['error'] = 'You do not have permission to edit this complaint.';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        // Check if complaint can still be edited
        if ($question['status'] !== 'open' && $question['status'] !== 'pending') {
            $_SESSION['error'] = 'This complaint cannot be edited as it has already been replied to.';
            header('Location: /dashboard/help/my-questions');
            return;
        }

        // Validate category
        $validCategories = ['academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks'];
        if (!in_array($category, $validCategories)) {
            $category = 'academic_issues';
        }

        // Handle image upload or removal
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->handleImageUpload($_FILES['image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
                // Delete old image if exists
                if (!empty($question['image_path'])) {
                    $this->deleteImageFile($question['image_path']);
                }
            } else {
                $_SESSION['error'] = $uploadResult['error'];
                header('Location: /dashboard/help/edit?id=' . $questionId);
                return;
            }
        } elseif ($removeImage && !empty($question['image_path'])) {
            // Delete the image file
            $this->deleteImageFile($question['image_path']);
            $imagePath = null;
        } else {
            // Keep existing image
            $imagePath = $question['image_path'];
        }

        // Update the question
        if ($userQuestion->update($questionId, $userId, $category, $subject, $message, $imagePath)) {
            $_SESSION['success'] = 'Your complaint has been updated successfully!';
            header('Location: /dashboard/help/my-questions');
        } else {
            $_SESSION['error'] = 'Failed to update complaint. Please try again.';
            header('Location: /dashboard/help/edit?id=' . $questionId);
        }
    }

    /**
     * Delete image file
     */
    private function deleteImageFile($imagePath)
    {
        try {
            $basePath = __DIR__ . '/../../public';
            $filePath = $basePath . $imagePath;
            $realPath = realpath($filePath);
            $allowedDir = realpath($basePath . '/storage/complaints');
            
            if ($realPath && strpos($realPath, $allowedDir) === 0 && file_exists($realPath)) {
                unlink($realPath);
            }
        } catch (Exception $e) {
            Logger::error("Error deleting image: " . $e->getMessage());
        }
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
