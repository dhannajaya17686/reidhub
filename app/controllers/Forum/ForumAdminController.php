<?php

class Forum_ForumAdminController extends Controller {
    public function showForumAdminDashboard() {
        $this->viewApp('Admin/edu-forum/manage-forum-view', [], 'Forum Admin Dashboard - ReidHub');
    }
    public function showCommunityAdminDashboard() {
        $this->viewApp('Admin/community-and-social/manage-community-view', [], 'Community Admin Dashboard - ReidHub');
    }
    public function manageLostAndFound() {
        $this->viewApp('Admin/lost-and-found/manage-lost-and-found-view', [], 'Manage Lost and Found - ReidHub');
    }
}
