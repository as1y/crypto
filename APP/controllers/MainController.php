<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class MainController extends AppController {

    public $BreadcrumbsControllerLabel = "Главная";
    public $BreadcrumbsControllerUrl = "/";


	public function indexAction(){

        $Panel = new Panel();

        $META = [
            'title' => 'Витрина промокодов и скидок '.APPNAME,
            'description' => 'Витрина промокодов и скидок'.APPNAME,
            'keywords' => 'Витрина промокодов и скидок'.APPNAME,
        ];



        $BREADCRUMBS['HOME'] = false;
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        \APP\core\base\View::setMeta($META);



        $couponsliseder =   $Panel->LoadCustomBanners([150, 95]);

        $discountproduct =   $Panel->LoadTopDiscount();


        //Музыка и звук
        $product2 =   $Panel->LoadProductCategory("2");


        $product1 =   $Panel->LoadProductCategory("1");

        $product3 =   $Panel->LoadProductCategory("5");


//
//
//        $couponsliseder2 =   $Panel->LoadCustomBanners([3159, 344]);
//
//        // GetShops
//        $widget8 =   $Panel->getShops(['limit' => 8]);
//
//
//
//        $widgetcoupons =   $Panel->getContentCoupons(['limit' => 10, 'sort' => 'time']);
//        $widgetcoupons2 = $Panel->getContentCoupons(['limit' => 8, 'sort' => 'used']);




        $this->set(compact( 'discountproduct', 'couponsliseder' ,'product2', 'product1', 'product3'));



	}











}
?>