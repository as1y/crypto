<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;


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


        $companiestoday = $Panel->companiestoday();

        $conversiontodaty = $Panel->conversiontodaty();


        $this->set(compact('companiestoday', 'conversiontodaty'));



    }

    public function statAction(){

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Статистика по компании',
            'description' => 'Статистика по компании',
            'keywords' => 'Статистика по компании',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


        if (empty($_GET['company'])){
            exit("error");
        }


        $detalstat = $Panel->detalstat($_GET['company']);


        $this->set(compact('detalstat'));



    }

    public function updateAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Обновление купонов и магазинов',
            'description' => 'Обновление купонов и магазинов',
            'keywords' => 'Обновление купонов и магазинов',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);



        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);



        $updatestatus = $Panel->updatestatus();

        $this->set(compact('updatestatus'));






    }

    public function generatelinkAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Создание ссылки для',
            'description' => 'Создание ссылки',
            'keywords' => 'Создание ссылки',
        ];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/wizards/steps.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/selects/select2.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/styling/uniform.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/inputs/inputmask.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/validation/validate.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/cookie.js"];

        $ASSETS[] = ["js" => "/assets/js/form_wizard.js"];
        $ASSETS[] = ["js" => "/global_assets/js/demo_pages/form_select2.js"];



        if ($_POST){
            $link = $Panel->GenerateLink($_POST);
            if ($link === false){
                $_SESSION['errors'] = "Заполните все поля";
                redir();
            }

        }









        \APP\core\base\View::setAssets($ASSETS);


        \APP\core\base\View::setMeta($META);



        $ADDINFO = $Panel->LoadAddInfo();




        $this->set(compact('ADDINFO', 'link'));




    }

    public function generateadvertAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Генерация объявлений',
            'description' => 'Генерация объявлений',
            'keywords' => 'Генерация объявлений',
        ];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/wizards/steps.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/selects/select2.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/styling/uniform.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/inputs/inputmask.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/validation/validate.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/cookie.js"];

        $ASSETS[] = ["js" => "/assets/js/form_wizard.js"];
        $ASSETS[] = ["js" => "/global_assets/js/demo_pages/form_select2.js"];



        if (!empty($_GET) && $_GET['action'] == "generateall"){

            $_SESSION['ADV'] = $Panel->GenerateAdvertAll();


            // Если нет файла, то создаем
            if (!file_exists(WWW."/upload/exportcsv/")){
                mkdir(WWW."/upload/exportcsv/", 0777, true);
            }
            // Если нет файла, то создаем


            $Panel->exportcsvgoogle(['namecompany' => 'ПОИСК ТЕСТ']);

            exit();

        }



        if ($_POST && $_GET['action'] == "generate"){

            $ADV = $Panel->GenerateAdvert($_POST);
            $_SESSION['ADV'] = $ADV;

            if ($ADV === false){
                $_SESSION['errors'] = "Заполните все поля";
                redir();
            }

            if ($ADV == "nooffers"){
                $_SESSION['errors'] = "У компании нет офферов";
                redir();
            }

        }



        if ($_POST && $_GET['action'] == "export"){

            // Если нет файла, то создаем
            if (!file_exists(WWW."/upload/exportcsv/")){
                mkdir(WWW."/upload/exportcsv/", 0777, true);
            }
            // Если нет файла, то создаем

            $Panel->exportcsvgoogle($_POST);




            exit();

        }




        \APP\core\base\View::setAssets($ASSETS);


        \APP\core\base\View::setMeta($META);



        $ADDINFO = $Panel->LoadAddInfo(true);


        $this->set(compact('ADDINFO', 'ADV'));




    }

    public function generateadvertyaAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Генерация объявлений ЯНДЕКС',
            'description' => 'Генерация объявлений ЯНДЕКС',
            'keywords' => 'Генерация объявлений ЯНДЕКС',
        ];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/wizards/steps.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/selects/select2.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/styling/uniform.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/inputs/inputmask.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/validation/validate.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/cookie.js"];

        $ASSETS[] = ["js" => "/assets/js/form_wizard.js"];
        $ASSETS[] = ["js" => "/global_assets/js/demo_pages/form_select2.js"];



        if (!empty($_GET) && $_GET['action'] == "generateall"){

            $_SESSION['ADV'] = $Panel->GenerateAdvertAllya();


            // Если нет файла, то создаем
            if (!file_exists(WWW."/upload/exportcsv/")){
                mkdir(WWW."/upload/exportcsv/", 0777, true);
            }
            // Если нет файла, то создаем


            $Panel->exportcsvyandex(['namecompany' => 'ПОИСК ТЕСТ']);

            exit();

        }



        if ($_POST && $_GET['action'] == "generate"){

            $ADV = $Panel->GenerateAdvertYA($_POST);
            $_SESSION['ADV'] = $ADV;

            if ($ADV === false){
                $_SESSION['errors'] = "Заполните все поля";
                redir();
            }

            if ($ADV == "nooffers"){
                $_SESSION['errors'] = "У компании нет офферов";
                redir();
            }

        }



        if ($_POST && $_GET['action'] == "export"){

            // Если нет файла, то создаем
            if (!file_exists(WWW."/upload/exportcsv/")){
                mkdir(WWW."/upload/exportcsv/", 0777, true);
            }
            // Если нет файла, то создаем

            $Panel->exportcsvyandex($_POST);




            exit();

        }




        \APP\core\base\View::setAssets($ASSETS);


        \APP\core\base\View::setMeta($META);



        $ADDINFO = $Panel->LoadAddInfo(true);


        $this->set(compact('ADDINFO', 'ADV'));




    }


    public function addcouponAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Добавить купон',
            'description' => 'Добавить купон',
            'keywords' => 'Добавить купон',
        ];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/jquery_ui/interactions.min.js"];


        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/wizards/steps.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/selects/select2.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/styling/uniform.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/inputs/inputmask.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/forms/validation/validate.min.js"];
        $ASSETS[] = ["js" => "/global_assets/js/plugins/extensions/cookie.js"];

        $ASSETS[] = ["js" => "/assets/js/form_wizard.js"];
        $ASSETS[] = ["js" => "/global_assets/js/demo_pages/form_select2.js"];




        \APP\core\base\View::setAssets($ASSETS);


        \APP\core\base\View::setMeta($META);



        $ADDINFO = $Panel->LoadAddInfo();


        if ($_POST){




             $result = $Panel->AddCustomCoupon($_POST);

             if ($result == false){
                 $_SESSION['errors'] = "Заполните все поля!";
                 redir();
             }

            if ($result == true){

                $_SESSION['success'] = "Купон добавлен";
                redir();

            }



        }



        $this->set(compact('ADDINFO'));




    }

    public function listcouponsAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Персональные купоны',
            'description' => 'Персональные купоны',
            'keywords' => 'Персональные купоны',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


        $customcoupons = $Panel->GetCustomCoupons();



        if ($_GET && $_GET['action'] =="del"){

            $Panel->DelCustomCoupons($_GET['id']);

            $_SESSION['success'] = "Купон удален";
            redir();
        }


        $this->set(compact('customcoupons'));



    }

    public function withoutcouponsAction()
    {

        //Информация о компаниях клиента

        $Panel =  new Panel();


        $META = [
            'title' => 'Магазины без купонов',
            'description' => 'Магазины без купонов',
            'keywords' => 'Магазины без купонов',
        ];

        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);

        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);


        $companywithoutcoupons = $Panel->Getshopswithoutcoupons();

        $allshops = $Panel->allshops();

        $shopsinwork = $Panel->shopsinwork();


        $shopsnotwork = $Panel->shopsnotwork();


        $this->set(compact('companywithoutcoupons', 'allshops', 'shopsinwork', 'shopsnotwork'));



    }





}
?>