<?php
$archiveAdminCssVersion = @filemtime(__DIR__ . '/../../../../public/css/app/admin/edu-archive/manage-archive.css') ?: time();
$archiveAdminJsVersion = @filemtime(__DIR__ . '/../../../../public/js/app/admin/edu-archive/manage-archive.js') ?: time();

$yearLabels = [
    '1' => '1st Year',
    '2' => '2nd Year',
    '3' => '3rd Year',
    '4' => '4th Year',
    '5' => 'Post Graduate'
];

$statusLabels = [
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected'
];

$returnQs = http_build_query([
    'status' => $filters['status'] ?? 'all',
    'type' => $filters['type'] ?? 'all',
    'subject' => $filters['subject'] ?? '',
    'year' => $filters['year'] ?? '',
    'q' => $filters['search'] ?? '',
    'tag' => $filters['tag'] ?? '',
    'hidden' => $filters['hidden'] ?? '',
    'removal' => $filters['removal'] ?? '',
    'page' => $pagination['current_page'] ?? 1
]);

$flashSuccess = $_GET['success'] ?? null;
$flashError = $_GET['error'] ?? null;

$statBaseFilters = [
    'type' => 'all',
    'subject' => '',
    'year' => '',
    'q' => '',
    'tag' => ''
];

$statusFilter = $filters['status'] ?? 'all';
$hiddenFilter = (string)($filters['hidden'] ?? '');
$removalFilter = (string)($filters['removal'] ?? '');
$currentPage = (int)($pagination['current_page'] ?? 1);
$totalPages = (int)($pagination['total_pages'] ?? 1);
$totalItems = (int)($pagination['total_items'] ?? count($resources));
$perPage = max(1, (int)($pagination['per_page'] ?? max(1, count($resources))));
$pageStart = $totalItems > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$pageEnd = $totalItems > 0 ? min($totalItems, $currentPage * $perPage) : 0;

$buildAdminPageUrl = function($page) use ($filters) {
    $query = [
        'status' => $filters['status'] ?? 'all',
        'type' => $filters['type'] ?? 'all',
        'subject' => $filters['subject'] ?? '',
        'year' => $filters['year'] ?? '',
        'q' => $filters['search'] ?? '',
        'tag' => $filters['tag'] ?? '',
        'hidden' => $filters['hidden'] ?? '',
        'removal' => $filters['removal'] ?? ''
    ];

    if ($page > 1) {
        $query['page'] = $page;
    }

    return '/dashboard/edu-archive/admin?' . http_build_query($query) . '#resources-panel';
};

$adminPageLinks = [];
if ($totalPages <= 7) {
    $adminPageLinks = range(1, $totalPages);
} else {
    $adminPageLinks = array_values(array_unique([1, 2, $currentPage - 1, $currentPage, $currentPage + 1, $totalPages - 1, $totalPages]));
    $adminPageLinks = array_values(array_filter($adminPageLinks, function($page) use ($totalPages) {
        return $page >= 1 && $page <= $totalPages;
    }));
}
?>

<link href="/css/app/admin/edu-archive/manage-archive.css?v=<?= $archiveAdminCssVersion ?>" rel="stylesheet">

<div class="edu-admin-page" data-edu-archive-admin>
    <header class="edu-admin-header">
        <h1>Edu Archive Moderation</h1>
        <p>Review submissions, edit metadata, moderate published resources, and manage filter tags.</p>
    </header>

    <?php if ($flashSuccess): ?>
        <div class="edu-admin-alert success" data-flash-alert>Success: <?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="edu-admin-alert error" data-flash-alert>Error: <?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <section class="edu-admin-stats">
        <?php
            $totalLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'all', 'hidden' => '']) . '#resources-panel';
            $pendingLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'pending', 'hidden' => '']) . '#resources-panel';
            $approvedLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'approved', 'hidden' => '']) . '#resources-panel';
            $rejectedLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'rejected', 'hidden' => '']) . '#resources-panel';
            $hiddenLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'approved', 'hidden' => '1']) . '#resources-panel';
            $removalLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'approved', 'hidden' => '', 'removal' => '1']) . '#resources-panel';
        ?>
        <a class="stat-card stat-card-link <?= ($statusFilter === 'all' && $hiddenFilter !== '1') ? 'is-active' : '' ?>" href="<?= htmlspecialchars($totalLink) ?>">
            <span>Total</span><strong><?= (int)($counts['total_count'] ?? 0) ?></strong>
        </a>
        <a class="stat-card stat-card-link <?= ($statusFilter === 'pending' && $hiddenFilter !== '1') ? 'is-active' : '' ?>" href="<?= htmlspecialchars($pendingLink) ?>">
            <span>Pending</span><strong><?= (int)($counts['pending_count'] ?? 0) ?></strong>
        </a>
        <a class="stat-card stat-card-link <?= ($statusFilter === 'approved' && $hiddenFilter !== '1') ? 'is-active' : '' ?>" href="<?= htmlspecialchars($approvedLink) ?>">
            <span>Approved</span><strong><?= (int)($counts['approved_count'] ?? 0) ?></strong>
        </a>
        <a class="stat-card stat-card-link <?= ($statusFilter === 'rejected' && $hiddenFilter !== '1') ? 'is-active' : '' ?>" href="<?= htmlspecialchars($rejectedLink) ?>">
            <span>Rejected</span><strong><?= (int)($counts['rejected_count'] ?? 0) ?></strong>
        </a>
        <a class="stat-card stat-card-link <?= $hiddenFilter === '1' ? 'is-active' : '' ?>" href="<?= htmlspecialchars($hiddenLink) ?>">
            <span>Hidden</span><strong><?= (int)($counts['hidden_count'] ?? 0) ?></strong>
        </a>
        <a class="stat-card stat-card-link <?= $removalFilter === '1' ? 'is-active' : '' ?>" href="<?= htmlspecialchars($removalLink) ?>">
            <span>Removal Requests</span><strong><?= (int)($counts['removal_request_count'] ?? 0) ?></strong>
        </a>
    </section>

    <section class="edu-admin-panel edu-admin-tags-panel">
        <button type="button" class="tag-panel-toggle" data-tag-panel-toggle aria-expanded="false">
            <span>
                <strong>Manage Filter Tags</strong>
                <small>Add, rename, or remove tags used in the archive filter dropdown.</small>
            </span>
            <span class="tag-panel-toggle-icon">Show</span>
        </button>

        <div class="tag-panel-body" data-tag-panel-body hidden>
            <form method="POST" action="/dashboard/edu-archive/admin/tags" class="tag-create-form">
                <input type="hidden" name="action" value="create_tag">
                <input type="hidden" name="return_qs" value="<?= htmlspecialchars($returnQs) ?>">
                <input type="text" name="tag_name" placeholder="Add new tag (e.g., operating systems)" required>
                <button type="submit">Add Tag</button>
            </form>
            <div class="tag-grid">
                <?php foreach ($filterTags as $tagRow): ?>
                    <div class="tag-card">
                        <form method="POST" action="/dashboard/edu-archive/admin/tags" class="tag-edit-form">
                            <input type="hidden" name="action" value="update_tag">
                            <input type="hidden" name="id" value="<?= (int)$tagRow['id'] ?>">
                            <input type="hidden" name="return_qs" value="<?= htmlspecialchars($returnQs) ?>">
                            <input type="text" name="tag_name" value="<?= htmlspecialchars($tagRow['name']) ?>" required>
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" action="/dashboard/edu-archive/admin/tags" data-tag-delete-form>
                            <input type="hidden" name="action" value="delete_tag">
                            <input type="hidden" name="id" value="<?= (int)$tagRow['id'] ?>">
                            <input type="hidden" name="return_qs" value="<?= htmlspecialchars($returnQs) ?>">
                            <button type="submit" class="danger">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($filterTags)): ?>
                    <p class="empty-text">No managed tags yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="edu-admin-panel" id="resources-panel">
        <h2>Resources</h2>
        <form method="GET" action="/dashboard/edu-archive/admin" class="filter-form" data-filter-form>
            <select name="status">
                <option value="all" <?= ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>

            <select name="type">
                <option value="all" <?= ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Types</option>
                <option value="video" <?= ($filters['type'] ?? '') === 'video' ? 'selected' : '' ?>>Video</option>
                <option value="note" <?= ($filters['type'] ?? '') === 'note' ? 'selected' : '' ?>>Note</option>
            </select>

            <select name="subject">
                <option value="">All Subjects</option>
                <option value="CS" <?= ($filters['subject'] ?? '') === 'CS' ? 'selected' : '' ?>>Computer Science</option>
                <option value="IS" <?= ($filters['subject'] ?? '') === 'IS' ? 'selected' : '' ?>>Information Systems</option>
                <option value="SE" <?= ($filters['subject'] ?? '') === 'SE' ? 'selected' : '' ?>>Software Engineering</option>
            </select>

            <select name="hidden">
                <option value="" <?= ($filters['hidden'] ?? '') === '' ? 'selected' : '' ?>>All Visibility</option>
                <option value="1" <?= ($filters['hidden'] ?? '') === '1' ? 'selected' : '' ?>>Hidden Only</option>
                <option value="0" <?= ($filters['hidden'] ?? '') === '0' ? 'selected' : '' ?>>Visible Only</option>
            </select>

            <input type="hidden" name="year" value="<?= htmlspecialchars($filters['year'] ?? '') ?>">
            <input type="hidden" name="tag" value="<?= htmlspecialchars($filters['tag'] ?? '') ?>">
            <input type="hidden" name="removal" value="<?= htmlspecialchars($filters['removal'] ?? '') ?>">
            <input type="text" name="q" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search resources">
            <button type="submit">Search</button>
            <a href="/dashboard/edu-archive/admin" class="reset-link">Reset</a>
        </form>

        <?php if (empty($resources)): ?>
            <p class="empty-text">No resources found for current filters.</p>
        <?php endif; ?>

        <div class="resource-list">
            <?php foreach ($resources as $resource): ?>
                <?php
                    $isHidden = (int)($resource['is_hidden'] ?? 0) === 1;
                    $hasRemovalRequest = (int)($resource['removal_requested'] ?? 0) === 1;
                    $status = $resource['status'] ?? 'pending';
                    $yearLabel = $yearLabels[(string)($resource['year_level'] ?? '')] ?? ('Year ' . htmlspecialchars((string)($resource['year_level'] ?? 'N/A')));
                    $ownerName = trim(($resource['first_name'] ?? '') . ' ' . ($resource['last_name'] ?? ''));
                    $resourceType = strtoupper($resource['type'] ?? 'N/A');
                    $openUrl = ($resource['type'] ?? '') === 'video' ? ($resource['video_link'] ?? '') : ($resource['file_path'] ?? '');
                    $openLabel = ($resource['type'] ?? '') === 'video' ? 'Open Video' : 'Open Note';
                ?>
                <article class="resource-card resource-card--<?= htmlspecialchars($status) ?>">
                    <div class="resource-card-head">
                        <div class="resource-type-mark <?= htmlspecialchars(strtolower($resource['type'] ?? 'resource')) ?>">
                            <?= htmlspecialchars($resourceType) ?>
                        </div>

                        <div class="resource-summary">
                            <div class="resource-meta">
                                <span>#<?= (int)$resource['id'] ?></span>
                                <span class="pill status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?></span>
                                <?php if ($status === 'approved' && $isHidden): ?><span class="pill warn">Hidden</span><?php endif; ?>
                                <?php if ($hasRemovalRequest): ?><span class="pill removal">Removal requested</span><?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($resource['title'] ?? 'Untitled resource') ?></h3>
                            <p>
                                By <?= htmlspecialchars($ownerName ?: 'Unknown user') ?> ·
                                <?= htmlspecialchars($resource['subject'] ?? 'N/A') ?> ·
                                <?= htmlspecialchars($yearLabel) ?>
                            </p>
                        </div>

                        <?php if (!empty($openUrl)): ?>
                            <a class="resource-link resource-link-button" target="_blank" href="<?= htmlspecialchars($openUrl) ?>">
                                <?= htmlspecialchars($openLabel) ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="/dashboard/edu-archive/admin/moderate" class="resource-form" data-resource-form>
                        <input type="hidden" name="id" value="<?= (int)$resource['id'] ?>">
                        <input type="hidden" name="return_qs" value="<?= htmlspecialchars($returnQs) ?>">

                        <?php if ($hasRemovalRequest): ?>
                            <div class="removal-request-box">
                                <strong>Removal request</strong>
                                <p><?= htmlspecialchars($resource['removal_reason'] ?? 'No reason provided.') ?></p>
                                <?php if (!empty($resource['removal_requested_at'])): ?>
                                    <span>Requested <?= htmlspecialchars(date('M j, Y g:i A', strtotime($resource['removal_requested_at']))) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="resource-editor-head">
                            <div>
                                <strong>Review Details</strong>
                                <span>Edit metadata before approving, rejecting, hiding, or handling requests.</span>
                            </div>
                        </div>

                        <div class="resource-field resource-field-full">
                            <label>Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($resource['title'] ?? '') ?>" required>
                        </div>

                        <div class="resource-field resource-field-full">
                            <label>Description</label>
                            <textarea name="description" rows="3"><?= htmlspecialchars($resource['description'] ?? '') ?></textarea>
                        </div>

                        <div class="resource-grid">
                            <div class="resource-field">
                                <label>Subject</label>
                                <select name="subject" required>
                                    <option value="CS" <?= ($resource['subject'] ?? '') === 'CS' ? 'selected' : '' ?>>CS</option>
                                    <option value="IS" <?= ($resource['subject'] ?? '') === 'IS' ? 'selected' : '' ?>>IS</option>
                                    <option value="SE" <?= ($resource['subject'] ?? '') === 'SE' ? 'selected' : '' ?>>SE</option>
                                </select>
                            </div>
                            <div class="resource-field">
                                <label>Year</label>
                                <select name="year_level" required>
                                    <?php foreach ($yearLabels as $k => $label): ?>
                                        <option value="<?= htmlspecialchars($k) ?>" <?= (string)($resource['year_level'] ?? '') === $k ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="resource-field resource-field-full">
                            <label>Tags</label>
                            <input type="text" name="tags" value="<?= htmlspecialchars($resource['tags'] ?? '') ?>" placeholder="comma separated tags">
                        </div>

                        <div class="resource-action-zone">
                            <div class="actions-row">
                                <button type="submit" name="action" value="save_metadata">Save Metadata</button>
                                <button type="submit" name="action" value="approve" class="success">Approve</button>
                                <?php if ($status === 'approved'): ?>
                                    <button type="submit" name="action" value="<?= $isHidden ? 'unhide' : 'hide' ?>" class="warn">
                                        <?= $isHidden ? 'Unhide' : 'Hide' ?>
                                    </button>
                                <?php endif; ?>
                                <?php if ($hasRemovalRequest): ?>
                                    <button type="submit" name="action" value="clear_removal_request" class="neutral">Mark Request Handled</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="resource-reject-zone">
                            <label>Rejection Feedback</label>
                            <textarea name="admin_feedback" rows="2" placeholder="Explain why this submission was rejected..."><?= htmlspecialchars($resource['admin_feedback'] ?? '') ?></textarea>
                            <button type="submit" name="action" value="reject" class="danger" data-reject-btn>Reject with Feedback</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="edu-admin-pagination" aria-label="Admin archive pagination">
                <div class="edu-admin-pagination-summary">
                    Showing <?= $pageStart ?>-<?= $pageEnd ?> of <?= $totalItems ?> resources
                </div>
                <div class="edu-admin-pagination-controls">
                    <?php if ($currentPage > 1): ?>
                        <a class="edu-admin-page-btn" href="<?= htmlspecialchars($buildAdminPageUrl($currentPage - 1)) ?>">Previous</a>
                    <?php else: ?>
                        <span class="edu-admin-page-btn is-disabled">Previous</span>
                    <?php endif; ?>

                    <?php
                    $previousPage = null;
                    foreach ($adminPageLinks as $pageNumber):
                        if ($previousPage !== null && $pageNumber - $previousPage > 1):
                    ?>
                        <span class="edu-admin-page-ellipsis">...</span>
                    <?php
                        endif;
                    ?>
                        <a class="edu-admin-page-btn <?= $pageNumber === $currentPage ? 'is-active' : '' ?>" href="<?= htmlspecialchars($buildAdminPageUrl($pageNumber)) ?>">
                            <?= $pageNumber ?>
                        </a>
                    <?php
                        $previousPage = $pageNumber;
                    endforeach;
                    ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a class="edu-admin-page-btn" href="<?= htmlspecialchars($buildAdminPageUrl($currentPage + 1)) ?>">Next</a>
                    <?php else: ?>
                        <span class="edu-admin-page-btn is-disabled">Next</span>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
    </section>
</div>

<script type="module" src="/js/app/admin/edu-archive/manage-archive.js?v=<?= $archiveAdminJsVersion ?>"></script>
