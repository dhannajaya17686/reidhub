<div class="profile-section">
    <div class="profile-header">
        <div class="profile-picture-container">
            <div class="profile-picture <?php echo empty($user['profile_picture']) ? 'empty' : ''; ?>" id="profilePicturePreview">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile picture">
                <?php else: ?>
                    <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-header-content">
            <h1>Edit Profile</h1>
            <p>Update your personal information</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" role="alert">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Edit Profile Form -->
    <form method="POST" action="/dashboard/profile/edit" class="profile-form" enctype="multipart/form-data">
        <!-- Profile Picture Upload -->
        <div class="form-group full-width">
            <label for="profile_picture">Profile Picture</label>
            <input
                type="file"
                id="profile_picture"
                name="profile_picture"
                accept="image/jpeg,image/png,image/gif,image/webp"
                onchange="previewProfilePicture(event)"
            >
            <small>Supported formats: JPEG, PNG, GIF, WebP. Max size: 5MB.</small>
        </div>

        <!-- Personal Information -->
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">
                    First Name
                    <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"
                    required
                    placeholder="Enter your first name"
                    minlength="1"
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label for="last_name">
                    Last Name
                    <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"
                    required
                    placeholder="Enter your last name"
                    minlength="1"
                    maxlength="100"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="email">
                Email Address
                <span class="required">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                required
                placeholder="Enter your email address"
                maxlength="255"
            >
            <small>We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
            <label>Registration Number</label>
            <p class="form-static"><?php echo htmlspecialchars($user['reg_no'] ?? 'Not set'); ?></p>
            <small>Registration number cannot be changed.</small>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/dashboard/profile" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function previewProfilePicture(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profilePicturePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.classList.remove('empty');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Profile picture preview">';
        };
        reader.readAsDataURL(file);
    }
}
</script>

<link rel="stylesheet" href="/css/app/components/profile-section.css">

