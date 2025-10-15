/**
 * Add Question Page JavaScript
 * 
 * Provides enhanced functionality for the question creation form including
 * rich text editing, auto-save, preview, tags management, and form validation.
 * 
 * Dependencies: None (vanilla JS)
 * Browser Support: Modern browsers with ES6+ support
 */

// ==========================================================================
// 1) Application State and Configuration
// ==========================================================================

const AppState = {
  currentUser: {
    id: 'dhannajaya17686',
    name: 'dhannajaya17686',
    avatar: 'DH'
  },
  formData: {
    title: '',
    description: '',
    category: '',
    tags: []
  },
  isDirty: false,
  isAutoSaving: false,
  lastSaved: null,
  validationErrors: []
};

const CONFIG = {
  autoSaveInterval: 10000, // 10 seconds
  maxTitleLength: 200,
  maxDescriptionLength: 5000,
  maxTags: 10,
  minTitleLength: 10,
  minDescriptionLength: 20,
  popularTags: [
    'javascript', 'react', 'python', 'css', 'html', 'node.js', 
    'database', 'algorithms', 'java', 'c++', 'sql', 'git'
  ],
  apiEndpoints: {
    submitQuestion: '/api/questions',
    saveDraft: '/api/drafts',
    loadDraft: '/api/drafts/latest',
    searchTags: '/api/tags/search'
  }
};

// ==========================================================================
// 2) DOM Content Loaded Event Listener
// ==========================================================================

document.addEventListener('DOMContentLoaded', function() {
  initializeApp();
});

function initializeApp() {
  initializeFormElements();
  initializeRichTextEditor();
  initializeTagsSystem();
  initializeFormValidation();
  initializeAutoSave();
  initializePreview();
  initializeKeyboardShortcuts();
  loadDraftIfExists();
  
  console.log('Add Question page initialized');
}

// ==========================================================================
// 3) Form Elements Initialization
// ==========================================================================

/**
 * Initializes basic form elements and event handlers
 */
function initializeFormElements() {
  const titleInput = document.querySelector('[data-title-input]');
  const descriptionInput = document.querySelector('[data-description-input]');
  const categoryInputs = document.querySelectorAll('input[name="category"]');
  const submitBtn = document.querySelector('[data-submit-question]');
  const saveDraftBtn = document.querySelector('[data-save-draft]');
  const previewBtn = document.querySelector('[data-preview-btn]');
  const clearBtn = document.querySelector('[data-clear-form]');
  const loadDraftBtn = document.querySelector('[data-load-draft]');

  // Title input handling
  if (titleInput) {
    titleInput.addEventListener('input', function() {
      AppState.formData.title = this.value;
      updateCharCount('title', this.value.length, CONFIG.maxTitleLength);
      markFormDirty();
      validateField('title');
      updatePreview();
    });

    titleInput.addEventListener('blur', function() {
      validateField('title');
    });
  }

  // Description input handling
  if (descriptionInput) {
    descriptionInput.addEventListener('input', function() {
      AppState.formData.description = this.value;
      updateCharCount('description', this.value.length, CONFIG.maxDescriptionLength);
      markFormDirty();
      validateField('description');
      updatePreview();
    });

    descriptionInput.addEventListener('blur', function() {
      validateField('description');
    });
  }

  // Category selection
  categoryInputs.forEach(input => {
    input.addEventListener('change', function() {
      if (this