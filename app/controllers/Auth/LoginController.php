<?php

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
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['user_id'] = $userId;
        Logger::info("New session started for user ID: $userId");
        $isAjax = $this->isAjax();
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'redirect' => '/dashboard']);
            return;
        }

        header('Location: /dashboard', true, 303);
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
}