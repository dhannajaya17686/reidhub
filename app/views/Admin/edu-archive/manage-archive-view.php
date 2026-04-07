<link href="/css/app/admin/edu-archive/manage-archive.css" rel="stylesheet">

<?php
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
    'hidden' => $filters['hidden'] ?? ''
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
?>

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
    </section>

    <section class="edu-admin-panel">
        <h2>Filter Tags</h2>
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

            <select name="year">
                <option value="">All Years</option>
                <?php foreach ($yearLabels as $k => $label): ?>
                    <option value="<?= htmlspecialchars($k) ?>" <?= ($filters['year'] ?? '') === $k ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="tag">
                <option value="">All Tags</option>
                <?php foreach ($filterTags as $tagRow): ?>
                    <option value="<?= htmlspecialchars($tagRow['name']) ?>" <?= ($filters['tag'] ?? '') === $tagRow['name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tagRow['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="hidden">
                <option value="" <?= ($filters['hidden'] ?? '') === '' ? 'selected' : '' ?>>All Visibility</option>
                <option value="1" <?= ($filters['hidden'] ?? '') === '1' ? 'selected' : '' ?>>Hidden Only</option>
                <option value="0" <?= ($filters['hidden'] ?? '') === '0' ? 'selected' : '' ?>>Visible Only</option>
            </select>

            <input type="text" name="q" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search title, description, tags">
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
                    $status = $resource['status'] ?? 'pending';
                    $yearLabel = $yearLabels[(string)($resource['year_level'] ?? '')] ?? ('Year ' . htmlspecialchars((string)($resource['year_level'] ?? 'N/A')));
                ?>
                <article class="resource-card">
                    <div class="resource-meta">
                        <span>#<?= (int)$resource['id'] ?></span>
                        <span><?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?></span>
                        <?php if ($status === 'approved' && $isHidden): ?><span class="pill warn">Hidden</span><?php endif; ?>
                        <span>By <?= htmlspecialchars(trim(($resource['first_name'] ?? '') . ' ' . ($resource['last_name'] ?? ''))) ?></span>
                    </div>

                    <form method="POST" action="/dashboard/edu-archive/admin/moderate" class="resource-form" data-resource-form>
                        <input type="hidden" name="id" value="<?= (int)$resource['id'] ?>">
                        <input type="hidden" name="return_qs" value="<?= htmlspecialchars($returnQs) ?>">

                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($resource['title'] ?? '') ?>" required>

                        <label>Description</label>
                        <textarea name="description" rows="2"><?= htmlspecialchars($resource['description'] ?? '') ?></textarea>

                        <div class="resource-grid">
                            <div>
                                <label>Subject</label>
                                <select name="subject" required>
                                    <option value="CS" <?= ($resource['subject'] ?? '') === 'CS' ? 'selected' : '' ?>>CS</option>
                                    <option value="IS" <?= ($resource['subject'] ?? '') === 'IS' ? 'selected' : '' ?>>IS</option>
                                    <option value="SE" <?= ($resource['subject'] ?? '') === 'SE' ? 'selected' : '' ?>>SE</option>
                                </select>
                            </div>
                            <div>
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

                        <label>Tags (comma separated)</label>
                        <input type="text" name="tags" value="<?= htmlspecialchars($resource['tags'] ?? '') ?>">

                        <p class="resource-type">
                            Type: <strong><?= htmlspecialchars(strtoupper($resource['type'] ?? 'N/A')) ?></strong> |
                            Subject: <strong><?= htmlspecialchars($resource['subject'] ?? 'N/A') ?></strong> |
                            Year: <strong><?= htmlspecialchars($yearLabel) ?></strong>
                        </p>

                        <?php if (($resource['type'] ?? '') === 'video' && !empty($resource['video_link'])): ?>
                            <a class="resource-link" target="_blank" href="<?= htmlspecialchars($resource['video_link']) ?>">Open Video</a>
                        <?php elseif (($resource['type'] ?? '') === 'note' && !empty($resource['file_path'])): ?>
                            <a class="resource-link" target="_blank" href="<?= htmlspecialchars($resource['file_path']) ?>">Open Note</a>
                        <?php endif; ?>

                        <div class="actions-row">
                            <button type="submit" name="action" value="save_metadata">Save Metadata</button>
                            <button type="submit" name="action" value="approve" class="success">Approve</button>
                            <?php if ($status === 'approved'): ?>
                                <button type="submit" name="action" value="<?= $isHidden ? 'unhide' : 'hide' ?>" class="warn">
                                    <?= $isHidden ? 'Unhide' : 'Hide' ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <label>Rejection Feedback</label>
                        <textarea name="admin_feedback" rows="2" placeholder="Explain why this submission was rejected..."><?= htmlspecialchars($resource['admin_feedback'] ?? '') ?></textarea>
                        <button type="submit" name="action" value="reject" class="danger" data-reject-btn>Reject with Feedback</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script type="module" src="/js/app/admin/edu-archive/manage-archive.js"></script>
