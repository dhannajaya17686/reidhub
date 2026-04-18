<link href="/css/app/user/edu-forum/add-question.css" rel="stylesheet">

<style>
    /* --- NEW: Styles for Similar Question Suggestions --- */
    .similar-questions-container {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 8px;
        padding: 10px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
    }

    .similar-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 8px;
        display: block;
    }

    .similar-item {
        display: block;
        padding: 8px;
        color: #334155;
        text-decoration: none;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .similar-item:last-child {
        border-bottom: none;
    }

    .similar-item:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-radius: 4px;
    }
</style>

<div class="question-form-page">

  <div class="page-header">
    <h1 class="page-title">Ask Your Question</h1>
    <p class="page-subtitle">
      Share your knowledge gaps with the community. Provide detailed information to help others understand and answer your question effectively.
    </p>
  </div>

  <div class="question-form-container">

    <div class="form-main">
      <form class="question-form" action="/dashboard/forum/create" method="POST" data-question-form>

        <div class="form-group">
          <label for="question-title" class="form-label form-label--required">Question Title</label>
          <p class="form-description">
            Write a clear, concise title that summarizes your question. Good titles help others find and understand your question quickly.
          </p>
          <input 
            type="text" 
            id="question-title" 
            name="title" 
            class="form-input" 
            placeholder="e.g., How to implement authentication in React?" 
            required 
            maxlength="200" 
            data-title-input
            autocomplete="off" 
          >
          
          <div id="similar-questions" class="similar-questions-container" style="display:none;"></div>

          <div class="input-help">
            <span class="char-count" data-title-count>0/200</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label form-label--required">Category</label>
          <p class="form-description">
            Select the most relevant category for your question to help others find it.
          </p>
          <div class="category-grid">
            <div class="category-option">
              <input type="radio" id="cat-programming" name="category" value="programming" class="category-input" required>
              <label for="cat-programming" class="category-label">
                <div class="category-icon">💻</div>
                <div class="category-name">Programming</div>
              </label>
            </div>
            <div class="category-option">
              <input type="radio" id="cat-web-dev" name="category" value="web-development" class="category-input">
              <label for="cat-web-dev" class="category-label">
                <div class="category-icon">🌐</div>
                <div class="category-name">Web Dev</div>
              </label>
            </div>
            <div class="category-option">
              <input type="radio" id="cat-mobile" name="category" value="mobile" class="category-input">
              <label for="cat-mobile" class="category-label">
                <div class="category-icon">📱</div>
                <div class="category-name">Mobile</div>
              </label>
            </div>
            <div class="category-option">
              <input type="radio" id="cat-database" name="category" value="database" class="category-input">
              <label for="cat-database" class="category-label">
                <div class="category-icon">🗄️</div>
                <div class="category-name">Database</div>
              </label>
            </div>
            <div class="category-option">
              <input type="radio" id="cat-algorithms" name="category" value="algorithms" class="category-input">
              <label for="cat-algorithms" class="category-label">
                <div class="category-icon">🧮</div>
                <div class="category-name">Algorithms</div>
              </label>
            </div>
            <div class="category-option">
              <input type="radio" id="cat-other" name="category" value="other" class="category-input">
              <label for="cat-other" class="category-label">
                <div class="category-icon">📚</div>
                <div class="category-name">Other</div>
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="question-description" class="form-label form-label--required">Question Description</label>
          <p class="form-description">
            Provide detailed information about your question. Include what you've tried, expected results, and any relevant code or context.
          </p>

          <div class="rich-textarea-container">
            <div class="textarea-toolbar">
              <div class="toolbar-group">
                <button type="button" class="toolbar-btn" data-format="bold" title="Bold (Ctrl+B)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z" />
                  </svg>
                </button>
                <button type="button" class="toolbar-btn" data-format="italic" title="Italic (Ctrl+I)">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z" />
                  </svg>
                </button>
                <button type="button" class="toolbar-btn" data-format="code" title="Inline code">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z" />
                  </svg>
                </button>
              </div>

              <div class="toolbar-group">
                <button type="button" class="toolbar-btn" data-format="list" title="Bullet list">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5 1.5-.68 1.5-1.5-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z" />
                  </svg>
                </button>
                <button type="button" class="toolbar-btn" data-format="link" title="Add link">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z" />
                  </svg>
                </button>
                <button type="button" class="toolbar-btn" data-format="code-block" title="Code block">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z" />
                  </svg>
                </button>
              </div>

              <div class="toolbar-group">
                <button type="button" class="toolbar-btn" data-format="attach" title="Attach file">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z" />
                  </svg>
                </button>
              </div>
            </div>

            <div id="question-description-editor" data-description-editor class="form-textarea wysiwyg-editor" contenteditable="true" role="textbox" aria-multiline="true" placeholder="Describe your question in detail. Include:\n• What you're trying to achieve\n• What you've already tried\n• Expected vs actual results\n• Relevant code snippets or error messages"></div>

            <textarea 
              id="question-description" 
              name="description" 
              class="form-textarea is-hidden" 
              style="display:none;" 
              rows="8" 
              maxlength="5000" 
              data-description-input
            ></textarea>

            <div class="textarea-footer">
              <div class="formatting-help">
                <a href="#" data-formatting-guide>Formatting guide</a>
              </div>
              <div class="char-count" data-description-count>0/5000</div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="question-tags" class="form-label">Tags</label>
          <p class="form-description">
            Add relevant tags to help others find your question. Press Enter or comma to add a tag.
          </p>

          <div class="tags-input-container">
            <div class="tags-display" data-tags-display>
              </div>

            <input 
              type="text" 
              id="question-tags" 
              class="tags-input" 
              placeholder="Add tags... (e.g., javascript, react, css)" 
              data-tags-input
            >
            <div class="tags-suggestions is-hidden" data-tags-suggestions>
              </div>
          </div>

          <div class="popular-tags">
            <div class="popular-tags-title">Popular tags:</div>
            <div class="popular-tags-list">
              <span class="popular-tag" data-add-tag="javascript">javascript</span>
              <span class="popular-tag" data-add-tag="react">react</span>
              <span class="popular-tag" data-add-tag="python">python</span>
              <span class="popular-tag" data-add-tag="css">css</span>
              <span class="popular-tag" data-add-tag="html">html</span>
              <span class="popular-tag" data-add-tag="node.js">node.js</span>
              <span class="popular-tag" data-add-tag="database">database</span>
              <span class="popular-tag" data-add-tag="algorithms">algorithms</span>
            </div>
          </div>
        </div>

        <input type="hidden" name="tags" id="hiddenTags">

        <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
          
          <div class="save-status">
            <span class="save-indicator"></span>
            <span data-save-text>Draft saved</span>
          </div>

          <div class="form-actions-right" style="display: flex; gap: 10px;">
            <button type="button" class="btn btn--ghost" id="btnPreview" style="display: flex; align-items: center; gap: 8px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
              Preview
            </button>

            <button type="button" class="btn btn--secondary" id="btnSaveDraft" style="display: flex; align-items: center; gap: 8px; background: white; border: 1px solid #cbd5e1;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
              </svg>
              Save Draft
            </button>

            <button type="submit" class="btn btn--primary" style="display: flex; align-items: center; gap: 8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="22" y1="2" x2="11" y2="13" />
                <polygon points="22 2 15 22 11 13 2 9 22 2" />
              </svg>
              Post Question
            </button>
          </div>
        </div>

      </form>
    </div>

    <div class="form-sidebar">
      <div class="sidebar-section">
        <h3 class="sidebar-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z" />
          </svg>
          Writing Tips
        </h3>
        <ul class="tips-list">
          <li class="tip-item">
            <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z" />
            </svg>
            <div>Be specific and clear in your title</div>
          </li>
          <li class="tip-item">
            <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z" />
            </svg>
            <div>Include relevant code snippets</div>
          </li>
          <li class="tip-item">
            <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z" />
            </svg>
            <div>Explain what you've already tried</div>
          </li>
          <li class="tip-item">
            <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z" />
            </svg>
            <div>Use relevant tags for better visibility</div>
          </li>
        </ul>
      </div>

      <div class="sidebar-section">
        <h3 class="sidebar-title">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
          </svg>
          Guidelines
        </h3>
        <div class="sidebar-content">
          <p>Please ensure your question follows our community guidelines:</p>
          <ul class="space-y">
            <li>• Be respectful and professional</li>
            <li>• Search before posting</li>
            <li>• Provide sufficient context</li>
            <li>• Use appropriate tags</li>
          </ul>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="loading-overlay is-hidden" data-loading-overlay>
  <div class="loading-spinner"></div>
</div>


<div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99;"></div>

<div id="previewModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
  <div class="modal-content" style="background: white; width: 90%; max-width: 800px; padding: 30px; border-radius: 16px; position: relative;">
    <button onclick="closePreview()" style="position: absolute; top: 15px; right: 15px; border: none; background: none; font-size: 24px; cursor: pointer;">&times;</button>
    <h2 style="margin-bottom: 20px; color: #64748B; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Preview</h2>

    <div class="question-card" style="border: 1px solid #e2e8f0; padding: 25px; border-radius: 12px;">
      <h1 id="previewTitle" style="font-size: 1.5rem; margin-bottom: 10px; color: #0f172a;"></h1>
      <div id="previewTags" style="display: flex; gap: 8px; margin-bottom: 20px;"></div>

      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding: 10px; background: #f8fafc; border-radius: 8px;">
        <div style="width: 32px; height: 32px; background: #0466C8; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">ME</div>
        <div>
          <div style="font-weight: 600; font-size: 0.9rem;">You</div>
          <div style="color: #64748B; font-size: 0.8rem;">Just now</div>
        </div>
      </div>

      <div id="previewDesc" style="color: #475569; line-height: 1.6;"></div>
    </div>
  </div>
</div>

<script type="module" src="/js/app/edu-forum/add-question.js"></script>

