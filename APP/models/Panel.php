<?php
namespace APP\models;
use APP\core\Mail;
use Psr\Log\NullLogger;
use RedBeanPHP\R;

class Panel extends \APP\core\base\Model {

    public $wID = CONFIG['ADMITAD']['WID'];



    public function getSEOPAGES($scripturl){

     return   R::findOne('seopages', 'WHERE `http` =?', [$scripturl]);

    }


    public function AddPagesinBD($DATA){

        foreach ($DATA as $val){

            $shop = R::findOne('seopages', 'WHERE `http` =?', [$val['http']]);

            if (empty($shop)){
                $this->addnewBD('seopages', $val);
                echo "Добавили страницу ".$val['http']." "."<br>";
                continue;
            }


            foreach ($val as $name=>$item){
                 $shop->$name = $item;
                    echo "Обновили страницу ".$val['http']." "."<br>";
                    R::store($shop);

                }



        }



        return true;
    }

    public function GeneratePages(){

        $DATA = [];

        $categorycoupons =  R::findall('categorycoupons');
        $companies =  R::findall('companies');

        // БРЕНД
        foreach ($companies as $key=>$val){

            $coupons = $val->ownCouponsList;
            if (empty($coupons)) continue;
            $couponsid = [];
            foreach ($val->ownCouponsList as $coupon){
                $couponsid[] = $coupon['id'];
            }


            $INFO['type'] = "company";
            $INFO['uri'] = $val['uri'];
            $INFO['http'] = "https://".CONFIG['DOMAIN']."/promocode/".$val['uri'];

            $INFO['META'] = [
                'title' => 'Промокоды "'.$val['name'].'" 📌, каталог промокодов и скидок '.APPNAME,
                'description' => 'Витрина промокодов и скидок '.APPNAME,
                'keywords' => $val['name'].' промокоды, '.$val['name'].' скидки, '.$val['name'].' акции' ,
                'H1' => trim($val['name']).' промокоды, скидки, акции, купоны',
            ];
            $INFO['META'] = json_encode($INFO['META'] , true);

            unset($BREADCRUMBS);
            $BREADCRUMBS['HOME'] = ['Label' => "Промокоды", 'Url' => "/promocode/vse/"];
            $BREADCRUMBS['DATA'][] = ['Label' => trim($val['name'])];
            $INFO['BREADCRUMBS'] = json_encode($BREADCRUMBS , true);


            $INFO['couponsid'] = json_encode($couponsid, true);
            $INFO['description'] = "";
            $INFO['selflink'] = "";

            $DATA[] = $INFO;
        }
        // БРЕНД

         //КАТЕГОРИИ
        foreach ($categorycoupons as $key=>$val){

            $coupons = $this->FilterCoupons(['arrCategory' => $val['id'], 'arrType' => ""]);
            if (empty($coupons)) continue;
            $couponsid = [];
            foreach ($coupons as $coupon){
                $couponsid[] = $coupon['id'];
            }


            $INFO['type'] = "category";
            $INFO['uri'] = $val['url'];
            $INFO['http'] = "https://".CONFIG['DOMAIN']."/promocode/vse/".$val['url'];


            $INFO['META'] = [
                'title' => 'Промокоды в разделе '.$val['name'].' 📌, каталог промокодов и скидок '.APPNAME,
                'description' => 'Витрина промокодов и скидок '.APPNAME,
                'keywords' => $val['name'].' промокоды, '.$val['name'].' скидки, '.$val['name'].' акции' ,
                'H1' => ' Промокоды, акции, скидки в разделе "'.$val['name'].'"',
            ];
            $INFO['META'] = json_encode($INFO['META'] , true);

            unset($BREADCRUMBS);
            $BREADCRUMBS['HOME'] = ['Label' => "Промокоды", 'Url' => "/promocode/vse/"];
            $BREADCRUMBS['DATA'][] = ['Label' => trim($val['name'])];
            $INFO['BREADCRUMBS'] = json_encode($BREADCRUMBS , true);


            $INFO['couponsid'] = json_encode($couponsid, true);
            $INFO['description'] = "";
            $INFO['selflink'] = "";

            $DATA[] = $INFO;



        }
         //КАТЕГОРИИ

        //БРЕНД + КАТЕГОРИЯ КУПОНА
        foreach ($companies as $key=>$val){

            // Получаем список купонов
            $couponslist = $val->ownCouponsList;
            // Получаем список купонов

            // Берем список категорий у всех купонов
            $massivcategory = [];
            foreach ($couponslist as $coupon){
                $massivcategory =  array_merge ($massivcategory, json_decode($coupon['category'], true));
            }
            $massivcategory = array_unique($massivcategory);
            // Берем список категорий у всех купонов


            // Делаем URL компания + категория
            foreach ($massivcategory as $catid){

                            $coupons = $this->FilterCoupons(['arrCategory' => $catid, 'arrType' => "", 'arrBrands' => $val['id']]);
                            if (empty($coupons)) continue;
                            $couponsid = [];
                            foreach ($coupons as $coupon){
                                $couponsid[] = $coupon['id'];
                            }


                            $INFO['type'] = "company-category";
                            $INFO['uri'] = $val['url'];
                            $INFO['http'] = "https://".CONFIG['DOMAIN']."/promocode/".$val['uri']."/".$categorycoupons[$catid]['url'];;

                            $INFO['META'] = [
                                'title' => 'Промокоды '.$val['name'].' в разделе "'.trim($categorycoupons[$catid]['name']).'". Витрина промокодов '.APPNAME,
                                'description' => 'Витрина промокодов и скидок '.APPNAME,
                                'keywords' => $val['name'].' промокоды, '.$val['name'].' скидки, '.$val['name'].' акции' ,
                                'H1' => 'Промокоды '.$val['name'].' в разделе "'.trim($categorycoupons[$catid]['name']).'"',
                            ];
                            $INFO['META'] = json_encode($INFO['META'] , true);


                            unset($BREADCRUMBS);
                           $BREADCRUMBS['HOME'] = ['Label' => "Промокоды", 'Url' => "/promocode/vse/"];
                           $BREADCRUMBS['DATA'][] = ['Label' => $val['name'], 'Url' => "/promocode/".$val['uri'].""];
                           $BREADCRUMBS['DATA'][] = ['Label' => trim($categorycoupons[$catid]['name'])];
                           $INFO['BREADCRUMBS'] = json_encode($BREADCRUMBS , true);



                            $INFO['couponsid'] = json_encode($couponsid, true);
                            $INFO['description'] = "";
                            $INFO['selflink'] = "";

                            $DATA[] = $INFO;

            }
            // Делаем URL компания + категория

        }
        //БРЕНД + КАТЕГОРИЯ КУПОНА



        return $DATA;

    }




    public function WorkWithBanners($token){

        $companies = R::findAll('companies', "WHERE `addbanner` = ? LIMIT 40 ", ["0"]);

        if (!empty($companies)){

            foreach ($companies as $key=>$company){
                $banners = $this->loadBanners($token, $company['idadmi']);
                $this->addBannersinBD($banners, $company);
            }

            echo "<h1><font color='red'>Загружены не все баннера. Включите скрипт еще раз</font></h1>";

        }








        return true;

    }

    public function SendCouponEmail($DATA){


            $DATA = [
                'email' => $DATA['email'],
                'idcoupon' => $DATA['idcoupon'],
            ];

            $this->addnewBD("sendcoupons", $DATA);



            return  R::Load('coupons', $DATA['idcoupon']);



    }


    public function SubscribeFooter($email){

        // Отсекаем дубли
        $dubl = R::findOne("subscribe", "WHERE email = ?" , [$email]);
        if (!empty($dubl)) return true;

        $DATA = [
            'email' => $email,
            'type' => "footer"
        ];

        $this->addnewBD("subscribe", $DATA);

        return true;

    }


    public function addBannersinBD($banners, $company){

        $RS = [];
        $BannersList = [];

        // Берем баннера которые уже есть в БД
        $BannersinBD = R::findAll("banners", "WHERE companies_id = ?" , [$company['id']]);
        foreach ($BannersinBD as $key=>$val){
            $RS[$val['idadmi']] = 1;
        }
        // Берем баннера которые уже есть в БД

        foreach ($banners as $key => $banner){

            if ($banner['type'] == "html5") continue;
            if ($banner['type'] == "flash") continue;


            // Берем ID баннеров которые в Адмитаде
            $BannersList[$banner['id']] = 1;
            // Берем ID баннеров которые в Адмитаде


            if (!empty($RS[$banner['id']])) {
                echo "Баннер уже добавлен".$banner['id']." уже добавлена. <br>";
                continue;
            }

 
            // Копируем баннер себе
//            $extension = getExtension($banner['banner_image_url']);
//            $picture = '/upload/banners/'.$banner['id'].'banner.'.$extension;
//            file_put_contents(WWW.$picture, file_get_contents($banner['banner_image_url']));
            // Копируем баннер себе

            $forma = getsizetypeimage($banner['size_width'], $banner['size_height']);


            $bannerbd = R::dispense("banners");
            $bannerbd->idadmi = $banner['id'];
            $bannerbd->companyadmi = $company['idadmi'];
            $bannerbd->type = $banner['type'];
            $bannerbd->pictureurl = $banner['banner_image_url'];
            $bannerbd->direct_link = $banner['direct_link'];
            $bannerbd->size_width = $banner['size_width'];
            $bannerbd->size_height = $banner['size_height'];
            $bannerbd->forma = $forma;
            $bannerbd->views = 0;

            $company->ownBannerList[] = $bannerbd;

            $company->addbanner = 1;

            echo "<b>Баннер ".$banner['name']." добавлен </b>  <br>";
            R::store($company);


        }


        // Если баннер есть в БД, но нет в Адмитаде. То удаляем файл из БД
        foreach ($RS as $key=>$val){
            if (empty($BannersList[$key])) {

                R::trash("banners", $val);
                echo "<font color='red'> Баннер ".$key." есть в БД, но нет в Адмитаде!!! </font> <br>  ";


            }
        }






    }


    public function loadBanners($token, $cid){

        $url = API."/banners/".$cid."/website/".$this->wID."/";
        $type = "GET";
        $limit = 200;

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token
        );

        $PARAMS = [
            'limit' => $limit,
            'offset' => 0
        ];


        $result = fCURL($url, [$type => $PARAMS], $headers);

        $nadozagruzok = ceil($result['_meta']['count']/$result['_meta']['limit'])-1;

        if ($nadozagruzok == 0)  return $result['results'];


        for ($i = 1; $i <= $nadozagruzok; $i++) {

            $offset = $i*$limit;
            //  echo "Загружаем $i ... $offset<br><hr>";

            $PARAMS = [
                'limit' => $limit,
                'offset' => $offset
            ];
            $add = fCURL($url, [$type => $PARAMS], $headers);

            if( isset( $result['error'] ) && $result['error'] == 'invalid_token' ){
                $token = $this->AuthAdmitad();
                return $this->loadBanners($token);
            }
            $result['results'] = array_merge($result['results'], $add['results']);

        }

        return $result['results'];





    }


    public static function loadOneCoupon($id){
        return R::Load('coupons', $id);
    }


    public static function DelCustomCoupons($id){
        return R::trash('coupons', $id);
    }



    public function LoadallCategories($idcat, $type = ""){

        if (!empty($idcat)) self::$CATEGORYcoupon[$idcat]['select'] = 1;

        return self::$CATEGORYcoupon;
    }



    public function getUrlSiteforSitemap(){


      $DATA =  R::findAll('seopages');

        return $DATA;
    }

    public function LoadCategoriesSimple($coupons, $idcat, $sizeoff = true){


        // Берем список категорий изходя из купонов
        foreach ($coupons as $key=>$coupon) {
            // СОВМЕСТИТЬ КАТЕГОРИИ

            $categories = json_decode(utf8_encode($coupon['category']), true);

//                    echo "ID купона ".$coupon['id']." Компания  ".$coupon['companies_id']." ||| ";

            if (empty($categories)) continue;
            foreach ($categories as $v) {
                $tempARR[$coupon['companies_id']][$v] = true;
            }
        }





        // Получаем массив для работы
        foreach ($tempARR as $k=>$v){
            foreach ($v as $b=>$c){
                $filtrArr[$b] = true;
            }
        }




        // Совмещаем если выбрано несколько брендов
        if ( $sizeoff == true){

                    $filtrArr = current($tempARR);


                    foreach ($tempARR as $k=>$value){

//                        echo "first---";
//                        show($filtrArr);
//                        echo "val---";
//                        show($value);

//                         Функция работы схождения массивов
                        $filtrArr =  array_intersect_key($filtrArr, $value);

//                        echo "itog---";
//                        show($filtrArr);

                    }
                }



        $ALLCATEGORIES = [];
        foreach ($filtrArr as $category=>$val){
            $ALLCATEGORIES[$category] = "ok";
            if (!empty($idcat) && $category == $idcat) $ALLCATEGORIES[$category] = "alias";

        }


        // МАССИВ КАТЕГОРИЙ
        foreach (self::$CATEGORYcoupon as $key=>$category){
            // Ставим Алисас
            if ( !empty($ALLCATEGORIES[$category['id']]) && $ALLCATEGORIES[$category['id']] === "alias" ) {
                self::$CATEGORYcoupon[$key]['select'] = 1;
            }
            // Убираем категории которых нет в массиве отобранном
            if (!array_key_exists($category['id'], $ALLCATEGORIES)) unset (self::$CATEGORYcoupon[$key]);
        }


        return self::$CATEGORYcoupon;






        return $ALLCATEGORIES;







    }


    public function AddCustomCoupon($DATA){

        foreach ($DATA as $val){
            if (empty($val)) return false;
        }

        $company = R::load('companies', $DATA['company']);

        $types = [
            0=> [
                'id' => 2,
                'name' => 'Эксклюзив',
            ]
        ];


        $types = json_encode($types, true);
        $categories = json_encode([$DATA['category']], true);


        $discount = ($DATA['discount'] == "NULL") ? $discount = null : $discount = $DATA['discount'];

        $species = ($DATA['promocode'] == "NULL") ? $species = "action" : $species = "promocode";
        $date_start = date("Y-m-d");

        $promocode = ($DATA['promocode'] == "NULL") ? $promocode = "НЕ НУЖЕН" : $promocode = $DATA['promocode'];



        if ($DATA['url'] != "NULL"){
            $token = AuthAdmitad();
            $gotolink = $this->getDeepLink($token, $company['idadmi'], $DATA['url']);
        }else{
            $gotolink = $company['ulp'];
        }

        $gotolink .= "?i=3";


        $coupon = R::dispense("coupons");
        $coupon->name = $DATA['name'];
        $coupon->short_name = $DATA['short_name'];
        $coupon->category = $categories;
        $coupon->used = 0;
        $coupon->species = $species;
        $coupon->datestart = $date_start;
        $coupon->dateend = NULL;
        $coupon->types = $types;
        $coupon->discount = $discount;
        $coupon->promocode = $promocode;
        $coupon->gotolink = $gotolink;
        $coupon->status = "active";
        $coupon->idamicompany = $company['idadmi'];
        $coupon->type = "custom";

        $company->ownCouponList[] = $coupon;


        R::store($company);

        $this->addnewscoupon("add", $DATA);



        return true;

    }


    public function LoadAddInfo($offers = false){

        $ADDINFO['source'] = [1 => "googlecpc"];
        $ADDINFO['companies'] =  $this->LoadAllCompanies();
        $ADDINFO['categorycoupons'] =  $this->LoadAllCompaniesCoupons();


       return $ADDINFO;

    }



    public function LoadAllCompaniesCoupons(){

        $ARR = R::findAll('categorycoupons');

        return $ARR;


    }

    public function LoadAllCompanies(){

        $ARR = R::findAll('companies');

//        foreach ($ARR as $k=>$v){
//            if ($v->countOwn("coupons") == 0) unset($ARR[$k]);
//        }

        return $ARR;

    }


    public function LoadTypes($coupons, $arrType = ""){

        $typeARR['action'] = 0;
        $typeARR['promocode']= 0;
        $typeARR['all'] = 0;

        foreach ($coupons as $key=>$coupon){
            if ($coupon['species'] == "action" ) $typeARR['action']++;
            if ($coupon['species'] == "promocode" ) $typeARR['promocode']++;
            $typeARR['all']++;
            $typeARR['select'] = $arrType;

        }

        return $typeARR;

    }




    public function LoadCompanies($coupons, $idbrand){

        // Показывать все бренды в категории
        // Выбрать все купоны где категория наша категория
        // Записать и посчитать

        $compARR = [];
        $idbrand = explode(",", $idbrand);

        foreach ($coupons as $key=>$coupon){

            if (array_key_exists($coupon['companies_id'], $compARR)) {
                $compARR[$coupon['companies_id']]['count']++;
            }

            if (!array_key_exists($coupon['companies_id'], $compARR)){


                $compARR[$coupon['companies_id']]['count'] = 1;
                $compARR[$coupon['companies_id']]['url'] = $coupon['companies']['uri'];
                $compARR[$coupon['companies_id']]['name'] = $coupon['companies']['name'];

                if (in_array($coupon['companies']['id'], $idbrand) ) {
                    $compARR[$coupon['companies_id']]['select'] = 1;
                    // Перекидываем в начало массива

                }



            }


        }




//        echo "ИСХОДНЫЙ МАССИВ!!";
//        show($compARR);


        $compARR = array_values($compARR);
        //Поднимаем выделенные бренды на верх
        foreach ($compARR as $key=>$val){

                if (!empty($val['select']) &&  $val['select'] == true) {

//                    echo "=== НАЧАЛО ИТЕРАЦИИ $key";
//                    show($compARR);

                    unset($compARR[$key]);

                    array_unshift($compARR, $val);


            }

        }



        return $compARR;

    }

    public function getContentCoupons($PARAMS){

        if ($PARAMS['sort'] == "time"){
            $result =  R::findAll('coupons', "WHERE `dateend` != '' ORDER BY `dateend` ASC  LIMIT ".$PARAMS['limit']);
            return $result;
        }

        if ($PARAMS['sort'] == "used"){
            $result =  R::findAll('coupons', "ORDER BY `".$PARAMS['sort']."` DESC  LIMIT ".$PARAMS['limit']);
            return $result;
        }





    }


    public function getRandomPassage(){

        $text = "Рандомный кусок текста для вставки в объявление";


        return $text;
    }

    public function GeneratetextAds($DATA){

//        show($DATA['company']);

        $coupons = $this->getAllCoupons($DATA['company']);

        $keywordmass = explode("\n", $DATA['keywords']);

        $randomtext = $this->getRandomPassage();

        for ($i = 0; $i < count($keywordmass)-1; $i++) {

            foreach ($coupons as $coupon){
                $dlinnastroki = strlen($coupon['name']);
                $discount = textdiscount($coupon['discount']);
                $OBJAVA['keyword'] = $keywordmass[$i];
                $OBJAVA['zagolovok'] = $keywordmass[$i]." ".trim($discount);

                // Создание текста объявления
                $OBJAVA['text'] = $coupon['name'];
                // Создание текста объявления

                $ADS[] = $OBJAVA;
            }

        }






         return $ADS;

    }

    public function exportcsvgoogleALL($DATA){



    }


    public function exportcsvyandex($DATA){
        ?>


        <table border="1">
            <tr>
                <td>Название кампании</td>
                <td>Тип объявления</td>
                <td>Название группы</td>
                <td>Фраза (с минус-словами)</td>
                <td>Заголовок 1</td>
                <td>Заголовок 2</td>
                <td>Текст</td>
                <td>Ссылка</td>
                <td>Отображаемая ссылка</td>
                <td>Регион</td>
                <td>Уточнения</td>
                <td>Ставка</td>
            </tr>
<?php

        // Если только 1 элмент
        if (empty($_SESSION['ADV'][1])){
            generatecsvYandex($_SESSION['ADV'],$DATA);
            echo "</table>";
            return true;
        }


        // Если генерируем сразу массив

        $i=0;
        foreach ($_SESSION['ADV'] as $ADV){
            // СЧЕТЧИК МАГАЗИНОВ
            $i++;
            if ($i < 60) continue; //Ограничение на кол-во магазинов
            generatecsvYandex($ADV,$DATA);
        }
        echo "</table>";
        return true;


    }


    public function exportcsvgoogle($DATA){

        // Первая строка
        echo "Campaign,AdGroup,KeyWord,Criterion Type,Final URL,Headline 1,Headline 2,Headline 3,Description Line 1,Description Line 2,Path 1,Path 2,Max CPC,Max CPM,Target CPM,Display Network Custom Bid Type,Targeting expansion,Ad Group Type"."<br>";
        // Вторая строка

        // Если только 1 элмент
        if (empty($_SESSION['ADV'][1])){
            generatecsvAdwords($_SESSION['ADV'],$DATA);
            return true;
        }


        // Если генерируем сразу массив

        $i=0;
        foreach ($_SESSION['ADV'] as $ADV){


            // СЧЕТЧИК МАГАЗИНОВ
//            $i++;
//            if ($i < 60) continue; //Ограничение на кол-во магазинов


            generatecsvAdwords($ADV,$DATA);
        }

        return true;


    }

    public function GenerateAdvertAllya(){

        // Берем список компаний у которых уже были клики
        $listcompanies = R::find("usertoday", "GROUP BY `cmpid` ");
        if (empty($listcompanies)) return [];
        foreach ($listcompanies as $company){
            if ($company['utm_source'] == "yandex") $workcompany[] = $company['cmpid'];
        }
        // Берем список компаний у которых уже были клики



        $companies =  R::findAll("companies");

        foreach ($companies as $key=>$company){

            // Не загружаем компании по которым уже были клики по рекламе
            if (array_search($company['id'], $workcompany)) continue;


            $coupons = $company->ownCouponsList;
            if (empty($coupons)) continue;

            $ADVMASS[$key]['keywords'] = $this->GenerateKeyWords($company['id']);
            $ADVMASS[$key]['url'] = $this->GenerateLink(['company' => $company['id'], 'traffictype' => 'googlesearch']);
            $ADVMASS[$key]['rekl'] = $company['name'];

            $ADVMASS[$key] = $ADVMASS[$key] + generatestrYandex($coupons, $company);


        }

        return $ADVMASS;


    }


    public function GenerateAdvertAll(){


        // Берем список компаний у которых уже были клики
        $listcompanies = R::find("usertoday", "GROUP BY `cmpid` ");
        if (empty($listcompanies)) return [];
        foreach ($listcompanies as $company){
            if ($company['utm_source'] == "google") $workcompany[] = $company['cmpid'];
        }
        // Берем список компаний у которых уже были клики


        $companies =  R::findAll("companies");
        foreach ($companies as $key=>$company){

            // Не загружаем компании по которым уже были клики по рекламе
            if (array_search($company['id'], $workcompany)) continue;



            $coupons = $company->ownCouponsList;
            if (empty($coupons)) continue;

            $ADVMASS[$key]['keywords'] = $this->GenerateKeyWords($company['id']);
            $ADVMASS[$key]['url'] = $this->GenerateLink(['company' => $company['id'], 'traffictype' => 'googlesearch']);
            $ADVMASS[$key]['rekl'] = $company['name'];

            $ADVMASS[$key] = $ADVMASS[$key] + generatestrAdwords($coupons, $company);


        }

        return $ADVMASS;


    }


    public function GenerateAdvertYA($DATA){

        if (empty($DATA['company'])) return false;

        $companybd =  R::load("companies", $DATA['company']);
        $coupons = $companybd->ownCouponsList;
        if (empty($coupons)) return "nooffers";
        $keywords = $this->GenerateKeyWords($DATA['company']);

        $mass['company'] = $DATA['company'];
        $mass['traffictype'] = "yandexsearch";
        $ADVMASS['url'] = $this->GenerateLink($mass);


        $ADVMASS = generatestrYandex($coupons, $companybd);



        $ADVMASS['url'] = $this->GenerateLink(['company' => $companybd['id'], 'traffictype' => $mass['traffictype']]);
        $ADVMASS['rekl'] = $companybd['name'];
        $ADVMASS['keywords'] = $keywords;


        // $ADV['zagolovok'] = mb_convert_case($keyword, MB_CASE_TITLE, "UTF-8");


        return $ADVMASS;


    }




    public function GenerateAdvert($DATA){

        if (empty($DATA['company'])) return false;

        $companybd =  R::load("companies", $DATA['company']);
        $coupons = $companybd->ownCouponsList;
        if (empty($coupons)) return "nooffers";
        $keywords = $this->GenerateKeyWords($DATA['company']);

        $mass['company'] = $DATA['company'];
        $mass['traffictype'] = "googlesearch";
        $ADVMASS['url'] = $this->GenerateLink($mass);

        $ADVMASS = generatestrAdwords($coupons, $companybd);

        $ADVMASS['url'] = $this->GenerateLink(['company' => $companybd['id'], 'traffictype' => 'googlesearch']);
        $ADVMASS['rekl'] = $companybd['name'];
        $ADVMASS['keywords'] = $keywords;


        // $ADV['zagolovok'] = mb_convert_case($keyword, MB_CASE_TITLE, "UTF-8");
        return $ADVMASS;


    }


    public function GenerateLink($DATA){

        if (empty($DATA['traffictype'])) return false;
        if (empty($DATA['company'])) return false;


        $companybd =  R::load("companies", $DATA['company']);
        $link = "https://".CONFIG['DOMAIN']."/promocode/".$companybd['uri'];



        if ($DATA['traffictype'] == "googlesearch"){
            $link .= "?utm_source=google&utm_medium=cpc&utm_campaign={network}&utm_content={creative}&utm_term={keyword}";
        }

        if ($DATA['traffictype'] == "yandexsearch"){
            $link .= "?utm_source=yandex&utm_medium=cpc&utm_campaign={campaign_id}&utm_content={ad_id}&utm_term={keyword}";
        }

        $link .= "&cmpid=".$companybd['id'];

        return $link;


    }


    public function GenerateKeyWords($idcompany){


        $company = R::Load('companies', $idcompany);

        $company = mb_strtolower($company['name']);

        $company = str_replace(".ru", "", $company);

        $company = trim($company);

        $keywords[] = $company." промокод";
        $keywords[] = "промокод ".$company;
        $keywords[] = $company." скидки";
        $keywords[] = "скидки ".$company;
        $keywords[] = $company." коды";
        $keywords[] = $company." дисконт";
        $keywords[] = $company." распродажа";
        $keywords[] = $company." промокод на скидку";
        $keywords[] = "промокод на скидку ".$company;
        $keywords[] = "купон ".$company;
        $keywords[] = $company." купон ";
        $keywords[] = $company." каталог";
        $keywords[] = $company." отзывы";


        return $keywords;



    }



    public function getAllCoupons($idcompany, $FILTER = ""){


        $SORT = "";

        if ($FILTER == "ORDERBY") $SORT = "ORDER BY `views` DESC";


        return R::findAll('coupons', "WHERE companies_id = ? ".$SORT, [$idcompany]);


    }



    public function getDeepLink($token, $cid, $ulp){


        $url = API."/deeplink/".$this->wID."/advcampaign/".$cid."/";
        $type = "GET";


        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token
        );


        $PARAMS = [
            'ulp' => $ulp,
        ];

        return fCURL($url, [$type => $PARAMS], $headers)[0];


    }




    public function getPrograms($token){
        $url = API."/advcampaigns/website/".$this->wID."/";
        $type = "GET";
        $limit = 100;

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token
        );

        $PARAMS = [
            'limit' => $limit,
            'offset' => 0
        ];

        $result = fCURL($url, [$type => $PARAMS], $headers);

        if( isset( $result['error'] ) && $result['error'] == 'invalid_token' ){
            $token = $this->AuthAdmitad();
            return $this->getPrograms($token);
        }

        $nadozagruzok = ceil($result['_meta']['count']/$result['_meta']['limit'])-1;

        // echo "Надо добавить еще  ".$nadozagruzok." загрузки <br>";



        if ($nadozagruzok == 0)  return $result['results'];

        // Дозагружаем остальные значения.

        for ($i = 1; $i <= $nadozagruzok; $i++) {

            $offset = $i*$limit;
            //  echo "Загружаем $i ... $offset<br><hr>";

            $PARAMS = [
                'limit' => $limit,
                'offset' => $offset
            ];
            $add = fCURL($url, [$type => $PARAMS], $headers);

            if( isset( $result['error'] ) && $result['error'] == 'invalid_token' ){
                $token = $this->AuthAdmitad();
                return $this->getPrograms($token);
            }
            $result['results'] = array_merge($result['results'], $add['results']);

        }

        return $result['results'];





    }


    public function LoadCustomCupons($ARR) {
        return R::loadAll("coupons", $ARR);
    }


    public function LoadCustomBanners($ARR) {
        return R::loadAll("banners", $ARR);
    }



    public function FindIdCategoryCoupon($url) {

        return R::findOne('categorycoupons', 'WHERE url =?', [$url]);
    }

    public function LoadCategoryCoupon($url) {
        return R::findOne('categorycoupons', 'WHERE url =?', [$url]);
    }


    public function FindIdBrandCoupon($url) {


        $mbmass = explode(",", $url);

        if ( count($mbmass) > 1){

            $all = R::findAll('companies');
            foreach ($all as $key=>$value){

                if (in_array ($value['uri'], $mbmass)){
                    unset($all[$key]);
                    $result[] = $value['id'];
                }
            }
            $result = implode(",", $result);
            return $result;

        }

        return R::findOne('companies', 'WHERE `uri` =?', [$url]);

    }


    public function FilterCoupons($ARR) {

        $WHERE = [];



        // Запрос в таблицу coupons
        if (!empty($ARR['arrBrands'])){
            $WHERE[] =  "`companies_id` IN (".$ARR['arrBrands'].")";
        }


        if ($ARR['arrType'] == "promocode" || $ARR['arrType'] == "action" ){
            $WHERE[] =  '`species` = "'.$ARR['arrType'].'" ';
        }


        if (!empty($ARR['arrCategory'])){
            $WHERE[] =  'JSON_CONTAINS(`category`, JSON_ARRAY("'.$ARR['arrCategory'].'") )';
        }

        $WHERE = constructWhere($WHERE);

        // Добавляем сортировку
        $WHERE .= "ORDER BY `used` DESC";


        $result = R::find("coupons", $WHERE);



        return $result;

    }



    public function getCoupons($token, $cid){
        $url = API."/coupons/website/".$this->wID."/";
        $type = "GET";
        $limit = 300;

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token
        );

        $PARAMS = [
            'limit' => $limit,
            'offset' => 0,
            'campaign' =>$cid
        ];

        $result = fCURL($url, [$type => $PARAMS], $headers);



        if( isset( $result['error'] ) && $result['error'] == 'invalid_token' ){
            $token = $this->AuthAdmitad();
            return $this->getCoupons($token);
        }

        $nadozagruzok = ceil($result['_meta']['count']/$result['_meta']['limit'])-1;

//         echo "Надо добавить еще  ".$nadozagruzok." загрузки <br>";


        if ($nadozagruzok == 0)  return $result['results'];

        // Дозагружаем остальные значения.

        for ($i = 1; $i <= $nadozagruzok; $i++) {

            $offset = $i*$limit;
            //  echo "Загружаем $i ... $offset<br><hr>";

            $PARAMS = [
                'limit' => $limit,
                'offset' => $offset
            ];
            $add = fCURL($url, [$type => $PARAMS], $headers);

            if( isset( $result['error'] ) && $result['error'] == 'invalid_token' ){
                $token = $this->AuthAdmitad();
                return $this->getCoupons($token);
            }
            $result['results'] = array_merge($result['results'], $add['results']);

        }

        return $result['results'];





    }



    public function addnewscoupon($action, $coupon){


        $DATA = [
            'action' => $action,
            'company' => $coupon['companies_id'],
            'name' => $coupon['name'],
            'shortname' => $coupon['short_name'],
            'discount' => $coupon['discount'],
        ];

        $this->addnewBD("couponnews", $DATA);

        return true;
    }





    public function removeFinishCoupon(){

        $allCoupons = R::findAll('coupons');

//        show($allCoupons);

        foreach ($allCoupons as $key=>$val){

            if (!$val['dateend']) continue;

            if  ( getOstatok($val['dateend']) < 0 ) {

                // Добавляем новость об удаленном купоне

               $this->addnewscoupon("delete", $val);

                echo "Купон id ".$val['id']." удален т.к. уже просрочен! <br>";
                R::trash($allCoupons[$key]);

            }




        }

        return true;


    }



//    public function AddCouponsinBD($coupons, $companies){
//
//
//    }


    public function addCoupons($token){

        //Удаляем таблицу категории купонов

        R::wipe('categorycoupons');

        $companies = R::findAll('companies');
        //Смотрим все компании


        // Проверяем наличие купонов
        $first = 0;
        $checkfirst = R::findAll('coupons');
        if (empty($checkfirst)) $first = 1;

//        $i=0;

        foreach ($companies as $key=>$company){

//            $i++;
//            if ($i == 10) continue;

            // Получаем список купонов
            $coupons = $this->getCoupons($token, $company['idadmi']);


            foreach ($coupons as $k=>$val){


//                echo "<h1>КУПОНЫ ДЛЯ ".$company['name']."</h1><br>";
//                echo "ID компании ".$company['id']." | ID компании в admi ".$company['idadmi']." <br>";
//                echo "ID купона компании ".$val['campaign']['id'];
//
//                if ($company['idadmi'] != $val['campaign']['id']) echo "<br><font color='#8b0000'>11111</font>";



                $categories =  extractcategoriesCoupons($val['categories']);
                $categories = $this->workcategoriesCoupons($categories);
                $categories = json_encode($categories);



                // Загрузка базовых параметров
                $framset = (!empty($val['frameset_link'])) ?  $val['frameset_link'] : $val['goto_link'];
                $types = json_encode($val['types'], true);
                $framset = str_ireplace("http", "https", $framset);
                // Загрузка базовых параметров


                $coupon = R::findOne("coupons", "WHERE idadmi = ?" , [$val['id']]);



                if (!empty($coupon)){
                    echo "Купон ".$val['name']." уже добавлен! Но мы его обновим! <br>";


                    $coupon->idadmi = $val['id'];
                    $coupon->name = $val['name'];
                    $coupon->description = $val['description'];
                    $coupon->category = $categories;
                    $coupon->short_name = $val['short_name'];
                    $coupon->species = $val['species'];
                    $coupon->dateend = $val['date_end'];
                    $coupon->types = $types;
                    $coupon->discount = $val['discount'];
                    $coupon->promocode = $val['promocode'];
                    $coupon->gotolink = $val['goto_link'];
                    $coupon->idamicompany = $val['campaign']['id'];
                    $coupon->framset = $framset;
                    $coupon->status = $val['status'];
                    R::store($coupon);

                    continue;
                }




                // Добавление
                $coupon = R::dispense("coupons");
                $coupon->idadmi = $val['id'];
                $coupon->name = $val['name'];
                $coupon->description = $val['description'];
                $coupon->category = $categories;
                $coupon->short_name = $val['short_name'];
                $coupon->used = 0;
                $coupon->species = $val['species'];
                $coupon->datestart = $val['date_start'];
                $coupon->dateend = $val['date_end'];
                $coupon->types = $types;
                $coupon->discount = $val['discount'];
                $coupon->promocode = $val['promocode'];
                $coupon->gotolink = $val['goto_link'];
                $coupon->idamicompany = $val['campaign']['id'];
                $coupon->framset = $framset;
                $coupon->status = $val['status'];
                $company->ownCouponList[] = $coupon;

                echo "<b>Купон ".$val['name']." добавлен </b>  <br>";

                // Если не первый запуск, то добавляем новости
             if ($first == 0)   $this->addnewscoupon("add", $val);


                R::store($company);


            }

            // Получаем список купонов



        }








        return true;

    }



    function RedirCoupon($coupon){


        $coupon =  R::Load('coupons', $_GET['coupon']);

        if (!empty($coupon)){
            $coupon->used = $coupon->used +1;
            R::store($coupon);

            // Отправка ПОСТБЕКА
            // subid1 = couponID
            // subid2 = uniqID наш
            // subid4 = subID google

            $link = $coupon['gotolink']."&subid1=".$coupon['id']."&subid2=".$_SESSION['SystemUserId']."&subid4=".gaUserId()."&subid3=".gaUserIdGA();
            // Отправка ПОСТБЕКА

            redir($link);
        }

        return true;


    }



    public function updatestatus(){

     return R::findAll("updatestatus");

    }

    public function updatecheck($type){

            $zapis = R::findOne("updatestatus", "WHERE type = ?" , [$type]);

            if (!empty($zapis)){
                $zapis->date = date("Y-m-d H:i:s");
                R::store($zapis);
            }else{
                $DATA = [
                    'type' => $type,
                    'date' => date("Y-m-d H:i:s"),
                ];
                $this->addnewBD("updatestatus", $DATA);

            }

            return true;


    }




    public function addMagazin($admicompanies, $token){

        $RS = [];
        $allShops = R::findAll('companies');


        // Записываем IDшники магазинов которые уже есть в БД
        foreach ($allShops as $key=>$val){
            $RS[$val['idadmi']] = 1;
        }


        foreach ($admicompanies as $key=>$val){
            // Проверяем наличие магазина в БД
            if (!empty($RS[$val['id']])) {
                echo "Партнерская программа ".$val['name']." уже добавлена. <br>";
                continue;
            }
            if ($val['connection_status'] != "active") continue;



            //Забираем себе лого
            $extension = getExtension($val['image']);
            $logo = '/upload/logos/'.$val['id'].'logo.'.$extension;
            file_put_contents(WWW.$logo, file_get_contents($val['image']));

            // Работа с категориями
            $categories =  extractcategories($val['categories']);
            $categories = $this->workcategories($categories);
            $categories = json_encode($categories, true);
            // Работа с категориями

            $val['name'] = str_replace("RU", "", $val['name']);
            $val['name'] = str_replace("WW", "", $val['name']);
            $val['name'] = str_replace("[CPS]", "", $val['name']);
            $val['name'] = str_replace("Many GEOs", "", $val['name']);



//            $deeplink = $this->getDeepLink($token, $val['id'], $val['site_url']);
//            if (empty($deeplink)) $deeplink = "";
        $deeplink = $val['gotolink'];


            $DATA = [
                'idadmi' => $val['id'],
                'name' => $val['name'],
                'url' => $val['site_url'],
                'ulp' => $deeplink,
                'uri' => translit_sef($val['name']),
                'ecpc' => $val['ecpc'],
                'category' => $categories,
                'logo' => $logo,
                'description' => "",
                'status' => $val['status'],
                'addbanner' => 0,
            ];

            $this->addnewBD("companies", $DATA);

//            echo "Добавлена партнерская программа <b>".$val['name']."</b> !";


        }







        return true;

    }


    public function workcategoriesCoupons($cat){

        $categoryarray = [];

        foreach ($cat as $key => $val){

            $categoriya = R::findOne("categorycoupons", "WHERE name = ?" , [$val]);

            if (!empty($categoriya)) {
                $categoriya->count = $categoriya->count +1;
                $categoryarray[] = $categoriya->id;
                R::store($categoriya);
            }

            if (empty($categoriya)){
                $url = translit_sef($val);
                $DATA = [
                    'name' => $val,
                    'url' => $url,
                    'description' => "",
                    'count' => 1,
                    'countview' => 1,
                ];
                $categoryarray[] =  $this->addnewBD("categorycoupons", $DATA);
            }



        }


        return $categoryarray;

    }




    public function workcategories($cat){

        $categoryarray = [];

        foreach ($cat as $key => $val){

            $categoriya = R::findOne("category", "WHERE name = ?" , [$val]);

            if (!empty($categoriya)) {
                $categoriya->countshop = $categoriya->countshop +1;
                $categoryarray[] = $categoriya->id;
                R::store($categoriya);
            }

            if (empty($categoriya)){
                $url = translit_sef($val);
                $DATA = [
                    'name' => $val,
                    'url' => $url,
                    'description' => "",
                    'countshop' => 1,
                    'countview' => 1,
                ];
                $categoryarray[] =  $this->addnewBD("category", $DATA);
            }



        }


        return $categoryarray;

    }

    public function getCategories($token, $id = ""){

        if (empty($id))    $url = API."/categories/";
        if (!empty($id)) $url = API."/categories/advcampaign/".$id."/";
        $type = "GET";

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token
        );


        $PARAMS = [];

        $result = fCURL($url, [$type => $PARAMS], $headers);

        return $result;

    }

    public function countCounpons($coupons, $idcompany)
    {


        $promocode =   R::count("coupons", "WHERE `species` = ? AND `companyid` =?  ", ["promocode", $idcompany]);
        $action =   R::count("coupons", "WHERE `species` = ? AND `companyid` =?", ["action", $idcompany]);


        $count['promocode'] = $promocode;
        $count['action'] = $action;

        return $count;

    }

    public function getGotoUrl($id)
    {

        $coupon = R::findOne("coupons", "WHERE id = ?" , [$id]);

        if ($coupon)  {
            $coupon->used = $coupon + 1;
            R::store($coupon);
            redir($coupon['gotolink']);

        }



        if (!$coupon)   redir("/");




    }

    public function AddUtminBD($DATA)
    {


        $UTM = [
            'utm_source' => "",
            'utm_medium' => "",
            'utm_campaign' => "",
            'utm_content' => "",
            'utm_term' => "",
            'cmpid' => "",
        ];

        foreach ($UTM as $key=>$value){

            if (!empty($DATA[$key])) $UTM[$key] = $DATA[$key];
        }

        $UTM['sysuserid'] = $_SESSION['SystemUserId'];
        $UTM['gaid'] = gaUserIdGA();
        $UTM['cid'] = gaUserId();


        $UTM['date'] = date("Y-m-d H:i:s");




        $this->addnewBD("usertoday", $UTM);



        return $UTM;



    }

    public function detalstat($id){

        // Раскладка кликов
        $zaprosi = R::findAll("usertoday", "WHERE `cmpid` =? GROUP BY `utm_term` ", [$id]);

        // Получаем список запросов
        foreach ($zaprosi as $zapros){
            if ($zapros['utm_term'] == "{keyword}") continue;
            $massivdata[$zapros['utm_term']]['clicks'] = R::count("usertoday", "WHERE `utm_term` = ? ", [$zapros['utm_term']]);
            $massivdata[$zapros['utm_term']]['zarabotok'] = 0;
            $massivdata[$zapros['utm_term']]['conversion'] = 0;
        }



        // Раскладка конверсий
        $converstion = R::findAll("conversion");
        foreach ($converstion as $key=>$value){
            $coupon = json_decode($value['coupon'], true);
            $utm = json_decode($value['utm'], true);

            if ($coupon['companies']['id'] != $id) continue;

            $massivdata[$utm['utm_term']]['conversions'][$key] ['name']= $coupon['name'];
            $massivdata[$utm['utm_term']]['conversions'][$key] ['zarabotok']= $value['zarabotok'];
            $massivdata[$utm['utm_term']]['conversions'][$key]['utm_term'] = $utm['utm_term'];;
            $massivdata[$utm['utm_term']]['conversions'][$key]['utm_source'] = $utm['utm_source'];


            $massivdata[$utm['utm_term']]['conversion'] = $massivdata[$utm['utm_term']]['conversion'] +1;
            $massivdata[$utm['utm_term']]['zarabotok'] = $massivdata[$utm['utm_term']]['zarabotok'] + $value['zarabotok'];


        }




        return $massivdata;

    }


    public function conversiontodaty(){

        $allconversions = R::findAll("conversion");



        return $allconversions;

    }

    public function companiestoday(){

        $listcompanies = R::find("usertoday", "GROUP BY `cmpid` ");

        if (empty($listcompanies)) return [];


        // Определяем по каким проектам были сегодня КЛИКИ
        foreach ($listcompanies as $company){
            $companytemp = R::load("companies", $company['cmpid']);
            $compname['name'] = $companytemp['name'];
            $compname['conversion'] = 0;
            $compname['zarabotok'] = 0;
            $allcompanies[$companytemp['id']] = $compname;
        }

        if (empty($allcompanies)) $allcompanies = [];

        // Берем клики
        foreach ($allcompanies as $key=>$val){
            $clicks = R::count("usertoday", "WHERE `cmpid` =? ", [$key]);
            $allcompanies[$key]['clicks'] = $clicks;
        }

        // Записываем конверсии
        $conversion = R::findAll("conversion");
        foreach ($conversion as $key=>$val){


            $coupon = json_decode($val['coupon'], true);
            if (empty($coupon['companies']['id'])) continue;

            $cid = $coupon['companies']['id'];
            $allcompanies[$cid]['conversion'] = $allcompanies[$cid]['conversion']+1;
            $allcompanies[$cid]['zarabotok'] = $allcompanies[$cid]['zarabotok'] + $val['zarabotok'];



        }



        return $allcompanies;

    }


    public function Getlastconversion(){
        return R::findALL("conversion", "ORDER BY `id` DESC LIMIT 50");
    }


    public function Getshopswithoutcoupons(){
        $companies = R::findALL("companies");

        foreach ($companies as $company){

//            echo $company['name']." - ".$company->countOwn("coupons")."<br>";

            if ($company->countOwn("coupons") > 0) continue;
            $companywithoutcoupons[] = $company;

        }



        return $companywithoutcoupons;


    }


    public function shopsinwork(){

        // Берем список компаний у которых уже были клики
        $listcompanies = R::find("usertoday", "GROUP BY `cmpid` ");

        if (empty($listcompanies)) return [];
        foreach ($listcompanies as $comp){
            $workcompany[] = $comp['cmpid'];
        }
        // Берем список компаний у которых уже были клики

        $companies = R::findALL("companies");

        foreach ($companies as $key=>$company){
            if (!array_search($company['id'], $workcompany)) unset($companies[$key]);

        }



        return $companies;


    }


    public function shopsnotwork(){

        // Берем список компаний у которых уже были клики
        $listcompanies = R::find("usertoday", "GROUP BY `cmpid` ");

        if (empty($listcompanies)) return [];
        foreach ($listcompanies as $comp){
            $workcompany[] = $comp['cmpid'];
        }
        // Берем список компаний у которых уже были клики

        $companies = R::findALL("companies");

        foreach ($companies as $key=>$company){
            if (array_search($company['id'], $workcompany)) unset($companies[$key]);

        }



        return $companies;


    }



    public function allshops(){
        $companies = R::findALL("companies");

        return $companies;


    }


    public function GetCustomCoupons(){
        return R::findALL("coupons", "WHERE type=?", ["custom"]);
    }



    public function getUTM($uid)
    {
        $UTM = R::findOne("usertoday", "WHERE sysuserid = ?" , [$uid]);

        return $UTM;

    }


    public function sendPostBackGA(){

        $PostBacks = R::findAll("sendpostback", "WHERE `status` =?", [1]);

        if (empty($PostBacks)){
            echo "Постбеков на отправку нет!<br>";
            return true;
        }


            foreach ($PostBacks as $postBack){

                $PARAMS = json_decode($postBack['params'], true);


                $url = "https://www.google-analytics.com/collect";
                $url = $url."?".http_build_query($PARAMS);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMS);
        curl_exec($ch);
        curl_close($ch);

                $postBack->status = 2;
                R::store($postBack);

                echo "Постбек отправлен!<br>";

            }









    }


    public function GetParamSendGoogleTransaction($coupon, $DATA)
    {


        $company = $coupon->companies['name'];

        $cid = $DATA['subid4'];
        if (empty($cid)) $cid = $DATA['subid3'];
        if (empty($cid)) $cid = $DATA['subid2'];

        $PARAMS = [
            'v' => 1,
            't' => 'pageview',
            'tid' => CONFIG['UA'],
            'cid' => $cid,
            'dp' => 'postbackconvert2',
            'ti'=> $coupon['name'],
            'ta' => $company,
            'tr'=> $DATA['payment_sum'],
            'pa'=> 'purchase',
            'pr1id'=> 'Admi',
            'pr1nm'=> $DATA['offer_name'],
        ];


        $DATA = [
            'status' => 1,
            'params' => json_encode($PARAMS),
            'source' => "google",
            'sysid' => $DATA['subid2'],
            'summa' => $DATA['payment_sum'],
        ];

        $this->addnewBD("sendpostback", $DATA);

        return true;

    }



    public function SendGoogleTransactionTest()
    {


        $url = "https://www.google-analytics.com/collect";

        $cid = gaUserId();

        $PARAMS = [
            'v' => 1,
            't' => 'pageview',
            'tid' => 'UA-174357261-1',
            'cid' => $cid,
            'dp' => 'postbackconvert',
            'ti' => 'Скидка 35% на все!',
            'ta' => 'Domino\'s Pizza',
            'tr' => 20.11,
            'pa' => 'purchase',
            'pr1id' => 'Admi',
            'pr1nm' => 'Domino\'s Pizza',


        ];

        $url = $url."?".http_build_query($PARAMS);

      //  show($PARAMS);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMS);
        curl_exec($ch);
        curl_close($ch);


        return true;

    }





}
?>