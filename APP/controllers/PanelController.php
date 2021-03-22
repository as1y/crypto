<?php
namespace APP\controllers;
use APP\core\Cache;
use APP\models\Addp;
use APP\models\Panel;
use APP\core\base\Model;
use RedBeanPHP\R;

class PanelController extends AppController {
	public $layaout = 'PANEL';
    public $BreadcrumbsControllerLabel = "Панель управления";
    public $BreadcrumbsControllerUrl = "/panel";


    public $ApiKey = "gTfMVyL2g8VKtb4JpfyzKxMOADMrXyFPJVeSinLxeK0mPj5F0rb3EQNa5nXgbbN4";
    public $SecretKey = "5ZSFHkvCi3bWspMNOgqeF74XF0KMbIM5E7O79M83kXkpiAhNori4wDA6GX2w3I3i";

    // Переменные для стратегии
    public $summazahoda = 40; // Сумма захода с оригинальным балансом
    public $leverege = 30;
    public $Exhcnage1 = "binance";
    public $symbol = "BTC/USDT";
    public $emailex  = "as1y@yandex.ru"; // Сумма захода USD
    public $namebdex = "treks";

    public $limTrek = 1;


    private $TypeGird = "long";
    private $RangeH = 57100;
    private $RangeL = 56100;
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
    private $OrdersRest = [];
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
        $this->EXCHANGECCXT = new \ccxt\binance (array(
            'apiKey' => $this->ApiKey,
            'secret' => $this->SecretKey,
            'timeout' => 30000,
            'enableRateLimit' => true,
            'options' => array('defaultType' => "future")
        ));


        $this->esymbol = $this->EkranSymbol();

        $this->FULLBALANCE = $this->GetBal();
        $this->BALANCE = $this->FULLBALANCE['info']['assets']['0']['availableBalance'];

        $this->OrdersRest = $this->GetOrdersREST();

        $this->ORDERBOOK = $this->GetOrderBook($this->symbol);

        // РАСЧЕТ ОРДЕРОВ

        $this->work();


//        $this->set(compact(''));

    }


    public function work(){

        echo "<h1>HUYA</h1>";
        $Panel = new Panel();

        $TREK = $this->GetTreksBD();

        foreach ($TREK as $key => $row) {
            $this->WORKTREKS[] = $row['symbol'];

            echo "<h2>СИМВОЛ: " . $row['symbol'] . " - STATUS - " . $row['status'] . " | " . $row['side'] . " | " . $row['id'] . "   </h2>";
            $f = 'WorkStatus' . $row['status'];
            $this->$f($row);
        }


        // Логирование запусков

        sleep("2");
        $this->LogZapuskov($TREK);
        if (count($TREK) < $this->limTrek) $this->AddTrek();


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
            $order = $this->EXCHANGECCXT->create_order($this->symbol,"LIMIT","BUY", $val['quantity'] , $val['price']);
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
            $ARR = [];
            $ARR['idtrek'] = $idtrek;
            $ARR['stat'] = 1;
            $ARR['orderid'] = $val['order']['id'];
            $ARR['status'] = $val['order']['status'];
            $ARR['type'] = $val['order']['type'];
            $ARR['side'] = $val['order']['side'];
            $ARR['amount'] = $val['order']['amount'];
            $ARR['price'] = $val['order']['price'];

            $this->AddARRinBD($ARR, "orders");

        }

        // Добавление ордеров в БД
        return true;

    }


    private function LogZapuskov($TREK){
        if (empty($TREK['id'])) $TREK['id'] = NULL;

        $ARR['stamp'] = date("H:i:s");
        $ARR['trekid'] = $TREK['id'];

        $idtrek = $this->AddARRinBD($ARR, "logrun");

        return true;
    }


    private function WorkStatus1($TREK)
    {


        echo "<h1>ВОРКСТАТУС</h1>";

        $timeposition = $this->CalculateHoldMin($TREK['stamp']);
        echo "<b>Время позиции:</b> $timeposition min <br>";


        $OrdersBD = $this->GetOrdersBD($TREK);
//        show($ORDERS);






        // ПРОВЕРКА ВЫСТАВЛЕННЫХ ОРДЕРОВ. ЗАЩИТА ОТ СБОЕВ В РЕСТ
        echo "Защита синхронихации<br>";
        foreach ($OrdersBD as $key=>$OrderBD){
            // $ORDER['orderid']- Ордера из наших баз данных
            // $this->TRADES - Трейды из REST
          //  echo $OrderBD['orderid']." - ID ордера в нашей БД <br>";
            if (!array_key_exists($OrderBD['orderid'], $this->OrdersRest)){
                         show($this->OrdersRest);
                echo "ОШИБКА!!! Ордер находиться в базе REST <br>";
                show($this->OrdersRest[$OrderBD['orderid']]);
                exit();
            }
        }


        foreach ($OrdersBD as $key=>$OrderBD){


            echo "<hr>";

            echo "СТАТУС ОРДЕРА".$OrderBD['orderid']."<br>";

            echo $this->OrdersRest[$OrderBD['orderid']]['status']."<br>";



            echo "Информация об ордере из BD<br>";
//            show($OrderBD);

            echo "Информация об ордере из REST<br>";
//            show($this->OrdersRest[$OrderBD['orderid']]);


            if ($this->OrderControl($this->OrdersRest[$OrderBD['orderid']]) === FALSE) continue;


            // Если откупились на первый статус
            if ($OrderBD['stat'] == 1){
                // Добавление сделки в БД
                $this->AddTrackHistoryBD($TREK, $OrderBD);
                // Удаление текущего ордера из БД
                $this->DeleteOrderBD($OrderBD);

                // Создание реверсного ордера
                $order = $this->CreateReversOrder($TREK, $OrderBD);


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
            $price = $ORD['price'] + $TREK['step'];

           // echo "Цена нашего выставления ".$price."<br>";
           // echo "Текущая цена".$this->GetPriceSide($this->symbol, "long")."<br>";

           // Tсли цена выставления ушла уже ВЫШЕ
            if ($price < $this->GetPriceSide($this->symbol, "short")) {
                $price = $this->GetPriceSide($this->symbol, "short");
            }


            $order = $this->EXCHANGECCXT->create_order($this->symbol,"LIMIT","SELL", $ORD['amount'] , $price);
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


            $order = $this->EXCHANGECCXT->create_order($this->symbol,"LIMIT","BUY", $ORD['amount'] , $price);
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
        $this->EXCHANGECCXT->fapiPrivatePostLeverage([
                'symbol' => $this->esymbol,
                'leverage' => $leverage
            ]
        );
        return true;
    }

    private function CalculateHoldMin($time)
    {

        $hodltime = time() - $time;
        $second = $hodltime;


        $minut = $second / 60;
        $minut = round($minut, 2);
        //СЧИТАЕМ СКОЛЬКО ХОЛДА

        //$days = $minut / 1440;

        return $minut;
    }

    private function Looking4Position($symbol)
    {

        $POSITION = $this->GetPosition($symbol);

        $POSITION['bid'] = $this->ORDERBOOK[$symbol]['bids'][0][0];
        $POSITION['ask'] = $this->ORDERBOOK[$symbol]['asks'][0][0];


        return $POSITION;
    }

    private function GetARRPOS($POSITION, $TREK)
    {

        $ARRPOS['quantity'] = abs($POSITION['positionAmt']);
        $ARRPOS['enter'] = $POSITION['entryPrice'];
        $ARRPOS['bid'] = $POSITION['bid'];
        $ARRPOS['ask'] = $POSITION['ask'];

        $ARRPOS['side'] = ($POSITION['positionSide'] == "BOTH") ? "long" : "short";
        $ARRPOS['orderside'] = ($ARRPOS['side'] == "long") ? "sell" : "buy";
        $ARRPOS['orderactualprice'] = ($ARRPOS['side'] == "long") ? $POSITION['ask'] : $POSITION['bid'];


        $ARRPOS['raznica'] = $this->GetRaznica($ARRPOS, $TREK);




        return $ARRPOS;
    }

    private function GetPosition($symbol){
        $symbol = $this->EkranSymbol($symbol);
//        show($symbol);
//        show($this->FULLBALANCE['info']['positions']);
        foreach ($this->FULLBALANCE['info']['positions'] as $val){
            if ($val['symbol'] == $symbol && $val['initialMargin'] != 0){

                $POSTION = $val;
                return $POSTION;

            }
        }

        return false;
    }

    private function GetRaznica($ARRPOS, $TREK)
    {

        if ($ARRPOS['side'] == "long") $raznica = changemet($TREK['enter'], $ARRPOS['ask']);
        if ($ARRPOS['side'] == "short") $raznica = changemet($ARRPOS['bid'], $TREK['enter']);

        return $raznica;
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
        $orders = $this->EXCHANGECCXT->fetchOrders($this->symbol, NULL, 200);
        $MASS = [];
        foreach ($orders as $key=>$val){
            $MASS[$val['id']] = $val;
        }
        return $MASS;
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

        echo "Добавили трек в БД <br>";
        return true;

    }



}
?>