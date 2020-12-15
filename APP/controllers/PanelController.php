<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use ccxt\ccxt;

class PanelController extends AppController {
	public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";

    public function indexAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();

        $META = [
            'title' => 'Панель Администратора',
            'description' => 'Панель Администратора',
            'keywords' => 'Панель Администратора',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


      //  var_dump (\ccxt\Exchange::$exchanges); // print a list of all available exchange classes

        $binance = new \ccxt\binance ();
        show($binance->load_markets ());



//        $this->set(compact('companiestoday', 'conversiontodaty'));



    }






}
?>