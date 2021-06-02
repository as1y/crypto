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
    public $summazahoda = 15; // Сумма захода с оригинальным балансом
    public $leverege = 90;
    public $symbol = "BTC/USDT";
    public $emailex  = "raskrutkaweb@yandex.ru"; // Сумма захода USD
    public $namebdex = "treks";

    public $limTrek = 1;

    private $RangeH = 36750; // Верхняя граница коридора
    private $RangeL = 36000; // Нижняя граница коридора

    private $CountOrders = 10; // Кол-во ордеров на одну позицию

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
    private $BOOST = 0;

    // Переменные для стратегии
    private $minst = 0.1; // Коэфицент минимального шага
    private $skolz = 10; // Допустимый процент проскальзования


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

      //  echo "<b>Наличие позиции (BOOL)</b><br>";
      //  var_dump($this->POSITIONBOOL);


        // РАСЧЕТ ОРДЕРОВ
        $this->work();


//        $this->set(compact(''));

    }

    public function work(){

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
            $timework = time() - $row['stamp'];

            $minute = $timework/60;

            echo "Время работы скрипта в минутах ".$minute."<br>";

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
        $startprice = round($pricenow);


        echo "Рассчет ордеров<br>";

        if ($this->RangeL > $this->RangeH){
            echo "НЕ КОРРЕКТНЫЕ ПАРАМЕТРЫ RANGEH и RANGEL";
            exit();
        }

        $minimumstep = ($pricenow/100)*$this->minst;
        $minimumstep = round($minimumstep);

            // Проверка на минимальный шаг
          echo "Минимальный шаг: ".$minimumstep."<br>";

          // Рассчет среднего
            $avg = ($this->RangeL + $this->RangeH)/2;
            $avg = round($avg);

        $delta = ($this->RangeH) - ($this->RangeL);
        $delta = round($delta/2);

           $this->step = $delta/$this->CountOrders;

           echo "Текущий шаг цены:".$this->step."<br>";

        if ($minimumstep > $this->step){
            echo "Увеличте коридор. Текущий шаг цены расстановки ордеров слишком мал <br>";
            exit();
        }
        // Проверка на минимальный шаг


        if ($this->BALANCE < $this->summazahoda){
            echo "НЕ ХВАТАЕТ БАЛАНСА";
            exit;
        }


        // РАСЧЕТ ШАГОВ
        $this->MASSORDERS = $this->GenerateStepPrice($avg);

        // Добавляем в массив ордеров сумму захода
        $this->CalculatePriceOrders();

        // Дополняем массив ордеров детальными значениями (сторона и quantity)
        $this->AddMassOrders();


        // Добавление ТРЕКА в БД

        $ARR['emailex'] = $this->emailex;
        $ARR['status'] = 1;
        $ARR['action'] = "ControlOrders";
        $ARR['boost'] = 0;
        $ARR['symbol'] = $this->symbol;
        $ARR['lever'] = $this->leverege;
        $ARR['count'] = $this->CountOrders;
        $ARR['rangeh'] = $this->RangeH;
        $ARR['rangel'] = $this->RangeL;
        $ARR['step'] = $this->step;
        $ARR['avg'] = $avg;
        $ARR['startbalance'] = $this->BALANCE;
        $ARR['date'] = date("h:i:s");
        $ARR['stamp'] = time();

        $idtrek = $this->AddARRinBD($ARR);
        echo "<b><font color='green'>ДОБАВИЛИ ТРЕК</font></b>";
        // Добавление ТРЕКА в БД


        // Добавление ордеров в БД
        foreach ($this->MASSORDERS['long'] as $key=>$val){
            $ARR = [];
            $ARR['idtrek'] = $idtrek;
            $ARR['stat'] = 1;
            $ARR['side'] = "long";
            $ARR['amount'] = $val['quantity'];
            $ARR['price'] = $val['price'];
            $ARR['first'] = 1; // Когда ордер выставляем первый раз
            $this->AddARRinBD($ARR, "orders");
        }
        foreach ($this->MASSORDERS['short'] as $key=>$val){
            $ARR = [];
            $ARR['idtrek'] = $idtrek;
            $ARR['stat'] = 1;
            $ARR['side'] = "short";
            $ARR['amount'] = $val['quantity'];
            $ARR['price'] = $val['price'];
            $ARR['first'] = 1; // Когда ордер выставляем первый раз
            $this->AddARRinBD($ARR, "orders");
        }


        // Добавление ордеров в БД
        return true;

    }

    // Статус 1 - когда находимся в КОРИДОРЕ
    private function WorkStatus1($TREK)
    {

        $pricenow = $this->GetPriceSide($this->symbol, "long");
        $WorkSide = $this->GetWorkSide($pricenow, $TREK);
        show($WorkSide);

        if ($TREK['workside'] != $WorkSide){
            // На случай смены активной стороны
            $TREK['workside'] = $WorkSide;

            $ARRTREK['workside'] = $WorkSide;
            $this->ChangeARRinBD($ARRTREK, $TREK['id']);
            // На случай смены активной стороны
        }

        // Вывод рабочей информации
        echo "<h3>Базовые параметры</h3>";
        echo "<b>BOOST:</b>".$TREK['boost']."<br>";
        echo "<b>Верхняя граница коридора:</b>".$TREK['rangeh']."<br>";
        echo "<b>Нижняя граница коридора:</b>".$TREK['rangel']."<br>";

        echo "<hr>";


        if ($WorkSide == "PLATO"){
            echo "<h2>Цена находиться в плато</h2><br>";
            echo "Текущая цена: <b>".$pricenow."</b><br>";
            return true;
        }
        // Определение в какой зоне работаем



        // Определение цены


        // Статус ордера - ОРДЕРА на НАРАЩИВАНИЕ ПОЗИЦИИ
        if ($TREK['action'] == "ControlOrders") $this->ActionControlOrders($TREK, $pricenow);
        // Ордера на фиксирование позиции

        // Контроллер ситуации

        return true;

    }

    // Статус 2 - Вышли из коридора
    private function WorkStatus2($TREK){

        echo "<h1><font color='#8b0000'>СТАТУС 2</font></h1>";

    }



    private function ActionControlOrders($TREK, $pricenow){

        echo  "<b>Запускаем Action ControlOrders. Контролируем работу ордеров</b> <br>";
        $pricenow = round($pricenow);
        $OrdersBD = $this->GetOrdersBD($TREK);

        foreach ($OrdersBD as $key=>$OrderBD) {

            echo "<hr>";
            echo "#".$OrderBD['id']." СТАТУС ОРДЕРА <b>".$OrderBD['stat']."</b> - ".$OrderBD['orderid']." - <b>".$OrderBD['side']."</b> <br>";


            // Ордер на наращивание позиции
            if ($OrderBD['stat'] == 1) {

                    // Выставление первых ордеров
                    if ($OrderBD['orderid'] == NULL){
                        echo  "Проверка откупа первоначального ордера <br>";

                        echo "Текущая цена".$pricenow."<br>";
                        echo "Цена для выставления ордера".$OrderBD['price']."<br>";

                        // Скоринг на выставление
                        $resultscoring =  $this->CheckFirstOrder($TREK, $pricenow, $OrderBD);

                        echo "Откупаем ордер по типу:<br>";
                        var_dump($resultscoring);
                        if ($resultscoring === FALSE) continue;

                        $order = $this->CreateFirstOrder($OrderBD, $resultscoring, $TREK);
                        // Записываем
                        $this->ChangeIDOrderBD($order, $OrderBD['id']);

                        continue;
                        // Проверяем на скоринг необходимости выставления ордера по маркету
                    }



                echo "Информация об ордере из REST<br>";
                $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);

                if ($OrderREST['status'] == "canceled"){
                    echo "<font color='#8b0000'>ОРДЕР отменен (canceled)!!! </font> <br>";
                    show($OrderREST);
                    echo "Обнуляем ID ордера!  <br>";
                    $order['id'] = NULL;
                    $this->ChangeIDOrderBD($order, $OrderBD['id']);
                    continue;
                }


                if ($this->OrderControl($OrderREST) === FALSE){
                    echo "ОРДЕР не откупился <br>";
                    continue;
                }

                // Ордер откупился! Что делаем дальше

                    // Цена по которой нужно выставлять ордер
                    $pricenow = $this->GetPriceSide($this->symbol, $OrderBD['side']);
                    if ($OrderBD['side'] == "long")  $price = $OrderBD['price'] + $TREK['step'];
                    if ($OrderBD['side'] == "short")  $price = $OrderBD['price'] - $TREK['step'];
                    echo "<font color='green'>Ордер откупился по цене</font> ".$OrderREST['price']."<br>";
                    echo "Цена нашего выставления ".$price."<br>";
                    echo "Текущая цена - ".$pricenow."<br>";


                    // ВЫСТАВЛЕНИЕ РЕВЕРСНОГО ОРДЕРА
                    // Если текущая цены выше цены которой мы планировали выставлять
                    $order = $this->CreateReverseOrder($pricenow, $price, $OrderREST, $OrderBD);

                    $this->AddTrackHistoryBD($TREK, $OrderBD);

                    $ARRCHANGE = [];
                    $ARRCHANGE['stat'] = 2;
                    $ARRCHANGE['orderid'] = $order['id'];
                    $ARRCHANGE['amount'] = $order['amount'];
                    $ARRCHANGE['first'] = 0;
                    $this->ChangeARRinBD($ARRCHANGE, $OrderBD['id'], "orders");


                    continue;




            }

            if ($OrderBD['stat'] == 2){

                echo "<b>Работа СТАТУС 2</b><br>";
                echo "Информация об ордере из REST<br>";
                $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);

                // Проверка на cancel (перевыставление)
                if ($OrderREST['status'] == "canceled"){
                    echo "<font color='#8b0000'>ОРДЕР отменен (canceled)!!! </font> <br>";
                    show($OrderREST);

                    $pricenow = $this->GetPriceSide($this->symbol, $OrderBD['side']);


                    if ($OrderBD['side'] == "long")  $price = $OrderBD['price'] + $TREK['step'];
                    if ($OrderBD['side'] == "short")  $price = $OrderBD['price'] - $TREK['step'];

                    echo "Текущая цена: ".$pricenow."<br>";
                    echo "Мы выставляем ордера по цене: ".$price."<br>";
                    exit("gfdg");


                    echo "Перевыставляем ордер на 2-м статусе! Обнуляем ID ордера!  <br>";
                   $order =  $this->CreateReverseOrder($pricenow, $price, $OrderREST, $OrderBD);
                    $this->ChangeIDOrderBD($order, $OrderBD['id']);
                    continue;
                }


                // Проверка на исоплненность
                if ($this->OrderControl($OrderREST) === FALSE){
                    echo "ОРДЕР не откупился <br>";
                    continue;
                }


                // ОРДЕР ИСПОЛНЕН

                $this->AddTrackHistoryBD($TREK, $OrderBD);

                $ARRCHANGE = [];
                $ARRCHANGE['stat'] = 1;
                $ARRCHANGE['orderid'] = NULL;
                $ARRCHANGE['first'] = 0;
                $this->ChangeARRinBD($ARRCHANGE, $OrderBD['id'], "orders");





            }




        }

            // Если откупились на первый первый статус (откупился первый раз после выставления)





        return true;

    }

    private function CheckFirstOrder($TREK, $pricenow, $OrderBD){

        // Если цена выше цены выставления +  от шага 10%, то выставляем лимитник
        // Если цена ниже цены выставления + от шага 10%, то ничего не делаем
        // Если цена в радиусе цены выставления + 10% от шага, то выставляем берем рынком


        if ($TREK['workside'] == "LONG"){
            echo "Сторона скоринга LONG<br>";
            $PercSTEP = ($TREK['step']/100)*$this->skolz;
            $PercSTEP = round($PercSTEP);
            $PercSTEP = $OrderBD['price'] + $PercSTEP;
            echo "Цена + ШАГ".$PercSTEP."<br>";

            if ($pricenow > $PercSTEP) return "LIMIT";
            if ($pricenow > $OrderBD['price'] && $pricenow < $PercSTEP && $OrderBD['first'] == 1) return "MARKET";


        }


        if ($TREK['workside'] == "SHORT"){
            echo "Сторона скоринга SHORT<br>";
            $PercSTEP = ($TREK['step']/100)*$this->skolz;
            $PercSTEP = round($PercSTEP);
            $PercSTEP = $OrderBD['price'] - $PercSTEP;

            echo "Цена с ШАГ".$PercSTEP."<br>";

            if ($pricenow < $PercSTEP) return "LIMIT";
            if ($pricenow < $OrderBD['price'] && $pricenow > $PercSTEP && $OrderBD['first'] == 1) return "MARKET";

        }

            echo "Цена не корректна для выставления ордеров в данном коридоре";
            return false;

    }

    private function GetWorkSide($pricenow, $TREK){

        $dop = round($TREK['step']/2);
        echo "Цена выхода в активную сетку LONG:".($TREK['avg'] + $dop)."<br>";
        echo "Цена выхода в активную сетку SHORT:".($TREK['avg'] - $dop)."<br>";


        if ($pricenow > $TREK['avg'] + $dop ) return "LONG";


        if ($pricenow < $TREK['avg'] - $dop) return "SHORT";


        if ( ($pricenow < $TREK['avg'] + $dop) && ($pricenow > $TREK['avg'] - $dop)) return "PLATO";

    }



    private function CreateReverseOrder($pricenow, $price, $OrderREST, $OrderBD){


        $params = [
            'time_in_force' => "PostOnly",
            'reduce_only' => true,
        ];

        if ($OrderBD['side'] == "long") {
            if ($pricenow > $price) $price = $pricenow;
            $side = "sell";
        }

        if ($OrderBD['side'] == "short") {
            if ($pricenow < $price) $price = $pricenow;
            $side = "buy";
        }


        $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$side, $OrderREST['amount'] , $price, $params);
        echo "<font color='#8b0000'>Создали реверсный ордер </font><br>";


            return $order;


    }

    private function CreateFirstOrder($OrderBD, $type, $TREK){


        $sideorder = $this->GetTextSide($OrderBD['side']);
        show($sideorder);
        var_dump($OrderBD['amount']);
        show($OrderBD['price']);

        // Проверка на BOOST
        if ($TREK['boost'] == 1) {
            $OrderBD['amount'] = $OrderBD['amount']*2;
        }
        // Проверка на BOOST

        if ($type == "LIMIT"){
            $params = [
                'time_in_force' => "PostOnly",
                'reduce_only' => false,
            ];
            $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$sideorder, $OrderBD['amount'], $OrderBD['price'], $params);
            return $order;

        }


        if ($type == "MARKET"){
            $order = $this->EXCHANGECCXT->create_order($this->symbol,"market",$sideorder, $OrderBD['amount']);
            return $order;
        }



        return false;


    }


    public function LookHPosition(){

        $POSITIONS = $this->EXCHANGECCXT->fetch_positions([$this->symbol]);

        $POSITIONS[0]['sidecode'] = "long";
        $POSITIONS[1]['sidecode'] = "short";

        // 0 - Позиция в BUY
        // 1 - Позиция в SELL


        if ($POSITIONS[0]['unrealised_pnl'] < $POSITIONS[1]['unrealised_pnl']){
            $POS['minuspos'] = $POSITIONS[0];
            $POS['pluspos'] = $POSITIONS[1];
        }

        if ($POSITIONS[1]['unrealised_pnl'] < $POSITIONS[0]['unrealised_pnl']){
            $POS['minuspos'] = $POSITIONS[1];
            $POS['pluspos'] = $POSITIONS[0];
        }


        return $POS;

    }

    public function LookingSpacePositions($CONTRPOSTION){

        $RES = [];

        $POSITIONS = $this->EXCHANGECCXT->fetch_positions([$this->symbol]);
        // 0 - Позиция в BUY
        // 1 - Позиция в SELL

        $POSITIONS[0]['sidecode'] = "long";
        $POSITIONS[1]['sidecode'] = "short";

        if ($CONTRPOSTION['side'] == "long"){
            $RES['raketa'] = $POSITIONS[0];
            $RES['koridor'] = $POSITIONS[1];
        }

        if ($CONTRPOSTION['side'] == "short"){
            $RES['raketa'] = $POSITIONS[1];
            $RES['koridor'] = $POSITIONS[0];
        }


        return $RES;


    }




    private function GetPosition(){

        //show($this->FULLBALANCE['info']['result']);

        foreach ($this->FULLBALANCE['info']['result'] as $k=>$val){
            if ($val['position_margin'] != 0) return true;
        }
        return false;



    }




    public function GetTextSide($textside){
        if ($textside == "long") $sideorder = "Buy";
        if ($textside == "short") $sideorder = "sell";
        return $sideorder;
    }




    public function OrderControl($order){

        if ($order['status'] == "open") return false;

        if ($order['amount'] == $order['filled']) return true;


    }


    public function ChangeIDOrderBD($ORD, $id){
        echo "Сменили ID ордера<br>";
        $ordbd = R::load("orders", $id);
        $ordbd->orderid = $ORD['id'];
        R::store($ordbd);
        return true;
    }


    public function GetOrderBook($symbol){
        $orderbook[$symbol] = $this->EXCHANGECCXT->fetch_order_book($symbol, 20);
        return $orderbook;

    }

    public function AddMassOrders(){

        foreach ($this->MASSORDERS['long'] as $key=>$val){
            $quantity = $this->GetQuantityBTC($val['summazahoda'] , $val['price']);
            $this->MASSORDERS['long'][$key]['quantity'] = $quantity;
        }

        foreach ($this->MASSORDERS['short'] as $key=>$val){
            $quantity = $this->GetQuantityBTC($val['summazahoda'] , $val['price']);
            $this->MASSORDERS['short'][$key]['quantity'] = $quantity;
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

        foreach ($this->MASSORDERS['long'] as $key=>$val){
            $this->MASSORDERS['long'][$key]['summazahoda'] = $zahod;
        }

        foreach ($this->MASSORDERS['short'] as $key=>$val){
            $this->MASSORDERS['short'][$key]['summazahoda'] = $zahod;
        }




        return true;
    }

    public function GenerateStepPrice($avg){
        $MASS = [];


        for ($i = 0; $i < $this->CountOrders; $i++) {
            $MASS['long'][]['price'] = $this->RangeH - $this->step*$i;
        }

            $MASS['avg']['price'] = $avg;

        for ($i = 0; $i < $this->CountOrders; $i++) {
            $MASS['short'][]['price'] = $this->RangeL + $this->step*$i;
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


    private function GetContrPosition($TREK)
    {
        $cp = R::findOne("contrposition", 'WHERE idtrek =?', [$TREK['id']]);
        return $cp;
    }


    private function GetTreksBD()
    {
        $terk = R::findAll($this->namebdex, 'WHERE emailex =? ORDER by status', [$this->emailex]);
        return $terk;
    }

    private function GetOrdersBD($TREK)
    {
        $MASS = R::findAll("orders", 'WHERE idtrek =? AND side=?', [$TREK['id'], $TREK['workside']]);

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
            $dollar = ($ORD['price']/100)*$delta*$ORD['amount'];
        }

        if ($ORD['stat'] == 2 && $ORD['side'] == "long"){
            $pexit = $ORD['price'] + $TREK['step'];
            $delta = changemet($ORD['price'], $pexit) + 0.025;
            $dollar = ($ORD['price']/100)*$delta*$ORD['amount'];

        }


        if ($ORD['stat'] == 2 && $ORD['side'] == "short"){
            $pexit = $ORD['price'] - $TREK['step'];
            $delta = changemet($pexit, $ORD['price']) + 0.025;
            $dollar = ($ORD['price']/100)*$delta*$ORD['amount'];
        }

        // Рассчет заработка в долларах

        // Рассчет заработка в долларах




        $MASS = [
            'trekid' => $TREK['id'],
            'side' => $ORD['side'],
            'dollar' => $dollar,
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

 
        return $dollar;

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

    private function ChangeARRinBD($ARR, $id, $BD = false)
    {

        if ($BD == false) $BD = $this->namebdex;

        echo('-----------------');
        echo('-----------------');
        echo('-----------------');
        show($ARR);
        echo('-----------------');
        echo('-----------------');
        echo('-----------------');

        $tbl = R::load($BD, $id);
        foreach ($ARR as $name => $value) {
            $tbl->$name = $value;
        }
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