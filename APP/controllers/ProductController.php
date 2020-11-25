<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class ProductController extends AppController {



	public function indexAction(){

        $Panel = new Panel();


        if (empty($this->route['alias']) || empty($this->route['alias2'])) redir("/catalog/");
        $product = $Panel->LoadProduct($this->route);
        if ($product === false)  redir("/catalog/");



        $META = [
            'title' => 'Кэшбек на '.$product['name'],
            'description' => 'Товар '.$product['name'],
            'keywords' => 'Товар '.$product['name'],
        ];


        $BREADCRUMBS = [];


        \APP\core\base\View::setMeta($META);
       \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        // Загрузка купонов



        $this->set(compact( 'product'));


            return true;




	}










}
?>