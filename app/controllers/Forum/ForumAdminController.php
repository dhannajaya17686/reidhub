<?php

class Forum_ForumAdminController extends Controller {
    public function showForumAdminDashboard() {
        $this->viewApp('Admin/edu-forum/manage-forum-view', [], 'Forum Admin Dashboard - ReidHub');
    }
}
