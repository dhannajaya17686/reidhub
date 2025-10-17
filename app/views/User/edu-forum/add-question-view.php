<link href="/css/app/user/edu-forum/add-question.css" rel="stylesheet">

<div class="question-form-page">
        
        <!-- Page Header -->
        <div class="page-header">
          <h1 class="page-title">Ask Your Question</h1>
          <p class="page-subtitle">
            Share your knowledge gaps with the community. Provide detailed information to help others understand and answer your question effectively.
          </p>
        </div>
        
        <!-- Form Container -->
        <div class="question-form-container">
          
          <!-- Main Form -->
          <div class="form-main">
            <form class="question-form" data-question-form>
              
              <!-- Question Title -->
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
                >
                <div class="input-help">
                  <span class="char-count" data-title-count>0/200</span>
                </div>
              </div>
              
              <!-- Category Selection -->
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
              
              <!-- Question Description -->
              <div class="form-group">
                <label for="question-description" class="form-label form-label--required">Question Description</label>
                <p class="form-description">
                  Provide detailed information about your question. Include what you've tried, expected results, and any relevant code or context.
                </p>
                
                <div class="rich-textarea-container">
                  <!-- Formatting Toolbar -->
                  <div class="textarea-toolbar">
                    <div class="toolbar-group">
                      <button type="button" class="toolbar-btn" data-format="bold" title="Bold (Ctrl+B)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z"/>
                        </svg>
                      </button>
                      <button type="button" class="toolbar-btn" data-format="italic" title="Italic (Ctrl+I)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"/>
                        </svg>
                      </button>
                      <button type="button" class="toolbar-btn" data-format="code" title="Inline code">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
                        </svg>
                      </button>
                    </div>
                    
                    <div class="toolbar-group">
                      <button type="button" class="toolbar-btn" data-format="list" title="Bullet list">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12c-.83 0-1.5.68-1.5 1.5s.68 1.5 1.5 1.5 1.5-.68 1.5-1.5-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"/>
                        </svg>
                      </button>
                      <button type="button" class="toolbar-btn" data-format="link" title="Add link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                        </svg>
                      </button>
                      <button type="button" class="toolbar-btn" data-format="code-block" title="Code block">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0L19.2 12l-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/>
                        </svg>
                      </button>
                    </div>
                    
                    <div class="toolbar-group">
                      <button type="button" class="toolbar-btn" data-format="attach" title="Attach file">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Textarea -->
                  <textarea 
                    id="question-description" 
                    name="description"
                    class="form-textarea" 
                    placeholder="Describe your question in detail. Include:&#10;• What you're trying to achieve&#10;• What you've already tried&#10;• Expected vs actual results&#10;• Relevant code snippets or error messages"
                    required
                    rows="8"
                    maxlength="5000"
                    data-description-input
                  ></textarea>
                  
                  <!-- Textarea Footer -->
                  <div class="textarea-footer">
                    <div class="formatting-help">
                      <a href="#" data-formatting-guide>Formatting guide</a>
                    </div>
                    <div class="char-count" data-description-count>0/5000</div>
                  </div>
                </div>
              </div>
              
              <!-- Tags -->
              <div class="form-group">
                <label for="question-tags" class="form-label">Tags</label>
                <p class="form-description">
                  Add relevant tags to help others find your question. Press Enter or comma to add a tag.
                </p>
                
                <div class="tags-input-container">
                  <div class="tags-display" data-tags-display>
                    <!-- Tags will be added here dynamically -->
                  </div>
                  
                  <input 
                    type="text" 
                    id="question-tags"
                    class="tags-input" 
                    placeholder="Add tags... (e.g., javascript, react, css)"
                    data-tags-input
                  >
                  
                  <!-- Tag Suggestions Dropdown -->
                  <div class="tags-suggestions is-hidden" data-tags-suggestions>
                    <!-- Suggestions will be populated here -->
                  </div>
                </div>
                
                <!-- Popular Tags -->
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
              
              <!-- Form Actions -->
              <div class="form-actions">
                <div class="form-actions-left">
                  <div class="save-status" data-save-status>
                    <div class="save-indicator" data-save-indicator></div>
                    <span data-save-text>Draft saved</span>
                  </div>
                </div>
                
                <div class="form-actions-right">
                  <button type="button" class="btn btn--ghost" data-preview-btn>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                    Preview
                  </button>
                  
                  <button type="button" class="btn btn--secondary" data-save-draft>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                    </svg>
                    Save Draft
                  </button>
                  
                  <button type="submit" class="btn btn--primary" data-submit-question>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                    Post Question
                  </button>
                </div>
              </div>
              
            </form>
          </div>
          
          <!-- Sidebar -->
          <div class="form-sidebar">
            
            <!-- Writing Tips -->
            <div class="sidebar-section">
              <h3 class="sidebar-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z"/>
                </svg>
                Writing Tips
              </h3>
              <ul class="tips-list">
                <li class="tip-item">
                  <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z"/>
                  </svg>
                  <div>Be specific and clear in your title</div>
                </li>
                <li class="tip-item">
                  <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z"/>
                  </svg>
                  <div>Include relevant code snippets</div>
                </li>
                <li class="tip-item">
                  <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z"/>
                  </svg>
                  <div>Explain what you've already tried</div>
                </li>
                <li class="tip-item">
                  <svg class="tip-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9l-5.91.74L12 16l-4.09-6.26L2 9l6.91-.74z"/>
                  </svg>
                  <div>Use relevant tags for better visibility</div>
                </li>
              </ul>
            </div>
            
            <!-- Preview Section -->
            <div class="sidebar-section">
              <div class="preview-section">
                <h3 class="preview-title">Question Preview</h3>
                <div class="preview-content" data-preview-content>
                  <p class="text-muted">Start typing to see a preview of your question...</p>
                </div>
                <div class="draft-actions">
                  <button class="draft-btn" data-clear-form>Clear All</button>
                  <button class="draft-btn" data-load-draft>Load Draft</button>
                </div>
              </div>
            </div>
            
            <!-- Community Guidelines -->
            <div class="sidebar-section">
              <h3 class="sidebar-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
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
      
    </main>
    
  </div>
  
  <!-- Loading Overlay -->
  <div class="loading-overlay is-hidden" data-loading-overlay>
    <div class="loading-spinner"></div>
  </div>
  
  <!-- Sidebar Overlay for Mobile -->
  <div class="sidebar-overlay" data-sidebar-overlay aria-hidden="true" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99;"></div>
  
  <!-- JavaScript -->
  <script type="module" src="add-question.js"></script>
  
</body>
</html>