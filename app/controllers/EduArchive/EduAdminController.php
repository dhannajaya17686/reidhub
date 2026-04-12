<?php
require_once __DIR__ . '/../Auth/LoginController.php';

class EduArchive_EduAdminController extends Controller {

    private function normalizeTags($rawTags) {
        $parts = array_filter(array_map('trim', explode(',', (string)$rawTags)));
        $parts = array_map(function($tag) {
            return strtolower($tag);
        }, $parts);
        $parts = array_values(array_unique($parts));
        return implode(', ', $parts);
    }

    private function sanitizeTagName($name) {
        $name = trim((string)$name);
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }

    private function validateMetadataInput($post) {
        $allowedSubjects = ['CS', 'IS', 'SE'];
        $allowedYears = ['1', '2', '3', '4', '5'];

        $data = [
            'title' => trim($post['title'] ?? ''),
            'description' => trim($post['description'] ?? ''),
            'subject' => trim($post['subject'] ?? ''),
            'tags' => $this->normalizeTags($post['tags'] ?? ''),
            'year_level' => trim((string)($post['year_level'] ?? ''))
        ];

        if ($data['title'] === '' || $data['subject'] === '' || $data['year_level'] === '') {
            return [null, 'missing_fields'];
        }
        if (!in_array($data['subject'], $allowedSubjects, true) || !in_array($data['year_level'], $allowedYears, true)) {
            return [null, 'invalid_input'];
        }

        return [$data, null];
    }

    private function redirectBack($status, $message, $qs = '') {
        $suffix = $qs ? ('&' . ltrim($qs, '&')) : '';
        header('Location: /dashboard/edu-archive/admin?' . $status . '=' . urlencode($message) . $suffix);
        exit;
    }

    public function showManageArchive() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $model = new EduResourceModel();

        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $subject = $_GET['subject'] ?? '';
        $year = $_GET['year'] ?? '';
        $search = $_GET['q'] ?? '';
        $tag = $_GET['tag'] ?? '';
        $hidden = $_GET['hidden'] ?? '';
        $removal = $_GET['removal'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $totalResources = $model->getAdminResourcesCount($status, $type, $subject ?: null, $year ?: null, $search ?: null, $tag ?: null, $hidden ?: null, $removal ?: null);
        $totalPages = max(1, (int)ceil($totalResources / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $resources = $model->getAdminResources($status, $type, $subject ?: null, $year ?: null, $search ?: null, $tag ?: null, $hidden ?: null, $perPage, $offset, $removal ?: null);
        $counts = $model->getAdminCounts();
        $filterTags = [];
        try {
            $filterTags = $model->getFilterTags();
        } catch (Exception $e) {
            $filterTags = [];
        }

        $this->viewApp('/Admin/edu-archive/manage-archive-view', [
            'admin' => $admin,
            'resources' => $resources,
            'counts' => $counts,
            'filters' => [
                'status' => $status,
                'type' => $type,
                'subject' => $subject,
                'year' => $year,
                'search' => $search,
                'tag' => $tag,
                'hidden' => $hidden,
                'removal' => $removal
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalResources,
                'total_pages' => $totalPages
            ],
            'filterTags' => $filterTags
        ], 'Edu Archive Admin - ReidHub');
    }

    public function moderateResource() {
        Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/edu-archive/admin');
            exit;
        }

        $model = new EduResourceModel();
        $id = (int)($_POST['id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $qs = trim($_POST['return_qs'] ?? '');

        if ($id <= 0 || $action === '') {
            $this->redirectBack('error', 'invalid_request', $qs);
        }

        $resource = $model->getResourceById($id);
        if (!$resource) {
            $this->redirectBack('error', 'not_found', $qs);
        }

        if ($action === 'save_metadata' || $action === 'approve') {
            [$data, $error] = $this->validateMetadataInput($_POST);
            if ($error) {
                $this->redirectBack('error', $error, $qs);
            }
            $model->updateResourceMetadataByAdmin($id, $data);
            if ($action === 'save_metadata') {
                $this->redirectBack('success', 'metadata_updated', $qs);
            }

            $ok = $model->approveResource($id);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'approved' : 'approve_failed', $qs);
        }

        if ($action === 'reject') {
            $feedback = trim($_POST['admin_feedback'] ?? '');
            if ($feedback === '') {
                $this->redirectBack('error', 'feedback_required', $qs);
            }
            $ok = $model->rejectResource($id, $feedback);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'rejected' : 'reject_failed', $qs);
        }

        if ($action === 'hide') {
            $ok = $model->setResourceHidden($id, 1);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'hidden' : 'hide_failed', $qs);
        }

        if ($action === 'unhide') {
            $ok = $model->setResourceHidden($id, 0);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'unhidden' : 'unhide_failed', $qs);
        }

        if ($action === 'clear_removal_request') {
            $ok = $model->clearRemovalRequest($id);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'removal_request_cleared' : 'clear_request_failed', $qs);
        }

        $this->redirectBack('error', 'unsupported_action', $qs);
    }

    public function manageFilterTag() {
        Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/edu-archive/admin');
            exit;
        }

        $model = new EduResourceModel();
        $action = trim($_POST['action'] ?? '');
        $tagName = $this->sanitizeTagName($_POST['tag_name'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $qs = trim($_POST['return_qs'] ?? '');

        if ($action === 'create_tag') {
            if ($tagName === '') {
                $this->redirectBack('error', 'tag_name_required', $qs);
            }
            $ok = $model->createFilterTag($tagName);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'tag_created' : 'tag_create_failed', $qs);
        }

        if ($action === 'update_tag') {
            if ($id <= 0 || $tagName === '') {
                $this->redirectBack('error', 'invalid_tag_input', $qs);
            }
            $ok = $model->updateFilterTag($id, $tagName);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'tag_updated' : 'tag_update_failed', $qs);
        }

        if ($action === 'delete_tag') {
            if ($id <= 0) {
                $this->redirectBack('error', 'invalid_tag_id', $qs);
            }
            $ok = $model->deleteFilterTag($id);
            $this->redirectBack($ok ? 'success' : 'error', $ok ? 'tag_deleted' : 'tag_delete_failed', $qs);
        }

        $this->redirectBack('error', 'invalid_tag_action', $qs);
    }
}
