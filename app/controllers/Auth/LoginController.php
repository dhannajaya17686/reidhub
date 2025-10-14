<?php

class Auth_LoginController extends Controller
{
     /**
     * COnstructor functions for the views
     * Initializes the database connection by retrieving it from the Database singleton.
     */
    public function showLoginForm(){$this->view('Auth/log-in-view');}
    public function showSignupForm(){$this->view('Auth/sign-up-view');}
    public function showRecoverPasswordForm(){$this->view('Auth/recover-password-view');}  
    public function showVerifyEmailForm(){$this->view('Auth/verify-email-view');}
    public function showRecoverPasswordFilledForm(){$this->view('Auth/recover-password-filled-view'); }
    public function showPasswordResetSucessForm(){$this->view('Auth/reset-password-success-view');}
    public function showPasswordResetEmailSendForm(){$this->view('Auth/reset-password-mail-view'); }
    
    
    public function login()
    {
        $userModel = new User();
        header('Location: /dashboard');
        /*if ($user && is_array($user)) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /dashboard');
            exit;
        } else {
            $error = "Invalid email or password";
            $this->view('Auth/log-in-view', ['error' => $error]);
        }
            */
    }

    public function revoverPassword()
    {
        $email = $_POST['email'] ?? '';
        $userModel = new User();
        header('Location: /reset-sender');
        exit;
    }

    public function verifyEmail()
    {
        $code = $_POST['code'] ?? '';
        $userModel = new User();
        header('Location: /dashboard');
        exit;
    }

    public function signup()
    {
        header('Location: /verify-email');
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}