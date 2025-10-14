<?php
class Forum_ForumController extends Controller
{
    public function showAllQuestions()
    {
        $this->viewApp('User/edu-forum/all-questions-view', [], 'All Questions - ReidHub');
    }
}