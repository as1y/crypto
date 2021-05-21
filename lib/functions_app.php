<?php

function changemet($open, $close)
{

    /*
    $change =  (($close*100)/$open) - 100;
    $change = round($change,2);
    */


    if($close == 0){
        return 0;
    }
    $change = 100 - ($open / $close) * 100;
    $change = round($change, 2);

    /*
       $change =  (($close*100)/$open) - 100;
       $change = round($change,2);
      */

    return $change;
}


function plusperc($PRICE, $tochkabid, $count)
{
    //СКОЛЬКО ТОЧЕК ПОСЛЕ ЗАПЯТОЙ У БИДА
    $sat = "1";
    for ($x = 0; $x < $tochkabid; $x++) $sat .= "0";
    //СКОЛЬКО ТОЧЕК ПОСЛЕ ЗАПЯТОЙ У БИДА
    $PRICE = $PRICE * $sat;

    $add = ($PRICE / 100) * $count;
    $PRICE = $PRICE + ceil($add);
    $PRICE = $PRICE / $sat;
    return number_format($PRICE, $tochkabid, '.', '');
}

function minusperc($PRICE, $tochkabid, $count)
{
    //СКОЛЬКО ТОЧЕК ПОСЛЕ ЗАПЯТОЙ У БИДА
    $sat = "1";
    for ($x = 0; $x < $tochkabid; $x++) $sat .= "0";
    //СКОЛЬКО ТОЧЕК ПОСЛЕ ЗАПЯТОЙ У БИДА
    $PRICE = $PRICE * $sat;

    $add = ($PRICE / 100) * $count;
    $PRICE = $PRICE - ceil($add);
    $PRICE = $PRICE / $sat;
    return number_format($PRICE, $tochkabid, '.', '');
}







?>