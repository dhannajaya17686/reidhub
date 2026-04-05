<?php
class Dashboard_FAQController extends Controller
{
    /**
     * Show FAQ page
     */
    public function showFAQ()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        $faq = new FAQ();
        $faqs = $faq->getAllActive();
        $totalCount = $faq->getTotalCount();

        $this->viewApp('User/help/faq-view', [
            'faqs' => $faqs,
            'totalCount' => $totalCount
        ], 'Frequently Asked Questions');
    }

    /**
     * Search FAQs API
     */
    public function searchFAQApi()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');

        $keyword = $_GET['q'] ?? '';

        if (empty($keyword)) {
            $faq = new FAQ();
            $results = $faq->getAllActive();
        } else {
            $faq = new FAQ();
            $results = $faq->search($keyword);
        }

        echo json_encode([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }
}
?>
