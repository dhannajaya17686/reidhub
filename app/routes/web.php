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

// --- NEW ROUTE: My Bookmarks ---
$routes['/dashboard/forum/bookmarks'] = [
    'GET' => 'Forum_ForumUserController@showBookmarks'
];

$routes['/dashboard/forum/admin'] = [
    'GET' => 'Forum_ForumAdminController@showForumAdminDashboard'
];
$routes['/dashboard/forum/admin/question/moderate'] = [
    'POST' => 'Forum_ForumAdminController@moderateQuestion'
];
$routes['/dashboard/forum/admin/answer/moderate'] = [
    'POST' => 'Forum_ForumAdminController@moderateAnswer'
];
$routes['/dashboard/forum/admin/comment/moderate'] = [
    'POST' => 'Forum_ForumAdminController@moderateComment'
];
$routes['/dashboard/forum/admin/question/update'] = [
    'POST' => 'Forum_ForumAdminController@updateQuestionMetadata'
];
$routes['/dashboard/forum/admin/answer/update'] = [
    'POST' => 'Forum_ForumAdminController@updateAnswer'
];
$routes['/dashboard/forum/admin/comment/update'] = [
    'POST' => 'Forum_ForumAdminController@updateComment'
];
$routes['/dashboard/forum/admin/report/review'] = [
    'POST' => 'Forum_ForumAdminController@reviewReport'
];
$routes['/dashboard/forum/admin/user/suspend'] = [
    'POST' => 'Forum_ForumAdminController@suspendUser'
];
$routes['/dashboard/forum/admin/user/lift-suspension'] = [
    'POST' => 'Forum_ForumAdminController@liftSuspension'
];
$routes['/dashboard/forum/admin/user/message'] = [
    'POST' => 'Forum_ForumAdminController@sendUserMessage'
];

// NEW: Action Routes (Handle clicks)
$routes['/dashboard/forum/vote'] = [
    'POST' => 'Forum_ForumUserController@vote'
];
$routes['/dashboard/forum/report'] = [
    'POST' => 'Forum_ForumUserController@report'
];

// Route to handle answer submission
$routes['/dashboard/forum/answer/create'] = [
    'POST' => 'Forum_ForumUserController@createAnswer'
];

$routes['/dashboard/forum/create'] = [
    'POST' => 'Forum_ForumUserController@createQuestion'
];

$routes['/dashboard/forum/bookmark'] = [
    'POST' => 'Forum_ForumUserController@bookmark'
];


// Edit & Delete
$routes['/dashboard/forum/delete'] = [
    'POST' => 'Forum_ForumUserController@deleteContent'
];
$routes['/dashboard/forum/update'] = [
    'POST' => 'Forum_ForumUserController@updateContent'
];

// Comments
$routes['/dashboard/forum/comment/create'] = [
    'POST' => 'Forum_ForumUserController@createComment'
];

$routes['/dashboard/forum/comment/delete'] = [
    'POST' => 'Forum_ForumUserController@deleteComment'
];

// Add this inside the Forum section
$routes['/dashboard/forum/answer/accept'] = [
    'POST' => 'Forum_ForumUserController@acceptAnswer'
];

// Search Suggestions
$routes['/dashboard/forum/search-similar'] = [
    'GET' => 'Forum_ForumUserController@searchSimilar'
];

//--------------------------------------FORUM ROUTES END---------------------------------------------//

// ------------------ EDU VIDEO ARCHIVE ROUTES ------------------ //

// Public Archive (View All)
$routes['/dashboard/edu-archive'] = [
    'GET' => 'EduArchive_EduController@index'
];

// Upload Content
$routes['/dashboard/edu-archive/upload'] = [
    'GET' => 'EduArchive_EduController@showUploadForm',
    'POST' => 'EduArchive_EduController@handleUpload'
];

// My Submissions (Track Status)
$routes['/dashboard/edu-archive/my-submissions'] = [
    'GET' => 'EduArchive_EduController@showMySubmissions'
];

// Edit Submission (pending only)
$routes['/dashboard/edu-archive/edit'] = [
    'GET' => 'EduArchive_EduController@showEditForm'
];

// Update Submission (pending only)
$routes['/dashboard/edu-archive/update'] = [
    'POST' => 'EduArchive_EduController@updateSubmission'
];

// Delete Submission
$routes['/dashboard/edu-archive/delete'] = [
    'POST' => 'EduArchive_EduController@deleteSubmission'
];

// Request approved resource removal
$routes['/dashboard/edu-archive/request-removal'] = [
    'POST' => 'EduArchive_EduController@requestRemoval'
];

// Bookmark Resource
$routes['/dashboard/edu-archive/bookmark'] = [
    'POST' => 'EduArchive_EduController@bookmark'
];

// Edu Archive Admin
$routes['/dashboard/edu-archive/admin'] = [
    'GET' => 'EduArchive_EduAdminController@showManageArchive'
];

$routes['/dashboard/edu-archive/admin/moderate'] = [
    'POST' => 'EduArchive_EduAdminController@moderateResource'
];

$routes['/dashboard/edu-archive/admin/tags'] = [
    'POST' => 'EduArchive_EduAdminController@manageFilterTag'
];

// -------------------------------------------------------------- //

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
    'GET' => 'Forum_ForumAdminController@showCommunityAdminDashboard'
];
$routes['/dashboard/lost-and-found/admin'] = [
    'GET' => 'Forum_ForumAdminController@manageLostAndFound'
];



//---------------------------------------HOME ROUTES START------------------------------------------//
$routes['/'] = [
    'GET' => 'Home_HomeController@index'
];
//---------------------------------------HOME ROUTES END -------------------------------------------//

return $routes;
