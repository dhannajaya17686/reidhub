/**
 * Question Detail Page Interactive Behaviors
 * ==========================================
 * * Provides enhanced interactivity for the question detail view including
 * voting, answer submission, and improved user interactions.
 */

/**
 * Vote System Controller
 * ---------------------
 * Handles voting functionality for questions and answers.
 */
class VoteSystem {
  constructor() {
    this.init();
  }

  init() {
    // Initialize vote buttons
    document.querySelectorAll('.vote-button').forEach(button => {
      button.addEventListener('click', (e) => this.handleQuestionVote(e));
    });

    document.querySelectorAll('.answer-vote-btn').forEach(button => {
      button.addEventListener('click', (e) => this.handleAnswerVote(e));
    });
  }

  handleQuestionVote(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const isVoted = button.classList.contains('is-voted');
    
    // Toggle vote state
    button.classList.toggle('is-voted');
    
    // Update button text and icon
    const voteText = button.querySelector('.vote-text');
    if (voteText) {
      voteText.textContent = isVoted ? 'Vote' : 'Voted';
    }
    
    // Add visual feedback
    this.showVoteAnimation(button, !isVoted);
    
    // Here you would typically make an API call
    this.simulateVoteRequest('question', !isVoted);
  }

  handleAnswerVote(event) {
    event.preventDefault();
    const button = event.currentTarget;
    const countElement = button.parentElement.querySelector('.answer-vote-count');
    const isUpvote = button.classList.contains('upvote');
    
    let currentCount = parseInt(countElement.textContent) || 0;
    
    if (isUpvote) {
      currentCount += button.classList.contains('voted') ? -1 : 1;
      button.classList.toggle('voted');
    }
    
    countElement.textContent = currentCount;
    
    // Visual feedback
    this.showVoteAnimation(button, button.classList.contains('voted'));
    
    // Simulate API call
    this.simulateVoteRequest('answer', button.classList.contains('voted'));
  }

  showVoteAnimation(button, isVoted) {
    // Add animation class
    button.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      button.style.transform = 'scale(1)';
      
      if (isVoted) {
        // Success animation
        const originalBg = button.style.backgroundColor;
        button.style.backgroundColor = '#10B981';
        setTimeout(() => {
          button.style.backgroundColor = originalBg;
        }, 200);
      }
    }, 100);
  }

  simulateVoteRequest(type, isVoted) {
    // Simulate API request delay
    console.log(`${type} ${isVoted ? 'upvoted' : 'vote removed'}`);
    
    // Show toast notification
    this.showToast(isVoted ? 'Vote added!' : 'Vote removed');
  }

  showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'vote-toast';
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: var(--secondary-color);
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      z-index: 1000;
      animation: slideInRight 0.3s ease-out;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
      toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
      setTimeout(() => {
        if (toast.parentElement) {
          toast.parentElement.removeChild(toast);
        }
      }, 300);
    }, 2000);
  }
}

/**
 * Answer Input Controller
 * ----------------------
 * Handles the answer submission form.
 * UPDATED: Allows standard form submission to PHP backend.
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

    // Bind to the FORM submit event instead of the button click. This
    // ensures the browser performs a normal POST when validation passes
    // and avoids accidental preventDefault() swallowing the submission.
    const form = this.submitButton.closest('form');
    this.form = form;
    if (form) {
      form.addEventListener('submit', (e) => this.handleSubmit(e));
    } else {
      // Fallback: still bind click if form not found (very unlikely)
      this.submitButton.addEventListener('click', (e) => this.handleSubmit(e));
    }
    
    // Auto-resize textarea
    this.setupAutoResize();
    
    // Initial state
    this.updateSubmitButton();
  }

  handleInput() {
    this.updateSubmitButton();
    this.showCharacterCount();
  }

  handleKeydown(event) {
    // Submit with Ctrl/Cmd + Enter
    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
      // For the shortcut, we need to prevent the default newline
      event.preventDefault(); 
      if (!this.submitButton.disabled) {
        // Trigger the form submit directly to avoid click handlers
        if (this.form) {
          this.form.submit();
        } else {
          this.submitButton.click();
        }
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
      buttonText.textContent = isValid ? 'Post Answer' : `${this.minLength - content.length} more characters`;
    }
  }

  showCharacterCount() {
    const content = this.textarea.value.trim();
    let counter = document.querySelector('.character-counter');
    
    if (!counter) {
      counter = document.createElement('div');
      counter.className = 'character-counter';
      counter.style.cssText = `
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 0.5rem;
      `;
      this.textarea.parentElement.appendChild(counter);
    }
    
    counter.textContent = `${content.length} characters`;
    
    if (content.length < this.minLength) {
      counter.style.color = '#DC2626';
    } else {
      counter.style.color = 'var(--text-muted)';
    }
  }

  handleSubmit(event) {
    // --- CRITICAL FIX START ---
    
    const content = this.textarea.value.trim();
    
    // 1. If content is invalid, stop the form.
    if (content.length < this.minLength) {
        if (event) event.preventDefault();
        return;
    }
    
    // 2. If content is valid, DO NOT preventDefault.
    // Let the HTML form send the POST request to your PHP Controller.
    
    // 3. Optional: Show loading state while the page is reloading
    this.setLoadingState(true);
    
    // --- CRITICAL FIX END ---
  }

  setLoadingState(isLoading) {
    this.submitButton.disabled = isLoading;
    const buttonText = this.submitButton.querySelector('.submit-text');
    const buttonIcon = this.submitButton.querySelector('.submit-icon');
    
    if (isLoading) {
      if (buttonText) buttonText.textContent = 'Posting...';
      if (buttonIcon) buttonIcon.style.animation = 'spin 1s linear infinite';
    } else {
      if (buttonText) buttonText.textContent = 'Post Answer';
      if (buttonIcon) buttonIcon.style.animation = 'none';
    }
  }
}


/**
 * Report Functionality
 * -------------------
 * Handles question reporting functionality.
 */
class ReportSystem {
  constructor() {
    this.init();
  }

  init() {
    const reportButton = document.querySelector('.report-button');
    if (reportButton) {
      reportButton.addEventListener('click', (e) => this.handleReport(e));
    }
  }

  handleReport(event) {
    event.preventDefault();
    
    const confirmed = confirm('Are you sure you want to report this question? This action will notify the moderators.');
    
    if (confirmed) {
      // Simulate reporting
      this.submitReport();
    }
  }

  submitReport() {
    // Show loading state
    const reportButton = document.querySelector('.report-button');
    const originalText = reportButton.textContent;
    
    reportButton.textContent = 'Reporting...';
    reportButton.disabled = true;
    
    setTimeout(() => {
      reportButton.textContent = 'Reported';
      reportButton.style.background = '#10B981';
      reportButton.style.color = 'white';
      reportButton.style.borderColor = '#10B981';
      
      // Show success message
      this.showReportSuccess();
    }, 1000);
  }

  showReportSuccess() {
    const message = document.createElement('div');
    message.textContent = 'Question reported successfully. Thank you for helping keep our community safe.';
    message.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10B981;
      color: white;
      padding: 16px 20px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      z-index: 1000;
      max-width: 300px;
      animation: slideInRight 0.3s ease-out;
    `;
    
    document.body.appendChild(message);
    
    setTimeout(() => {
      message.style.animation = 'slideOutRight 0.3s ease-in forwards';
      setTimeout(() => {
        if (message.parentElement) {
          message.parentElement.removeChild(message);
        }
      }, 300);
    }, 5000);
  }
}

/**
 * Animation Styles
 * ---------------
 * Add CSS animations for enhanced interactions.
 */
const animationStyles = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;

// Add animation styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = animationStyles;
document.head.appendChild(styleSheet);

/**
 * Initialize All Components
 * ------------------------
 */
document.addEventListener('DOMContentLoaded', () => {
  new VoteSystem();
  new AnswerInput();
  new ReportSystem();
  
  // Add entrance animations
  const animateElements = document.querySelectorAll('.question-detail-header, .answer-card, .answer-input-section');
  animateElements.forEach((element, index) => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
      element.style.opacity = '1';
      element.style.transform = 'translateY(0)';
    }, index * 100 + 200);
  });
});

/**
 * Export modules
 */
export { VoteSystem, AnswerInput, ReportSystem };