<?php

// Load required helper classes
require_once __DIR__ . '/../../helpers/EmailHelper.php';
require_once __DIR__ . '/../../helpers/RateLimiter.php';

class Auth_LoginController extends Controller
{
     /**
     * COnstructor functions for the views
     * Initializes the database connection by retrieving it from the Database singleton.
     */
    public function showLoginForm(){$this->view('Auth/log-in-view');}
    public function showSignupForm(){$this->view('Auth/sign-up-view');}
    
    
    
    private function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function login()
    {
        $identifier = trim($_POST['username'] ?? $_POST['email'] ?? '');
        $password   = $_POST['password'] ?? '';

        $errors = [];
        if ($identifier === '') { $errors['username'] = 'Email or Registration No is required.'; }
        if ($password === '')   { $errors['password']  = 'Password is required.'; }

        $isAjax = $this->isAjax();

        if (!empty($errors)) {
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                return;
            }
            $this->view('Auth/log-in-view', ['errors' => $errors, 'old' => ['username' => $identifier]]);
            return;
        }

        $userModel = new User();
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $userModel->findByEmail($identifier)
            : $userModel->findByRegNo(strtolower($identifier));

        if ($user && password_verify($password, $user['password'] ?? '')) {
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user'] = $user;
            Logger::info('Login success: user_id=' . $_SESSION['user_id']);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'redirect' => '/dashboard/user']);
                return;
            }
            header('Location: /dashboard/user', true, 303);
            exit;
        }

        // If not a user, check admin table
        require_once __DIR__ . '/../../models/Admin.php';
        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($identifier);
        if ($admin && password_verify($password, $admin['password'] ?? '')) {
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin'] = $admin;
            Logger::info('Admin login success: admin_id=' . $_SESSION['admin_id']);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'redirect' => '/dashboard/admin']);
                return;
            }
            header('Location: /dashboard/admin', true, 303);
            exit;
        }

        // Invalid credentials
        $fail = [
            'username' => 'Invalid credentials.',
            'password' => 'Invalid credentials.',
        ];
        if ($isAjax) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'errors' => $fail]);
            return;
        }
        $this->view('Auth/log-in-view', ['errors' => $fail, 'old' => ['username' => $identifier]]);
        return;
    }


    /**
     * Handle signup: validate, ensure uniqueness, create user, and redirect/JSON.
     *
     * Expected POST fields:
     *  - first_name, last_name, email, reg_no, password, confirm_password
     *
     * Responses:
     *  - JSON (AJAX): { ok: bool, errors?: object, message?: string, redirect?: string }
     *  - Non-AJAX: redirects to /dashboard on success or re-renders view with errors.
     *
     * @return void
     */
    public function signup()
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $regNo     = trim($_POST['reg_no'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if ($firstName === '') { $errors['first_name'] = 'First name is required.'; }
        if ($lastName === '')  { $errors['last_name']  = 'Last name is required.'; }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        // Reg no must be: XXXXisXXX or XXXXcsXXX (4 digits + is/cs + 3 digits), case-insensitive
        if ($regNo === '') {
            $errors['reg_no'] = 'Registration number is required.';
        } elseif (!preg_match('/^\d{4}(is|cs)\d{3}$/i', $regNo)) {
            $errors['reg_no'] = 'Use format 4 digits + is/cs + 3 digits (e.g., 2023is001).';
        } else {
            $regNo = strtolower($regNo);
        }

        if ($password === '') { $errors['password'] = 'Password is required.'; }
        if ($confirmPassword === '') { $errors['confirm_password'] = 'Confirm your password.'; }
        if ($password !== '' && $confirmPassword !== '' && $password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        $isAjax = $this->isAjax();

        // Uniqueness checks only if no basic errors
        if (empty($errors)) {
            $userModel = new User();
            if ($userModel->existsByEmail($email)) {
                $errors['email'] = 'Email is already in use.';
            }
            if ($userModel->existsByRegNo($regNo)) {
                $errors['reg_no'] = 'Registration number is already in use.';
            }
        }

        if (!empty($errors)) {
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                return; // stop here for AJAX
            }
            $this->view('Auth/sign-up-view', [
                'errors' => $errors,
                'old' => [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'reg_no'     => $regNo,
                ],
            ]);
            return;
        }

        $userId = (new User())->createWithProfile([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'reg_no'     => $regNo,
            'password'   => $password,
        ]);

        if (!$userId) {
            $fail = ['general' => 'Could not create the account. Try again later.'];
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $fail]);
                return;
            }
            $this->view('Auth/sign-up-view', ['errors' => $fail]);
            return;
        }
        
        Logger::info("New user signed up with email: $email and ID: $userId");
        
        // Generate and send OTP for email verification
        $userModel = new User();
        $otpCode = $userModel->generateAndSaveOTP($email);

        if ($otpCode) {
            $emailHelper = new EmailHelper();
            $emailHelper->sendOTPEmail($email, $otpCode, $firstName);
            Logger::info("OTP generated and sent to: $email");
        } else {
            Logger::error("Failed to generate OTP for: $email");
        }

        // Store unverified email in session
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['unverified_email'] = $email;

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => '/verify-email']);
            return;
        }

        header('Location: /verify-email', true, 303);
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /login', true, 302);
        exit;
    }

    /**
     * Checks if a user is logged in and returns the user array or null.
     * If not logged in, optionally redirects to login.
     */
    public static function getSessionUser(bool $redirectIfNotLoggedIn = true)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $sessionUser = $_SESSION['user'] ?? null;
        $userId = $_SESSION['user_id'] ?? ($sessionUser['id'] ?? null);

        if (!$userId) {
            if ($redirectIfNotLoggedIn) {
                header('Location: /login', true, 302);
                exit;
            }
            return null;
        }

        $user = $sessionUser;
        if (!$user || (int)($user['id'] ?? 0) !== (int)$userId) {
            $user = (new User())->findById((int)$userId);
            if (!$user) {
                $_SESSION = [];
                if ($redirectIfNotLoggedIn) {
                    header('Location: /login', true, 302);
                    exit;
                }
                return null;
            }
            $_SESSION['user'] = $user;
        }
        return $user;
    }

    /**
     * Validate admin session and return admin array.
     * Redirects to /login if not authenticated (configurable).
     * NOTE: Uses Admin model only. No direct DB access here.
     */
    public static function getSessionAdmin(bool $redirectIfNotAdmin = true): ?array
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $sessionAdmin = $_SESSION['admin'] ?? null;
        $adminId = $_SESSION['admin_id'] ?? ($sessionAdmin['id'] ?? null);

        if (!$adminId) {
            if ($redirectIfNotAdmin) {
                header('Location: /login', true, 302);
                exit;
            }
            return null;
        }

        // Re-fetch admin if session copy missing or mismatched
        $admin = $sessionAdmin;
        if (!$admin || (int)($admin['id'] ?? 0) !== (int)$adminId) {
            require_once __DIR__ . '/../../models/Admin.php';
            $adminModel = new Admin();
            $admin = $adminModel->findById((int)$adminId);

            if (!$admin) {
                unset($_SESSION['admin'], $_SESSION['admin_id']);
                if ($redirectIfNotAdmin) {
                    header('Location: /login', true, 302);
                    exit;
                }
                return null;
            }
            $_SESSION['admin'] = $admin;
        }

        return $admin;
    }

    /**
     * Quick boolean check for admin session.
     */
    public static function isAdminLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        return isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0;
    }

    // ==================== OTP VERIFICATION FLOW ====================

    /**
     * Show OTP verification form
     * Called after successful signup - user must verify email before accessing dashboard
     */
    public function showVerifyEmailForm()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        // Get unverified email from session
        $unverifiedEmail = $_SESSION['unverified_email'] ?? null;
        
        if (!$unverifiedEmail) {
            header('Location: /signup', true, 302);
            exit;
        }

        $this->view('Auth/verify-email-view', [
            'email' => $unverifiedEmail,
            'canResend' => true
        ]);
    }

    /**
     * Send OTP to student email
     * Called after successful signup
     *
     * @return void
     */
    public function sendOTP()
    {
        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');

        $errors = [];
        $isAjax = $this->isAjax();

        if (!$email) {
            $errors['email'] = 'Email is required.';
        }

        if (!empty($errors)) {
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                return;
            }
            $this->view('Auth/sign-up-view', ['errors' => $errors]);
            return;
        }

        // Generate OTP
        $userModel = new User();
        $otpCode = $userModel->generateAndSaveOTP($email);

        if (!$otpCode) {
            $fail = ['general' => 'Could not generate OTP. Please try again.'];
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $fail]);
                return;
            }
            $this->view('Auth/sign-up-view', ['errors' => $fail]);
            return;
        }

        // Send OTP via email
        $emailHelper = new EmailHelper();
        $emailSent = $emailHelper->sendOTPEmail($email, $otpCode, $firstName);

        if (!$emailSent) {
            Logger::warning("Failed to send OTP email to: $email");
            // Still return success - OTP was saved, email may recover later
        }

        Logger::info("OTP sent to email: $email");

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['unverified_email'] = $email;

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => '/verify-email']);
            return;
        }

        header('Location: /verify-email', true, 303);
        exit;
    }

    /**
     * Verify OTP code and complete signup
     * Once verified, redirect to dashboard with session
     *
     * @return void
     */
    public function verifyEmail()
    {
        $otpCode = trim($_POST['otp_code'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        $isAjax = $this->isAjax();

        if (!$otpCode) {
            $errors['otp_code'] = 'OTP code is required.';
        }
        if (!$email) {
            $errors['email'] = 'Email is required.';
        }

        if (!empty($errors)) {
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                return;
            }
            $this->view('Auth/verify-email-view', ['errors' => $errors, 'email' => $email]);
            return;
        }

        // Verify OTP
        $userModel = new User();
        $isValid = $userModel->verifyOTPCode($email, $otpCode);

        if (!$isValid) {
            $fail = ['otp_code' => 'Invalid or expired OTP. Please try again.'];
            
            // Check if max attempts exceeded
            $otp = $userModel->getLatestUnverifiedOTP($email);
            if ($otp && $otp['attempt_count'] >= 5) {
                $fail['otp_code'] = 'Too many failed attempts. Please request a new OTP.';
            }

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $fail]);
                return;
            }
            $this->view('Auth/verify-email-view', ['errors' => $fail, 'email' => $email]);
            return;
        }

        // OTP verified - get or find user, start session, redirect
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $fail = ['general' => 'User account not found. Please sign up again.'];
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'errors' => $fail]);
                return;
            }
            $this->view('Auth/verify-email-view', ['errors' => $fail, 'email' => $email]);
            return;
        }

        // Start session for verified user
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = $user;
        unset($_SESSION['unverified_email']);

        Logger::info("Email verified and user logged in: user_id=" . $_SESSION['user_id']);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => '/dashboard/user']);
            return;
        }

        header('Location: /dashboard/user', true, 303);
        exit;
    }

    /**
     * Resend OTP to email (rate-limited)
     *
     * @return void
     */
    public function resendOTP()
    {
        $email = trim($_POST['email'] ?? '');
        $isAjax = $this->isAjax();

        if (!$email) {
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'Email is required.']);
                return;
            }
            return;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $resendWaitSeconds = $config['OTP_RESEND_WAIT_SECONDS'] ?? 30;

        // Check if user has OTP and if enough time passed since creation
        $userModel = new User();
        $otp = $userModel->getLatestUnverifiedOTP($email);

        if ($otp && strtotime($otp['created_at']) > (time() - $resendWaitSeconds)) {
            $waitTime = $resendWaitSeconds - (time() - strtotime($otp['created_at']));
            if ($isAjax) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => "Please wait {$waitTime} seconds before requesting a new OTP."]);
                return;
            }
            return;
        }

        // Generate new OTP
        $otpCode = $userModel->generateAndSaveOTP($email);

        if (!$otpCode) {
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'Could not generate OTP. Please try again.']);
                return;
            }
            return;
        }

        // Send OTP
        $emailHelper = new EmailHelper();
        $emailHelper->sendOTPEmail($email, $otpCode);

        Logger::info("OTP resent to email: $email");

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'message' => 'OTP resent successfully!']);
            return;
        }
    }
}