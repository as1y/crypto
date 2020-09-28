<?php
namespace APP\controllers;
use APP\models\Main;
use APP\core\Cache;
use APP\core\base\Model;
use APP\models\Panel;

class SitemapController extends AppController {


	public function indexAction(){

        $Panel = new Panel();
        $this->layaout = false;


       $DATA = $Panel->getUrlSiteforSitemap();

        header ("content-type: text/xml");

        $Sitemap="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $Sitemap.="<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";

        foreach ($DATA as $val){
            $Sitemap.= "<url>";
            $Sitemap.= "<loc>".$val['http']."</loc>";
            $Sitemap.= "<changefreq>monthly</changefreq>";
            $Sitemap.= "</url>";
        }

        $Sitemap.="</urlset>";

        echo $Sitemap;

       //show($DATA);

        // Генерация УРЛ компаний

        // Урл категорий купонов

        // Генерация компания + категория






	}










}
?>