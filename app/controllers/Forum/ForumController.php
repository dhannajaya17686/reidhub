<?php
class Forum_ForumController extends Controller
{
    public function showAllQuestions()
    {
        $this->viewApp('User/edu-forum/all-questions-view', [], 'All Questions - ReidHub');
    }
    public function showQuestion()
    {
        $this->viewApp('User/edu-forum/one-question-view', [], 'One Question - ReidHub');
    }
    public function addQuestion()
    {
        $this->viewApp('User/edu-forum/add-question-view', [], 'Add Question - ReidHub');
    }
}