<link href="/css/app/user/edu-forum/all-questions.css" rel="stylesheet">
<link href="/css/app/user/edu-archive/archive.css" rel="stylesheet">

<style>
    .bookmark-layout { display: grid; gap: 28px; }
    .bookmark-section-title { margin: 0 0 14px; color: var(--text-primary); }
    .bookmark-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
        background: var(--surface);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
    }
    .bookmark-tabs {
        display: flex;
        gap: 28px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 18px;
    }
    .bookmark-tab {
        background: none;
        border: none;
        color: #64748b;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0 0 12px;
        border-bottom: 3px solid transparent;
    }
    .bookmark-tab.is-active {
        color: var(--secondary-color);
        border-bottom-color: var(--secondary-color);
    }
    .bookmark-panel { display: none; }
    .bookmark-panel.is-active { display: block; }
</style>

<main class="forum-main" role="main">

    <div class="forum-controls" style="margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <h1 class="questions-header" style="margin-bottom: 5px;">My Bookmarks</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Forum questions and Edu resources you have saved for later.
        </p>
    </div>

    <div class="bookmark-tabs" role="tablist" aria-label="Bookmark types">
        <button class="bookmark-tab is-active" type="button" data-target="forumBookmarks" role="tab" aria-selected="true">Edu Forum</button>
        <button class="bookmark-tab" type="button" data-target="archiveBookmarks" role="tab" aria-selected="false">Edu Archive</button>
    </div>

    <div class="bookmark-layout">
        <section class="questions-section bookmark-panel is-active" id="forumBookmarks">
            <h2 class="bookmark-section-title">Forum Bookmarks</h2>
            <div class="questions-list" role="list">
                <?php if (empty($questions)): ?>
                    <div class="bookmark-empty">
                        <p style="font-size:1rem; margin-bottom: 10px;">No forum bookmarks yet.</p>
                        <a href="/dashboard/forum/all" class="btn btn--primary">Browse Forum</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($questions as $q): ?>
                        <article class="question-card" role="listitem">
                            <div class="difficulty-indicator difficulty-indicator--medium"></div>

                            <header class="question-header">
                                <div class="question-title-group">
                                    <h2 class="question-title">
                                        <a href="/dashboard/forum/question?id=<?= $q['id'] ?>">
                                            <?= htmlspecialchars($q['title']) ?>
                                        </a>
                                    </h2>

                                    <div class="question-tags">
                                        <?php if (!empty($q['tags'])): ?>
                                            <?php foreach (explode(',', $q['tags']) as $tag): ?>
                                                <span class="question-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="question-actions">
                                    <button class="question-action-btn bookmark-btn active" data-id="<?= $q['id'] ?>" title="Remove bookmark">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </header>

                            <div class="question-author">
                                <img class="author-avatar" src="https://ui-avatars.com/api/?name=<?= urlencode($q['first_name'].' '.$q['last_name']) ?>&background=random" alt="Avatar">
                                <div class="author-info">
                                    <div class="author-name"><?= htmlspecialchars($q['first_name'] . ' ' . $q['last_name']) ?></div>
                                    <time class="question-time"><?= date('M j, Y', strtotime($q['created_at'] ?? 'now')) ?></time>
                                </div>
                            </div>

                            <footer class="question-stats">
                                <div class="stat-item">
                                    <span class="stat-number"><?= $q['vote_count'] ?></span>
                                    <span class="stat-label">Votes</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?= $q['answer_count'] ?></span>
                                    <span class="stat-label">Answers</span>
                                </div>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="bookmark-panel" id="archiveBookmarks">
            <h2 class="bookmark-section-title">Edu Resource Bookmarks</h2>
            <?php if (empty($resources ?? [])): ?>
                <div class="bookmark-empty">
                    <p style="font-size:1rem; margin-bottom: 10px;">No resource bookmarks yet.</p>
                    <a href="/dashboard/edu-archive" class="archive-btn archive-btn-primary">Browse Resources</a>
                </div>
            <?php else: ?>
                <div class="archive-cards-grid">
                    <?php foreach ($resources as $res): ?>
                        <?php
                        $vidId = '';
                        if (($res['type'] ?? '') === 'video' && !empty($res['video_link']) && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $res['video_link'], $matches)) {
                            $vidId = $matches[1];
                        }
                        $thumb = $vidId ? "https://img.youtube.com/vi/$vidId/mqdefault.jpg" : "/assets/placeholders/product.jpeg";
                        ?>
                        <article class="archive-card" data-resource-card-id="<?= (int)$res['id'] ?>">
                            <?php if (($res['type'] ?? '') === 'video'): ?>
                                <a href="<?= htmlspecialchars($res['video_link']) ?>" target="_blank" class="archive-thumb-link">
                                    <img src="<?= $thumb ?>" class="archive-thumb" alt="Resource thumbnail">
                                </a>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($res['file_path']) ?>" download class="archive-thumb-link">
                                    <div class="archive-note-thumb">NOTE</div>
                                </a>
                            <?php endif; ?>
                            <h3 class="archive-card-title"><?= htmlspecialchars($res['title']) ?></h3>
                            <p class="archive-card-meta"><?= htmlspecialchars($res['subject']) ?> <?= !empty($res['year_level']) ? ' ' . (int)$res['year_level'] : '' ?></p>
                            <div class="archive-card-footer">
                                <a class="archive-open-link" href="<?= ($res['type'] ?? '') === 'video' ? htmlspecialchars($res['video_link']) : htmlspecialchars($res['file_path']) ?>" <?= ($res['type'] ?? '') === 'video' ? 'target="_blank"' : 'download' ?>>
                                    <?= ($res['type'] ?? '') === 'video' ? 'Watch' : 'Download' ?>
                                </a>
                                <button
                                    type="button"
                                    class="archive-bookmark-btn active"
                                    data-id="<?= (int)$res['id'] ?>"
                                    onclick="toggleResourceBookmark(this)"
                                    aria-label="Remove resource bookmark"
                                >
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<script type="module" src="/js/app/edu-forum/all-questions.js"></script>
<script>
document.querySelectorAll('.bookmark-tab').forEach((tabBtn) => {
    tabBtn.addEventListener('click', () => {
        const target = tabBtn.dataset.target;

        document.querySelectorAll('.bookmark-tab').forEach((btn) => {
            btn.classList.remove('is-active');
            btn.setAttribute('aria-selected', 'false');
        });
        tabBtn.classList.add('is-active');
        tabBtn.setAttribute('aria-selected', 'true');

        document.querySelectorAll('.bookmark-panel').forEach((panel) => {
            panel.classList.toggle('is-active', panel.id === target);
        });
    });
});

async function toggleResourceBookmark(btn) {
    const id = btn.dataset.id;
    const card = btn.closest('[data-resource-card-id]');

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
        if (data.status === 'success') {
            if (data.action === 'removed' && card) {
                card.remove();
            }
            return;
        }
        alert(data.message || 'Unable to update bookmark.');
    } catch (error) {
        console.error('Bookmark error:', error);
    }
}
</script>
