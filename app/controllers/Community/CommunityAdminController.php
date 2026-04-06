<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';
require_once __DIR__ . '/../../models/CommunityAdmin.php';
require_once __DIR__ . '/../../models/Blog.php';
require_once __DIR__ . '/../../models/Club.php';
require_once __DIR__ . '/../../models/Event.php';
require_once __DIR__ . '/../../models/Report.php';

class Community_CommunityAdminController extends Controller
{
    private function jsonResponse(bool $success, $data = null, string $message = ''): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function showCommunityAdminDashboard()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);

        $data = ['admin' => $admin];
        $this->viewApp('/Admin/community-and-social/manage-community-view', $data, 'Community Management - ReidHub');
    }

    public function getCommunityAdmins()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $model = new CommunityAdmin();
            $admins = $model->getAllAdmins();
            $this->jsonResponse(true, $admins);
        } catch (Throwable $e) {
            Logger::error('getCommunityAdmins failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, [], 'Failed to load community admins');
        }
    }

    public function searchUsers()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $query = trim($_GET['q'] ?? '');
            if ($query === '') {
                $this->jsonResponse(true, []);
                return;
            }

            $model = new CommunityAdmin();
            $users = $model->searchUsers($query, 20);
            $this->jsonResponse(true, $users);
        } catch (Throwable $e) {
            Logger::error('searchUsers failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, [], 'Failed to search users');
        }
    }

    public function addCommunityAdmin()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $input = $this->getJsonBody();
            $userId = (int)($input['user_id'] ?? 0);
            $roleType = trim((string)($input['role_type'] ?? ''));

            $allowedRoles = ['club_admin', 'event_coordinator', 'community_admin'];

            if ($userId <= 0 || !in_array($roleType, $allowedRoles, true)) {
                http_response_code(422);
                $this->jsonResponse(false, null, 'Invalid user or permission');
                return;
            }

            $model = new CommunityAdmin();
            $ok = $model->addAdmin($userId, $roleType);

            if (!$ok) {
                http_response_code(409);
                $this->jsonResponse(false, null, 'Admin already exists for this permission');
                return;
            }

            $this->jsonResponse(true, null, 'Community admin added successfully');
        } catch (Throwable $e) {
            Logger::error('addCommunityAdmin failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, null, 'Failed to add community admin');
        }
    }

    public function removeCommunityAdmin()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $input = $this->getJsonBody();
            $communityAdminId = (int)($input['community_admin_id'] ?? 0);

            if ($communityAdminId <= 0) {
                http_response_code(422);
                $this->jsonResponse(false, null, 'Invalid community admin id');
                return;
            }

            $model = new CommunityAdmin();
            $ok = $model->removeAdminById($communityAdminId);

            if (!$ok) {
                http_response_code(404);
                $this->jsonResponse(false, null, 'Community admin not found');
                return;
            }

            $this->jsonResponse(true, null, 'Community admin removed successfully');
        } catch (Throwable $e) {
            Logger::error('removeCommunityAdmin failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, null, 'Failed to remove community admin');
        }
    }

    public function getReportedBlogs()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $model = new CommunityAdmin();
            $blogs = $model->getReportedBlogs();
            $this->jsonResponse(true, $blogs);
        } catch (Throwable $e) {
            Logger::error('getReportedBlogs failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, [], 'Failed to load reported blogs');
        }
    }

    public function deleteBlog()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $input = $this->getJsonBody();
            $blogId = (int)($input['blog_id'] ?? 0);

            if ($blogId <= 0) {
                http_response_code(422);
                $this->jsonResponse(false, null, 'Invalid blog id');
                return;
            }

            $model = new CommunityAdmin();
            $ok = $model->deleteBlogById($blogId);

            if (!$ok) {
                http_response_code(404);
                $this->jsonResponse(false, null, 'Blog not found');
                return;
            }

            $this->jsonResponse(true, null, 'Blog deleted successfully');
        } catch (Throwable $e) {
            Logger::error('deleteBlog failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, null, 'Failed to delete blog');
        }
    }

    public function getClubs()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $model = new CommunityAdmin();
            $clubs = $model->getAllClubsForAdmin();
            $this->jsonResponse(true, $clubs);
        } catch (Throwable $e) {
            Logger::error('getClubs failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, [], 'Failed to load clubs');
        }
    }

    public function deleteClub()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $input = $this->getJsonBody();
            $clubId = (int)($input['club_id'] ?? 0);

            if ($clubId <= 0) {
                http_response_code(422);
                $this->jsonResponse(false, null, 'Invalid club id');
                return;
            }

            $model = new CommunityAdmin();
            $ok = $model->deleteClubById($clubId);

            if (!$ok) {
                http_response_code(404);
                $this->jsonResponse(false, null, 'Club not found');
                return;
            }

            $this->jsonResponse(true, null, 'Club deleted successfully');
        } catch (Throwable $e) {
            Logger::error('deleteClub failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, null, 'Failed to delete club');
        }
    }

    public function getEvents()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $model = new CommunityAdmin();
            $events = $model->getAllEventsForAdmin();
            $this->jsonResponse(true, $events);
        } catch (Throwable $e) {
            Logger::error('getEvents failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, [], 'Failed to load events');
        }
    }

    public function deleteEvent()
    {
        Auth_LoginController::getSessionAdmin(true);

        try {
            $input = $this->getJsonBody();
            $eventId = (int)($input['event_id'] ?? 0);

            if ($eventId <= 0) {
                http_response_code(422);
                $this->jsonResponse(false, null, 'Invalid event id');
                return;
            }

            $model = new CommunityAdmin();
            $ok = $model->deleteEventById($eventId);

            if (!$ok) {
                http_response_code(404);
                $this->jsonResponse(false, null, 'Event not found');
                return;
            }

            $this->jsonResponse(true, null, 'Event deleted successfully');
        } catch (Throwable $e) {
            Logger::error('deleteEvent failed: ' . $e->getMessage());
            http_response_code(500);
            $this->jsonResponse(false, null, 'Failed to delete event');
        }
    }

    public function showViewBlog()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $blogId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($blogId <= 0) {
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }

        try {
            $blogModel = new Blog();
            $blog = $blogModel->getBlogById($blogId);
            if (!$blog) {
                header('Location: /dashboard/community/admin', true, 302);
                exit;
            }

            $reportModel = new Report();
            $reports = $reportModel->getReportsByType('blog');
            $hasReports = false;
            foreach ($reports as $report) {
                if ((int)($report['content_id'] ?? 0) === $blogId) {
                    $hasReports = true;
                    break;
                }
            }

            $data = [
                'admin' => $admin,
                'blog' => $blog,
                'hasReports' => $hasReports
            ];

            $this->viewApp('/Admin/community-and-social/view-blog', $data, 'View Blog - Admin - ReidHub');
        } catch (Throwable $e) {
            Logger::error('showViewBlog failed: ' . $e->getMessage());
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }
    }

    public function showViewClub()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $clubId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($clubId <= 0) {
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }

        try {
            $clubModel = new Club();
            $club = $clubModel->getClubById($clubId);
            if (!$club) {
                header('Location: /dashboard/community/admin', true, 302);
                exit;
            }

            $data = [
                'admin' => $admin,
                'club' => $club
            ];

            $this->viewApp('/Admin/community-and-social/view-club', $data, 'View Club - Admin - ReidHub');
        } catch (Throwable $e) {
            Logger::error('showViewClub failed: ' . $e->getMessage());
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }
    }

    public function showViewEvent()
    {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($eventId <= 0) {
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }

        try {
            $eventModel = new Event();
            $event = $eventModel->getEventById($eventId);
            if (!$event) {
                header('Location: /dashboard/community/admin', true, 302);
                exit;
            }

            $attendees = $eventModel->getEventAttendees($eventId);

            $data = [
                'admin' => $admin,
                'event' => $event,
                'attendees' => $attendees
            ];

            $this->viewApp('/Admin/community-and-social/view-event', $data, 'View Event - Admin - ReidHub');
        } catch (Throwable $e) {
            Logger::error('showViewEvent failed: ' . $e->getMessage());
            header('Location: /dashboard/community/admin', true, 302);
            exit;
        }
    }
}
