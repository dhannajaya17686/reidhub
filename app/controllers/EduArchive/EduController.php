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
    
    // Main Archive Page
    public function index() {
        $model = new EduResourceModel();
        
        // Filters
        $type = $_GET['type'] ?? 'all';
        $subject = $_GET['subject'] ?? null;
        $year = $_GET['year'] ?? null;
        $search = $_GET['q'] ?? null;
        $tag = $_GET['tag'] ?? null;

        $resources = $model->getAllResources($type, $subject, $year, $search, $tag);
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
            'filterTags' => $filterTags
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
                    $data['file_path'] = '/' . $path;
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
        $deleted = $model->deleteResource($_POST['id'], $_SESSION['user_id']);
        if ($deleted) {
            header("Location: /dashboard/edu-archive/my-submissions?success=deleted");
        } else {
            header("Location: /dashboard/edu-archive/my-submissions?error=cannot_delete");
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
                $data['file_path'] = '/' . $path;
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
            header("Location: /dashboard/edu-archive/my-submissions?success=updated");
        } else {
            header("Location: /dashboard/edu-archive/edit?id={$id}&error=failed");
        }
        exit;
    }
    
    // Bookmark Action (AJAX)
    public function bookmark() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new EduResourceModel();
        $action = $model->toggleBookmark($_SESSION['user_id'], $input['id']);
        echo json_encode(['status' => 'success', 'action' => $action]);
    }
}
