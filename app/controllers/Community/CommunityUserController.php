<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Community_CommunityUserController extends Controller
{
    // ============ BLOGS ============
    public function showAllBlogs()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/all-blogs', $data, 'Blogs - ReidHub');
    }

    public function showViewBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/view-blog', $data, 'View Blog - ReidHub');
    }

    public function showCreateBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = [
            'user' => $user,
            'categories' => [
                'academics' => 'Academics',
                'campus-life' => 'Campus Life',
                'student-tips' => 'Student Tips',
                'events' => 'Events'
            ]
        ];
        $this->viewApp('/User/community/blogs/create-blog', $data, 'Create Blog - ReidHub');
    }

    public function showEditBlog()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/blogs/edit-blog', $data, 'Edit Blog - ReidHub');
    }

    // ============ CLUBS ============
    public function showAllClubs()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/clubs/all-clubs', $data, 'Clubs - ReidHub');
    }

    public function showViewClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $clubId = $_GET['id'] ?? null;
        $data = [
            'user' => $user,
            'club' => [
                'id' => $clubId,
                'name' => 'Technology & Innovation Club',
                'description' => 'Join us to explore the latest in technology.',
                'category' => 'technology',
                'image_path' => 'https://via.placeholder.com/900x400/4A90E2/ffffff?text=Tech+Club'
            ],
            'isOwner' => false
        ];
        $this->viewApp('/User/community/clubs/view-club', $data, 'View Club - ReidHub');
    }

    public function showCreateClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = [
            'user' => $user,
            'categories' => [
                'academic' => 'Academic',
                'cultural' => 'Cultural',
                'sports' => 'Sports',
                'technology' => 'Technology',
                'arts' => 'Arts',
                'social' => 'Social',
                'other' => 'Other'
            ]
        ];
        $this->viewApp('/User/community/clubs/create-club', $data, 'Create Club - ReidHub');
    }

    public function showEditClub()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/clubs/edit-club', $data, 'Edit Club - ReidHub');
    }

    // ============ EVENTS ============
    public function showAllEvents()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/all-events', $data, 'Events - ReidHub');
    }

    public function showViewEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/view-event', $data, 'View Event - ReidHub');
    }

    public function showCreateEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/create-event', $data, 'Create Event - ReidHub');
    }

    public function showEditEvent()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/community/events/edit-event', $data, 'Edit Event - ReidHub');
    }
}