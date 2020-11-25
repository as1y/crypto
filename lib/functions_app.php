<?php


function extractcategoriesCoupons($categories){
    $result = [];
    foreach ($categories as $key=>$val) $result[] = $val['name'];
    return $result;
}

function extractcategories($categories){

    $result = [];

foreach ($categories as $key=>$val){

    if (empty($val['parent']) && empty($val['name']))  {
        $result[] = $val;
        continue;
    }

    if (empty($val['parent']) && !empty($val['name']) )  $result[] = $val['name'];

    if (!empty($val['parent'])) {
        $result[] = $val['name'];
    }

}

return $result;


}



function generateResult($coupons, $PAGESLIST, $catalogCategories,  $query ="", $catalogCompany = []){

    // Если ЧТО-ТО ПОШЛО НЕ ТАК
    if ($coupons === false){
        echo "<h5>По вашему запросу ничего не найдено!<br> Попробуйте восползоваться умным фильтром промокодов<br></h5>  

<a class='btn px-4 btn-primary-dark-w py-2 rounded-lg' href='/promocode/vse'>ПЕРЕЙТИ</a>";
        return false;

    }
    // Если ЧТО-ТО ПОШЛО НЕ ТАК


    // СБРАСЫВАЕМ ИНДЕКСЫ И НАЧИНАЕМ С 1
    $coupons =  array_values($coupons);
    array_unshift($coupons, NULL);
    unset($coupons[0]);

    // Всего купонов
    $allcoupons = count($coupons);
    // Всего страниц
    $Pages = ceil(($allcoupons/$PAGESLIST['CouponsPerPage'] ));



?>


    <?=generetuCouponinCode($coupons, $PAGESLIST['ViewPage'], $PAGESLIST['CouponsPerPage'])?>




    <?php
    $starpage = generateStartEndPage($PAGESLIST, $Pages)['starpage'];
    $endpage = generateStartEndPage($PAGESLIST, $Pages)['endpage'];
    require_once('includes/bootstrapnav.php');




}


function generatecsvYandex($ADV, $DATA){


    // Строки с объявления
    foreach ($ADV['description'] as $key=>$obja){
        ?>
        <tr>
            <td><?=$DATA['namecompany']?></td>
            <td>Текстово-графическое</td>
            <td><?=$ADV['rekl']?></td>
            <td></td>
            <td><?=$ADV['zagolovok1']?></td>
            <td><?=$ADV['zagolovok2']?></td>
            <td>
                <?=$obja?>
            </td>
            <td><?=$ADV['url']?></td>
            <td><?=$ADV['path1']?></td>
            <td>Москва и область, Санкт-Петербург и Ленинградская область</td>
            <td>Действующие промокоды||Ежедневный контроль||Каталог купонов</td>
            <td></td>
        </tr>
        <?php
    }


    foreach ($ADV['keywords'] as $keyword){

        $keyword = trim($keyword);
        if ($keyword == " " || empty($keyword) || $keyword == "" ) continue;

        ?>

        <tr>
            <td><?=$DATA['namecompany']?></td>
            <td>Текстово-графическое</td>
            <td><?=$ADV['rekl']?></td>
            <td><?='"'.$keyword.'"'?></td>
            <td></td>
            <td></td>
            <td>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>300</td>
        </tr>

        <?php

    }



}


function generatecsvAdwords($ADV, $DATA){

    echo "".$DATA['namecompany'].",".$ADV['rekl'].",,,,,,,,,,,0.01,0.01,0.01,None,Disabled,Default"."<br>";
    // Строки с Ключевыми словами

    foreach ($ADV['keywords'] as $keyword){
        $keyword = trim($keyword);
        if ($keyword == " " || empty($keyword) || $keyword == "" ) continue;

        echo "".$DATA['namecompany'].",".$ADV['rekl'].",".$keyword.",exact,,,,,,,,,,,,,,"."<br>";
    }

    // Строки с объявлениями

    foreach ($ADV['description'] as $key=>$obja){
        if ( ($key % 2) != 0) continue;
        echo "".$DATA['namecompany'].",".$ADV['rekl'].",,,".$ADV['url'].",".$ADV['zagolovok1'].",".$ADV['zagolovok2'].",".$ADV['zagolovok3'].",".$ADV['description'][$key].",".$ADV['description'][$key+1].",".$ADV['path1'].",".$ADV['path2'].",,,,,,"."<br>";
    }



}


function generatestrAdwords($coupons, $company){

    // Функция генерации записей для адвордса
    $bestdiscount = 0; // лучший размер скидки
    $nowdescription = $company['name'].": "; // Текущая строка описания
    $actuald = 0; // Текущий актуальный элемент массива для записи дескрипшена

    $ADVMASS = [];

    foreach ($coupons as $coupon){

        if ($coupon['discount'] == "1%") $coupon['discount'] = "";

        $cd =  mb_substr($coupon['discount'], 0, -1);
        if ($bestdiscount < $cd) $bestdiscount = $coupon['discount'];
        $coupon['short_name'] = obrezanie($coupon['short_name'], 90);

        // Если кол-во текущих символов плюс новые больше 90, то
        $counsymbols = iconv_strlen($coupon['short_name']); // Длинна описания которое хотим добавить
        $coutnow = iconv_strlen($nowdescription); // Текущая длинна

        if ( (($counsymbols + $coutnow) > 90) ){

            if (!empty($ADVMASS['description'][$actuald]))    $ADVMASS['description'][$actuald] = trim($ADVMASS['description'][$actuald]);
            if (empty($ADVMASS['description'][$actuald])) $ADVMASS['description'][$actuald] = "{Keyword:".$company['name']."} - каталог действующих скидок, акций, промокодов ";

            $nowdescription = "";
            $actuald++;
        }

        $nowdescription .= $coupon['short_name']." ";
        $ADVMASS['description'][$actuald] = $nowdescription;

    }


    foreach ($ADVMASS['description'] as $key=>$val){

        $val = trim($val);
        $val = str_replace("\n", '', $val);
        $val = str_replace("\r", '', $val);
        $val = str_replace(",", '', $val);

        $ADVMASS['description'][$key] = $val;
//        $ADVMASS['description'][$key] = substr($val, 0, -1);
    }
//
    $coundescription = count($ADVMASS['description']);

    $company['url'] = clearurl($company['url']);



    if ( ($coundescription % 2) != 0){
        $ADVMASS['description'][] = "На портале купоны и промокоды для покупок в интернет магазине ".$company['url'];
    }

    $ADVMASS['zagolovok1'] = "{Keyword:".$company['name']."}";
    $ADVMASS['zagolovok2'] = "Промокоды/Акции";
    $ADVMASS['zagolovok3'] = "".$bestdiscount." скидка на заказ";
    $ADVMASS['path1'] =  mb_strtolower(obrezanie($company['name'], 15));
    $ADVMASS['path2'] = "акция";

    return $ADVMASS;

}

function generatestrYandex($coupons, $company){

    // Функция генерации записей для адвордса
    $bestdiscount = 0; // лучший размер скидки
    $nowdescription = $company['name'].": "; // Текущая строка описания
    $actuald = 0; // Текущий актуальный элемент массива для записи дескрипшена

    $ADVMASS = [];

    foreach ($coupons as $coupon){

        if ($coupon['discount'] == "1%") $coupon['discount'] = "";
        $cd =  mb_substr($coupon['discount'], 0, -1);
        if ($bestdiscount < $cd) $bestdiscount = $coupon['discount'];


        $coupon['short_name'] = obrezanie($coupon['short_name'], 80);

        // Если кол-во текущих символов плюс новые больше 90, то
        $counsymbols = iconv_strlen($coupon['short_name']); // Длинна описания которое хотим добавить
        $coutnow = iconv_strlen($nowdescription); // Текущая длинна

        if ( (($counsymbols + $coutnow) > 80) ){

            if (!empty($ADVMASS['description'][$actuald]))    $ADVMASS['description'][$actuald] = trim($ADVMASS['description'][$actuald]);
            if (empty($ADVMASS['description'][$actuald])) $ADVMASS['description'][$actuald] = "#".$company['name']."# - каталог действующих скидок, акций, промокодов ";

            $nowdescription = "";
            $actuald++;
        }

        $nowdescription .= $coupon['short_name']." ";
        $ADVMASS['description'][$actuald] = $nowdescription;

    }




    foreach ($ADVMASS['description'] as $key=>$val){

        $val = trim($val);
        $val = str_replace("\n", '', $val);
        $val = str_replace("\r", '', $val);
        $val = str_replace(",", '', $val);

        $ADVMASS['description'][$key] = $val;
//        $ADVMASS['description'][$key] = substr($val, 0, -1);
    }
//

    $company['url'] = clearurl($company['url']);
    $ADVMASS['description'][] = "Промокоды до ".$bestdiscount." для покупок в интернет магазине ".$company['url'];


    $ADVMASS['zagolovok1'] = "#".$company['name']."#";
    $ADVMASS['zagolovok2'] = "купоны до ".$bestdiscount."";
    $ADVMASS['path1'] =  mb_strtolower(obrezanie($company['url'], 20));


    return $ADVMASS;

}


function generateStartEndPage($PAGESLIST, $Pages)
{

    $starpage = 1;
    $items = 3;

    $endpage = ($Pages >= $items) ? $items : $Pages;
    // Если больше стандартный пяти
    if ($PAGESLIST['ViewPage'] >= $items) {

        // Если больше стандартный пяти
        $starpage = $PAGESLIST['ViewPage'] - 1;
        $endpage = $PAGESLIST['ViewPage'] + 1;
        if ($starpage < 1) $starpage = 1;
        if ($endpage > $Pages) $endpage = $Pages;
    }

        $result['starpage'] = $starpage;
        $result['endpage'] = $endpage;

        return $result;


}



function paystatus ($status){

    if  ($status == 0) return "<span class='badge badge-warning'>В ПРОЦЕССЕ</span>";
    if  ($status == 1) return "<span class='badge badge-success'>ИСПОЛНЕН</span>";

}



function generetuCouponinCode($coupons, $ViewPage, $CouponsPerPage){

    $start = ($ViewPage * $CouponsPerPage) - ($CouponsPerPage - 1) ;
    $end = $ViewPage * $CouponsPerPage;

    for ($key = $start; $key <= $end; $key++) {
        if (empty($coupons[$key])) continue;

        echo '<div class="col-md-3 col-sm-4 col-xs-12">';
        renderCoupon($coupons[$key]);
        echo  ' </div>';

    }




}



function renderFilter($DATA){

    ?>


    <aside class="widget widget--vendor">
        <h5 class="widget-title">КАТЕГОРИЯ</h5>
        <div class="form-group">
            <select onchange="ChangeFilter()" name="category" class="ps-select">
                <option style="color: red" value="" >Все категории</option>
     <?php foreach ($DATA['catalogCategories'] as $key=>$val) :?>

         <?php if ($val['select'] && !empty($val['name'])) :?>
             <option  selected value="<?=$val['id']?>" ><?=$val['name']?></option>
         <?php endif;?>

         <?php if (!$val['select']) :?>
             <option value="<?=$val['id']?>" ><?=$val['name']?></option>
         <?php endif;?>

    <?php endforeach;?>
            </select>
        </div>
    </aside>

    <aside class="widget widget--vendor">
        <h5 class="widget-title">МАГАЗИН</h5>
        <div class="form-group">
            <select onchange="ChangeFilter()" name="company" class="ps-select">
                <option style="color: red" value="" >Все магазины</option>
                <?php foreach ($DATA['catalogCompany'] as $key=>$val) :?>


                    <?php if ($val['select'] && !empty($val['name'])) :?>
                        <option  selected value="<?=$val['id']?>" ><?=$val['name']?></option>
                    <?php endif;?>

                    <?php if (!$val['select']) :?>
                        <option value="<?=$val['id']?>" ><?=$val['name']?></option>
                    <?php endif;?>



                <?php endforeach;?>
            </select>
        </div>
    </aside>


<!--    <aside class="widget widget--vendor">-->
<!--        <h5 class="widget-title">БРЕНД</h5>-->
<!--        <div style=" height:300px; overflow:auto; padding-left: 0.5rem !important; border:solid 1px #818181;">-->
<!--            --><?php //foreach ($DATA['catalogBrands'] as $key=>$val) :?>
<!--                <div class="ps-checkbox">-->
<!--                    <input class="form-control" type="checkbox" id="brand---><?//=$val['id']?><!--" name="brand">-->
<!--                    <label for="brand---><?//=$val['id']?><!--">--><?//=$val['name']?><!-- (--><?//=$val['count']?><!--)</label>-->
<!--                </div>-->
<!--            --><?php //endforeach;?>
<!--        </div>-->
<!--    </aside>-->



<!--    <figure>-->
<!--        <h4 class="widget-title">ЦЕНА</h4>-->
<!--        <div id="nonlinear"></div>-->
<!--        <p class="ps-slider__meta">Price:<span class="ps-slider__value">$<span class="ps-slider__min"></span></span>-<span class="ps-slider__value">$<span class="ps-slider__max"></span></span></p>-->
<!--    </figure>-->
<!---->
<!--    -->
<!--    <figure>-->
<!--        <h4 class="widget-title">СКИДКА</h4>-->
<!--        <div id="nonlinear"></div>-->
<!--        <p class="ps-slider__meta">Price:<span class="ps-slider__value">$<span class="ps-slider__min"></span></span>-<span class="ps-slider__value">$<span class="ps-slider__max"></span></span></p>-->
<!--    </figure>-->



    <?php
    return true;

}


function textdiscount($discount){

    if($discount == "1%") $discount = NULL;

    if (empty($discount))   $caption = 'на скидку';

    if (!empty($discount))   $caption = $discount;

    return $caption;



}

function captiondiscount($discount){


    if($discount == "1%") $discount = NULL;

    if (empty($discount))   $caption = '<i class="fa fa-check-circle"> </i>';

    if (!empty($discount))   $caption = '<i class="fa fa-gift"> '.$discount.'</i>';

    return $caption;

}

function constructWhere($ARR){

    if (empty($ARR)) return "";

    if (count($ARR) == 1) return "WHERE ".$ARR[0]."";

    $WHERE = "WHERE ".$ARR[0];
    for ($key = 1; $key < count($ARR); $key++) {
        $WHERE .= " AND ".$ARR[$key];
    }

    return $WHERE;

}







function ConvertRUB($value, $cur){


    //          echo ConvertRUB(10, "USD");

    $url = "https://www.cbr-xml-daily.ru/latest.js";
    $rates = fCURL($url)['rates'];

    $result = $value/$rates[$cur];

    $result =  round($result, 2);


return $result;


}


function popUPcoupon (){

    if (!empty($_COOKIE['runmodal'])) $couponmodal =\APP\models\Panel::loadOneCoupon($_COOKIE['runmodal']);
    ?>


    <div id="couponmodal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&#xD7;</button>
                    <h4 class="modal-title"><?=$couponmodal->companies['name']?> - <?=captiondiscount($couponmodal['discount'])?></h4>
                </div>
                <div class="modal-body">
                    <div class="coupon-modal-content">
                        <div class="row">
                            <div class="col-md-12">
                                <font color="#df3737">Для Вашего удобства сайт магазина уже открыт в соседней вкладке.</font>
                                <hr>
                            </div>

                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="single-coupon-thumb">
                                    <img src="<?=$couponmodal->companies['logo']?>" width="600" class="img-thumbnail img-responsive">
                                </div>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-12">

                                <?php if (!empty($couponmodal['short_name'])):?>
                                    <p><?=$couponmodal['short_name']?><br></p>
                                <?php endif; ?>

                             <?php if ($couponmodal['species'] == "promocode"):?>
                                <div class="input-group">
                                    <input type="text" class="form-control" autocomplete="off" readonly value="<?=$couponmodal['promocode']?>">
                                    <div class="input-group-btn">
                                        <button class="clipboard btn btn-default" data-clipboard-text="<?=$couponmodal['promocode']?>"><i class="fa fa-clipboard" aria-hidden="true"></i> КОПИРОВАТЬ</button>
                                    </div>
                                </div>
                              <?php endif; ?>

                                <?php if ($couponmodal['species'] == "action"):?>
                                    <div class="input-group">
                                        <input type="text" class="form-control" autocomplete="off" readonly value="<?=$couponmodal['promocode']?>">
                                    </div>
                                <?php endif; ?>



                                <a class="btn btn-brand" href="//<?=CONFIG['DOMAIN']?>/go/?coupon=<?=$couponmodal['id']?>">ПЕРЕЙТИ В МАГАЗИН</a>

                            </div>

                        </div>
                    </div>
                </div>



            </div>

        </div>
    </div>

    <?php
}

function AuthAdmitad(){


    $url = API."/token/";
    $type = "POST";
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode( CONFIG['ADMITAD']['cliend_id'] . ':' . CONFIG['ADMITAD']['cliend_secret'] )
    ];
    $PARAMS = [
        "grant_type" => "client_credentials",
        "client_id" => CONFIG['ADMITAD']['cliend_id'],
        "scope" => "advcampaigns_for_website coupons_for_website deeplink_generator public_data banners_for_website"
    ];
    $PARAMS = http_build_query($PARAMS);

    $result = fCURL($url, [$type => $PARAMS], $headers);

    return $result['access_token'];


}


function rendersmallproduct($product){
    ?>


    <div class="ps-product">
        <div class="ps-product__thumbnail"><a href="/product/<?=$product['id']?>/<?=$product['uri']?>"><img src="<?=$product['picture']?>" alt=""></a>

    <?php if ($product['percentdiscount']> 10): ?>
            <div class="ps-product__badge">-<?=$product['percentdiscount']?>%</div>
    <?php endif;?>

<!--            <ul class="ps-product__actions">-->
<!--                <li><a href="#" data-toggle="tooltip" data-placement="top" title="Add To Cart"><i class="icon-bag2"></i></a></li>-->
<!--                <li><a href="#" data-placement="top" title="Quick View" data-toggle="modal" data-target="#product-quickview"><i class="icon-eye"></i></a></li>-->
<!--                <li><a href="#" data-toggle="tooltip" data-placement="top" title="Add to Whishlist"><i class="icon-heart"></i></a></li>-->
<!--                <li><a href="#" data-toggle="tooltip" data-placement="top" title="Compare"><i class="icon-chart-bars"></i></a></li>-->
<!--            </ul>-->
        </div>

        <div class="ps-product__container"><a class="ps-product__vendor" href="#"><?=$product->companies['name']?></a>

            <div class="ps-product__content"><a class="ps-product__title" href="/product/<?=$product['id']?>/<?=$product['uri']?>"><?=$product['name']?></a>

<!--                <div class="ps-product__rating">-->
<!--                    <select class="ps-rating" data-read-only="true">-->
<!--                        <option value="1">1</option>-->
<!--                        <option value="1">2</option>-->
<!--                        <option value="1">3</option>-->
<!--                        <option value="1">4</option>-->
<!--                        <option value="2">5</option>-->
<!--                    </select><span>01</span>-->
<!--                </div>-->
                <?php if ($product['percentdiscount']> 10): ?>
                    <p class="ps-product__price sale"><?=round($product['price'])?>₽ <del><?=round($product['oldprice'])?>₽ </del></p>
                 <?php endif;?>

                <?php if ($product['percentdiscount'] <= 10): ?>
                    <p class="ps-product__price"><?=round($product['price'])?>₽</p>
                <?php endif;?>

            </div>

            <div class="ps-product__content hover"><a class="ps-product__title" href="/product/<?=$product['id']?>/<?=$product['uri']?>"><?=$product['name']?></a>
    <?php if ($product['percentdiscount']> 10): ?>
                <p class="ps-product__price sale"><?=round($product['price'])?>₽ <del><?=round($product['oldprice'])?>₽ </del></p>
    <?php endif;?>


    <?php if ($product['percentdiscount'] <= 10): ?>
        <p class="ps-product__price"><?=round($product['price'])?>₽</p>
    <?php endif;?>

            </div>


        </div>
    </div>



    <?php
}



function renderCoupon($coupon){
    ?>


    <!-- Coupon Single Item Start -->
    <div class="item coupon-item">
        <div class="coupon-thumb">

                <img src="<?=$coupon->companies['logo']?>" alt="" class="img-responsive">


            <div class="coupon-badge">
                <?=captiondiscount($coupon['discount'])?>
            </div>

            <?php if ($coupon['species'] == "promocode"): ?>
                <a href="//<?=CONFIG['DOMAIN']?>/go/?coupon=<?=$coupon['id']?>"  onclick="clck(<?=$coupon['id']?>)" class="btn btn-brand">ОТКРЫТЬ ПРОМОКОД</a>

            <?php endif;?>

            <?php if ($coupon['species'] == "action"): ?>
                <a href="//<?=CONFIG['DOMAIN']?>/go/?coupon=<?=$coupon['id']?>" onclick="clck(<?=$coupon['id']?>)"class="btn btn-brand" >АКТИВИРОВАТЬ</a>
            <?php endif;?>





        </div>
        <div class="coupon-content">
            <h6><a href="//<?=CONFIG['DOMAIN']?>/go/?coupon=<?=$coupon['id']?>" onclick="clck(<?=$coupon['id']?>)"><?=$coupon->companies['name']?> </a></h6>
            <p><?=$coupon['short_name']?></p>
            <div class="coupon-content-bottom">
                <p><i class="fa fa-users"></i> <?=$coupon['used']?>
                    <i class="fa fa-clock-o"></i> <?=calculate_exp($coupon['dateend'])?>
                </p>

                <a href="//<?=CONFIG['DOMAIN']?>/go/?coupon=<?=$coupon['id']?>" onclick="clck(<?=$coupon['id']?>)"  class="btn btn-sm">ПЕРЕЙТИ</a>
            </div>
        </div>
    </div>
    <!-- Coupon Single Item End -->




<?php
}









?>