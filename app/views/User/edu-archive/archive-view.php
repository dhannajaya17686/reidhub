<link href="/css/app/user/edu-archive/archive.css" rel="stylesheet">

<?php
$yearLabels = [
    '1' => '1st Year',
    '2' => '2nd Year',
    '3' => '3rd Year',
    '4' => '4th Year',
    '5' => 'Post Graduate'
];

$videosByYear = ['1' => [], '2' => [], '3' => [], '4' => [], '5' => []];
$notesByYear = ['1' => [], '2' => [], '3' => [], '4' => [], '5' => []];

foreach ($resources as $res) {
    $yearKey = (string)($res['year_level'] ?? '');
    if (!isset($yearLabels[$yearKey])) {
        continue;
    }
    if (($res['type'] ?? '') === 'video') {
        $videosByYear[$yearKey][] = $res;
    } else {
        $notesByYear[$yearKey][] = $res;
    }
}
?>

<div class="archive-layout">
    <aside class="archive-sidebar">
        <div class="archive-sidebar-head">
            <h2>Filters</h2>
            <a href="/dashboard/edu-archive" class="archive-reset-link">Reset</a>
        </div>

        <form method="GET" action="/dashboard/edu-archive" class="archive-filter-form">
            <div class="archive-filter-group">
                <label for="archive-subject">Subject/Module</label>
                <div class="archive-select-wrap">
                    <select id="archive-subject" name="subject" class="archive-select" onchange="this.form.submit()">
                        <option value="">All Subjects</option>
                        <option value="CS" <?= ($filters['subject'] ?? '') == 'CS' ? 'selected' : '' ?>>Computer Science</option>
                        <option value="IS" <?= ($filters['subject'] ?? '') == 'IS' ? 'selected' : '' ?>>Information Systems</option>
                        <option value="SE" <?= ($filters['subject'] ?? '') == 'SE' ? 'selected' : '' ?>>Software Engineering</option>
                    </select>
                </div>
            </div>

            <div class="archive-filter-group">
                <label for="archive-year">Academic Year</label>
                <div class="archive-select-wrap">
                    <select id="archive-year" name="year" class="archive-select" onchange="this.form.submit()">
                        <option value="">Select</option>
                        <option value="1" <?= ($filters['year'] ?? '') == '1' ? 'selected' : '' ?>>1st Year</option>
                        <option value="2" <?= ($filters['year'] ?? '') == '2' ? 'selected' : '' ?>>2nd Year</option>
                        <option value="3" <?= ($filters['year'] ?? '') == '3' ? 'selected' : '' ?>>3rd Year</option>
                        <option value="4" <?= ($filters['year'] ?? '') == '4' ? 'selected' : '' ?>>4th Year</option>
                        <option value="5" <?= ($filters['year'] ?? '') == '5' ? 'selected' : '' ?>>Post Graduate</option>
                    </select>
                </div>
            </div>

            <div class="archive-filter-group">
                <label for="archive-type">Content Type</label>
                <div class="archive-select-wrap">
                    <select id="archive-type" name="type" class="archive-select" onchange="this.form.submit()">
                        <option value="all" <?= ($filters['type'] ?? 'all') == 'all' ? 'selected' : '' ?>>All Content</option>
                        <option value="video" <?= ($filters['type'] ?? '') == 'video' ? 'selected' : '' ?>>Videos</option>
                        <option value="note" <?= ($filters['type'] ?? '') == 'note' ? 'selected' : '' ?>>Notes</option>
                    </select>
                </div>
            </div>

            <div class="archive-filter-group">
                <label for="archive-tag">Tag</label>
                <div class="archive-select-wrap">
                    <select id="archive-tag" name="tag" class="archive-select" onchange="this.form.submit()">
                        <option value="">All Tags</option>
                        <?php foreach (($filterTags ?? []) as $tagRow): ?>
                            <option value="<?= htmlspecialchars($tagRow['name']) ?>" <?= ($filters['tag'] ?? '') == $tagRow['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tagRow['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </aside>

    <main class="archive-main">
        <div class="archive-top">
            <h1>Academic Resources</h1>
            <div class="archive-actions">
                <a href="/dashboard/edu-archive/my-submissions" class="archive-btn archive-btn-secondary">My Uploads</a>
                <a href="/dashboard/edu-archive/upload" class="archive-btn archive-btn-primary">Upload New</a>
            </div>
        </div>

        <form method="GET" action="/dashboard/edu-archive" class="archive-search-form">
            <input type="hidden" name="type" value="<?= htmlspecialchars($filters['type'] ?? 'all') ?>">
            <input type="hidden" name="subject" value="<?= htmlspecialchars($filters['subject'] ?? '') ?>">
            <input type="hidden" name="year" value="<?= htmlspecialchars($filters['year'] ?? '') ?>">
            <input type="hidden" name="tag" value="<?= htmlspecialchars($filters['tag'] ?? '') ?>">
            <svg class="archive-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input
                type="text"
                name="q"
                class="archive-search-input"
                placeholder="Search"
                value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
            >
        </form>

        <?php if (empty($resources)): ?>
            <section class="archive-empty">
                <h3>No resources found</h3>
                <p>Try adjusting your filters or search query.</p>
            </section>
        <?php else: ?>

            <?php
                $hasVideos = false;
                foreach ($videosByYear as $items) {
                    if (!empty($items)) { $hasVideos = true; break; }
                }
            ?>
            <?php if ($hasVideos): ?>
                <section class="archive-section">
                    <h2 class="archive-section-title">Videos</h2>
                    <?php foreach ($yearLabels as $yearKey => $yearLabel): ?>
                        <?php if (empty($videosByYear[$yearKey])) continue; ?>
                        <h3 class="archive-year-group-title"><?= htmlspecialchars($yearLabel) ?></h3>
                        <div class="archive-cards-grid">
                            <?php foreach ($videosByYear[$yearKey] as $res): ?>
                                <?php
                                $vidId = '';
                                if (!empty($res['video_link']) && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $res['video_link'], $matches)) {
                                    $vidId = $matches[1];
                                }
                                $thumb = $vidId ? "https://img.youtube.com/vi/$vidId/mqdefault.jpg" : "/assets/placeholders/product.jpeg";
                                ?>
                                <article class="archive-card">
                                    <a href="<?= htmlspecialchars($res['video_link']) ?>" target="_blank" class="archive-thumb-link">
                                        <img src="<?= $thumb ?>" class="archive-thumb" alt="Video thumbnail">
                                    </a>
                                    <h3 class="archive-card-title" title="<?= htmlspecialchars($res['title']) ?>">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </h3>
                                    <p class="archive-card-meta">
                                        <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
                                    </p>
                                    <div class="archive-card-footer">
                                        <a class="archive-open-link" href="<?= htmlspecialchars($res['video_link']) ?>" target="_blank">Watch</a>
                                        <button
                                            type="button"
                                            class="archive-bookmark-btn <?= (isset($res['is_bookmarked']) && $res['is_bookmarked']) ? 'active' : '' ?>"
                                            data-id="<?= $res['id'] ?>"
                                            onclick="toggleArchiveBookmark(this)"
                                            aria-label="Bookmark resource"
                                        >
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= (isset($res['is_bookmarked']) && $res['is_bookmarked']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?php
                $hasNotes = false;
                foreach ($notesByYear as $items) {
                    if (!empty($items)) { $hasNotes = true; break; }
                }
            ?>
            <?php if ($hasNotes): ?>
                <section class="archive-section">
                    <h2 class="archive-section-title">Notes</h2>
                    <?php foreach ($yearLabels as $yearKey => $yearLabel): ?>
                        <?php if (empty($notesByYear[$yearKey])) continue; ?>
                        <h3 class="archive-year-group-title"><?= htmlspecialchars($yearLabel) ?></h3>
                        <div class="archive-cards-grid">
                            <?php foreach ($notesByYear[$yearKey] as $res): ?>
                                <article class="archive-card">
                                    <a href="<?= htmlspecialchars($res['file_path']) ?>" class="archive-thumb-link" download>
                                        <div class="archive-note-thumb">
                                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                            </svg>
                                        </div>
                                    </a>
                                    <h3 class="archive-card-title" title="<?= htmlspecialchars($res['title']) ?>">
                                        <?= htmlspecialchars($res['title']) ?>
                                    </h3>
                                    <p class="archive-card-meta">
                                        <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
                                    </p>
                                    <div class="archive-card-footer">
                                        <a class="archive-open-link" href="<?= htmlspecialchars($res['file_path']) ?>" download>Download</a>
                                        <button
                                            type="button"
                                            class="archive-bookmark-btn <?= (isset($res['is_bookmarked']) && $res['is_bookmarked']) ? 'active' : '' ?>"
                                            data-id="<?= $res['id'] ?>"
                                            onclick="toggleArchiveBookmark(this)"
                                            aria-label="Bookmark resource"
                                        >
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= (isset($res['is_bookmarked']) && $res['is_bookmarked']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

        <?php endif; ?>
    </main>
</div>

<script>
async function toggleArchiveBookmark(btn) {
    const id = btn.dataset.id;
    const isActive = btn.classList.contains('active');

    btn.classList.toggle('active');
    const icon = btn.querySelector('svg');
    icon.setAttribute('fill', isActive ? 'none' : 'currentColor');

    try {
        const response = await fetch('/dashboard/edu-archive/bookmark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();
        if (data.status !== 'success') {
            btn.classList.toggle('active');
            icon.setAttribute('fill', isActive ? 'currentColor' : 'none');
            alert(data.message || 'Error bookmarking item');
        }
    } catch (error) {
        console.error('Error:', error);
        btn.classList.toggle('active');
        icon.setAttribute('fill', isActive ? 'currentColor' : 'none');
    }
}
</script>
