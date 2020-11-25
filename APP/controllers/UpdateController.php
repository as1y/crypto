<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class UpdateController extends AppController {


	public function indexAction(){

        $Panel = new Panel();
        $this->layaout = false;



      if (!empty($_GET))   $token = AuthAdmitad();



      // Взять все программы




        if (!empty($_GET) && ($_GET['action'] == "updatecompany")){

            // Если нет файла, то создаем
            if (!file_exists(WWW."/upload/logos/")){
                mkdir(WWW."/upload/logos/", 0777, true);
            }
            // Если нет файла, то создаем

            $campanings = $Panel->getPrograms($token);

            $Panel->addMagazin($campanings, $token);
            $Panel->updatecheck("company");


            echo "<h1>КОМПАНИИ ОБНОВЛЕНЫ!</h1>";

        }


        if (!empty($_GET) && ($_GET['action'] == "updateproduct")){


            $feed = $Panel->loadfeedCSV();
            $Panel->addfeedBD($feed);


//            $Panel->removeFinishCoupon();
//            $Panel->addCoupons($token);



            $Panel->updatecheck("product");
            echo "<h1>ТОВАРЫ ОБНОВЛЕНЫ!</h1>";



        }



        if (!empty($_GET) && ($_GET['action'] == "updatecoupons")){


            $Panel->removeFinishCoupon();
            $Panel->addCoupons($token);
            $Panel->updatecheck("coupons");
            echo "<h1>КУПОНЫ ОБНОВЛЕНЫ!</h1>";



        }


        if (!empty($_GET) && ($_GET['action'] == "updatebanners")){
            $Panel->WorkWithBanners($token);
            $Panel->updatecheck("banners");
            echo "<h1>БАННЕРА!</h1>";
        }




        if (!empty($_GET) && ($_GET['action'] == "seopages")){





            $PAGES = $Panel->GeneratePages();
            $Panel->AddPagesinBD($PAGES);

            $Panel->updatecheck("seopages");
            echo "<h1>SEO страницы обновлены</h1>";


        }





    }










}
?>