<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;


class BuyerController extends AppController {
	public $layaout = 'MAIN';
    public $BreadcrumbsControllerLabel = "Личный кабинет";
    public $BreadcrumbsControllerUrl = "/panel";

    public function indexAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();

        $META = [
            'title' => 'Личный кабинет',
            'description' => 'Личный кабинет',
            'keywords' => 'Личный кабинет',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


        $balancelog = $Panel->balancelog();

        if (empty($balancelog)) $balancelog = [];




        $this->set(compact('balancelog'));



    }


    public function purchasesAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Личный кабинет',
            'description' => 'Личный кабинет',
            'keywords' => 'Личный кабинет',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $moipokupki = $Panel->moipokupki();


        $this->set(compact('moipokupki', 'conversiontodaty'));



    }

    public function payoutAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Личный кабинет',
            'description' => 'Личный кабинет',
            'keywords' => 'Личный кабинет',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $requis = json_decode($Panel::$USER->requis, true);
        if (empty($requis)) $requis = [];

        if ($_POST){

            if (empty($_POST['summa']) ){
                $_SESSION['errors'] = "Укажите сумму вывода";
                redir();
            }

            if (empty($_POST['sposob'])){
                $_SESSION['errors'] = "Укажите способ вывода";
                redir();
            }

            if ($Panel::$USER->bal < 100){
                $_SESSION['errors'] = "Минимальный заказ выплаты 100 рублей";
                redir();
            }

            if ($Panel::$USER->bal < $_POST['summa']){
                $_SESSION['errors'] = "Недостаточно средств на балансе";
                redir();
            }

            $Panel->createviplata($_POST);

            redir("/buyer/");



        }




        $this->set(compact('requis'));



    }

    public function requisAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Личный кабинет',
            'description' => 'Личный кабинет',
            'keywords' => 'Личный кабинет',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


        $requis = json_decode($Panel::$USER->requis, true);
        if (empty($requis)) $requis = [];


        if ($_POST){

            foreach ($_POST as $key=>$value){
                if (!empty($value) && empty($requis[$key])){

                    $result = validationpay($key, $value);


                    if ($result == true){

                        $Panel->addrequis($_POST);
                        if (empty($_SESSION['success'])) $_SESSION['success'] = "";
                        $_SESSION['success'] .= "Метод оплаты $key успешно добавлен! <br>";


                    }
                    if ($result == false){
                        if (empty($_SESSION['errors'])) $_SESSION['errors'] = "";
                        $_SESSION['errors'] .= "Ошибка в заполнеии метода оплаты $key <br>";


                    }

                }


            }

            redir();


        }




        $this->set(compact('requis'));



    }


}
?>