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








?>