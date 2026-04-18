<?php
if (!function_exists('renderAdminQuestionPreviewHtml')) {
    function renderAdminQuestionPreviewHtml($text, $maxLength = 220) {
        $plain = trim((string)$text);
        if ($plain === '') return '';

        $plain = preg_replace('/^\s*[*_`-]{3,}\s*$/m', '', $plain);
        $plain = preg_replace('/\n{3,}/', "\n\n", $plain);

        $wasTruncated = mb_strlen($plain) > $maxLength;
        $snippet = $wasTruncated ? mb_substr($plain, 0, $maxLength) : $plain;

        $snippet = rtrim($snippet, "*_`");
        if ($wasTruncated) $snippet .= '...';

        $html = htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');
        $html = preg_replace('/\*\*([^\*]+)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
        $html = preg_replace('/(^|[\s>])([*_`]{2,})(?=\s|$)/m', '$1', $html);

        $lines = preg_split('/\r\n|\r|\n/', $html);
        $out = [];
        $inList = false;

        foreach ($lines as $line) {
            if (preg_match('/^\s*[-*]\s+(.+)$/', $line, $m)) {
                if (!$inList) {
                    $out[] = '<ul class="admin-question-preview-list">';
                    $inList = true;
                }
                $out[] = '<li>' . $m[1] . '</li>';
            } else {
                if ($inList) {
                    $out[] = '</ul>';
                    $inList = false;
                }
                if (trim($line) !== '') {
                    $out[] = '<p>' . $line . '</p>';
                }
            }
        }
        if ($inList) $out[] = '</ul>';

        return implode('', $out);
    }
}

$manageForumCssVersion = @filemtime(__DIR__ . '/../../../../public/css/app/admin/edu-forum/manage-forum.css') ?: time();
$manageForumJsVersion = @filemtime(__DIR__ . '/../../../../public/js/app/admin/edu-forum/manage-forum.js') ?: time();
$filters = $filters ?? [];
$questionStats = $question_stats ?? [];
$reportStats = $report_stats ?? [];
$questions = $questions ?? [];
$answers = $answers ?? [];
$comments = $comments ?? [];
$reports = $reports ?? [];
$activeSuspensions = $active_suspensions ?? [];
$recentAdminMessages = $recent_admin_messages ?? [];
$flashSuccess = $_GET['success'] ?? null;
$flashError = $_GET['error'] ?? null;
$flashMessages = [
    'question_moderated' => 'Question moderation action completed successfully.',
    'question_moderation_failed' => 'Question moderation failed. Please try again.',
    'answer_moderated' => 'Answer moderation action completed successfully.',
    'answer_moderation_failed' => 'Answer moderation failed. Please try again.',
    'comment_moderated' => 'Comment moderation action completed successfully.',
    'comment_moderation_failed' => 'Comment moderation failed. Please try again.',
    'question_updated' => 'Question details were updated successfully.',
    'question_update_failed' => 'Question update failed. Please try again.',
    'answer_updated' => 'Answer was updated successfully.',
    'answer_update_failed' => 'Answer update failed. Please try again.',
    'comment_updated' => 'Comment was updated successfully.',
    'comment_update_failed' => 'Comment update failed. Please try again.',
    'report_reviewed' => 'Report review was saved successfully.',
    'report_review_failed' => 'Report review failed. Please try again.',
    'user_suspended' => 'User suspension was applied successfully.',
    'user_suspend_failed' => 'User suspension failed. Please try again.',
    'suspension_lifted' => 'Suspension was lifted successfully.',
    'suspension_lift_failed' => 'Could not lift the suspension.',
    'message_sent' => 'Admin message was sent successfully.',
    'message_send_failed' => 'Admin message could not be sent.',
    'invalid_question_update_input' => 'Question update requires a title, category, and content.',
    'invalid_answer_update_input' => 'Answer update requires content.',
    'invalid_comment_update_input' => 'Comment update requires content.',
    'invalid_suspension_input' => 'Suspension requires a user ID and reason.',
    'invalid_suspension_duration' => 'Suspension duration must be greater than zero.',
    'invalid_message_input' => 'Message requires a user ID, subject, and body.'
];
$flashSuccessText = $flashSuccess && isset($flashMessages[$flashSuccess]) ? $flashMessages[$flashSuccess] : $flashSuccess;
$flashErrorText = $flashError && isset($flashMessages[$flashError]) ? $flashMessages[$flashError] : $flashError;
$questionSectionBaseFilters = [
    'q' => $filters['search'] ?? '',
    'date_from' => $filters['date_from'] ?? '',
    'date_to' => $filters['date_to'] ?? ''
];
?>

<link href="/css/app/admin/edu-forum/manage-forum.css?v=<?= (int)$manageForumCssVersion ?>" rel="stylesheet">

<div class="manage-page" data-forum-admin-page>
    <div class="page-header">
        <h1 class="page-title">Forum Moderation Console</h1>
        <p class="page-subtitle">Moderate posts, resolve reports, suspend users, reclassify questions, and send warnings/messages.</p>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="edu-admin-alert success" data-flash-alert>Success: <?= htmlspecialchars($flashSuccessText) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="edu-admin-alert error" data-flash-alert>Error: <?= htmlspecialchars($flashErrorText) ?></div>
    <?php endif; ?>

    <?php
        $allQuestionsLink = '/dashboard/forum/admin?' . http_build_query($questionSectionBaseFilters + ['status' => 'all']) . '#questions-section';
        $activeQuestionsLink = '/dashboard/forum/admin?' . http_build_query($questionSectionBaseFilters + ['status' => 'active']) . '#questions-section';
        $hiddenQuestionsLink = '/dashboard/forum/admin?' . http_build_query($questionSectionBaseFilters + ['status' => 'hidden']) . '#questions-section';
        $deletedQuestionsLink = '/dashboard/forum/admin?' . http_build_query($questionSectionBaseFilters + ['status' => 'deleted']) . '#questions-section';
        $pendingReportsLink = '#reports-section';
    ?>

    <div class="moderation-section-nav moderation-section-nav--main" data-section-nav>
        <div class="moderation-section-tabs" aria-label="Forum moderation areas">
            <a href="#questions-section" class="moderation-section-tab is-active" data-admin-tab="questions-section">
                <span>Question Moderation</span>
                <small><?= (int)($questionStats['total_questions'] ?? count($questions)) ?></small>
            </a>
            <a href="#answers-section" class="moderation-section-tab" data-admin-tab="answers-section">
                <span>Answer Moderation</span>
                <small><?= count($answers) ?></small>
            </a>
            <a href="#comments-section" class="moderation-section-tab" data-admin-tab="comments-section">
                <span>Comment Moderation</span>
                <small><?= count($comments) ?></small>
            </a>
            <a href="#reports-section" class="moderation-section-tab" data-admin-tab="reports-section">
                <span>Report Queue</span>
                <small><?= (int)($reportStats['pending_reports'] ?? count($reports)) ?></small>
            </a>
            <a href="#discipline-section" class="moderation-section-tab" data-admin-tab="discipline-section">
                <span>User Discipline</span>
                <small><?= count($activeSuspensions) ?></small>
            </a>
        </div>
    </div>

    <div class="page-stats page-stats--sub">
        <a href="<?= htmlspecialchars($allQuestionsLink) ?>" class="stat-card stat-card--link">
            <div class="stat-number"><?= (int)($questionStats['total_questions'] ?? 0) ?></div>
            <div class="stat-label">Total Questions</div>
        </a>
        <a href="<?= htmlspecialchars($activeQuestionsLink) ?>" class="stat-card stat-card--link">
            <div class="stat-number"><?= (int)($questionStats['active_questions'] ?? 0) ?></div>
            <div class="stat-label">Active</div>
        </a>
        <a href="<?= htmlspecialchars($hiddenQuestionsLink) ?>" class="stat-card stat-card--link">
            <div class="stat-number"><?= (int)($questionStats['hidden_questions'] ?? 0) ?></div>
            <div class="stat-label">Hidden</div>
        </a>
        <a href="<?= htmlspecialchars($deletedQuestionsLink) ?>" class="stat-card stat-card--link">
            <div class="stat-number"><?= (int)($questionStats['deleted_questions'] ?? 0) ?></div>
            <div class="stat-label">Deleted</div>
        </a>
        <a href="<?= htmlspecialchars($pendingReportsLink) ?>" class="stat-card stat-card--link">
            <div class="stat-number"><?= (int)($reportStats['pending_reports'] ?? 0) ?></div>
            <div class="stat-label">Pending Reports</div>
        </a>
    </div>

    <section id="questions-section" class="moderation-content-section">
        <div class="table-controls">
            <div class="section-heading">
                <h2 class="page-title" style="font-size: 20px;">Question Moderation</h2>
                <p class="section-heading-subtitle">Review forum questions, update their details, and apply moderation actions.</p>
            </div>
            <form method="GET" action="/dashboard/forum/admin" class="advanced-filters" data-filter-form>
                <div class="filter-group">
                    <label class="filter-label" for="status-filter">Status:</label>
                    <select id="status-filter" class="filter-select" name="status">
                        <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="hidden" <?= ($filters['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                        <option value="deleted" <?= ($filters['status'] ?? '') === 'deleted' ? 'selected' : '' ?>>Deleted</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label" for="date-from">Date:</label>
                    <input id="date-from" type="date" class="date-input" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                    <span class="date-separator">to</span>
                    <input type="date" class="date-input" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                </div>
                <div class="filter-group table-search">
                    <input type="search" class="table-search-input" name="q" placeholder="Search title, body, category, tags..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <button type="submit" class="export-btn">Filter</button>
                <a href="/dashboard/forum/admin" class="action-btn">Reset</a>
            </form>
        </div>

        <div class="data-table-container">
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Owner</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                <?php foreach ($questions as $q): ?>
                    <?php
                        $status = $q['moderation_status'] ?? 'active';
                        $statusClass = 'status-badge--active';
                        $ownerName = trim(($q['first_name'] ?? '') . ' ' . ($q['last_name'] ?? ''));
                        $createdTimestamp = !empty($q['created_at']) ? strtotime($q['created_at']) : false;
                        $createdDate = $createdTimestamp ? date('M j, Y', $createdTimestamp) : ($q['created_at'] ?? '-');
                        $createdTime = $createdTimestamp ? date('g:i A', $createdTimestamp) : '';
                        $questionTags = array_filter(array_map('trim', explode(',', (string)($q['tags'] ?? ''))), 'strlen');
                        $questionPreviewHtml = renderAdminQuestionPreviewHtml($q['content'] ?? '', 220);
                        $questionViewLink = '/dashboard/forum/question?id=' . (int)$q['id'];
                        if ($status === 'hidden') $statusClass = 'status-badge--pending';
                        if ($status === 'deleted') $statusClass = 'status-badge--deleted';
                    ?>
                    <tr class="question-row">
                        <td><span class="question-id">#<?= (int)$q['id'] ?></span></td>
                        <td>
                            <div class="question-summary">
                                <div class="question-summary-header">
                                    <div class="question-summary-title"><?= htmlspecialchars($q['title'] ?? '') ?></div>
                                    <a href="<?= htmlspecialchars($questionViewLink) ?>" class="question-view-link" target="_blank" rel="noopener noreferrer">View</a>
                                </div>
                                <?php if ($questionPreviewHtml !== ''): ?>
                                    <div class="question-preview-line question-preview-rich"><?= $questionPreviewHtml ?></div>
                                <?php endif; ?>
                                <div class="question-meta-line">
                                    <span class="question-meta-label">Category:</span>
                                    <span><?= htmlspecialchars($q['category'] ?? 'General') ?></span>
                                </div>
                                <?php if (!empty($questionTags)): ?>
                                    <div class="question-tag-list">
                                        <?php foreach ($questionTags as $tag): ?>
                                            <span class="question-tag-chip"><?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="question-summary-footer">
                                    <span class="question-answer-pill"><?= (int)($q['answer_count'] ?? 0) ?> <?= (int)($q['answer_count'] ?? 0) === 1 ? 'answer' : 'answers' ?></span>
                                </div>
                            </div>
                            <?php if (!empty($q['moderation_note'])): ?>
                                <div class="question-note-box">
                                    <span class="question-meta-label">Note:</span>
                                    <span><?= htmlspecialchars($q['moderation_note']) ?></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="owner-cell">
                                <div class="owner-name"><?= htmlspecialchars($ownerName !== '' ? $ownerName : 'Unknown User') ?></div>
                                <div class="owner-subtext">ID <?= (int)$q['user_id'] ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="date-cell">
                                <div class="date-primary"><?= htmlspecialchars($createdDate) ?></div>
                                <?php if ($createdTime !== ''): ?>
                                    <div class="date-secondary"><?= htmlspecialchars($createdTime) ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><div class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($status)) ?></div></td>
                        <td>
                            <div class="table-actions table-actions--question">
                                <button
                                    class="action-btn action-btn--primary"
                                    type="button"
                                    data-question-editor-toggle
                                    data-question-id="<?= (int)$q['id'] ?>"
                                    aria-expanded="false"
                                    aria-controls="question-editor-<?= (int)$q['id'] ?>"
                                >
                                    Edit
                                </button>
                                <?php if ($status === 'active'): ?>
                                    <form method="POST" action="/dashboard/forum/admin/question/moderate" data-confirm-form>
                                        <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                        <input type="hidden" name="action" value="hide">
                                        <input type="hidden" name="moderation_note" value="Hidden by admin moderation.">
                                        <button class="action-btn" type="submit">Hide</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/dashboard/forum/admin/question/moderate" data-confirm-form>
                                        <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="moderation_note" value="Restored by admin moderation.">
                                        <button class="action-btn" type="submit">Restore</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="/dashboard/forum/admin/question/moderate" data-confirm-form>
                                    <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="moderation_note" value="">
                                    <input type="hidden" name="question_title" value="<?= htmlspecialchars($q['title'] ?? '', ENT_QUOTES) ?>">
                                    <button class="action-btn action-btn--danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr
                        id="question-editor-<?= (int)$q['id'] ?>"
                        class="question-editor-row"
                        data-question-editor-row
                        data-question-id="<?= (int)$q['id'] ?>"
                        hidden
                    >
                        <td colspan="6">
                            <form method="POST" action="/dashboard/forum/admin/question/update" class="moderation-form" data-question-form>
                                <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                <div class="question-editor-shell">
                                    <div class="question-editor-header">
                                        <div>
                                            <h3 class="question-editor-title">Editing Question #<?= (int)$q['id'] ?></h3>
                                            <p class="question-editor-subtitle">Update the title, category, tags, or content before saving changes.</p>
                                        </div>
                                        <button
                                            type="button"
                                            class="action-btn"
                                            data-question-editor-cancel
                                            data-question-id="<?= (int)$q['id'] ?>"
                                        >
                                            Cancel
                                        </button>
                                    </div>

                                    <div class="question-editor-grid">
                                        <div class="question-editor-field question-editor-field--wide">
                                            <label class="filter-label" for="question-title-<?= (int)$q['id'] ?>">Title</label>
                                            <input
                                                id="question-title-<?= (int)$q['id'] ?>"
                                                class="table-search-input editor-input"
                                                type="text"
                                                name="title"
                                                value="<?= htmlspecialchars($q['title'] ?? '') ?>"
                                                placeholder="Enter a clear question title"
                                                required
                                            >
                                        </div>
                                        <div class="question-editor-field">
                                            <label class="filter-label" for="question-category-<?= (int)$q['id'] ?>">Category</label>
                                            <input
                                                id="question-category-<?= (int)$q['id'] ?>"
                                                class="filter-select editor-input"
                                                type="text"
                                                name="category"
                                                value="<?= htmlspecialchars($q['category'] ?? '') ?>"
                                                placeholder="e.g. programming"
                                                required
                                            >
                                        </div>
                                        <div class="question-editor-field">
                                            <label class="filter-label" for="question-tags-<?= (int)$q['id'] ?>">Tags</label>
                                            <input
                                                id="question-tags-<?= (int)$q['id'] ?>"
                                                class="table-search-input editor-input"
                                                type="text"
                                                name="tags"
                                                value="<?= htmlspecialchars($q['tags'] ?? '') ?>"
                                                placeholder="python, node.js, database"
                                            >
                                            <p class="question-editor-help">Use commas to separate tags.</p>
                                        </div>
                                    </div>

                                    <div class="question-editor-field question-editor-field--stacked">
                                        <label class="filter-label" for="question-content-<?= (int)$q['id'] ?>">Content</label>
                                        <textarea
                                            id="question-content-<?= (int)$q['id'] ?>"
                                            name="content"
                                            class="table-search-input editor-input editor-textarea"
                                            rows="5"
                                            data-editor-textarea
                                            required
                                        ><?= htmlspecialchars($q['content'] ?? '') ?></textarea>
                                        <p class="question-editor-help">Keep the question clear and well structured for moderators and users.</p>
                                    </div>
                                </div>

                                <div class="question-editor-actions">
                                    <button
                                        type="button"
                                        class="action-btn"
                                        data-question-editor-cancel
                                        data-question-id="<?= (int)$q['id'] ?>"
                                    >
                                        Cancel
                                    </button>
                                    <button type="submit" class="action-btn action-btn--primary">Save Edit/Reclassify</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($questions)): ?>
                    <tr><td colspan="6" class="text-center">No questions found for current filters.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="table-controls" id="answers-section" style="margin-top: 20px;">
        <div class="section-heading">
            <h2 class="page-title" style="font-size: 20px;">Answer Moderation</h2>
            <p class="section-heading-subtitle">Check answer quality, edit responses, and moderate answer visibility.</p>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Answer</th>
                        <th>Owner</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php foreach ($answers as $answer): ?>
                        <?php
                            $status = $answer['moderation_status'] ?? 'active';
                            $statusClass = 'status-badge--active';
                            $answerOwnerName = trim(($answer['first_name'] ?? '') . ' ' . ($answer['last_name'] ?? ''));
                            $answerCreatedTimestamp = !empty($answer['created_at']) ? strtotime($answer['created_at']) : false;
                            $answerCreatedDate = $answerCreatedTimestamp ? date('M j, Y', $answerCreatedTimestamp) : ($answer['created_at'] ?? '-');
                            $answerCreatedTime = $answerCreatedTimestamp ? date('g:i A', $answerCreatedTimestamp) : '';
                            $answerPreview = trim(strip_tags((string)($answer['content'] ?? '')));
                            if (strlen($answerPreview) > 150) {
                                $answerPreview = substr($answerPreview, 0, 147) . '...';
                            }
                            $answerQuestionLink = '/dashboard/forum/question?id=' . (int)$answer['question_id'];
                            if ($status === 'hidden') $statusClass = 'status-badge--pending';
                            if ($status === 'deleted') $statusClass = 'status-badge--deleted';
                        ?>
                        <tr class="answer-row">
                            <td><span class="question-id">#<?= (int)$answer['id'] ?></span></td>
                            <td>
                                <div class="answer-summary">
                                    <div class="answer-summary-header">
                                        <div class="answer-summary-title">Answer to: <?= htmlspecialchars($answer['question_title'] ?? '') ?></div>
                                        <a
                                            class="question-view-link"
                                            href="<?= htmlspecialchars($answerQuestionLink) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            View question
                                        </a>
                                    </div>
                                    <div class="answer-meta-chip-row">
                                        <span class="answer-meta-chip">Question #<?= (int)$answer['question_id'] ?></span>
                                        <span class="answer-meta-chip <?= (int)($answer['is_accepted'] ?? 0) === 1 ? 'answer-meta-chip--accepted' : '' ?>">
                                            Accepted: <?= (int)($answer['is_accepted'] ?? 0) === 1 ? 'Yes' : 'No' ?>
                                        </span>
                                    </div>
                                    <?php if ($answerPreview !== ''): ?>
                                        <div class="answer-preview-card"><?= htmlspecialchars($answerPreview) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($answer['moderation_note'])): ?>
                                    <div class="question-note-box">
                                        <span class="question-meta-label">Note:</span>
                                        <span><?= htmlspecialchars($answer['moderation_note']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="owner-cell">
                                    <div class="owner-name"><?= htmlspecialchars($answerOwnerName !== '' ? $answerOwnerName : 'Unknown User') ?></div>
                                    <div class="owner-subtext">ID <?= (int)$answer['user_id'] ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <div class="date-primary"><?= htmlspecialchars($answerCreatedDate) ?></div>
                                    <?php if ($answerCreatedTime !== ''): ?>
                                        <div class="date-secondary"><?= htmlspecialchars($answerCreatedTime) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><div class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($status)) ?></div></td>
                            <td>
                                <div class="table-actions table-actions--question">
                                    <button
                                        class="action-btn action-btn--primary"
                                        type="button"
                                        data-answer-editor-toggle
                                        data-answer-id="<?= (int)$answer['id'] ?>"
                                        aria-expanded="false"
                                        aria-controls="answer-editor-<?= (int)$answer['id'] ?>"
                                    >
                                        Edit
                                    </button>
                                    <?php if ($status === 'active'): ?>
                                        <form method="POST" action="/dashboard/forum/admin/answer/moderate" data-confirm-form>
                                            <input type="hidden" name="answer_id" value="<?= (int)$answer['id'] ?>">
                                            <input type="hidden" name="action" value="hide">
                                            <input type="hidden" name="moderation_note" value="Hidden by admin moderation.">
                                            <button class="action-btn" type="submit">Hide</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/dashboard/forum/admin/answer/moderate" data-confirm-form>
                                            <input type="hidden" name="answer_id" value="<?= (int)$answer['id'] ?>">
                                            <input type="hidden" name="action" value="restore">
                                            <input type="hidden" name="moderation_note" value="Restored by admin moderation.">
                                            <button class="action-btn" type="submit">Restore</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/dashboard/forum/admin/answer/moderate" data-confirm-form>
                                        <input type="hidden" name="answer_id" value="<?= (int)$answer['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="moderation_note" value="Deleted by admin moderation.">
                                        <button class="action-btn action-btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr
                            id="answer-editor-<?= (int)$answer['id'] ?>"
                            class="answer-editor-row"
                            data-answer-editor-row
                            data-answer-id="<?= (int)$answer['id'] ?>"
                            hidden
                        >
                            <td colspan="6">
                                <form method="POST" action="/dashboard/forum/admin/answer/update" class="moderation-form">
                                    <input type="hidden" name="answer_id" value="<?= (int)$answer['id'] ?>">
                                    <div class="question-editor-shell">
                                    <div class="question-editor-header">
                                        <div>
                                            <h3 class="question-editor-title">Editing Answer #<?= (int)$answer['id'] ?></h3>
                                            <p class="question-editor-subtitle">Refine the response content while keeping it aligned with the original question.</p>
                                        </div>
                                        </div>

                                        <div class="question-editor-field question-editor-field--stacked">
                                            <label class="filter-label" for="answer-content-<?= (int)$answer['id'] ?>">Content</label>
                                            <textarea
                                                id="answer-content-<?= (int)$answer['id'] ?>"
                                                name="content"
                                                class="table-search-input editor-input editor-textarea"
                                                rows="4"
                                                data-editor-textarea
                                                required
                                            ><?= htmlspecialchars($answer['content'] ?? '') ?></textarea>
                                            <p class="question-editor-help">Keep the answer concise, readable, and helpful for the original question.</p>
                                        </div>
                                    </div>
                                    <div class="question-editor-actions">
                                        <button
                                            type="button"
                                            class="action-btn"
                                            data-answer-editor-cancel
                                            data-answer-id="<?= (int)$answer['id'] ?>"
                                        >
                                            Cancel
                                        </button>
                                        <button type="submit" class="action-btn action-btn--primary">Save Answer Edit</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($answers)): ?>
                        <tr><td colspan="6" class="text-center">No answers found for current filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-controls" id="comments-section" style="margin-top: 20px;">
        <div class="section-heading">
            <h2 class="page-title" style="font-size: 20px;">Comment Moderation</h2>
            <p class="section-heading-subtitle">Review discussion comments, refine wording, and apply moderation actions.</p>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Comment</th>
                        <th>Owner</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php foreach ($comments as $comment): ?>
                        <?php
                            $status = $comment['moderation_status'] ?? 'active';
                            $statusClass = 'status-badge--active';
                            $commentOwnerName = trim(($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? ''));
                            $commentCreatedTimestamp = !empty($comment['created_at']) ? strtotime($comment['created_at']) : false;
                            $commentCreatedDate = $commentCreatedTimestamp ? date('M j, Y', $commentCreatedTimestamp) : ($comment['created_at'] ?? '-');
                            $commentCreatedTime = $commentCreatedTimestamp ? date('g:i A', $commentCreatedTimestamp) : '';
                            $commentPreview = trim(strip_tags((string)($comment['content'] ?? '')));
                            if (strlen($commentPreview) > 150) {
                                $commentPreview = substr($commentPreview, 0, 147) . '...';
                            }
                            $commentParentType = strtolower((string)($comment['parent_type'] ?? 'content'));
                            $commentLinkedQuestionId = $commentParentType === 'question'
                                ? (int)$comment['parent_id']
                                : (int)($comment['answer_question_id'] ?? 0);
                            $commentViewLink = $commentLinkedQuestionId > 0
                                ? '/dashboard/forum/question?id=' . $commentLinkedQuestionId
                                : '';
                            if ($status === 'hidden') $statusClass = 'status-badge--pending';
                            if ($status === 'deleted') $statusClass = 'status-badge--deleted';
                        ?>
                        <tr class="comment-row">
                            <td><span class="question-id">#<?= (int)$comment['id'] ?></span></td>
                            <td>
                                <div class="comment-summary">
                                    <div class="comment-summary-header">
                                        <div class="answer-summary-title">Comment on <?= htmlspecialchars($comment['parent_type'] ?? 'content') ?> #<?= (int)$comment['parent_id'] ?></div>
                                        <?php if ($commentViewLink !== ''): ?>
                                            <a
                                                class="question-view-link"
                                                href="<?= htmlspecialchars($commentViewLink) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                View discussion
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="answer-meta-chip-row">
                                        <span class="answer-meta-chip"><?= ucfirst($commentParentType) ?> context</span>
                                        <span class="answer-meta-chip">Parent #<?= (int)$comment['parent_id'] ?></span>
                                    </div>
                                    <?php if ($commentPreview !== ''): ?>
                                        <div class="comment-preview-card"><?= htmlspecialchars($commentPreview) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($comment['moderation_note'])): ?>
                                    <div class="question-note-box">
                                        <span class="question-meta-label">Note:</span>
                                        <span><?= htmlspecialchars($comment['moderation_note']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="owner-cell">
                                    <div class="owner-name"><?= htmlspecialchars($commentOwnerName !== '' ? $commentOwnerName : 'Unknown User') ?></div>
                                    <div class="owner-subtext">ID <?= (int)$comment['user_id'] ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <div class="date-primary"><?= htmlspecialchars($commentCreatedDate) ?></div>
                                    <?php if ($commentCreatedTime !== ''): ?>
                                        <div class="date-secondary"><?= htmlspecialchars($commentCreatedTime) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><div class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($status)) ?></div></td>
                            <td>
                                <div class="table-actions table-actions--question">
                                    <button
                                        class="action-btn action-btn--primary"
                                        type="button"
                                        data-comment-editor-toggle
                                        data-comment-id="<?= (int)$comment['id'] ?>"
                                        aria-expanded="false"
                                        aria-controls="comment-editor-<?= (int)$comment['id'] ?>"
                                    >
                                        Edit
                                    </button>
                                    <?php if ($status === 'active'): ?>
                                        <form method="POST" action="/dashboard/forum/admin/comment/moderate" data-confirm-form>
                                            <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                            <input type="hidden" name="action" value="hide">
                                            <input type="hidden" name="moderation_note" value="Hidden by admin moderation.">
                                            <button class="action-btn" type="submit">Hide</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/dashboard/forum/admin/comment/moderate" data-confirm-form>
                                            <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                            <input type="hidden" name="action" value="restore">
                                            <input type="hidden" name="moderation_note" value="Restored by admin moderation.">
                                            <button class="action-btn" type="submit">Restore</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/dashboard/forum/admin/comment/moderate" data-confirm-form>
                                        <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="moderation_note" value="Deleted by admin moderation.">
                                        <button class="action-btn action-btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr
                            id="comment-editor-<?= (int)$comment['id'] ?>"
                            class="comment-editor-row"
                            data-comment-editor-row
                            data-comment-id="<?= (int)$comment['id'] ?>"
                            hidden
                        >
                            <td colspan="6">
                                <form method="POST" action="/dashboard/forum/admin/comment/update" class="moderation-form">
                                    <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
                                    <div class="question-editor-shell">
                                        <div class="question-editor-header">
                                            <div>
                                                <h3 class="question-editor-title">Editing Comment #<?= (int)$comment['id'] ?></h3>
                                                <p class="question-editor-subtitle">Update the comment text while keeping the moderation context clear.</p>
                                            </div>
                                            <button
                                                type="button"
                                                class="action-btn"
                                                data-comment-editor-cancel
                                                data-comment-id="<?= (int)$comment['id'] ?>"
                                            >
                                                Cancel
                                            </button>
                                        </div>

                                        <div class="question-editor-field question-editor-field--stacked">
                                            <label class="filter-label" for="comment-content-<?= (int)$comment['id'] ?>">Content</label>
                                            <textarea
                                                id="comment-content-<?= (int)$comment['id'] ?>"
                                                name="content"
                                                class="table-search-input editor-input editor-textarea"
                                                rows="4"
                                                data-editor-textarea
                                                required
                                            ><?= htmlspecialchars($comment['content'] ?? '') ?></textarea>
                                            <p class="question-editor-help">Keep the comment readable, relevant, and consistent with the discussion context.</p>
                                        </div>
                                    </div>
                                    <div class="question-editor-actions">
                                        <button
                                            type="button"
                                            class="action-btn"
                                            data-comment-editor-cancel
                                            data-comment-id="<?= (int)$comment['id'] ?>"
                                        >
                                            Cancel
                                        </button>
                                        <button type="submit" class="action-btn action-btn--primary">Save Comment Edit</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                        <tr><td colspan="6" class="text-center">No comments found for current filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-controls" id="reports-section" style="margin-top: 20px;">
        <div class="section-heading">
            <h2 class="page-title" style="font-size: 20px;">Report Queue</h2>
            <p class="section-heading-subtitle">Review user reports, add moderation notes, and resolve or dismiss each case.</p>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead class="table-header">
                    <tr>
                        <th>ID</th>
                        <th>Report</th>
                        <th>Reporter</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php foreach ($reports as $report): ?>
                        <?php
                            $reporterName = trim(($report['reporter_first_name'] ?? '') . ' ' . ($report['reporter_last_name'] ?? ''));
                            $reportCreatedTimestamp = !empty($report['created_at']) ? strtotime($report['created_at']) : false;
                            $reportCreatedDate = $reportCreatedTimestamp ? date('M j, Y', $reportCreatedTimestamp) : ($report['created_at'] ?? '-');
                            $reportCreatedTime = $reportCreatedTimestamp ? date('g:i A', $reportCreatedTimestamp) : '';
                            $reportReason = trim((string)($report['reason'] ?? ''));
                            if (strlen($reportReason) > 130) {
                                $reportReason = substr($reportReason, 0, 127) . '...';
                            }
                            $reportPreview = trim((string)($report['target_preview'] ?? ''));
                            if (strlen($reportPreview) > 150) {
                                $reportPreview = substr($reportPreview, 0, 147) . '...';
                            }
                            $reportDiscussionLink = '/dashboard/forum/question?id=' . (int)$report['target_id'];
                        ?>
                        <tr class="report-row">
                            <td><span class="question-id">#<?= (int)$report['id'] ?></span></td>
                            <td>
                                <div class="report-summary">
                                    <div class="report-summary-header">
                                        <div class="answer-summary-title">Report on <?= htmlspecialchars($report['target_type']) ?> #<?= (int)$report['target_id'] ?></div>
                                        <?php if (($report['target_type'] ?? '') === 'question'): ?>
                                            <a
                                                class="question-view-link"
                                                href="<?= htmlspecialchars($reportDiscussionLink) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                View discussion
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="answer-meta-chip-row">
                                        <span class="answer-meta-chip"><?= ucfirst((string)($report['target_type'] ?? 'content')) ?> report</span>
                                        <span class="answer-meta-chip">Target #<?= (int)$report['target_id'] ?></span>
                                    </div>
                                    <?php if ($reportReason !== ''): ?>
                                        <div class="report-reason-box">
                                            <span class="question-meta-label">Reason:</span>
                                            <span><?= htmlspecialchars($reportReason) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($reportPreview !== ''): ?>
                                        <div class="report-preview-card">
                                            <span class="question-meta-label">Preview:</span>
                                            <span><?= htmlspecialchars($reportPreview) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="owner-cell">
                                    <div class="owner-name"><?= htmlspecialchars($reporterName !== '' ? $reporterName : 'Unknown User') ?></div>
                                    <div class="owner-subtext">ID <?= (int)$report['user_id'] ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <div class="date-primary"><?= htmlspecialchars($reportCreatedDate) ?></div>
                                    <?php if ($reportCreatedTime !== ''): ?>
                                        <div class="date-secondary"><?= htmlspecialchars($reportCreatedTime) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><div class="status-badge status-badge--pending">Pending</div></td>
                            <td>
                                <div class="table-actions table-actions--question">
                                    <button
                                        class="action-btn action-btn--primary"
                                        type="button"
                                        data-report-review-toggle
                                        data-report-id="<?= (int)$report['id'] ?>"
                                        aria-expanded="false"
                                        aria-controls="report-review-<?= (int)$report['id'] ?>"
                                    >
                                        Review
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr
                            id="report-review-<?= (int)$report['id'] ?>"
                            class="report-review-row"
                            data-report-review-row
                            data-report-id="<?= (int)$report['id'] ?>"
                            hidden
                        >
                            <td colspan="6">
                                <form method="POST" action="/dashboard/forum/admin/report/review" data-report-form class="moderation-form">
                                    <input type="hidden" name="report_id" value="<?= (int)$report['id'] ?>">
                                    <input type="hidden" name="target_type" value="<?= htmlspecialchars($report['target_type'] ?? '') ?>">
                                    <input type="hidden" name="target_id" value="<?= (int)($report['target_id'] ?? 0) ?>">
                                    <div class="question-editor-shell">
                                        <div class="question-editor-header">
                                            <div>
                                                <div class="report-review-kicker">Report Review Workspace</div>
                                                <h3 class="question-editor-title">Reviewing Report #<?= (int)$report['id'] ?></h3>
                                                <p class="question-editor-subtitle">Add a review note, choose an optional content action, then resolve or dismiss the report.</p>
                                                <div class="report-review-meta">
                                                    <span class="report-review-chip"><?= htmlspecialchars(ucfirst($report['target_type'] ?? 'target')) ?> #<?= (int)$report['target_id'] ?></span>
                                                    <span class="report-review-chip">Reporter: <?= htmlspecialchars($reporterName !== '' ? $reporterName : 'Unknown User') ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="question-editor-field question-editor-field--stacked">
                                            <label class="filter-label" for="report-message-<?= (int)$report['id'] ?>">Action note</label>
                                            <input
                                                id="report-message-<?= (int)$report['id'] ?>"
                                                class="table-search-input editor-input report-review-input"
                                                type="text"
                                                name="review_message"
                                                placeholder="Action note for this report..."
                                                required
                                            >
                                        </div>

                                        <div class="question-editor-field">
                                            <label class="filter-label" for="report-target-action-<?= (int)$report['id'] ?>">Target action</label>
                                            <select id="report-target-action-<?= (int)$report['id'] ?>" class="filter-select editor-input report-review-select" name="target_action">
                                                <option value="none">No content action</option>
                                                <option value="hide">Hide content</option>
                                                <option value="delete">Delete content</option>
                                                <option value="restore">Restore content</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="question-editor-actions">
                                        <button
                                            type="button"
                                            class="action-btn"
                                            data-report-review-cancel
                                            data-report-id="<?= (int)$report['id'] ?>"
                                        >
                                            Cancel
                                        </button>
                                        <button class="action-btn action-btn--primary" type="submit" name="action" value="resolved">Resolve</button>
                                        <button class="action-btn" type="submit" name="action" value="dismissed">Dismiss</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($reports)): ?>
                        <tr><td colspan="6" class="text-center">No pending reports.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-controls" id="discipline-section" style="margin-top: 20px;">
        <div class="section-heading">
            <h2 class="page-title" style="font-size: 20px;">User Discipline</h2>
            <p class="section-heading-subtitle">Suspend accounts, send warnings, and review recent discipline activity.</p>
        </div>
        <div class="discipline-grid">
            <form method="POST" action="/dashboard/forum/admin/user/suspend" class="moderation-form discipline-card" data-suspension-form>
                <div class="discipline-card-header">
                    <div>
                        <h3 class="text-primary">Suspend User</h3>
                        <p class="discipline-card-subtitle">Temporarily or permanently restrict a user from posting and participating in the forum.</p>
                    </div>
                </div>

                <div class="discipline-form-grid">
                    <div class="discipline-field">
                        <label class="filter-label" for="discipline-suspend-user-id">User ID</label>
                        <input id="discipline-suspend-user-id" class="filter-select discipline-input" type="number" name="user_id" min="1" placeholder="Enter user ID" required>
                    </div>

                    <div class="discipline-field">
                        <label class="filter-label" for="discipline-suspend-duration">Duration (days)</label>
                        <input id="discipline-suspend-duration" class="filter-select discipline-input" type="number" name="duration_days" min="1" value="7">
                        <p class="discipline-help">Use a number of days unless this is a permanent suspension.</p>
                    </div>

                    <div class="discipline-field discipline-field--full">
                        <label class="filter-label" for="discipline-suspend-reason">Reason</label>
                        <input id="discipline-suspend-reason" class="table-search-input discipline-input" type="text" name="reason" placeholder="Explain why this suspension is being applied" required>
                    </div>
                </div>

                <label class="discipline-toggle" for="discipline-suspend-permanent">
                    <span class="discipline-toggle-control">
                        <input id="discipline-suspend-permanent" type="checkbox" name="is_permanent" value="1">
                    </span>
                    <span class="discipline-toggle-copy">
                        <strong>Permanent suspension</strong>
                        <small>Ignore the duration field and keep the suspension active until an admin manually lifts it.</small>
                    </span>
                    <span class="discipline-toggle-badge">Duration disabled when enabled</span>
                </label>

                <div class="discipline-actions">
                    <button class="action-btn action-btn--danger" type="submit">Apply Suspension</button>
                </div>
            </form>

            <form method="POST" action="/dashboard/forum/admin/user/message" class="moderation-form discipline-card" data-message-form>
                <div class="discipline-card-header">
                    <div>
                        <h3 class="text-primary">Send Warning/Message</h3>
                        <p class="discipline-card-subtitle">Notify a user with a formal warning or a general moderation message.</p>
                    </div>
                </div>

                <div class="discipline-form-grid">
                    <div class="discipline-field">
                        <label class="filter-label" for="discipline-message-user-id">User ID</label>
                        <input id="discipline-message-user-id" class="filter-select discipline-input" type="number" name="user_id" min="1" placeholder="Enter user ID" required>
                    </div>

                    <div class="discipline-field">
                        <label class="filter-label" for="discipline-message-type">Type</label>
                        <select id="discipline-message-type" class="filter-select discipline-input" name="message_type">
                            <option value="warning">Warning</option>
                            <option value="message">Message</option>
                        </select>
                    </div>

                    <div class="discipline-field discipline-field--full">
                        <label class="filter-label" for="discipline-message-subject">Subject</label>
                        <input id="discipline-message-subject" class="table-search-input discipline-input" type="text" name="subject" placeholder="Short summary of the message" required>
                    </div>

                    <div class="discipline-field discipline-field--full">
                        <label class="filter-label" for="discipline-message-body">Body</label>
                        <textarea id="discipline-message-body" class="table-search-input discipline-input discipline-textarea" name="body" rows="4" placeholder="Write the full warning or message here" required></textarea>
                    </div>
                </div>

                <div class="discipline-actions">
                    <button class="action-btn action-btn--primary" type="submit">Send Message</button>
                </div>
            </form>
        </div>

        <div class="discipline-history-grid">
            <section class="discipline-history-panel">
                <div class="discipline-history-header">
                    <div>
                        <h3 class="text-primary">Active Suspensions</h3>
                        <p class="discipline-card-subtitle">Users currently restricted from forum participation.</p>
                    </div>
                </div>

                <?php foreach ($activeSuspensions as $s): ?>
                    <div class="discipline-history-card">
                        <div class="discipline-history-top">
                            <div>
                                <strong><?= htmlspecialchars(trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''))) ?></strong>
                                <div class="owner-subtext">User ID <?= (int)$s['user_id'] ?></div>
                            </div>
                            <span class="status-badge <?= (int)($s['is_permanent'] ?? 0) === 1 ? 'status-badge--deleted' : 'status-badge--pending' ?>">
                                <?= (int)($s['is_permanent'] ?? 0) === 1 ? 'Permanent' : 'Active' ?>
                            </span>
                        </div>
                        <div class="question-meta-line">
                            <span class="question-meta-label">Reason:</span>
                            <span><?= htmlspecialchars($s['reason'] ?? '') ?></span>
                        </div>
                        <div class="question-meta-line">
                            <span class="question-meta-label">Ends:</span>
                            <span><?= (int)($s['is_permanent'] ?? 0) === 1 ? 'Permanent' : htmlspecialchars($s['ends_at'] ?? '-') ?></span>
                        </div>
                        <form method="POST" action="/dashboard/forum/admin/user/lift-suspension" data-confirm-form class="discipline-inline-form">
                            <input type="hidden" name="user_id" value="<?= (int)$s['user_id'] ?>">
                            <button class="action-btn" type="submit">Lift Suspension</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($activeSuspensions)): ?>
                    <div class="discipline-empty-state">
                        <strong>No active suspensions</strong>
                        <span>Users you suspend will appear here until the suspension is lifted or expires.</span>
                    </div>
                <?php endif; ?>
            </section>

            <section class="discipline-history-panel">
                <div class="discipline-history-header">
                    <div>
                        <h3 class="text-primary">Recent Admin Messages</h3>
                        <p class="discipline-card-subtitle">Latest warnings and messages sent to forum users.</p>
                    </div>
                </div>

                <?php foreach ($recentAdminMessages as $m): ?>
                    <div class="discipline-history-card">
                        <div class="discipline-history-top">
                            <div>
                                <strong><?= htmlspecialchars(ucfirst($m['message_type'] ?? 'message')) ?></strong>
                                <div class="owner-subtext">To <?= htmlspecialchars(trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''))) ?> (User ID <?= (int)$m['user_id'] ?>)</div>
                            </div>
                            <span class="status-badge <?= ($m['message_type'] ?? 'message') === 'warning' ? 'status-badge--pending' : 'status-badge--active' ?>">
                                <?= htmlspecialchars(ucfirst($m['message_type'] ?? 'message')) ?>
                            </span>
                        </div>
                        <div class="question-meta-line">
                            <span class="question-meta-label">Subject:</span>
                            <span><?= htmlspecialchars($m['subject'] ?? '') ?></span>
                        </div>
                        <div class="question-meta-line"><?= htmlspecialchars($m['body'] ?? '') ?></div>
                        <div class="owner-subtext"><?= htmlspecialchars($m['created_at'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recentAdminMessages)): ?>
                    <div class="discipline-empty-state">
                        <strong>No message history yet</strong>
                        <span>Warnings and moderator messages will appear here once you send them.</span>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<script type="module" src="/js/app/admin/edu-forum/manage-forum.js?v=<?= (int)$manageForumJsVersion ?>"></script>
