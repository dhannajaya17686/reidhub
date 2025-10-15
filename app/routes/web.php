<?php
$routes['/login'] = [
    'GET' => 'Auth_LoginController@showLoginForm',
    'POST' => 'Auth_LoginController@login'
];
$routes['/signup'] = [
    'GET' => 'Auth_LoginController@showSignupForm',
    'POST' => 'Auth_LoginController@signup'
];
$routes['/recoverPassword'] = [
    'GET' => 'Auth_LoginController@showRecoverPasswordForm',
    'POST' => 'Auth_LoginController@revoverPassword'
];

$routes['/reset-sender'] = [
    'GET' => 'Auth_LoginController@showPasswordResetEmailSendForm',
    'POST' => 'Auth_LoginController@showRecoverPasswordFilledForm'
];

$routes['/verify-email'] = [
    'GET' => 'Auth_LoginController@showVerifyEmailForm',
    'POST' => 'Auth_LoginController@verifyEmail'
];


$routes['/logout'] = [
    'GET' => 'Auth_LoginController@logout'
];
$routes['/dashboard'] = [
    'GET' => 'User_DashboardController@index'
];

// Forum routes
$routes['/forum/'] = [
    'GET' => 'Forum_ForumUserController@showAllQuestions'
];
$routes['/forum/question'] = [
    'GET' => 'Forum_ForumUserController@showQuestion'
];
$routes['/forum/add'] = [
    'GET' => 'Forum_ForumUserController@addQuestion'
];
$routes['/forum/admin'] = [
    'GET' => 'Forum_ForumAdminController@showForumAdminDashboard'
];

$routes['/'] = [
    'GET' => 'Home_HomeController@index'
];

return $routes;