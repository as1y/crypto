<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class WorkController extends AppController {
	public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $ApiKey = "9juzIdfqflVMeQtZf9";
    public $SecretKey = "FwUD2Ux5sjLo8DyifqYr4cfWgxASblk7CZo7";

    // Переменные для стратегии
    public $summazahoda = 40; // Сумма захода с оригинальным балансом
    public $leverege = 30;
    public $Exhcnage1 = "bybit";
    public $symbol = "BTC/USDT";
    public $emailex  = "raskrutkaweb@yandex.ru"; // Сумма захода USD
    public $namebdex = "treks";

    public $limTrek = 1;


    private $TypeGird = "long";
    private $RangeH = 55410;
    private $RangeL = 54300;
    private $CountOrders = 20;


    // Переменные для стратегии


    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    private $WORKTREKS = [];
    private $ORDERBOOK = [];
    private $EXCHANGECCXT = [];
    private $BALANCE = [];
    private $FULLBALANCE = [];
    private $esymbol = "";
    private $MASSORDERS = [];
    private $step = "";


    // ТЕХНИЧЕСКИЕ ПЕРЕМЕННЫЕ
    public function indexAction()
    {

        $this->layaout = false;

        date_default_timezone_set('UTC');
        // Браузерная часть
        $Panel =  new Panel();
        $META = [
            'title' => 'Панель BURAN',
            'description' => 'Панель BURAN',
            'keywords' => 'Панель BURAN',
        ];
        $BREADCRUMBS['HOME'] = ['Label' => $this->BreadcrumbsControllerLabel, 'Url' => $this->BreadcrumbsControllerUrl];
        $BREADCRUMBS['DATA'][] = ['Label' => "FAQ"];
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        $ASSETS[] = ["js" => "/global_assets/js/plugins/tables/datatables/datatables.min.js"];
        $ASSETS[] = ["js" => "/assets/js/datatables_basic.js"];
        \APP\core\base\View::setAssets($ASSETS);
        \APP\core\base\View::setMeta($META);
        \APP\core\base\View::setBreadcrumbs($BREADCRUMBS);
        // Браузерная часть


        //  show(\ccxt\Exchange::$exchanges); // print a list of all available exchange classes

        //Запуск CCXT
        $this->EXCHANGECCXT = new \ccxt\bybit (array(
            'apiKey' => $this->ApiKey,
            'secret' => $this->SecretKey,
            'timeout' => 30000,
            'enableRateLimit' => true,
            'marketType' => "linear",
            'options' => array(
               // 'code'=> 'USDT',
              //  'marketType' => "linear"
            )
        ));

        $this->esymbol = $this->EkranSymbol();

        $this->FULLBALANCE = $this->GetBal();

        $this->BALANCE = $this->FULLBALANCE['USDT']['free'];


        $this->ORDERBOOK = $this->GetOrderBook($this->symbol);


        // Контроль повторного запуска


        // РАСЧЕТ ОРДЕРОВ
        $this->work();


//        $this->set(compact(''));

    }


    public function work(){

        echo "<h1>HUYA</h1>";
        $Panel = new Panel();

        $TREK = $this->GetTreksBD();

        foreach ($TREK as $key => $row) {
            //Проверка на работу трека
            if ($row['work'] == 1) continue;
            $this->StartTrek($row);
            //Проверка на работу трека

            $this->WORKTREKS[] = $row['symbol'];

            echo "<h2>СИМВОЛ: " . $row['symbol'] . " - STATUS - " . $row['status'] . " | " . $row['side'] . " | " . $row['id'] . "   </h2>";
            $f = 'WorkStatus' . $row['status'];
            $this->$f($row);
        }


        // Логирование запусков



        if (count($TREK) < $this->limTrek) $this->AddTrek();

        $this->LogZapuskov($TREK);
        $this->StopTrek($TREK);
        sleep("1");

    }

    private function AddTrek()
    {


        $this->SetLeverage($this->leverege);


        echo "Проверка на значение цены<br>";

        $pricenow = $this->GetPriceSide($this->symbol, $this->TypeGird);

        if ($this->RangeH > $pricenow && $this->TypeGird == "long"){
            echo "Сетка в лонг. Текущая цена должна быть выше верхней границы сетки<br>";
            echo "Текущая цена =  ".$pricenow." <br>";
            echo "Верхняя планка захода = ".$this->RangeH." <br>";
            return false;
        }
        echo "<hr>";



        echo "Рассчет ордеров<br>";

        $delta = ($this->RangeH) - ($this->RangeL);
        $this->step = $delta/$this->CountOrders;

        // РАСЧЕТ ШАГОВ
        $this->MASSORDERS = $this->GenerateStepPrice($delta, $this->step);
        $this->CalculatePriceOrders();



        if ($this->BALANCE < $this->summazahoda){
            echo "НЕ ХВАТАЕТ БАЛАНСА";
            exit;
        }

        foreach ($this->MASSORDERS as $key=>$val){
            $quantity = $this->GetQuantityBTC($val['summazahoda'] , $val['price']);
            $this->MASSORDERS[$key]['quantity'] = $quantity;
            echo $quantity."<br>";
        }
        foreach ($this->MASSORDERS as $key=>$val){

            $params = [
                'time_in_force' => "PostOnly",
//                'reduce_only' => true
            ];

            $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit","Buy", $val['quantity'], $val['price'], $params);
            $this->MASSORDERS[$key]['order'] = $order;
            echo "ОРДЕР ВЫСТАВЛЕН<br>";

        }
 

        // Добавление ТРЕКА в БД
        $avg = ($this->RangeL + $this->RangeH)/2;
        $avg = round($avg);

        $ARR['emailex'] = $this->emailex;
        $ARR['status'] = 1;
        $ARR['side'] = $this->TypeGird;
        $ARR['symbol'] = $this->symbol;
        $ARR['lever'] = $this->leverege;
        $ARR['count'] = $this->CountOrders;
        $ARR['rangeh'] = $this->RangeH;
        $ARR['rangel'] = $this->RangeL;
        $ARR['step'] = $this->step;
        $ARR['avg'] = $avg;
        $ARR['date'] = date("Y:m:d");
        $ARR['stamp'] = time();

        $idtrek = $this->AddARRinBD($ARR);
        echo "<b><font color='green'>ДОБАВИЛИ ТРЕК</font></b>";
        // Добавление ТРЕКА в БД


        // Добавление ордеров в БД
        foreach ($this->MASSORDERS as $key=>$val){

            if ($this->TypeGird  == "long") $nextstep = $val['order']['price'] + $this->step;

            $ARR = [];
            $ARR['idtrek'] = $idtrek;
            $ARR['stat'] = 1;
            $ARR['orderid'] = $val['order']['id'];
            $ARR['status'] = $val['order']['status'];
            $ARR['type'] = $val['order']['type'];
            $ARR['side'] = $val['order']['side'];
            $ARR['amount'] = $val['order']['amount'];
            $ARR['price'] = $val['order']['price'];
            $ARR['nextstep'] = $nextstep;


            $this->AddARRinBD($ARR, "orders");

        }

        // Добавление ордеров в БД
        return true;

    }


    private function LogZapuskov($TREK){

        foreach ($TREK as $key=>$val){
            $tbl = R::findOne("treks", "WHERE id =?", [$val['id']]);
            $tbl->lastrun = date("H:i:s");
            R::store($tbl);
        }

        return true;
    }


    private function StartTrek($TREK){

        $tbl = R::findOne("treks", "WHERE id =?", [$TREK['id']]);
        $tbl->work = 1;
        R::store($tbl);


        return true;
    }



    private function StopTrek($TREK){
        foreach ($TREK as $key=>$val){
            $tbl = R::findOne("treks", "WHERE id =?", [$val['id']]);
            $tbl->work = 0;
            R::store($tbl);
        }
        return true;
    }





    private function WorkStatus1($TREK)
    {


        echo "<h1>ВОРКСТАТУС</h1>";


        $OrdersBD = $this->GetOrdersBD($TREK);
//        show($ORDERS);






        // ПРОВЕРКА ВЫСТАВЛЕННЫХ ОРДЕРОВ. ЗАЩИТА ОТ СБОЕВ В РЕСТ
        echo "Защита Синхронизация<br>";


        foreach ($OrdersBD as $key=>$OrderBD){


            echo "<hr>";

            echo "СТАТУС ОРДЕРА".$OrderBD['orderid']."<br>";

            $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);



            echo "Информация об ордере из BD<br>";
//            show($OrderBD);

            echo "Информация об ордере из REST<br>";
//           show($OrderREST);



            if ($this->OrderControl($OrderREST) === FALSE){
                echo "ОРДЕР не откупился! <br>";
                continue;
            }


            // Если откупились на первый статус
            if ($OrderBD['stat'] == 1){
                // Добавление сделки в БД
                $this->AddTrackHistoryBD($TREK, $OrderBD);
                // Удаление текущего ордера из БД


                // Создание реверсного ордера
                $order = $this->CreateReversOrder($TREK, $OrderBD);

                if ($order === FALSE){
                    echo "Текущая цена выше выставления цены ордера. Если сейчас выставить, то он закроется в рынок и все прошуиться. Поэтому ждем цены";
                    continue;
                }

                $this->DeleteOrderBD($OrderBD);

                // Запись реверсного ордера в БД
                $ARR = [];
                $ARR['idtrek'] = $TREK['id'];
                $ARR['stat'] = 2;
                $ARR['orderid'] = $order['id'];
                $ARR['status'] = $order['status'];
                $ARR['type'] = $order['type'];
                $ARR['side'] = $order['side'];
                $ARR['amount'] = $order['amount'];
                $ARR['price'] = $order['price'];

                $this->AddARRinBD($ARR, "orders");

                continue;
            }




            // Если откупились на закрытие
            if ($OrderBD['stat'] == 2){
                // Добавление сделки в БД

                echo "ОРДЕР ВТОРОГО СТАТУСА <br>";

                $this->AddTrackHistoryBD($TREK, $OrderBD);

                // Удаление текущего ордера из БД
                $this->DeleteOrderBD($OrderBD);

                // Создание реверсного ордера
                $order = $this->CreateReNewOrder($TREK, $OrderBD);

                // Запись реверсного ордера в БД
                $ARR = [];
                $ARR['idtrek'] = $TREK['id'];
                $ARR['stat'] = 1;
                $ARR['orderid'] = $order['id'];
                $ARR['status'] = $order['status'];
                $ARR['type'] = $order['type'];
                $ARR['side'] = $order['side'];
                $ARR['amount'] = $order['amount'];
                $ARR['price'] = $order['price'];

                $this->AddARRinBD($ARR, "orders");

                continue;





            }

        }

        // Контроллер ситуации

        return true;

    }


    public function CreateReversOrder($TREK, $ORD){

        if ($TREK['side'] = "long"){


            echo "Выставляем реверсный ордер <br>";

            // Текущая цена актива
            $pricenow = $this->GetPriceSide($this->symbol, "long");
            // Цена по которой нужно выставлять ордер
            $price = $ORD['price'] + $TREK['step'];
            // Цена при которой выставляем реверсный ордер
            $scoringprice = round($price - ($price/100)*0.01);


            echo "Ордер откупился по цене ".$ORD['price']."<br>";
            echo "Цена нашего выставления ".$price."<br>";
            echo "Цена при которой будем выставлять ордер".$scoringprice."<br>";
            echo "Текущая цена".$pricenow."<br>";

            // Вариант не ВЫСТАВЛЯТЬ ОРДЕР, пока цена не придет в коридор
            if ($pricenow > $scoringprice){
                // Не выставляем ордер пока цена не вернется в коридор

                echo "<b>Цена выше выставления скоринга</b><br>";
                return false;
            }

            $params = [
                'time_in_force' => "PostOnly",
                'reduce_only' => true
            ];

            $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit","sell", $ORD['amount'] , $price, $params);
            echo "Создали реверсный ордер <br>";

            return $order;
        }

    }

    public function CreateReNewOrder($TREK, $ORD){

        if ($TREK['side'] = "long"){
            $price = $ORD['price'] - $TREK['step'];

            // echo "Цена нашего выставления ".$price."<br>";
            // echo "Текущая цена".$this->GetPriceSide($this->symbol, "long")."<br>";
            // Tсли цена выставления ушла уже ВЫШЕ
//            if ($price < $this->GetPriceSide($this->symbol, "short")) {
//                $price = $this->GetPriceSide($this->symbol, "short");
//            }

            $params = [
                'time_in_force' => "PostOnly",
 //               'reduce_only' => true
            ];

            $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit","Buy", $ORD['amount'] , $price, $params);
            echo "Создали новый ордер после отработки <br>";

            return $order;
        }

    }

    public function OrderControl($order){

        if ($order['status'] == "open") return false;

        if ($order['amount'] == $order['filled']) return true;


    }

    public function DeleteOrderBD($ORD){
        echo "Удалили ордер из БД<br>";
        R::trash($ORD);
        return true;
    }

    public function GetOrderBook($symbol){
        $orderbook[$symbol] = $this->EXCHANGECCXT->fetch_order_book($symbol, 20);
        return $orderbook;

    }

    public function CalculatePriceOrders(){

        $allbal = $this->summazahoda * $this->leverege;

        $zahod = round($allbal/$this->CountOrders);

        if ($zahod < 60){
            echo "Размер захода на 1 ордер".$zahod."<br>";
            echo "Не хватает баланса на такое кол-во ордеров";
            exit();
        }

        foreach ($this->MASSORDERS as $key=>$val){
            $this->MASSORDERS[$key]['summazahoda'] = $zahod;
        }

        return true;
    }

    public function GenerateStepPrice($delta, $step){
        $MASS = [];
        for ($i = 0; $i < $this->CountOrders; $i++) {

            $MASS[]['price'] = $this->RangeL + $step*$i;

        }
        return $MASS;
    }

    public function SetLeverage($leverage){

        $this->EXCHANGECCXT->privateLinearGetPositionList([
                'symbol' => $this->esymbol,
                'leverage' => $leverage
            ]
        );

        return true;
    }



    public function GetBal(){
        $balance = $this->EXCHANGECCXT->fetch_balance();
        return $balance;
    }

    private function GetPriceSide($symbol, $side)
    {
        if ($side == "buy" || $side == "long") $price = $this->ORDERBOOK[$symbol]['bids'][0][0];
        if ($side == "sell" || $side == "short") $price = $this->ORDERBOOK[$symbol]['asks'][0][0];
        return $price;
    }

    private function GetQuantityBTC($summazahoda, $price){

        $quantity = $summazahoda/$price;

        $quantity = round($summazahoda/$price, 5);

        return $quantity;
    }

    private function EkranSymbol()
    {
        $newsymbol = str_replace("/", "", $this->symbol);
        return $newsymbol;
    }

    private function GetTreksBD()
    {
        $terk = R::findAll($this->namebdex, 'WHERE emailex =? ORDER by status', [$this->emailex]);
        return $terk;
    }

    private function GetOrdersBD($TREK)
    {
        $MASS = R::findAll("orders", 'WHERE idtrek =?', [$TREK['id']]);

        return $MASS;
    }

    private function AddARRinBD($ARR, $BD = false)
    {

        if ($BD == false) $BD = $this->namebdex;

        $tbl = R::dispense($BD);
        //ДОБАВЛЯЕМ В ТАБЛИЦУ

        foreach ($ARR as $name => $value) {
            $tbl->$name = $value;
        }

        $id = R::store($tbl);

        echo "<font color='green'><b>ДОБАВИЛИ ЗАПИСЬ В БД!</b></font><br>";

        return $id;


    }

    private function GetOrdersREST()
    {


        $orders = $this->EXCHANGECCXT->fetch_orders($this->symbol);

//        try
//        {
//            $orders = $this->EXCHANGECCXT->fetch_orders($this->symbol);
//        }
//        catch (Exception $e)
//        {
//            show($e);
//        }

        $MASS = [];
        foreach ($orders as $key=>$val){
            $MASS[$val['id']] = $val;
        }

      //  show($MASS);

        return $MASS;
    }


    private function GetOneOrderREST($id)
    {
        $order = $this->EXCHANGECCXT->fetchOrder($id,$this->symbol);
       // $MASS[$order['id']] = $order;
        return $order;

    }


    private function AddTrackHistoryBD($TREK, $ORD)
    {

        //$QTY = $this->GetEnterExitQTY($TREK, $ORD);
//        if ($TREK['side'] == "long") $delta = changemet($TREK['enter'], $ORD['p']);
//        if ($TREK['side'] == "short") $delta = changemet($ORD['p'], $TREK['enter']);
//        if ($TREK['action'] == "SellLimitFIX") $delta = $delta + 0.025;
//        if ($TREK['action'] == "SellMarket") $delta = $delta - 0.068;
//
//        if ($TREK['cashback'] == 1) $delta = $delta + 0.025;
//        if ($TREK['cashback'] == 0 || $TREK['cashback'] == NULL) $delta = $delta - 0.068;
//        $delta = $delta*$TREK['mrg'];

        if ($ORD['stat'] == 1){
            $delta = 0.025;
            $penter = $ORD['price'];
        }

        if ($ORD['stat'] == 2 && $TREK['side'] == "long"){
            $TREK['side'] = "short";
            $pexit = $ORD['price'] + $TREK['step'];
            $delta = changemet($ORD['price'], $pexit) + 0.025;

        }





        $MASS = [
            'trekid' => $TREK['id'],
            'side' => $TREK['side'],
            'orderid' => $ORD['id'],
            'statusorder' => $ORD['stat'],
            'timeexit' => date("H:i:s"),
            'delta' => $delta,
            'penter' => $ORD['price'],
        ];
        //ДОБАВЛЯЕМ В ТАБЛИЦУ
        $tbl3 = R::dispense("trekhistory");
        //ДОБАВЛЯЕМ В ТАБЛИЦУ

        //ДОБАВЛЯЕМ В ТАБЛИЦУ
        foreach ($MASS as $name => $value) {
            $tbl3->$name = $value;
        }
        R::store($tbl3);

        echo "Сохранили запись о сделке в БД <br>";
        return true;

    }



}
?>