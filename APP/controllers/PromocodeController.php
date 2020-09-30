<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class PromocodeController extends AppController {



	public function indexAction(){

        $Panel = new Panel();
        $CouponsPerPage = 20;

        $META = [
            'title' => 'Витрина промокодов и скидок '.APPNAME,
            'description' => 'Витрина промокодов и скидок'.APPNAME,
            'keywords' => 'Витрина промокодов и скидок'.APPNAME,
        ];

        $BREADCRUMBS = [];

        // Забор SEO информации
        $scripturl = "https://".CONFIG['DOMAIN'].$_SERVER['SCRIPT_URL'];
        $seopage =    $Panel->getSEOPAGES($scripturl);
        if (!empty($seopage)) {
            $META = json_decode($seopage['meta'], true);
            $BREADCRUMBS = json_decode($seopage['breadcrumbs'], true);
        }
        \APP\core\base\View::setMeta($META);
       \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);

//    show($BREADCRUMBS);

        // Перелистывание страниц
        if($this->isAjax()){

            $this->layaout = false;

            if (!empty($_POST['arrCategory'])) $_POST['arrCategory'] = $Panel->FindIdCategoryCoupon($_POST['arrCategory'])['id'];
            if (!empty($_POST['arrBrands'])) $_POST['arrBrands'] = $Panel->FindIdBrandCoupon($_POST['arrBrands'])['id'];


            // Загрузка купонов
            $coupons = $Panel->FilterCoupons(['arrCategory' => $_POST['arrCategory'], 'arrType' => $_POST['arrType'], 'arrBrands' => $_POST['arrBrands']]);


            // Пагинация
            if (empty($_POST['arrCount'])){

                $PAGESLIST['CouponsPerPage'] = $CouponsPerPage;
                $PAGESLIST['ViewPage'] = (!empty($_POST['page'])) ? $_POST['page']  : 1;

                $catalogCategories = $Panel->LoadCategoriesSimple($coupons, $_POST['arrCategory']);

                generateResult($coupons, $PAGESLIST, $catalogCategories);
                $_SESSION['POST'] = $_POST;
                return true;
            }
            // Пагинация
            return true;

        }
        // Перелистывание страниц


            if (empty($this->route['alias'])) $this->route['alias'] = "";
            if (empty($this->route['alias2'])) $this->route['alias2'] = "";

            // Базовые страницы
            $PAGESLIST['ViewPage'] = 1;
            $PAGESLIST['CouponsPerPage'] = $CouponsPerPage;
            // Базовые страницы

            // Забираем Определяем ID бренда или Категории
           $idbrand = $Panel->FindIdBrandCoupon($this->route['alias'])['id'];
           $idcat = $Panel->FindIdCategoryCoupon($this->route['alias2'])['id'];



            // Обработка GET параметров
            $arrtype = (!empty($_GET['type'])) ? $_GET['type'] : "";

            if (empty($idbrand) && $this->route['alias'] != "vse") redir("/promocode/vse/");

            // Если запущена модалка
            if (!empty($_COOKIE['runmodal']) && !empty($_SESSION['POST'])){
                if (!empty($_SESSION['POST']['page'])) $PAGESLIST['ViewPage'] = $_SESSION['POST']['page'];
                if (!empty($_SESSION['POST']['arrType'])) $arrtype = $_SESSION['POST']['arrType'];
                $idbrand = $_SESSION['POST']['arrBrands'];
            }
            // Если запущена модалка

            // Отбираем купоны
            $coupons = $Panel->FilterCoupons(['arrCategory' => $idcat, 'arrType' => $arrtype, 'arrBrands' => $idbrand]);

            // Если не выбран бренд
            if (empty($idbrand)){
                $catalogCategories = $Panel->LoadallCategories($idcat);
                $catalogCompany = $Panel->LoadCompanies($coupons, $idbrand);
            }

            // Если выбран
            if (!empty($idbrand)){
                $catalogCategories = $Panel->LoadCategoriesSimple($coupons, $idcat);
                $list = $Panel->FilterCoupons(['arrCategory' => $idcat, 'arrType' => "", 'arrBrands' => ""]);
                $catalogCompany = $Panel->LoadCompanies($list, $idbrand);

            }

            $catalogType = $Panel->LoadTypes($coupons, $arrtype);



            $this->set(compact( 'coupons', 'catalogCompany', 'catalogCategories', 'catalogType', 'PAGESLIST'));


            return true;




	}










}
?>