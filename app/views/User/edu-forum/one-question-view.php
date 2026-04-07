<?php
// Ensure session is started for user checks
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<link href="/css/app/user/edu-forum/one-question.css" rel="stylesheet">

<style>
    /* =========================================
       1. Global & Layout 
       ========================================= */
    :root {
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --surface-hover: #f1f5f9;
        --border-color: #e2e8f0;
    }

    /* =========================================
       2. Owner Actions (Edit/Delete)
       ========================================= */
    .owner-actions { margin-top: 10px; display: flex; gap: 10px; }
    
    .btn-delete { 
        background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; 
        padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 500;
        transition: all 0.2s;
    }
    .btn-delete:hover { background: #fecaca; }

    .btn-edit { 
        background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; 
        padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 500;
        transition: all 0.2s;
    }
    .btn-edit:hover { background: #bae6fd; }

    /* =========================================
       3. Modern Comment Section Styling
       ========================================= */
    .comments-wrapper {
        margin-top: 25px;
        border-top: 1px solid var(--border-color);
        padding-top: 20px;
    }

    .comments-header-row {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .comments-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .comments-count-badge {
        background: var(--surface-hover);
        color: var(--text-secondary);
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
    }

    /* Comment List */
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .comment-item {
        display: flex;
        gap: 12px;
        animation: fadeIn 0.3s ease-out;
    }

    .comment-avatar img {
        width: 32px; height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .comment-content {
        flex-grow: 1;
        background: #F8FAFC;
        padding: 10px 14px;
        border-radius: 12px;
        border-top-left-radius: 2px; /* Chat bubble effect */
    }

    .comment-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        font-size: 0.85rem;
    }

    .author-name { font-weight: 700; color: var(--text-primary); }
    .time-ago { color: var(--text-muted); font-size: 0.75rem; }

    .comment-text {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .comment-actions-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 5px;
    }

    .btn-delete-comment {
        color: #ef4444;
        background: none; border: none;
        font-size: 0.75rem; cursor: pointer;
        font-weight: 500;
    }
    .btn-delete-comment:hover { text-decoration: underline; }

    /* Compose Box */
    .comment-compose-box {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        align-items: flex-start;
    }

    .user-avatar-sm {
        width: 32px; height: 32px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--secondary-color);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: bold;
    }

    .compose-input-wrapper { flex-grow: 1; }

    .comment-textarea {
        width: 100%;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.9rem;
        color: var(--text-primary);
        transition: all 0.2s;
        resize: none;
        min-height: 42px;
        font-family: inherit;
    }

    .comment-textarea:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(4, 102, 200, 0.1);
        outline: none;
        min-height: 80px;
    }

    .compose-actions {
        display: none; /* Hidden by default, shown via JS */
        justify-content: flex-end;
        gap: 10px;
        margin-top: 8px;
    }
    
    .btn-cancel-comment {
        background: none; border: none; color: var(--text-muted);
        font-size: 0.85rem; cursor: pointer;
    }
    
    .btn-submit-comment {
        background: var(--secondary-color); color: white; border: none;
        padding: 6px 14px; border-radius: 16px; font-size: 0.85rem; font-weight: 600;
        cursor: pointer;
    }

    /* =========================================
       4. Interactive States (Votes/Bookmarks)
       ========================================= */
    .vote-button.is-voted { background-color: #e0f2fe; color: #0466C8; }
    .answer-vote-btn.is-voted { color: #0466C8; }
    .bookmark-btn.active { color: #EAB308; border-color: #EAB308; }

    /* =========================================
       5. Question Tags
       ========================================= */
    .question-tags {
        margin-top: 15px;
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .question-tag {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, rgba(4, 102, 200, 0.1) 0%, rgba(29, 78, 216, 0.08) 100%);
        color: #1d4ed8;
        border: 1px solid rgba(4, 102, 200, 0.18);
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        line-height: 1;
        transition: all 0.2s ease;
    }

    .question-tag-link {
        text-decoration: none;
    }

    .question-tag:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, rgba(4, 102, 200, 0.16) 0%, rgba(29, 78, 216, 0.12) 100%);
        border-color: rgba(4, 102, 200, 0.28);
    }

    .question-tag-link:focus-visible {
        outline: 2px solid #1d4ed8;
        outline-offset: 2px;
    }

    /* =========================================
       6. Accepted Answer
       ========================================= */
    .answer-card.accepted-solution { border: 2px solid #059669; background-color: #f0fdf4; position: relative; }
    .badge-solved { background-color: #059669; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; vertical-align: middle; }
    .btn-accept { background: none; border: 1px solid #059669; color: #059669; padding: 4px 10px; border-radius: 15px; cursor: pointer; font-size: 0.75rem; margin-left: 10px; transition: all 0.2s; }
    .btn-accept:hover { background: #059669; color: white; }

    /* =========================================
       7. Edit Modal
       ========================================= */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-content { background: white; padding: 25px; border-radius: 10px; width: 90%; max-width: 600px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
    .modal-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; }
    .modal-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 5px; font-weight: 500; color: #475569; }
    .form-input, .form-textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; }
    .form-textarea { resize: vertical; min-height: 150px; font-family: inherit; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
    .btn-cancel { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
    .btn-save { background: #0466C8; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }

    /* Utilities */
    .hidden { display: none; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>

<main class="forum-details-main" role="main">

    <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="/dashboard/forum/all" class="breadcrumb-link">Forum</a>
        <span class="breadcrumb-separator" aria-hidden="true">›</span>
        <span class="breadcrumb-current">Question Details</span>
    </nav>

    <section class="question-detail-header">
        <div class="question-author-info">
            <img class="question-author-avatar" 
                 src="https://ui-avatars.com/api/?name=<?= urlencode($question['first_name'].' '.$question['last_name']) ?>&background=0466C8&color=fff" 
                 alt="Avatar">
            
            <div class="author-details">
                <h2 class="author-name"><?= htmlspecialchars($question['first_name'] . ' ' . $question['last_name']) ?></h2>
                <span class="author-badge"><?= date('M j, Y', strtotime($question['created_at'])) ?></span>
            </div>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id']): ?>
                <div class="owner-actions" style="margin-left: auto;">
                    <button class="btn-edit" 
                            onclick="openEditModal('question', <?= $question['id'] ?>, <?= htmlspecialchars(json_encode($question['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($question['content']), ENT_QUOTES, 'UTF-8') ?>)">
                        Edit
                    </button>

                    <form action="/dashboard/forum/delete" method="POST" onsubmit="return confirm('Delete this question completely?');">
                        <input type="hidden" name="type" value="question">
                        <input type="hidden" name="id" value="<?= $question['id'] ?>">
                        <button type="submit" class="btn-delete">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <h1 class="question-title-main"><?= htmlspecialchars($question['title']) ?></h1>

        <div class="question-content-main">
            <div id="question-content-markdown" data-question-markdown><?= htmlspecialchars($question['content']) ?></div>
        </div>

        <?php if (!empty($question['tags'])): ?>
        <div class="question-tags">
            <?php foreach (explode(',', $question['tags']) as $tag): ?>
                <?php $tag = trim($tag); ?>
                <?php if ($tag === '') continue; ?>
                <a href="/dashboard/forum/all?tag=<?= urlencode($tag) ?>" class="question-tag question-tag-link" aria-label="View all questions tagged <?= htmlspecialchars($tag) ?>">
                    #<?= htmlspecialchars($tag) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="vote-section">
            <button class="vote-button" data-id="<?= $question['id'] ?>" aria-label="Vote for this question">
                <svg class="vote-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                </svg>
                <span class="vote-text">Vote</span>
                <span class="vote-count-span" style="margin-left: 8px; font-weight: bold;"><?= $question['vote_count'] ?></span>
            </button>

            <div class="question-actions">
                <button class="action-button report-button" data-id="<?= $question['id'] ?>" data-type="question">Report</button>
                <button class="action-button bookmark-btn" data-id="<?= $question['id'] ?>">Bookmark</button>
            </div>
        </div>

        <div class="comments-wrapper">
            <div class="comments-header-row">
                <h3 class="comments-title">
                    Comments <span class="comments-count-badge"><?= count($question_comments ?? []) ?></span>
                </h3>
            </div>

            <div class="comments-list">
                <?php if (!empty($question_comments)): ?>
                    <?php foreach ($question_comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($comment['first_name'].' '.$comment['last_name']) ?>&background=random" alt="Avatar">
                            </div>
                            <div class="comment-content">
                                <div class="comment-meta">
                                    <span class="author-name"><?= htmlspecialchars($comment['first_name']) ?></span>
                                    <span class="time-ago">User</span>
                                </div>
                                <div class="comment-text">
                                    <?= htmlspecialchars($comment['content']) ?>
                                </div>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                    <div class="comment-actions-row">
                                        <form action="/dashboard/forum/comment/delete" method="POST" onsubmit="return confirm('Delete this comment?');">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                                            <button type="submit" class="btn-delete-comment">Delete</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="font-size:0.9rem; color:var(--text-muted); font-style:italic;">No comments yet.</div>
                <?php endif; ?>
            </div>

            <div class="comment-compose-box">
                <div class="user-avatar-sm">You</div>
                <div class="compose-input-wrapper">
                    <form action="/dashboard/forum/comment/create" method="POST">
                        <input type="hidden" name="parent_type" value="question">
                        <input type="hidden" name="parent_id" value="<?= $question['id'] ?>">
                        <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                        
                        <textarea name="content" class="comment-textarea" placeholder="Add a comment to the question..." rows="1" required></textarea>
                        
                        <div class="compose-actions">
                            <button type="button" class="btn-cancel-comment">Cancel</button>
                            <button type="submit" class="btn-submit-comment">Post Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="answers-section" id="answers">
        <div class="answers-header">
            <h2 class="answers-title">Answers</h2>
            <span class="answers-count"><?= count($answers) ?></span>
        </div>

        <?php if (empty($answers)): ?>
            <p style="color: var(--text-muted); padding: 20px 0;">No answers yet. Be the first to help!</p>
        <?php else: ?>
            <?php foreach ($answers as $answer): ?>
                <?php 
                    $isAccepted = isset($answer['is_accepted']) && $answer['is_accepted']; 
                    $isQuestionOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $question['user_id'];
                ?>
                <article class="answer-card <?= $isAccepted ? 'accepted-solution' : '' ?>" style="margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 30px;">
                    
                    <div class="answer-header">
                        <img class="answer-author-avatar" 
                             src="https://ui-avatars.com/api/?name=<?= urlencode($answer['first_name'].' '.$answer['last_name']) ?>&background=059669&color=fff" 
                             alt="Avatar">
                        <div class="answer-author-info">
                            <div class="answer-author-name">
                                <?= htmlspecialchars($answer['first_name'] . ' ' . $answer['last_name']) ?>
                                <?php if ($isAccepted): ?>
                                    <span class="badge-solved">✓ Solved</span>
                                <?php endif; ?>
                            </div>
                            <div class="answer-timestamp"><?= date('M j, Y', strtotime($answer['created_at'])) ?></div>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $answer['user_id']): ?>
                            <div style="margin-left: auto; display:flex; gap:10px;">
                                <button class="btn-edit" 
                                        onclick="openEditModal('answer', <?= $answer['id'] ?>, null, <?= htmlspecialchars(json_encode($answer['content']), ENT_QUOTES, 'UTF-8') ?>)">
                                    Edit
                                </button>
                                
                                <form action="/dashboard/forum/delete" method="POST" onsubmit="return confirm('Delete this answer?');">
                                    <input type="hidden" name="type" value="answer">
                                    <input type="hidden" name="id" value="<?= $answer['id'] ?>">
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="answer-content">
                        <p><?= nl2br(htmlspecialchars($answer['content'])) ?></p>
                    </div>
                    
                    <div class="answer-actions">
                        <div class="answer-vote">
                            <button class="answer-vote-btn upvote" data-id="<?= $answer['id'] ?>" aria-label="Upvote answer">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
                                </svg>
                            </button>
                            <span class="answer-vote-count"><?= $answer['vote_count'] ?></span>
                        </div>
                        
                        <button class="report-button" style="background:none; border:none; color:var(--text-muted); font-size:0.8rem; cursor:pointer;" 
                                data-id="<?= $answer['id'] ?>" data-type="answer">Report</button>

                        <?php if ($isQuestionOwner): ?>
                            <form action="/dashboard/forum/answer/accept" method="POST" style="display:inline;">
                                <input type="hidden" name="answer_id" value="<?= $answer['id'] ?>">
                                <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                                <?php if (!$isAccepted): ?>
                                    <button type="submit" class="btn-accept" title="Mark this answer as the correct solution">✔ Mark as Solution</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="comments-wrapper" style="margin-left: 20px; border-left: 3px solid var(--surface-hover); padding-left: 15px; border-top: none;">
                        <div class="comments-list">
                            <?php if (!empty($answer['comments'])): ?>
                                <?php foreach ($answer['comments'] as $ansComment): ?>
                                    <div class="comment-item">
                                        <div class="comment-avatar">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($ansComment['first_name']) ?>&background=random" style="width:28px; height:28px;" alt="Avatar">
                                        </div>
                                        <div class="comment-content" style="padding: 8px 12px;">
                                            <div class="comment-meta">
                                                <span class="author-name"><?= htmlspecialchars($ansComment['first_name']) ?></span>
                                            </div>
                                            <div class="comment-text">
                                                <?= htmlspecialchars($ansComment['content']) ?>
                                            </div>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ansComment['user_id']): ?>
                                                <div class="comment-actions-row">
                                                    <form action="/dashboard/forum/comment/delete" method="POST" onsubmit="return confirm('Delete this comment?');">
                                                        <input type="hidden" name="comment_id" value="<?= $ansComment['id'] ?>">
                                                        <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                                                        <button type="submit" class="btn-delete-comment">Delete</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="comment-compose-box">
                            <div class="compose-input-wrapper">
                                <form action="/dashboard/forum/comment/create" method="POST">
                                    <input type="hidden" name="parent_type" value="answer">
                                    <input type="hidden" name="parent_id" value="<?= $answer['id'] ?>">
                                    <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                                    
                                    <textarea name="content" class="comment-textarea" placeholder="Reply to this answer..." rows="1" required></textarea>
                                    
                                    <div class="compose-actions">
                                        <button type="button" class="btn-cancel-comment">Cancel</button>
                                        <button type="submit" class="btn-submit-comment">Reply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="answer-input-section">
            <div class="answer-input-header">
                <div class="current-user-avatar" style="width:40px; height:40px; background:var(--secondary-color); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">
                    You
                </div>
                <label for="answer-input" class="sr-only">Type your answer</label>
            </div>
            
            <form action="/dashboard/forum/answer/create" method="POST" style="width: 100%;">
                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                <textarea name="content" id="answer-input" class="answer-textarea" placeholder="Type your answer here..." rows="4" required></textarea>
                <div class="answer-submit-section">
                    <div class="input-info"><small class="text-muted">Use clear language. Ctrl+Enter to submit.</small></div>
                    <button type="submit" class="submit-button" style="cursor: pointer; background: #0466C8; border:none; color: white;">
                        <span class="submit-text">Post Answer</span>
                    </button>
                </div>
            </form>
        </section>
    </section>

</main>

<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Edit Content</h3>
            <button class="modal-close" onclick="closeEditModal()">×</button>
        </div>
        <form action="/dashboard/forum/update" method="POST" id="editForm">
            <input type="hidden" name="type" id="editType">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="question_id" value="<?= $question['id'] ?>"> 
            
            <div class="form-group" id="titleGroup">
                <label for="editTitle" class="form-label">Title</label>
                <input type="text" name="title" id="editTitle" class="form-input">
            </div>

            <div class="form-group">
                <label for="editContent" class="form-label">Content</label>
                <textarea name="content" id="editContent" class="form-textarea" required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * 1. Vote & Interaction System
 */
class InteractionSystem {
  constructor() {
    this.init();
  }

  init() {
    // Question Vote
    const qVoteBtn = document.querySelector('.vote-button');
    if (qVoteBtn) {
      qVoteBtn.addEventListener('click', (e) => this.handleVote(e, 'question'));
    }
    // Answer Votes
    document.querySelectorAll('.answer-vote-btn').forEach(btn => {
      btn.addEventListener('click', (e) => this.handleVote(e, 'answer'));
    });
    // Bookmark
    const bookmarkBtn = document.querySelector('.bookmark-btn');
    if (bookmarkBtn) {
      bookmarkBtn.addEventListener('click', (e) => this.handleBookmark(e));
    }
  }

  async handleVote(event, type) {
    event.preventDefault();
    const button = event.currentTarget;
    const id = button.dataset.id;
    
    // UI Feedback
    button.classList.toggle('is-voted');
    this.animateButton(button);

    try {
      const response = await fetch('/dashboard/forum/vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: type, id: id })
      });

      const data = await response.json();

      if (data.status === 'success') {
        let countEl;
        if (type === 'question') {
          countEl = button.querySelector('.vote-count-span');
          const textEl = button.querySelector('.vote-text');
          if (textEl) textEl.textContent = button.classList.contains('is-voted') ? 'Voted' : 'Vote';
        } else {
          countEl = button.parentElement.querySelector('.answer-vote-count');
        }
        if (countEl) countEl.textContent = data.new_count;
        
      } else if (data.message === 'Please login to vote') {
        button.classList.toggle('is-voted');
        alert(data.message);
      }
    } catch (error) {
      console.error('Vote Error:', error);
      button.classList.toggle('is-voted');
    }
  }

  async handleBookmark(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const id = button.dataset.id;
    button.classList.toggle('active');
    this.animateButton(button);

    try {
      const response = await fetch('/dashboard/forum/bookmark', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      });
      const data = await response.json();
      if (data.status !== 'success') {
        button.classList.toggle('active'); // Revert
      }
    } catch (error) {
      console.error('Bookmark Error:', error);
      button.classList.toggle('active');
    }
  }

  animateButton(button) {
    button.style.transform = 'scale(0.95)';
    setTimeout(() => button.style.transform = 'scale(1)', 150);
  }
}

/**
 * 2. Report System
 */
class ReportSystem {
  constructor() { this.init(); }
  init() {
    document.querySelectorAll('.report-button').forEach(btn => {
      btn.addEventListener('click', (e) => this.handleReport(e));
    });
  }
  async handleReport(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const type = button.dataset.type; 
    const id = button.dataset.id;

    const reasonInput = prompt(
      'Please enter the reason for this report (minimum 5 characters):\n\nExample: Spam, abusive language, wrong information'
    );
    if (reasonInput === null) return;

    const reason = reasonInput.trim();
    if (reason.length < 5) {
      alert('Please provide a clearer reason (at least 5 characters).');
      return;
    }

    const originalText = button.textContent;
    button.textContent = 'Reporting...';
    button.disabled = true;

    try {
      const response = await fetch('/dashboard/forum/report', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: type, id: id, reason: reason })
      });
      const data = await response.json();
      if (data.status === 'success') {
        button.textContent = 'Reported';
        button.style.color = '#ef4444';
        alert('Thank you. Content reported.');
      } else {
        button.textContent = originalText;
        button.disabled = false;
        alert(data.message || 'Error reporting.');
      }
    } catch (error) {
      button.textContent = originalText;
      button.disabled = false;
    }
  }
}

/**
 * 3. Comment System (New Interaction Logic)
 */
class CommentSystem {
    constructor() {
        this.init();
    }

    init() {
        // Expand on Focus
        document.querySelectorAll('.comment-textarea').forEach(textarea => {
            textarea.addEventListener('focus', function() {
                const actionsDiv = this.parentElement.querySelector('.compose-actions');
                if (actionsDiv) {
                    actionsDiv.style.display = 'flex';
                    this.rows = 3;
                }
            });

            // Auto Resize
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Cancel Button
        document.querySelectorAll('.btn-cancel-comment').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('form');
                const textarea = form.querySelector('textarea');
                const actionsDiv = form.querySelector('.compose-actions');
                
                textarea.value = '';
                textarea.style.height = 'auto';
                textarea.rows = 1;
                actionsDiv.style.display = 'none';
            });
        });
    }
}

/**
 * 4. Main Answer Input
 */
class AnswerInput {
  constructor() {
    this.textarea = document.querySelector('.answer-textarea');
    this.submitButton = document.querySelector('.submit-button');
    this.minLength = 10;
    this.init();
  }
  init() {
    if (!this.textarea || !this.submitButton) return;
    this.textarea.addEventListener('input', () => this.handleInput());
    this.textarea.addEventListener('keydown', (e) => this.handleKeydown(e));
    this.updateSubmitButton();
  }
  handleInput() { this.updateSubmitButton(); }
  handleKeydown(event) {
    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
      event.preventDefault(); 
      if (!this.submitButton.disabled) this.submitButton.closest('form').submit();
    }
  }
  updateSubmitButton() {
    const content = this.textarea.value.trim();
    const isValid = content.length >= this.minLength;
    this.submitButton.disabled = !isValid;
    this.submitButton.style.opacity = isValid ? '1' : '0.6';
    this.submitButton.style.cursor = isValid ? 'pointer' : 'not-allowed';
    
    const txt = this.submitButton.querySelector('.submit-text');
    if (txt) txt.textContent = isValid ? 'Post Answer' : `${this.minLength - content.length} more chars`;
  }
}

// Markdown Parser
function initializeMarkdown() {
    function parseMarkdown(text) {
        if (!text) return '';
        let html = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        html = html.replace(/\*\*([\s\S]*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*([\s\S]*?)\*/g, '<em>$1</em>');
        html = html.replace(/```([\s\S]*?)```/g, '<pre style="background:#1e293b; color:#e2e8f0; padding:10px; border-radius:6px; overflow-x:auto; margin:10px 0;"><code>$1</code></pre>');
        html = html.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9; color:#ef4444; padding:2px 4px; border-radius:4px; font-family:monospace;">$1</code>');
        html = html.replace(/^\s*-\s+(.*)$/gm, '• $1<br>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }
    const el = document.querySelector('[data-question-markdown]');
    if (el) el.innerHTML = parseMarkdown(el.textContent || el.innerText || '');
}

// Edit Modal
function initializeEditModal() {
    window.openEditModal = function(type, id, title, content) {
        document.getElementById('editType').value = type;
        document.getElementById('editId').value = id;
        
        const titleGroup = document.getElementById('titleGroup');
        const titleInput = document.getElementById('editTitle');
        
        if (type === 'question') {
            titleGroup.style.display = 'block';
            titleInput.value = title || '';
            titleInput.setAttribute('required', 'required');
            document.getElementById('modalTitle').innerText = "Edit Question";
        } else {
            titleGroup.style.display = 'none';
            titleInput.removeAttribute('required');
            document.getElementById('modalTitle').innerText = "Edit Answer";
        }
        document.getElementById('editContent').value = content;
        document.getElementById('editModal').classList.add('active');
    }
    window.closeEditModal = function() {
        document.getElementById('editModal').classList.remove('active');
    }
}

// Initialize All Systems
document.addEventListener('DOMContentLoaded', () => {
    new InteractionSystem();
    new ReportSystem();
    new CommentSystem(); // New Class
    new AnswerInput();
    initializeMarkdown();
    initializeEditModal();
});
</script>
