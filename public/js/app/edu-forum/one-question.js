
/**
 * Question Detail Page Interactive Behaviors
 * ==========================================
 * 
 * Provides enhanced interactivity for the question detail view including
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
 * Handles the answer submission form with enhanced UX.
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
    this.submitButton.addEventListener('click', (e) => this.handleSubmit(e));
    
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
      event.preventDefault();
      if (!this.submitButton.disabled) {
        this.handleSubmit();
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
    if (event) event.preventDefault();
    
    const content = this.textarea.value.trim();
    if (content.length < this.minLength) return;
    
    // Show loading state
    this.setLoadingState(true);
    
    // Simulate API call
    setTimeout(() => {
      this.submitAnswer(content);
    }, 1000);
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

  submitAnswer(content) {
    // Create new answer element
    const newAnswer = this.createAnswerElement(content);
    
    // Add to answers list
    const answersList = document.querySelector('.answers-section');
    const answerInput = document.querySelector('.answer-input-section');
    
    if (answersList && answerInput) {
      answersList.insertBefore(newAnswer, answerInput);
    }
    
    // Clear form
    this.textarea.value = '';
    this.setLoadingState(false);
    this.updateSubmitButton();
    
    // Update answer count
    this.updateAnswerCount();
    
    // Show success message
    this.showSuccessMessage();
    
    // Scroll to new answer
    newAnswer.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  createAnswerElement(content) {
    const answerDiv = document.createElement('div');
    answerDiv.className = 'answer-card fade-in';
    answerDiv.innerHTML = `
      <div class="answer-header">
        <img class="answer-author-avatar" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%230466C8'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='14' font-weight='bold'%3E${this.getCurrentUserInitials()}%3C/text%3E%3C/svg%3E" alt="Your avatar">
        <div class="answer-author-info">
          <div class="answer-author-name">dhannajaya17686</div>
          <div class="answer-timestamp">just now</div>
        </div>
      </div>
      <div class="answer-content">
        <p>${this.escapeHtml(content)}</p>
      </div>
      <div class="answer-actions">
        <div class="answer-vote">
          <button class="answer-vote-btn upvote" aria-label="Upvote answer">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
              <path d="M8 1l2.5 5h5.5l-4.5 3.5 1.5 5.5-4.5-3.5-4.5 3.5 1.5-5.5-4.5-3.5h5.5z"/>
            </svg>
          </button>
          <span class="answer-vote-count">0</span>
        </div>
        <button class="reply-button">Reply</button>
      </div>
    `;
    
    return answerDiv;
  }

  getCurrentUserInitials() {
    return 'DM'; // Based on "dhannajaya17686"
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  updateAnswerCount() {
    const countElement = document.querySelector('.answers-count');
    if (countElement) {
      const currentCount = parseInt(countElement.textContent) || 0;
      countElement.textContent = currentCount + 1;
    }
  }

  showSuccessMessage() {
    const message = document.createElement('div');
    message.className = 'success-message';
    message.textContent = 'Your answer has been posted!';
    message.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10B981;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      z-index: 1000;
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
    }, 3000);
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
  new MobileSidebar();
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