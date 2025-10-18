<?php
require_once __DIR__ . '/../../controllers/Auth/LoginController.php';

class Marketplace_MarketplaceUserController extends Controller
{
    public function showMerchStore()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/merch-store-view', ['user' => $user], 'Merch Store - ReidHub Marketplace');
    }
    public function showSecondHandStore()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/second-hand-store-view', ['user' => $user], 'Second Hand Store - ReidHub Marketplace');
    }
    public function showSpecificProduct()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/specific-product-view', ['user' => $user], 'Product Details - ReidHub Marketplace');
    }
    public function showMyCart()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/my-cart-view', ['user' => $user], 'My Cart - ReidHub Marketplace');
    }
    public function showMyOrders()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/my-orders-view', ['user' => $user], 'My Orders - ReidHub Marketplace');
    }

}