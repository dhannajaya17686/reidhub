<?php

class EduArchive_EduController extends Controller {

    private function normalizeTags($rawTags) {
        $parts = array_filter(array_map('trim', explode(',', (string)$rawTags)));
        $parts = array_map(function($tag) {
            return strtolower($tag);
        }, $parts);
        $parts = array_values(array_unique($parts));
        return implode(', ', $parts);
    }

    private function isValidYoutubeUrl($url) {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return (bool)preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)/i', $url);
    }

    private function deleteStoredNoteFile($filePath) {
        if (empty($filePath)) {
            return false;
        }

        $relativePath = str_replace('\\', '/', parse_url((string)$filePath, PHP_URL_PATH) ?: '');
        $relativePath = ltrim($relativePath, '/');
        
        // Accommodate both old DB entries ('public/storage/...') and new ones ('storage/...')
        if (str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, 7); // Removes 'public/'
        }

        $storagePrefix = 'storage/edu-notes/';

        if (!str_starts_with($relativePath, $storagePrefix)) {
            return false;
        }

        $storageRoot = realpath(__DIR__ . '/../../../public/storage/edu-notes');
        $targetPath = realpath(__DIR__ . '/../../../public/' . $relativePath);

        if (!$storageRoot || !$targetPath || !is_file($targetPath)) {
            return false;
        }

        $normalizedRoot = rtrim(strtolower(str_replace('\\', '/', $storageRoot)), '/') . '/';
        $normalizedTarget = strtolower(str_replace('\\', '/', $targetPath));

        if (!str_starts_with($normalizedTarget, $normalizedRoot)) {
            return false;
        }

        return unlink($targetPath);
    }
    
    // Main Archive Page
    public function index() {
        $model = new EduResourceModel();
        
        // Filters
        $type = $_GET['type'] ?? 'all';
        $subject = $_GET['subject'] ?? null;
        $year = $_GET['year'] ?? null;
        $search = $_GET['q'] ?? null;
        $tag = $_GET['tag'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag);
        $totalPages = max(1, (int)ceil($totalResources / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset);
        $filterTags = [];
        try {
            $filterTags = $model->getFilterTags();
        } catch (Exception $e) {
            $filterTags = [];
        }
        
        // Check bookmarks if logged in
        if (isset($_SESSION['user_id'])) {
            foreach ($resources as &$res) {
                $res['is_bookmarked'] = $model->isBookmarked($res['id'], $_SESSION['user_id']);
            }
        }

        $this->viewApp('User/edu-archive/archive-view', [
            'resources' => $resources,
            'filters' => ['type' => $type, 'subject' => $subject, 'year' => $year, 'search' => $search, 'tag' => $tag],
            'filterTags' => $filterTags,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalResources,
                'total_pages' => $totalPages
            ]
        ], 'Edu Archive - ReidHub');
    }

    // Show Upload Form
    public function showUploadForm() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        $model = new EduResourceModel();
        $myResources = $model->getMySubmissions($_SESSION['user_id']);
        $this->viewApp('User/edu-archive/upload-view', ['resources' => $myResources], 'Upload Resource - ReidHub');
    }

    // Handle Upload Logic
    public function handleUpload() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allowedSubjects = ['CS', 'IS', 'SE'];
            $allowedYears = ['1', '2', '3', '4', '5'];
            $allowedTypes = ['video', 'note'];

            $data = [
                'user_id' => $_SESSION['user_id'],
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'subject' => $_POST['subject'],
                'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
                'year_level' => $_POST['year_level'],
                'type' => $_POST['type']
            ];

            if (empty($data['title']) || empty($data['subject']) || empty($data['year_level']) || empty($data['type'])) {
                header("Location: /dashboard/edu-archive/upload?error=missing_fields");
                exit;
            }
            if (!in_array($data['subject'], $allowedSubjects, true) ||
                !in_array((string)$data['year_level'], $allowedYears, true) ||
                !in_array($data['type'], $allowedTypes, true)) {
                header("Location: /dashboard/edu-archive/upload?error=invalid_input");
                exit;
            }

            // Handle Video Link
            if ($data['type'] === 'video') {
                $videoLink = trim($_POST['video_link'] ?? '');
                if (!$this->isValidYoutubeUrl($videoLink)) {
                    header("Location: /dashboard/edu-archive/upload?error=invalid_youtube");
                    exit;
                }
                $data['video_link'] = $videoLink;
            } 
            // Handle File Upload (Notes)
            else if ($data['type'] === 'note' && isset($_FILES['note_file'])) {
                $file = $_FILES['note_file'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];

                if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
                    header("Location: /dashboard/edu-archive/upload?error=file_upload_failed");
                    exit;
                }

                if ((int)$file['size'] > 10 * 1024 * 1024) {
                    header("Location: /dashboard/edu-archive/upload?error=file_too_large");
                    exit;
                }

                if (in_array(strtolower($ext), $allowed, true)) {
                    $filename = uniqid('note_') . '.' . $ext;
                    $path = 'public/storage/edu-notes/' . $filename;
                    
                    // Create directory if not exists
                    if (!is_dir(__DIR__ . '/../../../public/storage/edu-notes/')) {
                        mkdir(__DIR__ . '/../../../public/storage/edu-notes/', 0777, true);
                    }

                    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../' . $path)) {
                        header("Location: /dashboard/edu-archive/upload?error=file_upload_failed");
                        exit;
                    }
                $data['file_path'] = '/storage/edu-notes/' . $filename;
                } else {
                    header("Location: /dashboard/edu-archive/upload?error=invalid_file");
                    exit;
                }
            } else {
                header("Location: /dashboard/edu-archive/upload?error=invalid_type");
                exit;
            }

            $model = new EduResourceModel();
            if ($model->createResource($data)) {
                header("Location: /dashboard/edu-archive/upload?success=uploaded");
            } else {
                header("Location: /dashboard/edu-archive/upload?error=failed");
            }
            exit;
        }
        header("Location: /dashboard/edu-archive/upload?error=invalid_request");
        exit;
    }

    // Show My Submissions (Track Status)
    public function showMySubmissions() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        
        $model = new EduResourceModel();
        $myResources = $model->getMySubmissions($_SESSION['user_id']);

        $this->viewApp('User/edu-archive/my-submissions-view', ['resources' => $myResources], 'My Submissions');
    }

    // Delete Submission
    public function deleteSubmission() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header("Location: /dashboard/edu-archive/my-submissions?error=invalid_request");
            exit;
        }

        $model = new EduResourceModel();
        $resource = $model->getResourceById($_POST['id'], $_SESSION['user_id']);
        $deleted = $model->deleteResource($_POST['id'], $_SESSION['user_id']);
        if ($deleted) {
            if (($resource['type'] ?? '') === 'note') {
                $this->deleteStoredNoteFile($resource['file_path'] ?? null);
            }
            header("Location: /dashboard/edu-archive/my-submissions?success=deleted");
        } else {
            header("Location: /dashboard/edu-archive/my-submissions?error=cannot_delete");
        }
        exit;
    }

    // Request removal for already approved resources
    public function requestRemoval() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
            header("Location: /dashboard/edu-archive/my-submissions?error=invalid_request");
            exit;
        }

        $reason = trim($_POST['removal_reason'] ?? '');
        if ($reason === '') {
            header("Location: /dashboard/edu-archive/my-submissions?error=reason_required");
            exit;
        }

        if (strlen($reason) > 500) {
            $reason = substr($reason, 0, 500);
        }

        $model = new EduResourceModel();
        $requested = $model->requestRemoval((int)$_POST['id'], $_SESSION['user_id'], $reason);

        if ($requested) {
            header("Location: /dashboard/edu-archive/my-submissions?success=removal_requested");
        } else {
            header("Location: /dashboard/edu-archive/my-submissions?error=request_failed");
        }
        exit;
    }

    // Show Edit Form (only pending + owner)
    public function showEditForm() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: /dashboard/edu-archive/my-submissions?error=missing_id");
            exit;
        }

        $model = new EduResourceModel();
        $resource = $model->getResourceById($id, $_SESSION['user_id']);

        if (!$resource) {
            header("Location: /dashboard/edu-archive/my-submissions?error=not_found");
            exit;
        }

        if ($resource['status'] !== 'pending') {
            header("Location: /dashboard/edu-archive/my-submissions?error=cannot_edit");
            exit;
        }

        $this->viewApp('User/edu-archive/edit-submission-view', ['resource' => $resource], 'Edit Submission');
    }

    // Handle edit update (only pending + owner)
    public function updateSubmission() {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /dashboard/edu-archive/my-submissions'); exit; }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header("Location: /dashboard/edu-archive/my-submissions?error=missing_id");
            exit;
        }

        $model = new EduResourceModel();
        $existing = $model->getResourceById($id, $_SESSION['user_id']);
        if (!$existing) {
            header("Location: /dashboard/edu-archive/my-submissions?error=not_found");
            exit;
        }
        if ($existing['status'] !== 'pending') {
            header("Location: /dashboard/edu-archive/my-submissions?error=cannot_edit");
            exit;
        }

        $type = $_POST['type'] ?? $existing['type'];
        $allowedSubjects = ['CS', 'IS', 'SE'];
        $allowedYears = ['1', '2', '3', '4', '5'];
        $allowedTypes = ['video', 'note'];
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
            'year_level' => trim($_POST['year_level'] ?? ''),
            'type' => $type,
            'video_link' => null,
            'file_path' => null
        ];
        $previousFilePath = $existing['file_path'] ?? null;
        $uploadedFilePath = null;

        if (empty($data['title']) || empty($data['subject']) || empty($data['year_level']) || empty($data['type'])) {
            header("Location: /dashboard/edu-archive/edit?id={$id}&error=missing_fields");
            exit;
        }
        if (!in_array($data['subject'], $allowedSubjects, true) ||
            !in_array((string)$data['year_level'], $allowedYears, true) ||
            !in_array($data['type'], $allowedTypes, true)) {
            header("Location: /dashboard/edu-archive/edit?id={$id}&error=invalid_input");
            exit;
        }

        if ($type === 'video') {
            $videoLink = trim($_POST['video_link'] ?? $existing['video_link'] ?? '');
            if (!$this->isValidYoutubeUrl($videoLink)) {
                header("Location: /dashboard/edu-archive/edit?id={$id}&error=invalid_youtube");
                exit;
            }
            $data['video_link'] = $videoLink;
            $data['file_path'] = null;
        } elseif ($type === 'note') {
            $data['video_link'] = null;
            $data['file_path'] = $existing['file_path'] ?? null;

            if (isset($_FILES['note_file']) && !empty($_FILES['note_file']['tmp_name'])) {
                $file = $_FILES['note_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    header("Location: /dashboard/edu-archive/edit?id={$id}&error=file_upload_failed");
                    exit;
                }

                if ((int)$file['size'] > 10 * 1024 * 1024) {
                    header("Location: /dashboard/edu-archive/edit?id={$id}&error=file_too_large");
                    exit;
                }

                if (!in_array($ext, $allowed, true)) {
                    header("Location: /dashboard/edu-archive/edit?id={$id}&error=invalid_file");
                    exit;
                }

                $filename = uniqid('note_') . '.' . $ext;
                $path = 'public/storage/edu-notes/' . $filename;
                if (!is_dir(__DIR__ . '/../../../public/storage/edu-notes/')) {
                    mkdir(__DIR__ . '/../../../public/storage/edu-notes/', 0777, true);
                }
                if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../../' . $path)) {
                    header("Location: /dashboard/edu-archive/edit?id={$id}&error=file_upload_failed");
                    exit;
                }
                $data['file_path'] = '/storage/edu-notes/' . $filename;
                $uploadedFilePath = $data['file_path'];
            }

            if (empty($data['file_path'])) {
                header("Location: /dashboard/edu-archive/edit?id={$id}&error=missing_file");
                exit;
            }
        } else {
            header("Location: /dashboard/edu-archive/edit?id={$id}&error=invalid_type");
            exit;
        }

        if ($model->updateResource($id, $_SESSION['user_id'], $data)) {
            if (!empty($previousFilePath) && $previousFilePath !== ($data['file_path'] ?? null)) {
                $this->deleteStoredNoteFile($previousFilePath);
            }
            header("Location: /dashboard/edu-archive/my-submissions?success=updated");
        } else {
            if (!empty($uploadedFilePath)) {
                $this->deleteStoredNoteFile($uploadedFilePath);
            }
            header("Location: /dashboard/edu-archive/edit?id={$id}&error=failed");
        }
        exit;
    }
    
    // Bookmark Action (AJAX)
    public function bookmark() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            return;
        }

        $resourceId = (int)($input['id'] ?? 0);
        if ($resourceId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid resource']);
            return;
        }

        $model = new EduResourceModel();

        if (!$model->isResourceVisibleForBookmark($resourceId)) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Resource is not available']);
            return;
        }

        $action = $model->toggleBookmark($_SESSION['user_id'], $resourceId);
        echo json_encode(['status' => 'success', 'action' => $action]);
    }
}
