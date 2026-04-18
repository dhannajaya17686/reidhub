<div class="profile-section">
    <div class="profile-header">
        <h1>Change Password</h1>
        <p>Update your account password for security</p>
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

    <!-- Password Requirements Info -->
    <div class="info-box">
        <p class="info-box-title">Password Requirements</p>
        <ul class="requirements-list">
            <li>At least 8 characters long</li>
            <li>Include uppercase and lowercase letters</li>
            <li>Include numbers and special characters for better security</li>
        </ul>
    </div>

    <!-- Change Password Form -->
    <form method="POST" action="/dashboard/profile/change-password" class="profile-form">
        <div class="form-group">
            <label for="current_password">
                Current Password
                <span class="required">*</span>
            </label>
            <input
                type="password"
                id="current_password"
                name="current_password"
                required
                placeholder="Enter your current password"
                minlength="8"
                autocomplete="current-password"
            >
            <small>We need your current password to verify your identity.</small>
        </div>

        <div class="form-group">
            <label for="new_password">
                New Password
                <span class="required">*</span>
            </label>
            <input
                type="password"
                id="new_password"
                name="new_password"
                required
                placeholder="Enter your new password"
                minlength="8"
                autocomplete="new-password"
            >
            <small>Use a strong password with a mix of letters, numbers, and symbols.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">
                Confirm New Password
                <span class="required">*</span>
            </label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
                placeholder="Re-enter your new password"
                minlength="8"
                autocomplete="new-password"
            >
            <small>Passwords must match.</small>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Password</button>
            <a href="/dashboard/profile" class="btn btn-outline">Cancel</a>
        </div>
    </form>

    <!-- Security Tips -->
    <div class="security-tips">
        <p class="tips-title">Security Tips:</p>
        <ul class="tips-list">
            <li>Never share your password with anyone</li>
            <li>Use unique passwords for different accounts</li>
            <li>Change your password regularly for better security</li>
            <li>If you suspect unauthorized access, change your password immediately</li>
        </ul>
    </div>
</div>

<link rel="stylesheet" href="/css/app/components/profile-section.css">
