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
    public $summazahoda = 18; // Сумма захода с оригинальным балансом
    public $leverege = 90;
    public $symbol = "BTC/USDT";
    public $emailex  = "raskrutkaweb@yandex.ru"; // Сумма захода USD
    public $namebdex = "treks";

    public $limTrek = 1;


    private $RangeH = 38000; // Верхняя граница коридора
    private $RangeL = 35500; // Нижняя граница коридора

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
    private $POSITIONBOOL = "";
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

        // Наличие открытых позиций.
        $this->POSITIONBOOL = $this->GetPosition();

        echo "<b>Наличие позиции</b><br>";
        var_dump($this->POSITIONBOOL);

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
            if ($row['work'] == 1) {
                echo "Скрипт в работе. Пропускаем цикл<br>";
                continue;
            }
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

        $pricenow = $this->GetPriceSide($this->symbol, "long");

        echo "Текущая цена".$pricenow."<br>";


//        if ($this->RangeH > $pricenow && $this->TypeGird == "long"){
//            echo "Сетка в лонг. Текущая цена должна быть выше верхней границы сетки<br>";
//            echo "Текущая цена =  ".$pricenow." <br>";
//            echo "Верхняя планка захода = ".$this->RangeH." <br>";
//            return false;
//        }
//        echo "<hr>";


        echo "Рассчет ордеров<br>";

        if ($this->RangeL > $this->RangeH){
            echo "НЕ КОРРЕКТНЫЕ ПАРАМЕТРЫ RANGEH и RANGEL";
            exit();
        }

        $delta = ($this->RangeH) - ($this->RangeL);
        $this->step = $delta/$this->CountOrders;

        if ($this->BALANCE < $this->summazahoda){
            echo "НЕ ХВАТАЕТ БАЛАНСА";
            exit;
        }




        // РАСЧЕТ ШАГОВ
        $this->MASSORDERS = $this->GenerateStepPrice($delta, $this->step);
        // Добавляем в массив ордеров сумму захода
        $this->CalculatePriceOrders();
        // Дополняем массив ордеров детальными значениями (сторона и quantity)
        $this->AddMassOrders();



        // Выставляем ордера физически
        foreach ($this->MASSORDERS as $key=>$val){
            $params = [
                'time_in_force' => "PostOnly",
//                'reduce_only' => true
            ];


            // Проверка цены выставляемого ордера и текущей цены
            // Если цена не попадает, то удаляем ордер из массива и делаем continue
            $ResultCheck = $this->CheckValidateOrderFirst($val['side'], $pricenow, $val['price']);

            if ($ResultCheck === FALSE){
                //unset($this->MASSORDERS[$key]);
                continue;
            }

            $sideorder = $this->GetTextSide($val['side']);
            $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$sideorder, $val['quantity'], $val['price'], $params);
            $this->MASSORDERS[$key]['order'] = $order;
            echo "ОРДЕР ВЫСТАВЛЕН<br>";
        }

        // Добавление ТРЕКА в БД
        $avg = ($this->RangeL + $this->RangeH)/2;
        $avg = round($avg);

        $ARR['emailex'] = $this->emailex;
        $ARR['status'] = 1;
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

            if (empty($val['order']['id'])) $val['order']['id'] = NULL;
            if (empty($val['order']['id'])) $val['order']['status'] = NULL;
            if (empty($val['order']['id'])) $val['order']['type'] = NULL;
            if (empty($val['order']['id'])) $val['order']['amount'] = NULL;

            $ARR = [];
            $ARR['idtrek'] = $idtrek;
            $ARR['stat'] = 1;
            $ARR['orderid'] = $val['order']['id'];
            $ARR['status'] = $val['order']['status'];
            $ARR['type'] = $val['order']['type'];
            $ARR['side'] = $val['side'];
            $ARR['amount'] = $val['order']['amount'];
            $ARR['price'] = $val['price'];

            $this->AddARRinBD($ARR, "orders");

        }

        // Добавление ордеров в БД
        return true;

    }

    private function WorkStatus1($TREK)
    {

        $OrdersBD = $this->GetOrdersBD($TREK);


        // Определение в каком диапозоне неходиться цена
            // Определяем в каком диапозоне цена.
            // Определяем есть ли ордер выставленный ордер
            // Если ордер не выставлен, то выставляем

        //


        //show($OrdersBD);


        foreach ($OrdersBD as $key=>$OrderBD){
            echo "<hr>";

            // ПРОВЕРЯЕМ ОРДЕР НА НАЛИЧИЕ
            // ЕСЛИ ЕГО НЕТ, ТО ВЫСТАВЛЯЕМ
            echo "#".$OrderBD['id']." СТАТУС ОРДЕРА <b>".$OrderBD['stat']."</b> - ".$OrderBD['orderid']." - <b>".$OrderBD['side']."</b> <br>";

            $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);

            if ($OrderBD['orderid'] == NULL){
                echo "<font color='#8b0000'>Ордер НЕ существует! </font>  <br>";
                echo "Будем создавать новый если проходим по скорингу   <br>";
                continue;
            }

             // Если отменен из-за POST-ONLY
            if ($OrderREST['status'] == "canceled"){
                echo "<font color='#8b0000'>ОРДЕР отменен (canceled)!!! </font> <br>";

                show($OrderREST);

                echo "Дублируем выставление ордера!   <br>";
                continue;
            }


            echo "Информация об ордере из REST<br>";


            if ($this->OrderControl($OrderREST) === FALSE){
                echo "ОРДЕР не откупился <br>";
                continue;
            }


//            show($OrderREST);
//            echo "<hr>";
//            show($OrderBD);


            if ($OrderBD['side'] == "long"){


                //  Валидация выставления ордера
                echo "Выставляем реверсный ордер LONG (противополжный SHORT). Нужно выставить ВЫШЕ цены текущей <br>";
                // Должны выставить ордер, ШОРТ и цена должна быть ниже текущей
                $pricenow = $this->GetPriceSide($this->symbol, "long");
                // Цена по которой нужно выставлять ордер
                $price = $OrderREST['price'] + $TREK['step'];

                // Цена при которой выставляем реверсный ордер
                $scoringprice = round($price - ($price/100)*0.01);

                echo "Ордер откупился по цене ".$OrderREST['price']."<br>";
                echo "Цена нашего выставления ".$price."<br>";
                echo "Цена при которой сможем выставлять ордер - ".$scoringprice."<br>";
                echo "Текущая цена - ".$pricenow."<br>";




                if ($pricenow > $scoringprice){
                    // Не выставляем ордер пока цена не вернется в коридор
                    echo "<b>Цена выставления не прошла скоринга</b><br>";
                    continue;
                }

                // Если ордер перевыствляется, то он идет на наращиване позиции
                if ($OrderBD['stat'] == 2) $this->POSITIONBOOL = false;
                $params = [
                    'time_in_force' => "PostOnly",
                    'reduce_only' => $this->POSITIONBOOL,
                ];

                $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit","sell", $OrderREST['amount'] , $price, $params);
                echo "<font color='#8b0000'>Создали реверсный ордер </font><br>";


                $this->AddTrackHistoryBD($TREK, $OrderBD);

                $this->DeleteOrderBD($OrderBD);

                // Добавляем противоположному ордеру корректное название для БД
                 $order['side'] = "short";

                 if ($OrderBD['stat'] == 1) $stat = 2;
                 if ($OrderBD['stat'] == 2) $stat = 1;


                // Запись реверсного ордера в БД
                $ARR = [];
                $ARR['idtrek'] = $TREK['id'];
                $ARR['orderid'] = $order['id'];
                $ARR['status'] = $order['status'];
                $ARR['stat'] = $stat;
                $ARR['type'] = $order['type'];
                $ARR['side'] = $order['side'];
                $ARR['amount'] = $order['amount'];
                $ARR['price'] = $order['price'];

                $this->AddARRinBD($ARR, "orders");

                continue;


            }



            if ($OrderBD['side'] == "short"){

                //  Валидация выставления ордера
                echo "Выставляем реверсный ордер SHORT (противополжный LONG). Нужно выставить НИЖЕ цены текущей <br>";
                // Должны выставить ордер, ШОРТ и цена должна быть ниже текущей
                $pricenow = $this->GetPriceSide($this->symbol, "short");
                // Цена по которой нужно выставлять ордер
                $price = $OrderREST['price'] - $TREK['step'];

                // Цена при которой выставляем реверсный ордер
                $scoringprice = round($price + ($price/100)*0.01);

                echo "Ордер откупился по цене ".$OrderREST['price']."<br>";
                echo "Цена нашего выставления ".$price."<br>";
                echo "Цена при которой сможем выставлять ордер - ".$scoringprice."<br>";
                echo "Текущая цена - ".$pricenow."<br>";


                if ($pricenow < $scoringprice){
                    // Не выставляем ордер пока цена не вернется в коридор
                    echo "<b>Текущая цена НИЖЕ выставления. Не прошла скоринг</b><br>";
                    continue;
                }

                // Если ордер перевыствляется, то он идет на наращиване позиции
                if ($OrderBD['stat'] == 2) $this->POSITIONBOOL = false;
                $params = [
                    'time_in_force' => "PostOnly",
                    'reduce_only' => $this->POSITIONBOOL,
                ];

                $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit","buy", $OrderREST['amount'] , $price, $params);
                echo "<font color='#8b0000'>Создали реверсный ордер </font><br>";


                $this->AddTrackHistoryBD($TREK, $OrderBD);

                $this->DeleteOrderBD($OrderBD);

                // Добавляем противоположному ордеру корректное название для БД
                $order['side'] = "long";

                if ($OrderBD['stat'] == 1) $stat = 2;
                if ($OrderBD['stat'] == 2) $stat = 1;


                // Запись реверсного ордера в БД
                $ARR = [];
                $ARR['idtrek'] = $TREK['id'];
                $ARR['orderid'] = $order['id'];
                $ARR['status'] = $order['status'];
                $ARR['stat'] = $stat;
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

    // РАБОЧИЕ ФУНКЦИИ
    public function CheckValidateOrderFirst($sideorder, $pricenow, $priceorder){
        echo "Сторона выставления ордера ".$sideorder."<br>";
        echo "Текущая цена ".$pricenow."<br>";
        echo "Цена выставления ордера ".$priceorder."<br>";

        $result = true;
        if ($sideorder == "long" && $priceorder > $pricenow ) $result = false;
        if ($sideorder == "short" && $priceorder < $pricenow ) $result = false;

        echo "Валидация на выставление <br>".$result."";
        echo "<hr>";

        return $result;


    }

    public function ReCreaterOrder($OrderREST, $OrderBD){

        $params = [
            'time_in_force' => "PostOnly",
            'reduce_only' => $this->POSITIONBOOL,
        ];


        $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$OrderREST['side'], $OrderREST['amount'] , $OrderREST['price'], $params);
        echo "Перевыставили отказной ордер <br>";
        return $order;






    }



    private function GetPosition(){

        //show($this->FULLBALANCE['info']['result']);

        foreach ($this->FULLBALANCE['info']['result'] as $k=>$val){
            if ($val['position_margin'] != 0) return true;
        }
        return false;



    }

    private function GlobalPosition(){
        $pricenow = $this->GetPriceSide($this->symbol, "long");
        if ($pricenow > $this->RangeH) return "HIGH";
        if ($pricenow < $this->RangeH) return "LOW";
        return "NORMAL";


    }




    public function GetTextSide($textside){
        if ($textside == "long") $sideorder = "Buy";
        if ($textside == "short") $sideorder = "sell";
        return $sideorder;
    }

    public function GetFirstSide($key){
        $side = "short";
        if ( $key+1 <= $this->CountOrders/2 ) $side = "long";


        return $side;
    }


    public function OrderControl($order){

        if ($order['status'] == "open") return false;

        if ($order['amount'] == $order['filled']) return true;


    }


    public function ChangeIDOrderBD($ORD){
        echo "Сменили ID ордера<br>";
        $ordbd = R::load("orders", $ORD['id']);
        $ordbd->orderid = $ORD['id'];
        R::load($ordbd);
        return true;
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

    public function AddMassOrders(){

        foreach ($this->MASSORDERS as $key=>$val){

            $quantity = $this->GetQuantityBTC($val['summazahoda'] , $val['price']);
            $side = $this->GetFirstSide($key);

            $this->MASSORDERS[$key]['side'] = $side;
            $this->MASSORDERS[$key]['quantity'] = $quantity;
        }


        return true;
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

        if ($ORD['stat'] == 2 && $ORD['side'] == "long"){
            $pexit = $ORD['price'] + $TREK['step'];
            $delta = changemet($ORD['price'], $pexit) + 0.025;
        }


        if ($ORD['stat'] == 2 && $ORD['side'] == "short"){
            $pexit = $ORD['price'] - $TREK['step'];
            $delta = changemet($pexit, $ORD['price']) + 0.025;
        }



        $MASS = [
            'trekid' => $TREK['id'],
            'side' => $ORD['side'],
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


}
?>