<div class="profile-section">
    <div class="profile-header">
        <div class="profile-picture-container">
            <div class="profile-picture <?php echo empty($user['profile_picture']) ? 'empty' : ''; ?>">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile picture">
                <?php else: ?>
                    <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile-header-content">
            <h1><?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?> <?php echo htmlspecialchars($user['last_name'] ?? ''); ?></h1>
            <p><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
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

    <!-- Profile Information -->
    <div class="profile-info-grid">
        <div class="info-item">
            <label class="info-label">Registration Number</label>
            <p class="info-value"><?php echo htmlspecialchars($user['reg_no'] ?? 'Not set'); ?></p>
        </div>

        <div class="info-item">
            <label class="info-label">Account Created</label>
            <p class="info-value">
                <?php 
                    if (!empty($user['created_at'])) {
                        $date = new DateTime($user['created_at']);
                        echo htmlspecialchars($date->format('F d, Y'));
                    } else {
                        echo 'Not available';
                    }
                ?>
            </p>
        </div>

        <div class="info-item">
            <label class="info-label">Last Updated</label>
            <p class="info-value">
                <?php 
                    if (!empty($user['updated_at'])) {
                        $date = new DateTime($user['updated_at']);
                        echo htmlspecialchars($date->format('F d, Y g:i A'));
                    } else {
                        echo 'Not available';
                    }
                ?>
            </p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="profile-actions">
        <a href="/dashboard/profile/edit" class="btn btn-primary">Edit Profile</a>
        <a href="/dashboard/profile/change-password" class="btn btn-secondary">Change Password</a>
    </div>
</div>

<link rel="stylesheet" href="/css/app/components/profile-section.css">
