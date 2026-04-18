<?php
class Dashboard_FAQAdminController extends Controller
{
    /**
     * Show FAQ admin dashboard
     */
    public function showFAQDashboard()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        $faq = new FAQ();
        $faqs = $faq->getAllForAdmin();

        $this->viewApp('Admin/faq-admin-dashboard-view', [
            'faqs' => $faqs
        ], 'FAQ Management');
    }

    /**
     * Show add FAQ form
     */
    public function showAddFAQForm()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        $this->viewApp('Admin/faq-add-form-view', [], 'Add FAQ');
    }

    /**
     * Add new FAQ
     */
    public function addFAQ()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/admin/faq');
            return;
        }

        $question = $_POST['question'] ?? '';
        $answer = $_POST['answer'] ?? '';
        $displayOrder = $_POST['display_order'] ?? 0;

        // Validation
        if (empty($question) || empty($answer)) {
            $_SESSION['error'] = 'Question and answer are required.';
            header('Location: /dashboard/admin/faq/add');
            return;
        }

        if (strlen($question) > 500) {
            $_SESSION['error'] = 'Question must be 500 characters or less.';
            header('Location: /dashboard/admin/faq/add');
            return;
        }

        $faq = new FAQ();
        if ($faq->create($question, $answer, (int)$displayOrder)) {
            $_SESSION['success'] = 'FAQ added successfully!';
            header('Location: /dashboard/admin/faq');
        } else {
            $_SESSION['error'] = 'Failed to add FAQ. Please try again.';
            header('Location: /dashboard/admin/faq/add');
        }
    }

    /**
     * Show edit FAQ form
     */
    public function showEditFAQForm()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        $faqId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$faqId) {
            header('Location: /dashboard/admin/faq');
            return;
        }

        $faq = new FAQ();
        $faqItem = $faq->getFAQById($faqId);

        if (!$faqItem) {
            $_SESSION['error'] = 'FAQ not found.';
            header('Location: /dashboard/admin/faq');
            return;
        }

        $this->viewApp('Admin/faq-edit-form-view', [
            'faq' => $faqItem
        ], 'Edit FAQ');
    }

    /**
     * Update FAQ
     */
    public function updateFAQ()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/admin/faq');
            return;
        }

        $faqId = $_POST['faq_id'] ?? 0;
        $question = $_POST['question'] ?? '';
        $answer = $_POST['answer'] ?? '';
        $displayOrder = $_POST['display_order'] ?? 0;

        // Validation
        if (empty($faqId) || empty($question) || empty($answer)) {
            $_SESSION['error'] = 'FAQ ID, question, and answer are required.';
            header('Location: /dashboard/admin/faq/edit?id=' . $faqId);
            return;
        }

        if (strlen($question) > 500) {
            $_SESSION['error'] = 'Question must be 500 characters or less.';
            header('Location: /dashboard/admin/faq/edit?id=' . $faqId);
            return;
        }

        $faq = new FAQ();
        if ($faq->update($faqId, $question, $answer, (int)$displayOrder)) {
            $_SESSION['success'] = 'FAQ updated successfully!';
            header('Location: /dashboard/admin/faq');
        } else {
            $_SESSION['error'] = 'Failed to update FAQ. Please try again.';
            header('Location: /dashboard/admin/faq/edit?id=' . $faqId);
        }
    }

    /**
     * Delete FAQ
     */
    public function deleteFAQ()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/admin/faq');
            return;
        }

        header('Content-Type: application/json');

        $faqId = $_POST['faq_id'] ?? 0;

        if (empty($faqId)) {
            echo json_encode(['error' => 'FAQ ID is required']);
            return;
        }

        $faq = new FAQ();
        if ($faq->softDelete($faqId)) {
            echo json_encode([
                'success' => true,
                'message' => 'FAQ deleted successfully'
            ]);
        } else {
            echo json_encode(['error' => 'Failed to delete FAQ']);
        }
    }
}
?>
