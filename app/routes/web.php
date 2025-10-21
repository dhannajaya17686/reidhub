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
    'POST' => 'Auth_LoginController@logout'
];
$routes['/dashboard'] = [
    'GET' => 'Dashboard_UserDashboardController@showUserDashboard'
];
$routes['/dashboard/admin'] = [
    'GET' => 'Dashboard_AdminDashboardController@showAdminDashboard'
];
//Marketplace User routes
$routes['/dashboard/marketplace/merch-store'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMerchStore'
];  
$routes['/dashboard/marketplace/show-product'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSpecificProduct'
];
$routes['/dashboard/marketplace/my-cart'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMyCart'
];
$routes['/dashboard/marketplace/orders'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMyOrders'
];
$routes['/dashboard/marketplace/seller/analytics'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalAnalytics'
];
$routes['/dashboard/marketplace/seller/add'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalAddItems'
];
$routes['/dashboard/marketplace/seller/active'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalActiveItems'
];
$routes['/dashboard/marketplace/seller/archived'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalArchivedItems'
];
$routes['/dashboard/marketplace/seller/edit'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalEditItems'
];
$routes['/dashboard/marketplace/seller/orders'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalOrders'
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

// ============ COMMUNITY BLOGS ROUTES (MATCHING MARKETPLACE PATTERN) ============
$routes['/dashboard/community/blogs'] = [
    'GET' => 'Community_CommunityUserController@showAllBlogs'
];
$routes['/dashboard/community/blogs/view'] = [
    'GET' => 'Community_CommunityUserController@showViewBlog'
];
$routes['/dashboard/community/blogs/create'] = [
    'GET' => 'Community_CommunityUserController@showCreateBlog'
];
$routes['/dashboard/community/blogs/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditBlog'
];

// ============ COMMUNITY CLUBS ROUTES ============
$routes['/dashboard/community/clubs'] = [
    'GET' => 'Community_CommunityUserController@showAllClubs'
];
$routes['/dashboard/community/clubs/view'] = [
    'GET' => 'Community_CommunityUserController@showViewClub'
];
$routes['/dashboard/community/clubs/create'] = [
    'GET' => 'Community_CommunityUserController@showCreateClub'
];
$routes['/dashboard/community/clubs/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditClub'
];

// ============ COMMUNITY EVENTS ROUTES ============
$routes['/dashboard/community/events'] = [
    'GET' => 'Community_CommunityUserController@showAllEvents'
];
$routes['/dashboard/community/events/view'] = [
    'GET' => 'Community_CommunityUserController@showViewEvent'
];
$routes['/dashboard/community/events/create'] = [
    'GET' => 'Community_CommunityUserController@showCreateEvent'
];
$routes['/dashboard/community/events/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditEvent'
];

return $routes;