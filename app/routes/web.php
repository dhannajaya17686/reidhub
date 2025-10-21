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

// Cart APIs
$routes['/dashboard/marketplace/cart/get'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getCartItemsApi'
];
$routes['/dashboard/marketplace/cart/remove'] = [
    'POST' => 'Marketplace_MarketplaceUserController@removeFromCart'
];
$routes['/dashboard/marketplace/cart/update'] = [
    'POST' => 'Marketplace_MarketplaceUserController@updateCartQuantity'
];
$routes['/dashboard/marketplace/orders'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMyOrders'
];
$routes['/dashboard/marketplace/orders/get'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getOrdersApi'
];
$routes['/dashboard/marketplace/seller/analytics'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalAnalytics'
];
$routes['/dashboard/marketplace/seller/analytics/data'] = [
    'GET' => 'Marketplace_MarketplaceUserController@sellerAnalyticsData',
];

$routes['/dashboard/marketplace/seller/add'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalAddItems',
    'POST' => 'Marketplace_MarketplaceUserController@addItem'
];
$routes['/dashboard/marketplace/seller/active'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalActiveItems'
];
//Make an Item archive
$routes['/dashboard/marketplace/seller/active/archive'] = [
    'POST' => 'Marketplace_MarketplaceUserController@archiveItem'
];
// Show archived items page
$routes['/dashboard/marketplace/seller/archived'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalArchivedItems'
];
//Make an Item Unarchived
$routes['/dashboard/marketplace/seller/archived/update'] = [
    'POST' => 'Marketplace_MarketplaceUserController@updateUnarchiveItem',
];


$routes['/dashboard/marketplace/seller/edit'] = [
    'GET'  => 'Marketplace_MarketplaceUserController@showSellerPortalEditItems',
    'POST' => 'Marketplace_MarketplaceUserController@updateItem',
];
$routes['/dashboard/marketplace/seller/orders'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showSellerPortalOrders'
];
$routes['/dashboard/marketplace/seller/orders/get'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getSellerOrdersApi'
];
$routes['/dashboard/marketplace/seller/orders/mark-delivered'] = [
    'POST' => 'Marketplace_MarketplaceUserController@markSellerOrderDelivered'
];
$routes['/dashboard/marketplace/seller/orders/cancel'] = [
    'POST' => 'Marketplace_MarketplaceUserController@cancelSellerOrder'
];
$routes['/dashboard/marketplace/seller/active/get'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getActiveItems'
];
$routes['/dashboard/marketplace/cart/add'] = [
    'POST' => 'Marketplace_MarketplaceUserController@addToCart'
];


// Edu forum user routes 
$routes['/dashboard/forum/add'] = [
    'GET' => 'Forum_ForumUserController@addQuestion'
];
$routes['/dashboard/forum/question'] = [
    'GET' => 'Forum_ForumUserController@showQuestion'
];
$routes['/dashboard/forum/all'] = [
    'GET' => 'Forum_ForumUserController@showAllQuestions'
];

$routes['/'] = [
    'GET' => 'Home_HomeController@index'
];

// Add this to your existing routes in web.php
$routes['/dashboard/marketplace/checkout'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showCheckout'
];

$routes['/dashboard/marketplace/checkout/place-order'] = [
    'POST' => 'Marketplace_MarketplaceUserController@submitCheckout'
];
$routes['/dashboard/marketplace/cart/payment-method'] = [
    'POST' => 'Marketplace_MarketplaceUserController@updateCartPaymentMethod',
];

return $routes;