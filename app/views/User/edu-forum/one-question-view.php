<?php
// Ensure session is started for user checks
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<link href="/css/app/user/edu-forum/one-question.css" rel="stylesheet">

<style>
    /* Owner Actions */
    .owner-actions { margin-top: 10px; display: flex; gap: 10px; }
    
    .btn-delete { background: #fee2e2; color: #dc2626; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.8rem; }
    .btn-delete:hover { background: #fecaca; }

    .btn-edit { background: #e0f2fe; color: #0284c7; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.8rem; }
    .btn-edit:hover { background: #bae6fd; }
    
    /* Comment Styling */
    .comments-section { margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 3px solid #cbd5e1; }
    .comment-item { font-size: 0.9rem; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start; }
    .comment-author { font-weight: 600; color: #334155; margin-right: 5px; }
    .comment-text { color: #475569; }
    .comment-form { display: flex; gap: 8px; margin-top: 10px; }
    .comment-input { flex: 1; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 20px; font-size: 0.85rem; }
    .btn-comment { background: var(--secondary-color); color: white; border: none; padding: 6px 12px; border-radius: 20px; cursor: pointer; font-size: 0.8rem; }
    
    /* Delete X Button */
    .btn-delete-comment { color: #ef4444; background: none; border: none; font-size: 1.0rem; cursor: pointer; padding: 0 5px; line-height: 1; font-weight: bold; }
    .btn-delete-comment:hover { color: #dc2626; }

    /* Interactive States */
    .vote-button.is-voted { background-color: #e0f2fe; color: #0466C8; }
    .answer-vote-btn.is-voted { color: #0466C8; }
    .bookmark-btn.active { color: #EAB308; fill: currentColor; }

    /* Accepted Answer Styling */
    .answer-card.accepted-solution { border: 2px solid #059669; background-color: #f0fdf4; position: relative; }
    .badge-solved { background-color: #059669; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; display: inline-flex; align-items: center; gap: 4px; margin-left: 10px; vertical-align: middle; }
    .btn-accept { background: none; border: 1px solid #059669; color: #059669; padding: 4px 10px; border-radius: 15px; cursor: pointer; font-size: 0.75rem; margin-left: 10px; transition: all 0.2s; }
    .btn-accept:hover { background: #059669; color: white; }

    /* Edit Modal Styles */
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
        <div class="question-tags" style="margin-top: 15px;">
            <?php foreach (explode(',', $question['tags']) as $tag): ?>
                <span class="question-tag" style="background: var(--surface-hover); padding: 4px 12px; border-radius: 15px; font-size: 0.85rem; margin-right: 8px;">
                    #<?= htmlspecialchars(trim($tag)) ?>
                </span>
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

        <div class="comments-section">
            <?php if (!empty($question_comments)): ?>
                <?php foreach ($question_comments as $comment): ?>
                    <div class="comment-item">
                        <div>
                            <span class="comment-author"><?= htmlspecialchars($comment['first_name']) ?>:</span>
                            <span class="comment-text"><?= htmlspecialchars($comment['content']) ?></span>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <form action="/dashboard/forum/comment/delete" method="POST" onsubmit="return confirm('Delete this comment?');" style="margin:0;">
                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                                <button type="submit" class="btn-delete-comment" title="Delete Comment">✕</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="comment-item" style="color:#94a3b8; font-style:italic;">No comments yet.</div>
            <?php endif; ?>

            <form action="/dashboard/forum/comment/create" method="POST" class="comment-form">
                <input type="hidden" name="parent_type" value="question">
                <input type="hidden" name="parent_id" value="<?= $question['id'] ?>">
                <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                <input type="text" name="content" class="comment-input" placeholder="Add a comment to the question..." required>
                <button type="submit" class="btn-comment">Comment</button>
            </form>
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
                <article class="answer-card <?= $isAccepted ? 'accepted-solution' : '' ?>" style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
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

                    <div style="margin-top: 15px; padding-left: 20px; border-left: 2px solid #e2e8f0;">
                        <?php if (!empty($answer['comments'])): ?>
                            <?php foreach ($answer['comments'] as $ansComment): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                    <div style="font-size: 0.85rem; color: #475569;">
                                        <span style="font-weight:600; color:#334155;"><?= htmlspecialchars($ansComment['first_name']) ?>:</span>
                                        <?= htmlspecialchars($ansComment['content']) ?>
                                    </div>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ansComment['user_id']): ?>
                                        <form action="/dashboard/forum/comment/delete" method="POST" onsubmit="return confirm('Delete this comment?');" style="margin:0;">
                                            <input type="hidden" name="comment_id" value="<?= $ansComment['id'] ?>">
                                            <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                                            <button type="submit" class="btn-delete-comment" title="Delete Comment">✕</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <form action="/dashboard/forum/comment/create" method="POST" style="margin-top: 8px;">
                            <input type="hidden" name="parent_type" value="answer">
                            <input type="hidden" name="parent_id" value="<?= $answer['id'] ?>">
                            <input type="hidden" name="redirect_id" value="<?= $question['id'] ?>">
                            <input type="text" name="content" placeholder="Reply to this answer..." 
                                   style="width:100%; padding: 5px 10px; border:1px solid #e2e8f0; border-radius: 4px; font-size: 0.85rem;">
                        </form>
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
 * Vote & Interaction System
 */
class InteractionSystem {
  constructor() {
    this.init();
  }

  init() {
    // 1. Question Vote Button
    const qVoteBtn = document.querySelector('.vote-button');
    if (qVoteBtn) {
      qVoteBtn.addEventListener('click', (e) => this.handleVote(e, 'question'));
    }

    // 2. Answer Vote Buttons
    document.querySelectorAll('.answer-vote-btn').forEach(btn => {
      btn.addEventListener('click', (e) => this.handleVote(e, 'answer'));
    });

    // 3. Bookmark Button
    const bookmarkBtn = document.querySelector('.bookmark-btn');
    if (bookmarkBtn) {
      bookmarkBtn.addEventListener('click', (e) => this.handleBookmark(e));
    }
  }

  async handleVote(event, type) {
    event.preventDefault();
    const button = event.currentTarget;
    const id = button.dataset.id;
    
    // Optimistic UI Update (Visual Feedback immediately)
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
        // Update the count from server
        let countEl;
        if (type === 'question') {
          countEl = button.querySelector('.vote-count-span');
          // Update text label if it exists
          const textEl = button.querySelector('.vote-text');
          if (textEl) textEl.textContent = button.classList.contains('is-voted') ? 'Voted' : 'Vote';
        } else {
          countEl = button.parentElement.querySelector('.answer-vote-count');
        }

        if (countEl) countEl.textContent = data.new_count;
        
      } else if (data.message === 'Please login to vote') {
        // Revert if not logged in
        button.classList.toggle('is-voted');
        alert(data.message);
      }
    } catch (error) {
      console.error('Vote Error:', error);
      button.classList.toggle('is-voted'); // Revert on error
    }
  }

  async handleBookmark(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const id = button.dataset.id;

    // Optimistic UI
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
        if (data.message) alert(data.message);
      } else {
        // Optional: Show toast
        const msg = button.classList.contains('active') ? 'Bookmarked!' : 'Bookmark removed';
        this.showToast(msg);
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

  showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'interaction-toast';
    toast.textContent = message;
    Object.assign(toast.style, {
      position: 'fixed', bottom: '20px', right: '20px',
      background: '#334155', color: '#fff', padding: '10px 20px',
      borderRadius: '8px', zIndex: '9999', opacity: '0', transition: 'opacity 0.3s'
    });
    document.body.appendChild(toast);
    
    // Fade In
    requestAnimationFrame(() => toast.style.opacity = '1');
    
    // Fade Out
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 2000);
  }
}

/**
 * Report System
 */
class ReportSystem {
  constructor() {
    this.init();
  }

  init() {
    document.querySelectorAll('.report-button').forEach(btn => {
      btn.addEventListener('click', (e) => this.handleReport(e));
    });
  }

  async handleReport(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const type = button.dataset.type; // 'question' or 'answer'
    const id = button.dataset.id;

    if (!confirm('Are you sure you want to report this?')) return;

    const originalText = button.textContent;
    button.textContent = 'Reporting...';
    button.disabled = true;

    try {
      const response = await fetch('/dashboard/forum/report', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            type: type, 
            id: id, 
            reason: 'User flagged content' 
        })
      });

      const data = await response.json();

      if (data.status === 'success') {
        button.textContent = 'Reported';
        button.style.color = '#ef4444';
        alert('Thank you. The content has been reported to moderators.');
      } else {
        button.textContent = originalText;
        button.disabled = false;
        alert(data.message || 'Error reporting content.');
      }
    } catch (error) {
      console.error('Report Error:', error);
      button.textContent = originalText;
      button.disabled = false;
    }
  }
}

/**
 * Answer Input Controller
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

    const form = this.submitButton.closest('form');
    this.form = form;
    
    if (form) {
      form.addEventListener('submit', (e) => this.handleSubmit(e));
    }
    
    this.setupAutoResize();
    this.updateSubmitButton();
  }

  handleInput() {
    this.updateSubmitButton();
  }

  handleKeydown(event) {
    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
      event.preventDefault(); 
      if (!this.submitButton.disabled) {
         if(this.form) this.form.submit();
      }
    }
  }

  setupAutoResize() {
    this.textarea.style.height = 'auto';
    this.textarea.style.height = this.textarea.scrollHeight + 'px';
    this.textarea.addEventListener('input', () => {
      this.textarea.style.height = 'auto';
      this.textarea.style.height = this.textarea.scrollHeight + 'px';
    });
  }

  updateSubmitButton() {
    const content = this.textarea.value.trim();
    const isValid = content.length >= this.minLength;
    
    this.submitButton.disabled = !isValid;
    const buttonText = this.submitButton.querySelector('.submit-text');
    
    if (buttonText) {
      buttonText.textContent = isValid ? 'Post Answer' : `${this.minLength - content.length} more chars`;
    }
    
    // Style the button
    this.submitButton.style.opacity = isValid ? '1' : '0.6';
    this.submitButton.style.cursor = isValid ? 'pointer' : 'not-allowed';
  }

  handleSubmit(event) {
    const content = this.textarea.value.trim();
    if (content.length < this.minLength) {
        if (event) event.preventDefault();
        return;
    }
    // Show loading state
    this.submitButton.disabled = true;
    const buttonText = this.submitButton.querySelector('.submit-text');
    if(buttonText) buttonText.textContent = 'Posting...';
  }
}

// Markdown Parser
function initializeMarkdown() {
    function parseMarkdown(text) {
        if (!text) return '';
        let html = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Simple Markdown Rules
        html = html.replace(/\*\*([\s\S]*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*([\s\S]*?)\*/g, '<em>$1</em>');
        html = html.replace(/```([\s\S]*?)```/g, '<pre style="background:#1e293b; color:#e2e8f0; padding:10px; border-radius:6px; overflow-x:auto; margin:10px 0;"><code>$1</code></pre>');
        html = html.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9; color:#ef4444; padding:2px 4px; border-radius:4px; font-family:monospace;">$1</code>');
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" style="color:#0466C8; text-decoration:underline;">$1</a>');
        html = html.replace(/^\s*-\s+(.*)$/gm, '• $1<br>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    const el = document.querySelector('[data-question-markdown]');
    if (el) {
        const raw = el.textContent || el.innerText || '';
        el.innerHTML = parseMarkdown(raw);
    }
}

// Edit Modal Logic
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

        // Updated: Simplified content assignment since JSON encodes correctly
        document.getElementById('editContent').value = content;

        document.getElementById('editModal').classList.add('active');
    }

    window.closeEditModal = function() {
        document.getElementById('editModal').classList.remove('active');
    }
}

// Initialize Everything
document.addEventListener('DOMContentLoaded', () => {
    new InteractionSystem();
    new AnswerInput();
    new ReportSystem();
    initializeMarkdown();
    initializeEditModal();
});
</script>