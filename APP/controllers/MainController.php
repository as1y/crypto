<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;
use RedBeanPHP\R;



class MainController extends AppController {


	public function indexAction(){

        $Panel = new Panel();

        $allprofit = $this->AllProfit();

        show($allprofit);

	}


	public function AllProfit(){

        $TrekHistory = R::findAll("trekhistory");

        $allprofit = 0;
        foreach ($TrekHistory as $key=>$value){
            $allprofit = $allprofit + $value['delta'];
        }


        return $allprofit;

    }










}
?>