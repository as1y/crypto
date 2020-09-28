<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class TurboController extends AppController {


	public function indexAction(){

        $Panel = new Panel();
        $this->layaout = false;


       $DATA = $Panel->getUrlSiteforSitemap();

        header ("content-type: text/xml");

        $rss='<?xml version="1.0" encoding="UTF-8"?>';

        $rss.='<rss xmlns:yandex="http://news.yandex.ru"';
        $rss.='     xmlns:media="http://search.yahoo.com/mrss/"';
        $rss.='     xmlns:turbo="http://turbo.yandex.ru"';
        $rss.='     version="2.0">';

          $rss.='<channel>';

              $rss.='<title>Турбо страницы сайта '.APPNAME.'</title>';
              $rss.='<link>'.'https://'.CONFIG['DOMAIN'].'</link>';
              $rss.='<description>'.description.'</description>';


        foreach ($DATA as $val){
            $rss.= '<item turbo="true">';
            $rss.= '<link>'.$val['http'].'</link>';


            $rss.= '<turbo:content>';
            $rss.= '<![CDATA[';


            $meta = json_decode($val['meta'], true);

            $rss.= '<header>';
              $rss.= '<h1>'.htmlspecialchars($meta['H1']).'</h1>';
            $rss.= '</header>';

            $couponsid = json_decode($val['couponsid'], true);
            foreach ($couponsid as $id){

                 $coupon = $Panel->loadOneCoupon($id);


                 $rss.= '<p>'.trim($coupon->companies['name']).'</p>';
                $rss.= '<p><figure><img src="https://'.CONFIG['DOMAIN'].$coupon->companies['logo'].'"/></figure></p>';

                
                $rss.= '<p><b>'.trim($coupon->companies['name']).'</b></p>';
                $rss.= '<p>'.$coupon['short_name'].'</p>';

                if ($coupon['type'] == "promocode"){
                    $rss.= '<p>ПРОМОКОД: '.$coupon['promocode'].'</p>';
                }
          $rss.=      '<button
  formaction="https://'.CONFIG['DOMAIN'].'go/?coupon='.$coupon['id'].'"
  data-background-color="#eee"
  data-color="white"
  data-turbo="false"
  data-primary="true"
  disabled
  >
  ИСПОЛЬЗОВАТЬ СКИДКУ
                </button>';



            }




            // КОНТЕНТ
//            $meta = json_decode($val['meta'], true);
//            $rss.= '<title>'.htmlspecialchars($meta['H1']).'</title>';
//            $rss.= '<description>'.htmlspecialchars($meta['title']).'</description>';
            // КОНТЕНТ




            $rss.= ']]>';
            $rss.= '</turbo:content>';

            $rss.= '</item>';
        }





            $rss.='</channel>';
        $rss.='</rss>';
        
        echo $rss;

       //show($DATA);

        // Генерация УРЛ компаний

        // Урл категорий купонов

        // Генерация компания + категория






	}










}
?>