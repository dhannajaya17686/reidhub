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
    public function showSellerPortalAnalytics()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-analytics-view', ['user' => $user], 'Seller Portal Analytics - ReidHub Marketplace');
    }
    public function showSellerPortalAddItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-add-items-view', ['user' => $user], 'Seller Portal Add Items - ReidHub Marketplace');
    }
    public function showSellerPortalActiveItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-active-items-view', ['user' => $user], 'Seller Portal Active Items - ReidHub Marketplace');
    }
    public function showSellerPortalArchivedItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-archived-items-view', ['user' => $user], 'Seller Portal Archived Items - ReidHub Marketplace');
    }
    public function showSellerPortalEditItems()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-edit-items-view', ['user' => $user], 'Seller Portal Edit Items - ReidHub Marketplace');
    }
    public function showSellerPortalOrders()
    {
        $user = Auth_LoginController::getSessionUser(true);
        $this->viewApp('/User/marketplace/seller-portal-orders-view', ['user' => $user], 'Seller Portal Orders - ReidHub Marketplace');
    }
}