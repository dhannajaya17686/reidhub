<?php

//-------------------------------------------AUTH ROUTER START-------------------------------------------
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
$routes['/dashboard/user'] = [
    'GET' => 'Dashboard_UserDashboardController@showUserDashboard'
];
$routes['/dashboard/admin'] = [
    'GET' => 'Dashboard_AdminDashboardController@showAdminDashboard'
];

// Debug route (temporary)
$routes['/debug-session'] = [
    'GET' => 'Auth_LoginController@showDebugSession'
];
//-----------------------------------AUTH ROUTER END--------------------------------------------//

//-----------------------------------MARKETPLACE ROUTES-----------------------------------//

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
$routes['/dashboard/marketplace/transactions'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMyTransactions'
];

// New data endpoints for the transactions page
$routes['/dashboard/marketplace/transactions/data'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getTransactionsData'
];
$routes['/dashboard/marketplace/transactions/view'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getTransactionById'
];
$routes['/dashboard/marketplace/transactions/view'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showTransaction'
];
/* Download invoice */
$routes['/dashboard/marketplace/transactions/invoice'] = [
    'GET' => 'Marketplace_MarketplaceUserController@downloadInvoice'
];
// Marketplace Admin routes
$routes['/dashboard/marketplace/admin/analytics'] = [
    'GET' => 'Marketplace_MarketplaceAdminController@showAdminMarketplaceAnalytics'
];
$routes['/dashboard/marketplace/admin/reported'] = [
    'GET' => 'Marketplace_MarketplaceAdminController@showAdminMarketplaceReportedItems'
];
$routes['/dashboard/marketplace/admin/archived'] = [
    'GET' => 'Marketplace_MarketplaceAdminController@showAdminMarketplaceArchivedItems'
];
$routes['/dashboard/marketplace/checkout'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showCheckout'
];

$routes['/dashboard/marketplace/checkout/place-order'] = [
    'POST' => 'Marketplace_MarketplaceUserController@submitCheckout'
];
$routes['/dashboard/marketplace/cart/payment-method'] = [
    'POST' => 'Marketplace_MarketplaceUserController@updateCartPaymentMethod',
];
//---------------------------------------MARKETLPACE ROUTE END------------------------------------------//

//---------------------------------------CLUB AND SOCIETY ROUTER START---------------------------------//

// ============ MAIN COMMUNITY FEED ROUTE ============
$routes['/dashboard/community'] = [
    'GET' => 'Community_CommunityUserController@showCommunityDashboard'
];

// ============ COMMUNITY ADMIN POST MANAGEMENT ============
$routes['/dashboard/community/create-post'] = [
    'GET' => 'Community_CommunityUserController@showCreatePost',
    'POST' => 'Community_CommunityUserController@createPost'
];
$routes['/dashboard/community/my-posts'] = [
    'GET' => 'Community_CommunityUserController@showMyPosts'
];
$routes['/dashboard/community/delete-post'] = [
    'POST' => 'Community_CommunityUserController@deletePost'
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
    'GET' => 'Community_CommunityUserController@showCreateClub',
    'POST' => 'Community_CommunityUserController@createClub'
];
$routes['/dashboard/community/clubs/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditClub',
    'POST' => 'Community_CommunityUserController@editClub'
];
$routes['/dashboard/community/clubs/join'] = [
    'POST' => 'Community_CommunityUserController@joinClub'
];
$routes['/dashboard/community/clubs/leave'] = [
    'POST' => 'Community_CommunityUserController@leaveClub'
];
$routes['/dashboard/community/clubs/delete'] = [
    'POST' => 'Community_CommunityUserController@deleteClub'
];

// ============ CLUB ADMIN PORTAL ROUTES ============
$routes['/dashboard/club-admin/dashboard'] = [
    'GET' => 'Community_CommunityUserController@showClubAdminDashboard'
];
$routes['/dashboard/club-admin/events'] = [
    'GET' => 'Community_CommunityUserController@showClubAdminEvents'
];
$routes['/dashboard/club-admin/announcements'] = [
    'GET' => 'Community_CommunityUserController@showClubAdminAnnouncements'
];
$routes['/dashboard/club-admin/applications'] = [
    'GET' => 'Community_CommunityUserController@showClubAdminApplications'
];

// ============ COMMUNITY EVENTS ROUTES ============
$routes['/dashboard/community/events'] = [
    'GET' => 'Community_CommunityUserController@showAllEvents'
];
$routes['/dashboard/community/events/view'] = [
    'GET' => 'Community_CommunityUserController@showViewEvent'
];
$routes['/dashboard/community/events/create'] = [
    'GET' => 'Community_CommunityUserController@showCreateEvent',
    'POST' => 'Community_CommunityUserController@createEvent'
];
$routes['/dashboard/community/events/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditEvent'
];
$routes['/dashboard/community/events/update'] = [
    'POST' => 'Community_CommunityUserController@updateEvent'
];
$routes['/dashboard/community/events/delete'] = [
    'POST' => 'Community_CommunityUserController@deleteEvent'
];
$routes['/dashboard/community/events/register'] = [
    'POST' => 'Community_CommunityUserController@registerForEvent'
];
$routes['/dashboard/community/events/unregister'] = [
    'POST' => 'Community_CommunityUserController@unregisterFromEvent'
];
//---------------------------------------CLUB AND SOCIETY ROUTES END-----------------------------------//

//---------------------------------------FORUM ROUTES START------------------------------------------//
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
$routes['/dashboard/forum/admin'] = [
    'GET' => 'Forum_ForumAdminController@showForumAdminDashboard'
];

//--------------------------------------FORUM ROUTES END---------------------------------------------//

//-------------------------------------LOST AND FOUND ROUTES START--------------------------------------//
$routes['/dashboard/lost-and-found/report-lost-item'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showReportLostItem'
];
$routes['/dashboard/lost-and-found/report-found-item'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showReportFoundItem'
];
$routes['/dashboard/lost-and-found/items'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showLostAndFoundItems'
];
$routes['/dashboard/community/admin'] = [
    'GET' => 'Community_CommunityAdminController@showCommunityAdminDashboard'
];
$routes['/dashboard/lost-and-found/admin'] = [
    'GET' => 'Forum_ForumAdminController@manageLostAndFound'
];

//---------------------------------------HOME ROUTES START------------------------------------------//
$routes['/'] = [
    'GET' => 'Home_HomeController@index'
];
//---------------------------------------HOME ROUTES END -------------------------------------------//

// Add this to your existing routes in web.php

return $routes;