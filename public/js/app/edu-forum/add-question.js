/**
 * Add Question Page JavaScript
 * Features: Markdown Parsing, Preview, Tags, Auto-Save, Similar Question Search
 */

// ==========================================================================
// 1) Application State and Configuration
// ==========================================================================

const AppState = {
  formData: {
    title: '',
    description: '',
    category: '',
    tags: []
  },
  isDirty: false,
  lastSaved: null
};

const CONFIG = {
  autoSaveKey: 'edu_forum_draft',
  maxTitleLength: 200,
  maxDescriptionLength: 5000,
  maxTags: 5,
  minTitleLength: 10,
  minDescriptionLength: 20
};

// ==========================================================================
// 2) Initialization
// ==========================================================================

document.addEventListener('DOMContentLoaded', function() {
  initializeApp();
});

function initializeApp() {
  initializeFormElements();
  initializeButtons(); 
  initializeRichTextEditor();
  initializeTagsSystem();
  initializeAutoSave();
  initializeSimilarQuestionsSearch(); 
  
  // Initial load
  loadDraftIfExists();
  updateLivePreview(); 

  console.log('Add Question page initialized');
}

// ==========================================================================
// 3) Improved Markdown Parser (Fixes Bold/Italic)
// ==========================================================================

function parseMarkdown(text) {
    if (!text) return '';
    let html = text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    html = html.replace(/\*\*([\s\S]*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([\s\S]*?)\*/g, '<em>$1</em>');
    html = html.replace(/```([\s\S]*?)```/g, '<pre style="background:#1e293b; color:#e2e8f0; padding:10px; border-radius:6px; overflow-x:auto; margin:10px 0;"><code>$1</code></pre>');
    html = html.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9; color:#ef4444; padding:2px 4px; border-radius:4px; font-family:monospace;">$1</code>');
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" style="color:#0466C8; text-decoration:underline;">$1</a>');
    html = html.replace(/^\s*-\s+(.*)$/gm, '• $1<br>');
    html = html.replace(/\n/g, '<br>');
    return html;
}

// ==========================================================================
// 4) Core Form Logic
// ==========================================================================

function initializeFormElements() {
  const titleInput = document.querySelector('[data-title-input]');
  const descriptionInput = document.querySelector('[data-description-input]');
  const categoryInputs = document.querySelectorAll('input[name="category"]');

  // Title Listener
  if (titleInput) {
    titleInput.addEventListener('input', function() {
      AppState.formData.title = this.value;
      updateCharCount('title', this.value.length, CONFIG.maxTitleLength);
      markFormDirty();
      updateLivePreview();
    });
    // Validation on blur
    titleInput.addEventListener('blur', () => validateField('title'));
  }

  // Description Listener
  if (descriptionInput) {
    descriptionInput.addEventListener('input', function() {
      AppState.formData.description = this.value;
      updateCharCount('description', this.value.length, CONFIG.maxDescriptionLength);
      markFormDirty();
      updateLivePreview();
    });
    // Validation on blur
    descriptionInput.addEventListener('blur', () => validateField('description'));
  }

  // Category Listener
  categoryInputs.forEach(input => {
    input.addEventListener('change', function() {
      AppState.formData.category = this.value;
      markFormDirty();
    });
  });
}

function validateField(fieldName) {
  const value = AppState.formData[fieldName];
  let isValid = true;
  let input;

  if (fieldName === 'title') {
    isValid = value.length >= CONFIG.minTitleLength;
    input = document.querySelector('[data-title-input]');
  } else if (fieldName === 'description') {
    isValid = value.length >= CONFIG.minDescriptionLength;
    input = document.querySelector('[data-description-input]');
  }

  if(input) {
    input.style.borderColor = isValid ? '' : '#DC2626';
  }
}

// ==========================================================================
// 5) Button Actions (Preview & Save)
// ==========================================================================

function initializeButtons() {
  // Save Draft Button
  const btnSave = document.getElementById('btnSaveDraft');
  if (btnSave) {
    btnSave.addEventListener('click', (e) => {
      e.preventDefault();
      forceSaveDraft();
      
      // Visual Feedback
      const originalHTML = btnSave.innerHTML;
      btnSave.innerHTML = `<span style="color:#059669; font-weight:bold;">✓ Saved!</span>`;
      setTimeout(() => btnSave.innerHTML = originalHTML, 2000);
    });
  }

  // Preview Button
  const btnPreview = document.getElementById('btnPreview');
  if (btnPreview) {
    btnPreview.addEventListener('click', (e) => {
      e.preventDefault();
      openPreviewModal();
    });
  }
}

// ==========================================================================
// 6) Preview System (Sidebar & Modal)
// ==========================================================================

function updateLivePreview() {
  // Update the sidebar preview box
  const sidebarPreview = document.querySelector('[data-preview-content]');
  if (sidebarPreview) {
    const desc = AppState.formData.description || '<span style="color:#94a3b8">Start typing to see a preview...</span>';
    sidebarPreview.innerHTML = parseMarkdown(desc);
  }
}

function openPreviewModal() {
  const modal = document.getElementById('previewModal');
  const titleEl = document.getElementById('previewTitle');
  const descEl = document.getElementById('previewDesc');
  const tagsContainer = document.getElementById('previewTags');

  if (!modal) return;

  // Fill Title
  titleEl.textContent = AppState.formData.title || "Untitled Question";

  // Fill Description (Parsed)
  descEl.innerHTML = parseMarkdown(AppState.formData.description || "No description provided.");

  // Fill Tags (Updated to use CSS class instead of inline styles)
  tagsContainer.innerHTML = '';
  AppState.formData.tags.forEach(tag => {
    const span = document.createElement('span');
    span.className = 'tag-item'; // Use the CSS class
    span.textContent = tag;
    tagsContainer.appendChild(span);
  });

  // Show Modal
  modal.style.display = 'flex';
}

window.closePreview = function() {
  document.getElementById('previewModal').style.display = 'none';
};

// ==========================================================================
// 7) Rich Text Editor Toolbar
// ==========================================================================

function initializeRichTextEditor() {
  const toolbar = document.querySelector('.textarea-toolbar');
  const hiddenTextarea = document.querySelector('[data-description-input]');
  const editor = document.querySelector('[data-description-editor]');

  if (!toolbar || !editor || !hiddenTextarea) return;

  function htmlToMarkdown(html) {
    const container = document.createElement('div');
    container.innerHTML = html;

    function walk(node) {
      let out = '';
      node.childNodes.forEach(child => {
        if (child.nodeType === Node.TEXT_NODE) {
          out += child.nodeValue.replace(/\u00A0/g, ' ');
        } else if (child.nodeType === Node.ELEMENT_NODE) {
          const tag = child.tagName.toLowerCase();
          if (tag === 'strong' || tag === 'b') {
            out += '**' + walk(child).trim() + '**';
          } else if (tag === 'em' || tag === 'i') {
            out += '*' + walk(child).trim() + '*';
          } else if (tag === 'code' && child.parentElement && child.parentElement.tagName.toLowerCase() === 'pre') {
            out += '\n```\n' + child.textContent + '\n```\n';
          } else if (tag === 'pre') {
            const code = child.querySelector('code');
            if (code) out += '\n```\n' + code.textContent + '\n```\n';
            else out += '\n```\n' + child.textContent + '\n```\n';
          } else if (tag === 'code') {
            out += '`' + child.textContent + '`';
          } else if (tag === 'a') {
            const href = child.getAttribute('href') || '';
            out += '[' + walk(child).trim() + '](' + href + ')';
          } else if (tag === 'ul') {
            const items = Array.from(child.children).map(li => '- ' + walk(li).trim()).join('\n');
            out += '\n' + items + '\n';
          } else if (tag === 'ol') {
            const items = Array.from(child.children).map((li, i) => (i+1) + '. ' + walk(li).trim()).join('\n');
            out += '\n' + items + '\n';
          } else if (tag === 'br') {
            out += '\n';
          } else if (tag === 'p' || tag === 'div') {
            const inner = walk(child).trim();
            if (inner) out += inner + '\n\n';
          } else {
            out += walk(child);
          }
        }
      });
      return out;
    }

    return walk(container).trim();
  }

  toolbar.addEventListener('click', (e) => {
    const btn = e.target.closest('.toolbar-btn');
    if (!btn) return;
    e.preventDefault();
    const format = btn.dataset.format;
    editor.focus();
    switch (format) {
      case 'bold': document.execCommand('bold'); break;
      case 'italic': document.execCommand('italic'); break;
      case 'code': document.execCommand('insertHTML', false, '<code>' + (window.getSelection().toString() || '') + '</code>'); break;
      case 'link': {
        const url = prompt('Enter URL (include http:// or https://):');
        if (url) document.execCommand('createLink', false, url);
        break;
      }
      case 'list': document.execCommand('insertUnorderedList'); break;
      case 'code-block': {
        const sel = window.getSelection();
        const text = sel.toString() || '';
        const html = '<pre><code>' + (text || '') + '</code></pre><p></p>';
        document.execCommand('insertHTML', false, html);
        break;
      }
    }

    AppState.formData.description = htmlToMarkdown(editor.innerHTML);
    updateCharCount('description', AppState.formData.description.length, CONFIG.maxDescriptionLength);
    markFormDirty();
    updateLivePreview();
  });

  editor.addEventListener('input', () => {
    AppState.formData.description = htmlToMarkdown(editor.innerHTML);
    updateCharCount('description', AppState.formData.description.length, CONFIG.maxDescriptionLength);
    markFormDirty();
    updateLivePreview();
  });

  const form = document.querySelector('form[data-question-form]') || editor.closest('form');
  if (form) {
    form.addEventListener('submit', (e) => {
      hiddenTextarea.value = htmlToMarkdown(editor.innerHTML);
    });
  }

  window.insertBoldMarkdown = function() {
    if (!editor) return;
    try {
      const sel = window.getSelection();
      if (editor.contains(document.activeElement) || (sel && sel.rangeCount)) {
        document.execCommand('bold');
        const md = htmlToMarkdown(editor.innerHTML);
        AppState.formData.description = md;
        hiddenTextarea.value = md;
        updateCharCount('description', md.length, CONFIG.maxDescriptionLength);
        markFormDirty();
        updateLivePreview();
        return;
      }
    } catch (err) {}

    if (hiddenTextarea) {
      const start = hiddenTextarea.selectionStart || 0;
      const end = hiddenTextarea.selectionEnd || 0;
      const val = hiddenTextarea.value || '';
      const selected = val.slice(start, end) || 'bold text';
      const newVal = val.slice(0, start) + `**${selected}**` + val.slice(end);
      hiddenTextarea.value = newVal;
      AppState.formData.description = newVal;
      updateCharCount('description', newVal.length, CONFIG.maxDescriptionLength);
      markFormDirty();
      updateLivePreview();
    }
  };
}

// ==========================================================================
// 8) Tags System (UPDATED: Cleaned up Styles)
// ==========================================================================

function initializeTagsSystem() {
  const tagInput = document.querySelector('[data-tags-input]');
  const tagsContainer = document.querySelector('[data-tags-display]');
  const popularTags = document.querySelectorAll('[data-add-tag]');
  const hiddenInput = document.getElementById('hiddenTags');

  if (!tagInput) return;

  tagInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ',') {
      e.preventDefault();
      addTag(tagInput.value);
    }
  });

  popularTags.forEach(btn => {
    btn.addEventListener('click', () => addTag(btn.dataset.addTag));
  });

  function addTag(tag) {
    tag = tag.trim().toLowerCase();
    if (!tag) return;
    
    if (AppState.formData.tags.length >= CONFIG.maxTags) {
      alert(`Max ${CONFIG.maxTags} tags allowed.`);
      return;
    }
    if (AppState.formData.tags.includes(tag)) return;

    AppState.formData.tags.push(tag);
    tagInput.value = '';
    
    renderTags();
    markFormDirty();
  }

  function removeTag(tagToRemove) {
    AppState.formData.tags = AppState.formData.tags.filter(t => t !== tagToRemove);
    renderTags();
    markFormDirty();
  }

  function renderTags() {
    tagsContainer.innerHTML = '';
    AppState.formData.tags.forEach(tag => {
      const tagEl = document.createElement('span');
      
      // UPDATED: Use the new CSS classes directly
      tagEl.className = 'tag-item'; 
      
      // Note: Removed the inline style.cssText. 
      // The class .tag-item in CSS now handles the blue chip look.
      
      tagEl.innerHTML = `
        ${tag}
        <button type="button" class="tag-remove" aria-label="Remove tag">&times;</button>
      `;
      
      tagEl.querySelector('button').addEventListener('click', () => removeTag(tag));
      tagsContainer.appendChild(tagEl);
    });

    if (hiddenInput) {
      hiddenInput.value = AppState.formData.tags.join(',');
    }
  }
}

// ==========================================================================
// 9) Auto-Save Logic
// ==========================================================================

function initializeAutoSave() {
  setInterval(() => {
    if (AppState.isDirty) {
      forceSaveDraft();
    }
  }, 5000);
}

function forceSaveDraft() {
  const data = {
    title: AppState.formData.title,
    description: AppState.formData.description,
    tags: AppState.formData.tags,
    category: AppState.formData.category,
    timestamp: new Date().toISOString()
  };
  
  localStorage.setItem(CONFIG.autoSaveKey, JSON.stringify(data));
  
  const indicator = document.querySelector('[data-save-text]');
  if (indicator) indicator.textContent = 'Draft Saved just now';
  
  AppState.isDirty = false;
}

function loadDraftIfExists() {
  const saved = localStorage.getItem(CONFIG.autoSaveKey);
  if (!saved) return;

  try {
    const data = JSON.parse(saved);
    const titleInput = document.querySelector('[data-title-input]');
    const descInput = document.querySelector('[data-description-input]');
    const editor = document.querySelector('[data-description-editor]');
    const tagsDisplay = document.querySelector('[data-tags-display]');

    if(titleInput && data.title) {
      titleInput.value = data.title;
      AppState.formData.title = data.title;
      updateCharCount('title', data.title.length, CONFIG.maxTitleLength);
    }

    if (editor && data.description) {
      editor.innerHTML = parseMarkdown(data.description);
      AppState.formData.description = data.description;
      updateCharCount('description', data.description.length, CONFIG.maxDescriptionLength);
    } else if(descInput && data.description) {
      descInput.value = data.description;
      AppState.formData.description = data.description;
      updateCharCount('description', data.description.length, CONFIG.maxDescriptionLength);
    }

    if(data.tags && Array.isArray(data.tags)) {
      AppState.formData.tags = data.tags;
      if (tagsDisplay) {
        tagsDisplay.innerHTML = '';
        data.tags.forEach(tag => {
           const span = document.createElement('span');
           span.className = 'tag-item'; // Use CSS class
           span.textContent = tag;
           // Removed inline style.cssText
           tagsDisplay.appendChild(span);
        });
      }
    }
    
    updateLivePreview();
  } catch(e) {
    console.error("Error loading draft", e);
  }
}

function updateCharCount(field, currentLength, maxLength) {
  const counter = document.querySelector(`[data-${field}-count]`);
  if (counter) {
    counter.textContent = `${currentLength}/${maxLength}`;
    counter.style.color = currentLength >= maxLength ? '#DC2626' : '';
  }
}

function markFormDirty() {
  AppState.isDirty = true;
}

// ==========================================================================
// 10) Similar Question Search (Suggestions)
// ==========================================================================

function initializeSimilarQuestionsSearch() {
    const titleInput = document.getElementById('question-title');
    const suggestionsBox = document.getElementById('similar-questions');
    let timeout = null;

    if (titleInput && suggestionsBox) {
        titleInput.addEventListener('keyup', function() {
            const query = this.value.trim();

            // Clear previous timeout (Debouncing)
            clearTimeout(timeout);

            // Hide if input is too short
            if (query.length < 4) {
                suggestionsBox.style.display = 'none';
                suggestionsBox.innerHTML = '';
                return;
            }

            // Wait 500ms after user stops typing before fetching
            timeout = setTimeout(() => {
                fetch(`/dashboard/forum/search-similar?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.results.length > 0) {
                            let html = '<span class="similar-title">Similar questions found:</span>';
                            data.results.forEach(item => {
                                html += `<a href="/dashboard/forum/question?id=${item.id}" target="_blank" class="similar-item">
                                            📄 ${item.title}
                                         </a>`;
                            });
                            suggestionsBox.innerHTML = html;
                            suggestionsBox.style.display = 'block';
                        } else {
                            suggestionsBox.style.display = 'none';
                        }
                    })
                    .catch(err => console.error('Error fetching suggestions:', err));
            }, 500);
        });
    }
}