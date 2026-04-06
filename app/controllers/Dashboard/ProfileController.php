<?php
class Dashboard_ProfileController extends Controller
{
    /**
     * Display user's profile information (read-only).
     */
    public function showProfile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) {
            header('Location: /login', true, 302);
            exit;
        }

        // Fetch user data
        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user) {
            $_SESSION = [];
            header('Location: /login', true, 302);
            exit;
        }

        // Update session cache
        $_SESSION['user'] = $user;

        // Prepare view data
        $data = [
            'user' => $user,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        // Clear session messages
        unset($_SESSION['success'], $_SESSION['error']);

        $this->viewApp('/User/profile/profile-view', $data, 'My Profile - ReidHub');
    }

    /**
     * Display edit profile form.
     */
    public function showEditProfile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) {
            header('Location: /login', true, 302);
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user) {
            $_SESSION = [];
            header('Location: /login', true, 302);
            exit;
        }

        $_SESSION['user'] = $user;

        $data = [
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];

        unset($_SESSION['success'], $_SESSION['error']);

        $this->viewApp('/User/profile/edit-profile-view', $data, 'Edit Profile - ReidHub');
    }

    /**
     * Handle profile update (POST).
     */
    public function updateProfile()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/profile/edit', true, 302);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) {
            $_SESSION['error'] = 'Session expired. Please log in again.';
            header('Location: /login', true, 302);
            exit;
        }

        // Validate and sanitize input
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validation
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $_SESSION['error'] = 'First name, last name, and email are required.';
            header('Location: /dashboard/profile/edit', true, 302);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email address.';
            header('Location: /dashboard/profile/edit', true, 302);
            exit;
        }

        // Check email uniqueness (if email is changing)
        $currentUser = (new User())->findById((int)$userId);
        if ($currentUser && $currentUser['email'] !== $email) {
            if ((new User())->existsByEmail($email)) {
                $_SESSION['error'] = 'This email is already in use.';
                header('Location: /dashboard/profile/edit', true, 302);
                exit;
            }
        }

        // Handle profile picture upload
        $updateData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email
        ];

        // Handle profile picture if uploaded
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            
            // Validate file
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                $_SESSION['error'] = 'Profile picture must be smaller than 5MB.';
                header('Location: /dashboard/profile/edit', true, 302);
                exit;
            }

            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedMimes)) {
                $_SESSION['error'] = 'Only JPEG, PNG, GIF, and WebP images are allowed.';
                header('Location: /dashboard/profile/edit', true, 302);
                exit;
            }

            // Generate unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/storage/profiles/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Delete old profile picture if exists
                if (!empty($currentUser['profile_picture'])) {
                    $oldPath = __DIR__ . '/../../public' . $currentUser['profile_picture'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $updateData['profile_picture'] = '/storage/profiles/' . $filename;
            } else {
                $_SESSION['error'] = 'Failed to upload profile picture. Please try again.';
                header('Location: /dashboard/profile/edit', true, 302);
                exit;
            }
        }

        // Update profile
        $userModel = new User();
        if ($userModel->updateProfile((int)$userId, $updateData)) {
            // Fetch updated user and cache in session
            $updatedUser = $userModel->findById((int)$userId);
            $_SESSION['user'] = $updatedUser;
            $_SESSION['success'] = 'Profile updated successfully!';
            header('Location: /dashboard/profile', true, 302);
            exit;
        } else {
            $_SESSION['error'] = 'Failed to update profile. Please try again.';
            header('Location: /dashboard/profile/edit', true, 302);
            exit;
        }
    }

    /**
     * Display change password form.
     */
    public function showChangePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) {
            header('Location: /login', true, 302);
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user) {
            $_SESSION = [];
            header('Location: /login', true, 302);
            exit;
        }

        $_SESSION['user'] = $user;

        $data = [
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];

        unset($_SESSION['success'], $_SESSION['error']);

        $this->viewApp('/User/profile/change-password-view', $data, 'Change Password - ReidHub');
    }

    /**
     * Handle password update (POST).
     */
    public function updatePassword()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        if (!$userId) {
            $_SESSION['error'] = 'Session expired. Please log in again.';
            header('Location: /login', true, 302);
            exit;
        }

        // Get input
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'All fields are required.';
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match.';
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long.';
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }

        // Verify current password
        $userModel = new User();
        $user = $userModel->findById((int)$userId);

        if (!$user || !$userModel->verify($user['email'], $currentPassword)) {
            $_SESSION['error'] = 'Current password is incorrect.';
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }

        // Update password
        if ($userModel->updatePassword((int)$userId, $newPassword)) {
            $_SESSION['success'] = 'Password changed successfully!';
            header('Location: /dashboard/profile', true, 302);
            exit;
        } else {
            $_SESSION['error'] = 'Failed to update password. Please try again.';
            header('Location: /dashboard/profile/change-password', true, 302);
            exit;
        }
    }
}
