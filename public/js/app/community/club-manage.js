/**
 * Club Manage JavaScript
 */

// Upload Post Card Click
const uploadPostCard = document.getElementById('upload-post-card');
const uploadModal = document.getElementById('upload-modal');

if (uploadPostCard) {
  uploadPostCard.addEventListener('click', () => {
    uploadModal.style.display = 'flex';
  });
}

// Post Card Click - View Post
const postCards = document.querySelectorAll('.post-card');
const viewPostModal = document.getElementById('view-post-modal');

postCards.forEach(card => {
  card.addEventListener('click', (e) => {
    // Don't open modal if clicking on menu button
    if (e.target.closest('.post-card__menu')) {
      return;
    }
    
    viewPostModal.style.display = 'flex';
  });
});

// Close modals
const modalCloseButtons = document.querySelectorAll('.modal-close');

modalCloseButtons.forEach(button => {
  button.addEventListener('click', () => {
    button.closest('.modal-overlay').style.display = 'none';
  });
});

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      overlay.style.display = 'none';
    }
  });
});

// File Upload Handling
const fileInput = document.getElementById('post-image');
const fileUploadArea = document.getElementById('file-upload-area');
const filePreview = document.getElementById('file-preview');
const previewImage = document.getElementById('preview-image');
const previewRemove = document.getElementById('preview-remove');

// Trigger file input on area click
if (fileUploadArea) {
  fileUploadArea.addEventListener('click', () => {
    fileInput.click();
  });
}

// Handle file selection
if (fileInput) {
  fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    
    if (file) {
      // Validate file type
      const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
      if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image file (PNG, JPEG, or JPG)');
        fileInput.value = '';
        return;
      }
      
      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        fileInput.value = '';
        return;
      }
      
      // Display preview
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImage.src = e.target.result;
        fileUploadArea.style.display = 'none';
        filePreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });
}

// Remove file
if (previewRemove) {
  previewRemove.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.value = '';
    previewImage.src = '';
    fileUploadArea.style.display = 'block';
    filePreview.style.display = 'none';
  });
}

// Drag and drop
if (fileUploadArea) {
  fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#0466C8';
    fileUploadArea.style.backgroundColor = '#f3f8ff';
  });
  
  fileUploadArea.addEventListener('dragleave', () => {
    fileUploadArea.style.borderColor = '#d1d5db';
    fileUploadArea.style.backgroundColor = '#f9fafb';
  });
  
  fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.style.borderColor = '#d1d5db';
    fileUploadArea.style.backgroundColor = '#f9fafb';
    
    const file = e.dataTransfer.files[0];
    if (file) {
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      fileInput.files = dataTransfer.files;
      
      // Trigger change event
      const event = new Event('change', { bubbles: true });
      fileInput.dispatchEvent(event);
    }
  });
}

// Upload Form Submission
const uploadForm = document.getElementById('upload-form');

if (uploadForm) {
  uploadForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const formData = new FormData(uploadForm);
    
    // Validate required fields
    const title = document.getElementById('post-title-input').value;
    const description = document.getElementById('post-description').value;
    const image = fileInput.files[0];
    
    if (!title || !description || !image) {
      alert('Please fill in all required fields');
      return;
    }
    
    console.log('Uploading post...');
    console.log('Title:', title);
    console.log('Description:', description);
    console.log('Image:', image.name);
    
    // Send to backend
    // fetch('/api/clubs/posts/create', {
    //   method: 'POST',
    //   body: formData
    // })
    
    alert('Post uploaded successfully!');
    uploadModal.style.display = 'none';
    uploadForm.reset();
    fileUploadArea.style.display = 'block';
    filePreview.style.display = 'none';
    
    // Reload page to show new post
    // window.location.reload();
  });
}

// Report Post
const reportPostBtn = document.getElementById('report-post-btn');

if (reportPostBtn) {
  reportPostBtn.addEventListener('click', () => {
    console.log('Reporting post...');
    
    if (confirm('Are you sure you want to report this post?')) {
      // Send report to backend
      alert('Post reported successfully. Our team will review it.');
      viewPostModal.style.display = 'none';
    }
  });
}

// Delete Post
const deletePostBtn = document.getElementById('delete-post-btn');

if (deletePostBtn) {
  deletePostBtn.addEventListener('click', () => {
    if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
      console.log('Deleting post...');
      
      // Send delete request to backend
      // fetch('/api/clubs/posts/delete', { method: 'DELETE' })
      
      alert('Post deleted successfully');
      viewPostModal.style.display = 'none';
      
      // Reload page
      // window.location.reload();
    }
  });
}

// Edit Profile Button
const editProfileBtn = document.getElementById('edit-profile-btn');

if (editProfileBtn) {
  editProfileBtn.addEventListener('click', () => {
    console.log('Navigating to edit profile...');
    // window.location.href = '/community/clubs/edit-profile';
  });
}

console.log('Club manage page loaded');