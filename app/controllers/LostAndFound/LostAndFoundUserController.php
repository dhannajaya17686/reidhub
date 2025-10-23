<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class LostAndFound_LostAndFoundUserController extends Controller
{
    public function showReportLostItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-lost-item-view', $data, 'Report Lost Item - ReidHub');
    }
    public function showReportFoundItem()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/report-found-item-view', $data, 'Report Found Item - ReidHub');
    }
    public function showLostAndFoundItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $data = ['user' => $user];
        $this->viewApp('/User/lost-and-found/lost-and-found-items-view', $data, 'Lost and Found Items - ReidHub');
    }
}