<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class CatalogController extends AppController {



	public function indexAction(){

        $Panel = new Panel();
        $CouponsPerPage = 20;

        $META = [
            'title' => 'Витрина промокодов и скидок '.APPNAME,
            'description' => 'Витрина промокодов и скидок'.APPNAME,
            'keywords' => 'Витрина промокодов и скидок'.APPNAME,
        ];



        // ПАГИНАЦИЯ


        // ФУНКЦИЯ ФИЛЬТРАЦИИ ТОВАРОВ


        // УСТАНОВКА ПАРАМЕТРОВ ФИЛЬТРА
        $RENDERFILTER = $Panel->GenerateFilter($_GET);


        $tovari = $Panel->FilterProduct($_GET);




        $this->set(compact( 'tovari', 'RENDERFILTER'));


            return true;




	}










}
?>