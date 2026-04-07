<link href="/css/app/user/edu-forum/all-questions.css" rel="stylesheet">
<?php
if (!function_exists('renderQuestionPreviewHtml')) {
    function renderQuestionPreviewHtml($text, $maxLength = 220) {
        $plain = trim((string)$text);
        if ($plain === '') return '';

        // Normalize obvious markdown noise first.
        $plain = preg_replace('/^\s*[*_`-]{3,}\s*$/m', '', $plain); // lines like **** or ----
        $plain = preg_replace('/\n{3,}/', "\n\n", $plain);

        $wasTruncated = mb_strlen($plain) > $maxLength;
        $snippet = $wasTruncated ? mb_substr($plain, 0, $maxLength) : $plain;

        // If truncated in the middle of markdown token, trim trailing marker chars.
        $snippet = rtrim($snippet, "*_`");
        if ($wasTruncated) $snippet .= '...';

        // Escape first, then apply small markdown-style formatting safely.
        $html = htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');
        $html = preg_replace('/\*\*([^\*]+)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $html);
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
        $html = preg_replace('/(^|[\s>])([*_`]{2,})(?=\s|$)/m', '$1', $html); // orphan tokens

        $lines = preg_split('/\r\n|\r|\n/', $html);
        $out = [];
        $inList = false;

        foreach ($lines as $line) {
            if (preg_match('/^\s*[-*]\s+(.+)$/', $line, $m)) {
                if (!$inList) {
                    $out[] = '<ul class="question-preview-list">';
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
?>

<main class="forum-main" role="main">

    <div class="forum-controls" style="margin-bottom: 20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap: 15px;">
        <h1 class="questions-header" style="margin-bottom:0;">
            <?php if (!empty($current_tag)): ?>
                Tag: <span style="color:var(--secondary-color);">#<?= htmlspecialchars($current_tag) ?></span>
            <?php elseif (!empty($current_search)): ?>
                Search results for: "<?= htmlspecialchars($current_search) ?>"
            <?php else: ?>
                All Questions
            <?php endif; ?>
        </h1>

        <form action="/dashboard/forum/all" method="GET" class="search-form" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="Search topics..." 
                   value="<?= htmlspecialchars($current_search ?? '') ?>" 
                   style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; width: 250px; font-size: 0.9rem;">
            
            <input type="hidden" name="filter" value="<?= htmlspecialchars($current_filter) ?>">
            
            <button type="submit" class="btn btn--secondary" style="padding: 8px 16px;">Search</button>
        </form>
    </div>

    <nav class="content-tabs" role="tablist" aria-label="Question filters">
        <a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
           class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>
           
        <a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
           class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>
           
        <a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
           class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
    </nav>

    <section class="questions-section">
        <div class="questions-list" role="list">
            
            <?php if (empty($questions)): ?>
                <div style="text-align:center; padding: 60px 20px; color: var(--text-muted); background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom:15px; opacity:0.5;">
                        <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <p style="font-size:1.1rem;">No questions found matching your criteria.</p>
                    <a href="/dashboard/forum/all" style="color:var(--secondary-color); text-decoration:none; font-weight:500; margin-top:10px; display:inline-block;">Clear Filters</a>
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
                                            <?php $tag = trim($tag); ?>
                                            <a href="/dashboard/forum/all?tag=<?= urlencode($tag) ?>" class="question-tag">
                                                <?= htmlspecialchars($tag) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="question-actions">
                                <button class="question-action-btn bookmark-btn" data-id="<?= $q['id'] ?>" title="Bookmark this question">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </header>
                        
                        <div class="question-author">
                            <img class="author-avatar" src="https://ui-avatars.com/api/?name=<?= urlencode($q['first_name'].' '.$q['last_name']) ?>&background=random" alt="Avatar">
                            <div class="author-info">
                                <div class="author-name"><?= htmlspecialchars($q['first_name'] . ' ' . $q['last_name']) ?></div>
                                <time class="question-time"><?= date('M j, Y', strtotime($q['created_at'])) ?></time>
                            </div>
                        </div>
                        
                        <div class="question-content question-preview-content">
                            <?= renderQuestionPreviewHtml($q['content'], 220) ?>
                        </div>
                        
                        <footer class="question-stats">
                            <button class="stat-item stat-item--votes vote-button" data-id="<?= $q['id'] ?>" style="cursor:pointer; background:var(--stat-badge-bg); border:1px solid var(--stat-badge-border);">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                                <span class="stat-number vote-text"><?= $q['vote_count'] ?></span>
                                <span class="stat-label">Votes</span>
                            </button>

                            <a href="/dashboard/forum/question?id=<?= $q['id'] ?>#answers" class="stat-item stat-item--answers" style="text-decoration:none;">
                                <svg class="stat-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M2 2h12a2 2 0 012 2v8a2 2 0 01-2 2H4l-2 2V4a2 2 0 012-2z"/>
                                </svg>
                                <span class="stat-number"><?= $q['answer_count'] ?></span>
                                <span class="stat-label">Answers</span>
                            </a>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>

    <?php if ($total_pages > 1): ?>
    <div class="pagination" style="display:flex; justify-content:center; gap:10px; margin-top:40px;">
        <?php 
            // Helper to build pagination links keeping current filters
            $base_link = "?filter=" . urlencode($current_filter);
            if ($current_search) $base_link .= "&search=" . urlencode($current_search);
            if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
        ?>

        <?php if ($current_page > 1): ?>
            <a href="<?= $base_link ?>&page=<?= $current_page - 1 ?>" class="btn btn--secondary">Previous</a>
        <?php endif; ?>
        
        <span style="padding: 10px 15px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500;">
            Page <?= $current_page ?> of <?= $total_pages ?>
        </span>
        
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_link ?>&page=<?= $current_page + 1 ?>" class="btn btn--secondary">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</main>

<script type="module" src="/js/app/edu-forum/all-questions.js"></script>
<script src="/js/app/edu-forum/vote.js"></script>
