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

    private $RangeH = 37000; // Верхняя граница коридора
    private $RangeL = 35000; // Нижняя граница коридора

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

        echo "<b>Наличие позиции (BOOL)</b><br>";
        var_dump($this->POSITIONBOOL);


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

        $minimumstep = ($pricenow/100*0.075)*2;
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

        if ($TREK['workside'] == NULL){
            // На случай смены активной стороны
            $ARRTREK['workside'] = $WorkSide;
            $this->ChangeARRinBD($ARRTREK, $TREK['id']);
            // На случай смены активной стороны
        }


        // Статус ордера - ОРДЕРА на НАРАЩИВАНИЕ ПОЗИЦИИ
        if ($TREK['action'] == "ControlOrders") $this->ActionControlOrders($TREK, $pricenow);


        // Ордера на фиксирование позиции



        echo "СТАРТУЕМ!!!!!!!!";
        exit("fgf");


        // Определяем в какой мы части коридора

        // Если не в коридоре, то меняем СТАТУС трека и завершаем ЦИКЛ



        foreach ($OrdersBD as $key=>$OrderBD){
            echo "<hr>";

            // ПРОВЕРЯЕМ ОРДЕР НА НАЛИЧИЕ
            // ЕСЛИ ЕГО НЕТ, ТО ВЫСТАВЛЯЕМ
            echo "#".$OrderBD['id']." СТАТУС ОРДЕРА <b>".$OrderBD['stat']."</b> - ".$OrderBD['orderid']." - <b>".$OrderBD['side']."</b> <br>";

            $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);

            if ($OrderBD['orderid'] == NULL){

                echo "<font color='#8b0000'>Ордер НЕ существует! </font>  <br>";
                echo "Проверяем можно его сейчас выставлять или нет   <br>";
                $pricenow = $this->GetPriceSide($this->symbol, $OrderBD['side']);
                $resultscoring = $this->CheckValidateOrderRe($OrderBD['side'], $pricenow, $OrderBD['price']);
                var_dump($resultscoring);

                if ($resultscoring == true){

                    if ($OrderBD['stat'] == 2) $this->POSITIONBOOL = false;
                    if ($OrderBD['first'] == 1) $this->POSITIONBOOL = false; // Если первый раз, то позицию наращиваем

                    $params = [
                        'time_in_force' => "PostOnly",
                        'reduce_only' => $this->POSITIONBOOL,
                    ];

                    $sideorder = $this->GetTextSide($OrderBD['side']);
                    show($sideorder);
                    var_dump($OrderBD['amount']);
                    show($OrderBD['price']);
                    $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$sideorder, $OrderBD['amount'], $OrderBD['price'], $params);
                    $this->ChangeIDOrderBD($order, $OrderBD['id']);
                    show($order);
                }

                continue;

            }

             // Если отменен из-за POST-ONLY
            if ($OrderREST['status'] == "canceled"){
                echo "<font color='#8b0000'>ОРДЕР отменен (canceled)!!! </font> <br>";
                show($OrderREST);
                echo "Обнуляем ID ордера!  <br>";
                $order['id'] = NULL;
                $this->ChangeIDOrderBD($order, $OrderBD['id']);
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
                $ARR['first'] = 0;

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
                $ARR['first'] = 0;

                $this->AddARRinBD($ARR, "orders");

                continue;


            }


            // Если откупились на первый первый статус (откупился первый раз после выставления)


        }

        // Контроллер ситуации

        return true;

    }


    // Статус 2 - Вышли из коридора
    private function WorkStatus2($TREK){

        echo "<h1><font color='#8b0000'>ВНЕ КОРИДОРА</font></h1>";


        // Получаем КонтрПозицию ТЗ БД
        $CONTRPOSTION = $this->GetContrPosition($TREK);

        if ($CONTRPOSTION['action'] == "CreatePosition") $this->ActionCreatePosition($TREK, $CONTRPOSTION);

        if ($CONTRPOSTION['action'] == "ControlPosition") $this->ActionControlPosition($TREK, $CONTRPOSTION);


    }

    // Статус 3 - Закрытие всех позиций. Перезапуск цикла
    private function WorkStatus3($TREK){

        echo "<h1><font color='#8b0000'>Закрываем все позиции и обнуляемся</font></h1>";

        $cancelall = $this->EXCHANGECCXT->cancel_all_orders($this->symbol);
        show($cancelall);

        $OrdersBD = $this->GetOrdersBD($TREK);

        R::trash($OrdersBD);


        show($TREK);

        return true;

    }


    private function GetWorkSide($pricenow, $TREK){

        if ($pricenow > $TREK['avg']) return "LONG";
        if ($pricenow < $TREK['avg']) return "SHORT";

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

                    // Выставление первых ордеров которые по маркету
                    if ($OrderBD['orderid'] == NULL){
                        echo  "Проверка откупа первоначального ордера <br>";

                        echo "Текущая цена".$pricenow."<br>";
                        echo "Цена для выставления ордера".$OrderBD['price']."<br>";

                        // Скоринг на выставление
                        $resultscoring =  $this->CheckFirstOrder($TREK, $pricenow, $OrderBD);

                        echo "Откупаем ордер по типу:<br>";
                        var_dump($resultscoring);
                        if ($resultscoring === FALSE) continue;

                        $order = $this->CreateFirstOrder($OrderBD, $resultscoring);
                        // Записываем ID ордера в БД
                        $this->ChangeIDOrderBD($order, $OrderBD['id']);

                        continue;
                        // Проверяем на скоринг необходимости выставления ордера по маркету
                    }



                echo "Информация об ордере из REST<br>";
                $OrderREST = $this->GetOneOrderREST($OrderBD['orderid']);

                if ($this->OrderControl($OrderREST) === FALSE){
                    echo "ОРДЕР не откупился <br>";
                    continue;
                }


                // Ордер откупился! Что делаем дальше
                if ($OrderBD['side'] == "long"){
                    // Цена по которой нужно выставлять ордер
                    $price = $OrderREST['price'] + $TREK['step'];
                    echo "Ордер откупился по цене ".$OrderREST['price']."<br>";
                    echo "Цена нашего выставления ".$price."<br>";
                    echo "Текущая цена - ".$pricenow."<br>";
                }
                // Выставляем реверсный ордер

                // Меняем статус ордера


                // СТАРТ СКРИПТА ПО КОГДА ОН ЕСТЬ В БД





            }






        }

            // Если откупились на первый первый статус (откупился первый раз после выставления)





        return true;

    }


    private function ActionCreatePosition($TREK, $CONTRPOSTION){

        echo  "<b>Запускаем Action CreatePosition. Создаем контр ХЕДЖ позицию</b> <br>";


        // Проверка на наличие ордера. Если ордера нет, то создаем
        if($CONTRPOSTION['horder'] == NULL){
            // Определяем минусовую позицию

            $POSITIONS =  $this->LookHPosition();
            show($POSITIONS['minuspos']);

            // Создаем ордер для увеличения контр позиции
            $order = $this->CreateContrPositionOrder($TREK, $POSITIONS);


            $ARR['horder'] = $order['id'];
            $ARR['size'] = $POSITIONS['minuspos']['size'];
            $ARR['price'] = $order['price'];
            $ARR['side'] = $POSITIONS['pluspos']['sidecode']; // Сторона позиции, которую наращиваем
            $ARR['contrside'] = $POSITIONS['minuspos']['sidecode']; // Сторона позиции, которую наращиваем
            $ARR['pluspos'] = json_encode($POSITIONS['pluspos'], true);
            $ARR['minuspos'] =json_encode($POSITIONS['minuspos'], true);
            $this->ChangeARRinBD($ARR, $CONTRPOSTION['id'], "contrposition");

            // echo "Создаем ордер";
            return true;
        }

        if ($CONTRPOSTION['horder'] != NULL){

            echo "<b> Проверяем откупился ордер на контр позицию или нет или нет </b>";
            $OrderREST = $this->GetOneOrderREST($CONTRPOSTION['horder']);
            if ($this->OrderControl($OrderREST) === FALSE){
                echo "ОРДЕР не откупился! Ждем <br>";
                //    continue;
            }

            if ($OrderREST['status'] == "canceled"){
                echo "Ордер отменен биржей! Пускаем повторно на CreatePosition!";
                $ARR['horder'] = NULL;
                $this->ChangeARRinBD($ARR, $CONTRPOSTION['id'], "contrposition");
                return true;
            }



            show($OrderREST);

            // Позиция создана
            //      if ($CONTRPOSTION['side'] == "short") $stopl = $TREK['rangel'] + $TREK['step']*2;
            //      if ($CONTRPOSTION['side'] == "long") $stopl = $TREK['rangeh'] - $TREK['step']*2;



            $ARR['horder'] = NULL;
          //  $ARR['stopl'] = $stopl;
            $ARR['action'] = "ControlPosition";
            $this->ChangeARRinBD($ARR, $CONTRPOSTION['id'], "contrposition");





            return true;
            // Добавление всех параметров по контролю позиции
            // Смена экшена на ControlPosition




        }



        return true;



    }


    private function ActionControlPosition($TREK, $CONTRPOSTION){






        echo  "<b>Работает ActionControlPosition. Контролируем ХЭДЖ позицию</b> <br>";

        $pricenow = $this->GetPriceSide($this->symbol, $CONTRPOSTION['side']);

        echo "Цена входа в контр позицию:".$CONTRPOSTION['price']."<br>";
        echo "Текущая цена: ".$pricenow."<br>";

        // Получение базовой информации о позициях
        $BASEINFO = $this->LookingSpacePositions($CONTRPOSTION);

        show($BASEINFO);

        echo "Unrealized PNL RAKETA: ".$BASEINFO['raketa']['unrealised_pnl']."<br>";
        echo "Unrealized PNL KORIDOR: ".$BASEINFO['koridor']['unrealised_pnl']."<br>";

        $raznica = $BASEINFO['raketa']['unrealised_pnl'] + $BASEINFO['koridor']['unrealised_pnl'];

        echo "<hr>";
        echo  "<b>Разница позиции: </b> ".$raznica."<br>";

        // Отменяем все ордера

        $startpriceLONG = $TREK['rangeh'] - $TREK['step']*8;
        $startpriceSHORT = $TREK['rangel'] + $TREK['step']*8;

        echo "Цена при которой возобновляем сетку LONG:".$startpriceLONG."<br>";
        echo "Цена при которой возобновляем сетку SHORT:".$startpriceSHORT."<br>";


        $cancelall = $this->EXCHANGECCXT->cancel_all_orders($this->symbol);
        show($cancelall);


        if ($CONTRPOSTION['side'] == "long" && $pricenow < $startpriceLONG ){
            $ARRTREK['status'] = 3;
            $this->ChangeARRinBD($ARRTREK, $TREK['id']);
           R::trash($CONTRPOSTION);

            return true;
        }

        if ($CONTRPOSTION['side'] == "short" && $pricenow > $startpriceSHORT ){
            $ARRTREK['status'] = 3;
            $this->ChangeARRinBD($ARRTREK, $TREK['id']);
            R::trash($CONTRPOSTION);
            return true;
        }






        // Контролируем текущую цену и середину коридора
        //

        return true;



    }


    private function CheckFirstOrder($TREK, $pricenow, $OrderBD){

        // Если цена выше цены выставления +  от шага 10%, то выставляем лимитник
        // Если цена ниже цены выставления + от шага 10%, то ничего не делаем
        // Если цена в радиусе цены выставления + 10% от шага, то выставляем берем рынком


        if ($TREK['workside'] == "LONG"){
            echo "Сторона скоринга LONG<br>";
            $PercSTEP = ($TREK['step']/100)*10;
            $PercSTEP = round($PercSTEP);
            $PercSTEP = $OrderBD['price'] + $PercSTEP;
            echo "Цена + ШАГ".$PercSTEP."<br>";

            if ($pricenow > $PercSTEP) return "LIMIT";
            if ($pricenow > $OrderBD['price'] && $pricenow < $PercSTEP && $OrderBD['first'] == 1) return "MARKET";


        }


        if ($TREK['workside'] == "SHORT"){
            echo "Сторона скоринга SHORT<br>";
            $PercSTEP = ($TREK['step']/100)*10;
            $PercSTEP = round($PercSTEP);
            $PercSTEP = $OrderBD['price'] - $PercSTEP;

            echo "Цена с ШАГ".$PercSTEP."<br>";

            if ($pricenow < $PercSTEP) return "LIMIT";
            if ($pricenow < $OrderBD['price'] && $pricenow > $PercSTEP && $OrderBD['first'] == 1) return "MARKET";

        }

            echo "Цена не корректна для выставления ордеров в данном коридоре";
            return false;

    }


    private function CreateFirstOrder($OrderBD, $type){


        $sideorder = $this->GetTextSide($OrderBD['side']);
        show($sideorder);
        var_dump($OrderBD['amount']);
        show($OrderBD['price']);


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


    private function CreateContrPositionOrder($TREK, $POSITIONS){


        // Определение параметров для выставления ордера увеличения позиции
        $PARAMS = $this->GetParamCreateHPosition($POSITIONS);
        // Выставления ордера
        show($PARAMS);


//        $par = [
//            'time_in_force' => "PostOnly",
//            'reduce_only' => false,
//        ];
//
//        $order = $this->EXCHANGECCXT->create_order($this->symbol,"limit",$PARAMS['side'], $PARAMS['amount'] , $PARAMS['price'], $par);

        $order = $this->EXCHANGECCXT->create_order($this->symbol,"market",$PARAMS['side'], $PARAMS['amount']);

        echo "<font color='#8b0000'>Создали ордер под контр-позицию</font><br>";

        return $order;
    }

    private function GetParamCreateHPosition($POSITIONS){


        $PARAMS = [];

        // Цена
        $PARAMS['price'] = $this->GetPriceSide($this->symbol, $POSITIONS['pluspos']['sidecode']);

        $PARAMS['amount'] = $POSITIONS['minuspos']['size'] - $POSITIONS['pluspos']['size'];

        $PARAMS['side'] = $POSITIONS['pluspos']['side'];

        return $PARAMS;

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


    public function CheckValidateOrderRe($sideorder, $pricenow, $priceorder){
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