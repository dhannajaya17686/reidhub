<?php

require_once __DIR__ . '/../Auth/LoginController.php';

class Forum_ForumAdminController extends Controller {
    public function showForumAdminDashboard() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        $forumAdminModel = new ForumAdminModel();

        $status = $_GET['status'] ?? 'all';
        $search = trim($_GET['q'] ?? '');
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');

        $questions = $forumAdminModel->getQuestionsForModeration(
            $status,
            $search ?: null,
            $dateFrom ?: null,
            $dateTo ?: null
        );

        $this->viewApp('Admin/edu-forum/manage-forum-view', [
            'admin' => $admin,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            'question_stats' => $forumAdminModel->getQuestionStats(),
            'report_stats' => $forumAdminModel->getReportStats(),
            'questions' => $questions,
            'answers' => $forumAdminModel->getAnswersForModeration(
                $status,
                $search ?: null,
                $dateFrom ?: null,
                $dateTo ?: null
            ),
            'comments' => $forumAdminModel->getCommentsForModeration(
                $status,
                $search ?: null,
                $dateFrom ?: null,
                $dateTo ?: null
            ),
            'reports' => $forumAdminModel->getPendingReports(),
            'active_suspensions' => $forumAdminModel->getActiveSuspensions()
        ], 'Forum Admin Dashboard - ReidHub');
    }

    public function moderateQuestion() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $questionId = (int)($_POST['question_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['moderation_note'] ?? '');

        Logger::debug('Forum admin moderateQuestion request: ' . json_encode([
            'question_id' => $questionId,
            'action' => $action,
            'note' => $note,
            'admin_id' => (int)($admin['id'] ?? 0),
            'post_keys' => array_keys($_POST)
        ]));

        $forumAdminModel = new ForumAdminModel();
        $ok = $questionId > 0 && $forumAdminModel->moderateQuestion($questionId, (int)$admin['id'], $action, $note ?: null);

        Logger::debug('Forum admin moderateQuestion result: ' . json_encode([
            'question_id' => $questionId,
            'ok' => $ok
        ]));

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=question_moderated' : 'error=question_moderation_failed'));
        exit;
    }

    public function moderateAnswer() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $answerId = (int)($_POST['answer_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['moderation_note'] ?? '');

        $forumAdminModel = new ForumAdminModel();
        $ok = $answerId > 0 && $forumAdminModel->moderateAnswer($answerId, (int)$admin['id'], $action, $note ?: null);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=answer_moderated' : 'error=answer_moderation_failed'));
        exit;
    }

    public function moderateComment() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['moderation_note'] ?? '');

        $forumAdminModel = new ForumAdminModel();
        $ok = $commentId > 0 && $forumAdminModel->moderateComment($commentId, (int)$admin['id'], $action, $note ?: null);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=comment_moderated' : 'error=comment_moderation_failed'));
        exit;
    }

    public function updateQuestionMetadata() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $questionId = (int)($_POST['question_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $tags = trim($_POST['tags'] ?? '');

        if ($questionId <= 0 || $title === '' || $content === '' || $category === '') {
            header('Location: /dashboard/forum/admin?error=invalid_question_update_input');
            exit;
        }

        $forumAdminModel = new ForumAdminModel();
        $okContent = $forumAdminModel->updateQuestionContentByAdmin($questionId, (int)$admin['id'], $title, $content);
        $okMeta = $forumAdminModel->updateQuestionClassification($questionId, (int)$admin['id'], $category, $tags);
        $ok = $okContent || $okMeta;

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=question_updated' : 'error=question_update_failed'));
        exit;
    }

    public function updateAnswer() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $answerId = (int)($_POST['answer_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($answerId <= 0 || $content === '') {
            header('Location: /dashboard/forum/admin?error=invalid_answer_update_input');
            exit;
        }

        $forumAdminModel = new ForumAdminModel();
        $ok = $forumAdminModel->updateAnswerContentByAdmin($answerId, (int)$admin['id'], $content);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=answer_updated' : 'error=answer_update_failed'));
        exit;
    }

    public function updateComment() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if ($commentId <= 0 || $content === '') {
            header('Location: /dashboard/forum/admin?error=invalid_comment_update_input');
            exit;
        }

        $forumAdminModel = new ForumAdminModel();
        $ok = $forumAdminModel->updateCommentContentByAdmin($commentId, (int)$admin['id'], $content);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=comment_updated' : 'error=comment_update_failed'));
        exit;
    }

    public function reviewReport() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $reportId = (int)($_POST['report_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $reviewMessage = trim($_POST['review_message'] ?? '');
        $targetType = trim($_POST['target_type'] ?? '');
        $targetId = (int)($_POST['target_id'] ?? 0);
        $targetAction = trim($_POST['target_action'] ?? '');
        if ($reviewMessage === '') {
            $reviewMessage = 'No details provided.';
        }

        $forumAdminModel = new ForumAdminModel();
        $ok = $reportId > 0 && $forumAdminModel->reviewReport($reportId, (int)$admin['id'], $action, $reviewMessage);
        if ($ok && $targetAction !== '' && $targetAction !== 'none' && $targetId > 0) {
            $forumAdminModel->moderateReportedTarget((int)$admin['id'], $targetType, $targetId, $targetAction, $reviewMessage);
        }

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=report_reviewed' : 'error=report_review_failed'));
        exit;
    }

    public function suspendUser() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $durationDays = (int)($_POST['duration_days'] ?? 0);
        $isPermanent = (int)($_POST['is_permanent'] ?? 0) === 1;

        if ($userId <= 0 || $reason === '') {
            header('Location: /dashboard/forum/admin?error=invalid_suspension_input');
            exit;
        }

        $endsAt = null;
        if (!$isPermanent) {
            if ($durationDays <= 0) {
                header('Location: /dashboard/forum/admin?error=invalid_suspension_duration');
                exit;
            }
            $endsAt = date('Y-m-d H:i:s', strtotime('+' . $durationDays . ' days'));
        }

        $forumAdminModel = new ForumAdminModel();
        $ok = $forumAdminModel->createUserSuspension((int)$admin['id'], $userId, $reason, $endsAt, $isPermanent);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=user_suspended' : 'error=user_suspend_failed'));
        exit;
    }

    public function liftSuspension() {
        Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $forumAdminModel = new ForumAdminModel();
        $ok = $userId > 0 && $forumAdminModel->liftUserSuspension($userId);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=suspension_lifted' : 'error=suspension_lift_failed'));
        exit;
    }

    public function sendUserMessage() {
        $admin = Auth_LoginController::getSessionAdmin(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/forum/admin');
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $messageType = trim($_POST['message_type'] ?? 'warning');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($userId <= 0 || $subject === '' || $body === '') {
            header('Location: /dashboard/forum/admin?error=invalid_message_input');
            exit;
        }

        $forumAdminModel = new ForumAdminModel();
        $ok = $forumAdminModel->sendAdminMessage((int)$admin['id'], $userId, $messageType, $subject, $body);

        header('Location: /dashboard/forum/admin?' . ($ok ? 'success=message_sent' : 'error=message_send_failed'));
        exit;
    }
    public function showCommunityAdminDashboard() {
        $this->viewApp('Admin/community-and-social/manage-community-view', [], 'Community Admin Dashboard - ReidHub');
    }
    public function manageLostAndFound() {
        $this->viewApp('Admin/lost-and-found/manage-lost-and-found-view', [], 'Manage Lost and Found - ReidHub');
    }
}
