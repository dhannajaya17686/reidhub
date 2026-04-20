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

//-----------------------------------PROFILE ROUTES-----------------------------------//
$routes['/dashboard/profile'] = [
    'GET' => 'Dashboard_ProfileController@showProfile'
];
$routes['/dashboard/profile/edit'] = [
    'GET' => 'Dashboard_ProfileController@showEditProfile',
    'POST' => 'Dashboard_ProfileController@updateProfile'
];
$routes['/dashboard/profile/change-password'] = [
    'GET' => 'Dashboard_ProfileController@showChangePassword',
    'POST' => 'Dashboard_ProfileController@updatePassword'
];
//-----------------------------------PROFILE ROUTES END-----------------------------------//

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
// Buyer Stats API
$routes['/dashboard/marketplace/stats'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getBuyerStats'
];
$routes['/dashboard/marketplace/orders'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showMyOrders'
];
$routes['/dashboard/marketplace/orders/get'] = [
    'GET' => 'Marketplace_MarketplaceUserController@getOrdersApi'
];

// Order Chat routes
$routes['/dashboard/marketplace/orders/{id}/chat'] = [
    'GET' => 'Marketplace_MarketplaceChatController@showOrderChat'
];
$routes['/dashboard/marketplace/orders/{id}/chat/send'] = [
    'POST' => 'Marketplace_MarketplaceChatController@sendMessage'
];
$routes['/dashboard/marketplace/orders/{id}/chat/get'] = [
    'POST' => 'Marketplace_MarketplaceChatController@getMessages'
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

$routes['/dashboard/marketplace/admin/analytics/data'] = [
    'GET' => 'Marketplace_MarketplaceAdminController@adminAnalyticsData'
];

$routes['/dashboard/marketplace/checkout'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showCheckout'
];

$routes['/dashboard/marketplace/checkout/place-order'] = [
    'POST' => 'Marketplace_MarketplaceUserController@submitCheckout'
];

$routes['/dashboard/marketplace/terms-and-conditions'] = [
    'GET' => 'Marketplace_MarketplaceUserController@showTermsAndConditions'
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
    'GET' => 'Community_CommunityUserController@showCreateBlog',
    'POST' => 'Community_CommunityUserController@createBlog'
];
$routes['/dashboard/community/blogs/edit'] = [
    'GET' => 'Community_CommunityUserController@showEditBlog',
    'POST' => 'Community_CommunityUserController@updateBlog'
];

// Blog API endpoints
$routes['/dashboard/community/blogs/api/all'] = [
    'GET' => 'Community_CommunityUserController@getBlogsApi'
];
$routes['/dashboard/community/blogs/api/my-blogs'] = [
    'GET' => 'Community_CommunityUserController@getMyBlogsApi'
];
$routes['/dashboard/community/blogs/api/search'] = [
    'GET' => 'Community_CommunityUserController@searchBlogsApi'
];
$routes['/dashboard/community/blogs/api/delete'] = [
    'POST' => 'Community_CommunityUserController@deleteBlogApi'
];

// ============ COMMUNITY REPORT API ROUTES ============
$routes['/api/community/blogs/report'] = [
    'POST' => 'Community_CommunityUserController@submitBlogReport'
];
$routes['/api/community/events/report'] = [
    'POST' => 'Community_CommunityUserController@submitEventReport'
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

// ============ ADMIN REQUEST ROUTES ============
$routes['/dashboard/community/request-admin'] = [
    'GET' => 'Community_CommunityUserController@showAdminRequestForm'
];
$routes['/dashboard/community/submit-admin-request'] = [
    'POST' => 'Community_CommunityUserController@submitAdminRequest'
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
// Quick ask API from dashboard
$routes['/dashboard/community/forum/quick-ask'] = [
    'POST' => 'Forum_ForumUserController@quickAsk'
];
// My Bookmarks
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
// Report Lost Item - Page & Submission
$routes['/dashboard/lost-and-found/report-lost-item'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showReportLostItem',
    'POST' => 'LostAndFound_LostAndFoundUserController@submitLostItemReport'
];
// Report Found Item - Page & Submission
$routes['/dashboard/lost-and-found/report-found-item'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showReportFoundItem',
    'POST' => 'LostAndFound_LostAndFoundUserController@submitFoundItemReport'
];
// View All Items Page
$routes['/dashboard/lost-and-found/items'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showLostAndFoundItems'
];
// My Submissions Page
$routes['/dashboard/lost-and-found/my-submissions'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@showMySubmissions'
];
// API: Get All Items (with filtering)
$routes['/dashboard/lost-and-found/items/get-all'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@getAllItems'
];
// API: Get User's Own Items
$routes['/dashboard/lost-and-found/items/get-my-items'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@getMyItems'
];
// API: Get Item Details
$routes['/dashboard/lost-and-found/items/details'] = [
    'GET' => 'LostAndFound_LostAndFoundUserController@getItemDetails'
];
// API: Update Lost Item Status
$routes['/dashboard/lost-and-found/items/update-status'] = [
    'POST' => 'LostAndFound_LostAndFoundUserController@updateLostItemStatus'
];
// Admin Routes
$routes['/dashboard/community/admin'] = [
    'GET' => 'Community_CommunityAdminController@showCommunityAdminDashboard'
];
$routes['/dashboard/community/admin/blogs/view'] = [
    'GET' => 'Community_CommunityAdminController@showViewBlog'
];
$routes['/dashboard/community/admin/clubs/view'] = [
    'GET' => 'Community_CommunityAdminController@showViewClub'
];
$routes['/dashboard/community/admin/events/view'] = [
    'GET' => 'Community_CommunityAdminController@showViewEvent'
];

// ============ COMMUNITY ADMIN API ROUTES ============
$routes['/api/admin/community/admins/list'] = [
    'GET' => 'Community_CommunityAdminController@getCommunityAdmins'
];
$routes['/api/admin/community/admins/add'] = [
    'POST' => 'Community_CommunityAdminController@addCommunityAdmin'
];
$routes['/api/admin/community/admins/remove'] = [
    'POST' => 'Community_CommunityAdminController@removeCommunityAdmin'
];
$routes['/api/admin/community/users/search'] = [
    'GET' => 'Community_CommunityAdminController@searchUsers'
];

$routes['/api/admin/community/blogs/reported'] = [
    'GET' => 'Community_CommunityAdminController@getReportedBlogs'
];
$routes['/api/admin/community/blogs/delete'] = [
    'POST' => 'Community_CommunityAdminController@deleteBlog'
];

$routes['/api/admin/community/clubs/list'] = [
    'GET' => 'Community_CommunityAdminController@getClubs'
];
$routes['/api/admin/community/clubs/delete'] = [
    'POST' => 'Community_CommunityAdminController@deleteClub'
];

$routes['/api/admin/community/events/list'] = [
    'GET' => 'Community_CommunityAdminController@getEvents'
];
$routes['/api/admin/community/events/delete'] = [
    'POST' => 'Community_CommunityAdminController@deleteEvent'
];
$routes['/dashboard/lost-and-found/admin'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@showAdminDashboard'
];
// Admin API Routes for Lost & Found
$routes['/dashboard/lost-and-found/admin/get-lost-items'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@getAllLostItems'
];
$routes['/dashboard/lost-and-found/admin/get-found-items'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@getAllFoundItems'
];
$routes['/dashboard/lost-and-found/admin/get-reports'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@getAllReports'
];
$routes['/dashboard/lost-and-found/admin/get-item-details'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@getItemDetails'
];
$routes['/dashboard/lost-and-found/admin/update-status'] = [
    'POST' => 'LostAndFound_LostAndFoundAdminController@updateItemStatus'
];
$routes['/dashboard/lost-and-found/admin/delete-item'] = [
    'POST' => 'LostAndFound_LostAndFoundAdminController@deleteItem'
];
$routes['/dashboard/lost-and-found/admin/create-report'] = [
    'POST' => 'LostAndFound_LostAndFoundAdminController@createReport'
];
$routes['/dashboard/lost-and-found/admin/debug-images'] = [
    'GET' => 'LostAndFound_LostAndFoundAdminController@debugImages'
];
//-------------------------------------LOST AND FOUND ROUTES END--------------------------------------//


//-------------------------------------HELP & FEEDBACK ROUTES START--------------------------------------//

// User Help Routes
$routes['/dashboard/help'] = [
    'GET' => 'Dashboard_HelpController@showHelpForm',
    'POST' => 'Dashboard_HelpController@submitQuestion'
];
$routes['/dashboard/help/my-questions'] = [
    'GET' => 'Dashboard_HelpController@showMyQuestions'
];
$routes['/dashboard/help/edit'] = [
    'GET' => 'Dashboard_HelpController@showEditForm'
];
$routes['/dashboard/help/save-edit'] = [
    'POST' => 'Dashboard_HelpController@saveEdit'
];
$routes['/dashboard/help/questions-api'] = [
    'GET' => 'Dashboard_HelpController@getQuestionsApi'
];

// FAQ Routes
$routes['/dashboard/help/faq'] = [
    'GET' => 'Dashboard_FAQController@showFAQ'
];
$routes['/dashboard/help/faq-search'] = [
    'GET' => 'Dashboard_FAQController@searchFAQApi'
];

// Admin Help Routes
$routes['/dashboard/admin/help'] = [
    'GET' => 'Dashboard_HelpAdminController@showAdminHelpDashboard'
];
$routes['/dashboard/admin/help/questions-api'] = [
    'GET' => 'Dashboard_HelpAdminController@getAdminQuestionsApi'
];
$routes['/dashboard/admin/help/question'] = [
    'GET' => 'Dashboard_HelpAdminController@showQuestionDetails'
];
$routes['/dashboard/admin/help/reply'] = [
    'POST' => 'Dashboard_HelpAdminController@submitReply'
];
$routes['/dashboard/admin/help/resolve'] = [
    'POST' => 'Dashboard_HelpAdminController@resolveQuestion'
];
$routes['/dashboard/admin/help/download-image'] = [
    'GET' => 'Dashboard_HelpAdminController@downloadImage'
];

// Admin FAQ Routes
$routes['/dashboard/admin/faq'] = [
    'GET' => 'Dashboard_FAQAdminController@showFAQDashboard'
];
$routes['/dashboard/admin/faq/add'] = [
    'GET' => 'Dashboard_FAQAdminController@showAddFAQForm',
    'POST' => 'Dashboard_FAQAdminController@addFAQ'
];
$routes['/dashboard/admin/faq/edit'] = [
    'GET' => 'Dashboard_FAQAdminController@showEditFAQForm',
    'POST' => 'Dashboard_FAQAdminController@updateFAQ'
];
$routes['/dashboard/admin/faq/delete'] = [
    'POST' => 'Dashboard_FAQAdminController@deleteFAQ'
];

//-------------------------------------HELP & FEEDBACK ROUTES END--------------------------------------//

//---------------------------------------HOME ROUTES START------------------------------------------//
$routes['/'] = [
    'GET' => 'Home_HomeController@index'
];
//---------------------------------------HOME ROUTES END -------------------------------------------//

return $routes;
