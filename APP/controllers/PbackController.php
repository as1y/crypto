<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;


class PbackController extends AppController {

    public function indexAction()
    {

     $this->layaout = false;

        $Panel =  new Panel();



        if (!empty($_POST)){

            // Получение постбека


            $productid = $_POST['subid1'];
            $uid = $_POST['subid2'];
            $gaid = $_POST['subid4'];



            $product = $Panel->loadOneProduct($productid);
            $Panel->GetParamSendGoogleTransaction($product, $_POST);


            $producttext = json_encode($product, true);

            $UTM = $Panel->getUTM($uid);
            $UTM = json_encode($UTM, true);

            if ($_POST['currency'] == "USD") $_POST['payment_sum'] = ConvertRUB($_POST['payment_sum'], "USD");

            $cid = $product->companies['id'];

            $DATA = [
                'product' => $producttext,
                'cid' => $cid,
                'UTM' => $UTM,
                'zarabotok' => $_POST['payment_sum'],
                'offer' => $_POST['offer_name'],
                'action' => $_POST['action'],
                'conversiontime' => $_POST['conversion_time'],
                'date' => date("Y-m-d H:i:s"),
                'gaid' => $gaid,
                'uid' => $uid,
                'status' => 1,

            ];




            $Panel->addnewBD("conversion", $DATA);

            exit();

        }



//                $Panel->SendGoogleTransactionTest();



    }




}
?>