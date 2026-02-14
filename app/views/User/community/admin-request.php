<link rel="stylesheet" href="/css/globals.css">
<link rel="stylesheet" href="/css/app/user/community/blog-form.css">

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard" class="breadcrumb__link">Dashboard</a>
    </li>
    <li class="breadcrumb__item">
      <a href="/dashboard/community" class="breadcrumb__link">Community</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      Request Club Admin Access
    </li>
  </ol>
</nav>

<main class="blog-form-main" role="main" aria-label="Request Admin Access">
  
  <div class="page-header">
    <h1 class="page-title">Request Club Admin Access</h1>
    <p class="page-subtitle">
      Become a club admin to create and manage clubs and events
    </p>
  </div>

  <?php if ($data['hasPendingRequest']): ?>
  <div class="info-box">
    <div class="info-box__icon">📋</div>
    <div class="info-box__content">
      <h3 class="info-box__title">Request Pending Review</h3>
      <p class="info-box__text">
        Your admin request has been submitted and is awaiting review by system administrators.
        You will receive a notification once your request has been reviewed.
      </p>
      <div class="info-box__details">
        <small>
          <strong>Request Date:</strong> <?= date('M d, Y', strtotime($data['pendingRequest']['requested_at'])) ?><br>
          <strong>Status:</strong> <span class="badge" style="background: #fbbf24; color: #78350f;">Pending</span>
        </small>
      </div>
    </div>
  </div>
  <?php else: ?>

  <form class="blog-form" id="admin-request-form" method="POST" action="/dashboard/community/submit-admin-request">
    
    <div class="form-container">
      
      <div class="form-section">
        <h2 class="form-section__title">Why do you want to become a club admin?</h2>
        
        <div class="form-group">
          <label for="reason" class="form-label">Your Reason</label>
          <textarea 
            id="reason" 
            name="reason" 
            class="form-input" 
            placeholder="Tell us why you want to become a club admin. What clubs are you interested in managing?" 
            rows="6" 
            required
            style="font-family: inherit; padding: 0.75rem;">
          </textarea>
          <small style="color: #6b7280;">Please provide a detailed explanation to help administrators make an informed decision.</small>
          <div class="form-error" id="reason-error"></div>
        </div>

        <div style="background: #f0f9ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 1rem; margin: 1.5rem 0;">
          <h3 style="margin-top: 0; color: #0284c7; font-size: 0.95rem;">What will you have access to as a Club Admin?</h3>
          <ul style="margin: 0.5rem 0; padding-left: 1.25rem; color: #475569; font-size: 0.9rem;">
            <li>Create and manage clubs</li>
            <li>Create and manage events for your clubs</li>
            <li>Manage club members and roles</li>
            <li>Post announcements and updates</li>
            <li>View club attendee statistics</li>
            <li>Manage club requests and approvals</li>
          </ul>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
          <button type="submit" class="btn btn--primary" style="width: 100%;">
            Submit Admin Request
          </button>
          <a href="/dashboard/community/clubs" class="btn btn--outline" style="width: 100%; text-align: center; margin-top: 0.5rem;">
            Cancel
          </a>
        </div>
      </div>
    </div>
  </form>

  <?php endif; ?>
</main>

<style>
.info-box {
  background: #dbeafe;
  border-left: 4px solid #0284c7;
  border-radius: 8px;
  padding: 1.5rem;
  margin: 2rem auto;
  max-width: 600px;
  display: flex;
  gap: 1rem;
}

.info-box__icon {
  font-size: 2rem;
  flex-shrink: 0;
}

.info-box__content {
  flex: 1;
}

.info-box__title {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #0284c7;
}

.info-box__text {
  margin: 0 0 1rem 0;
  color: #0c4a6e;
  line-height: 1.5;
}

.info-box__details {
  padding-top: 0.75rem;
  border-top: 1px solid #7dd3fc;
  color: #0c4a6e;
}

.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('admin-request-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

    const formData = new FormData(form);

    console.log('Submitting admin request with data:', Object.fromEntries(formData));

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData
      });

      console.log('Response status:', response.status);
      const responseText = await response.text();
      console.log('Response text:', responseText);
      
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON parse error:', parseError);
        alert('Server returned invalid response: ' + responseText.substring(0, 200));
        return;
      }

      if (data.success) {
        alert(data.message);
        window.location.href = '/dashboard/community/request-admin';
      } else {
        alert(data.message || 'An error occurred');
        
        // Display field-specific errors if available
        if (data.errors) {
          Object.keys(data.errors).forEach(field => {
            const errorElement = document.getElementById(field + '-error');
            if (errorElement) {
              errorElement.textContent = data.errors[field];
            }
          });
        }
      }
    } catch (error) {
      console.error('Fetch error:', error);
      alert('An error occurred while processing the form: ' + error.message);
    }
  });
});
</script>
