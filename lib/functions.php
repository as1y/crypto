<?php
// ВЫКЛЮЧАТЬ ОШИБКИ
$er = ERRORS;
if($er == 1){
	//ВЫВОД ОШИБОК
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	//ВЫВОД ОШИБОК
} else{
	error_reporting(E_ERROR);
}
function not_found() { // ЕСЛИ НЕ НАЙДЕНА СТРАНЦИА


	if (isset($_SESSION['ulogin'])){
		echo "<script>document.location.href='/panel';</script>\n";
		exit();
	}
	else{
		http_response_code(404);
		include ("404.php");
		exit();
	}
} // ЕСЛИ НЕ НАЙДЕНА СТРАНЦИА


// Функция вывода сообщений в обработчике форм
function message( $text ) {
	exit('{ "message" : "'.$text.'"}');
}
// Функция вывода сообщений в обработчике форм
// Функция редирект при возвращении из формы через ajax
function go( $url ) {
	exit('{ "go" : "'.$url.'"}');
}
// Функция редирект при возвращении из формы через ajax



function redir($http = FALSE){
	if($http){
		$redirect = $http;
	}else{
		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
	}
	header("Location: $redirect");
	exit();
}
// Екзит из интерфейса


// СООБЩЕНИЕ ЧЕРЕЗ PHP
function mes ( $mess ) {
	echo ( '
	<script>
		alert(" '.$mess. '");
	</script>
		 ') ;
}
// СООБЩЕНИЕ ЧЕРЕЗ PHP
function dumpf($PARAM){
	file_put_contents($_SERVER["DOCUMENT_ROOT"] .'/log.log', var_export($PARAM, true), FILE_APPEND);
}


function hurl($url){
	$url = str_replace("http://", "", $url); // Убираем http
	$url = str_replace("https://", "", $url); // Убираем https
	$url = str_replace("www.", "", $url); // Убираем https
	return $url;
}
// Генерируем рандом символы 30 шт
function random_str( $num = 30 ) {
	return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $num);
}
// Генерируем рандом символы 30 шт
function show($par){
	echo "<pre>";
	print_r($par);
	echo "</pre>";
}
// ФУНКЦИЯ ЗАГРУЗКИ КЛАССОВ
spl_autoload_register(function ($class) {
		$path = str_replace( '\\', '/', $class.'.php' );
		if (file_exists($path)){
			require $path;
		}
	});
// ФУНКЦИЯ ЗАГРУЗКИ КЛАССОВ
function h($str){
	return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Выводит ошибку в специальном формате
 * @param string $error_message
 */
function e(string $error_message): void
{
    if(ERRORS) {
        ob_clean();
        ob_start();
        echo(sprintf('<h1 style="color:red">%s</h1>', $error_message));
        echo('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
        ob_end_flush();
        exit();
    }
}



function monthtoday(){
    $datetoday = date ("Y-m-d");
    $d = date_parse_from_format("Y-m-d", $datetoday);

    if ($d['month'] == 1) $d['month'] = "январь";
    if ($d['month'] == 2) $d['month'] = "февраль";
    if ($d['month'] == 3) $d['month'] = "март";
    if ($d['month'] == 4) $d['month'] = "апрель";
    if ($d['month'] == 5) $d['month'] = "май";
    if ($d['month'] == 6) $d['month'] = "июнь";
    if ($d['month'] == 7) $d['month'] = "июль";
    if ($d['month'] == 8) $d['month'] = "август";
    if ($d['month'] == 9) $d['month'] = "сентябрь";
    if ($d['month'] == 10) $d['month'] = "октябрь";
    if ($d['month'] == 11) $d['month'] = "ноябрь";
    if ($d['month'] == 12) $d['month'] = "декабрь";

    return $d['month'];
}



function getOstatok ($expiration_date){

    $d = date_parse_from_format('Y-m-d', $expiration_date);
    $date1 = mktime(0, 0, 0, $d['month'], $d['day'], $d['year']);
    $date1 = $date1 + 86300;
    $datetoday = time();
    $ostatok = $date1 - $datetoday;
    $ostatokday = round($ostatok/86400);

    return $ostatokday;
}


function calculate_exp($expiration_date){


    if (!$expiration_date){
        $answer = "<font color='green'>БЕЗ ЛИМИТА</font>" ;
        return $answer;
    }

    $ostatokday = getOstatok($expiration_date);


    if ($ostatokday < 0 )return $answer = "<font color='red'>ПРОСРОЧЕН</font>" ;
    if ($ostatokday == 0) return $answer = "<font color='red'> СЕГОДНЯ! </font>" ;
    if ($ostatokday == 1)  return $answer = "<font color='red'>ЗАВТРА</font>";
    if ($ostatokday == 2) return $answer = $ostatokday." дня";
    if ($ostatokday == 3) return $answer = $ostatokday." дня";

//    if ($ostatokday > 30) return  $answer = date_parse_from_format('Y-m-d', $expiration_date);
//    if ($ostatokday > 60) return   $answer = date_parse_from_format('Y-m-d', $expiration_date);

     return $answer = date("d-m-Y", strtotime($expiration_date));


}



function validatebanner($WHATNEED, $WHATHAVE){

    if ($WHATNEED['w']*2.2 < $WHATHAVE['w']) return false;
    if ($WHATNEED['w'] > $WHATHAVE['w']*1.5) return false;

    if ($WHATNEED['h']*2.2 < ($WHATHAVE['h'])) return false;
    if ($WHATNEED['h'] > ($WHATHAVE['h']*1.5)) return false;

    return true;

}


function obrezanie ($text, $symbols){

    $result = mb_strimwidth($text, 0, $symbols, "...");

    return $result;

}

function translit_sef($value)
{
    $converter = array(
        'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
        'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
        'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
        'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
        'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
        'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
        'э' => 'e',    'ю' => 'yu',   'я' => 'ya', '1' => 'odin',
        '2' => 'dva',
        '3' => 'tri',
        '4' => 'chetire',
        '5' => 'pyat',
        '6' => 'shest',
        '7' => 'sem',
        '8' => 'vosem',
        '9' => 'devyat',
        '10' => 'desyat',
        '0' => 'nol',
    );

    $value = mb_strtolower($value);
    $value = strtr($value, $converter);
    $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
    $value = mb_ereg_replace('[-]+', '-', $value);
    $value = trim($value, '-');

//    $value = str_replace("-", "-", $value); //Убираем тире, чтобы использовать его для IDшника


    return $value;
}

function generatepayform($form){

    echo '<form method="post" action="'.$form['action'].'">
    ';

    foreach ($form['input'] as $key=>$val) {
        ?>
        <input type="<?= $val['type'] ?>" name="<?= $val['name'] ?>" value="<?= $val['value'] ?>">
        <?php
    }

    echo "</form>";

}


function getExtension($filename) {
    $path_info = pathinfo($filename);
    return $path_info['extension'];
}


function teleph($tel){
	$tel = str_replace("'", "", $tel); // Убираем +
	$tel = str_replace("+", "", $tel); // Убираем +
	$tel = str_replace("(", "", $tel); // Убираем (
	$tel = str_replace(")", "", $tel); // Убираем )
	$tel = str_replace("-", "", $tel); // Убираем )
	$tel = str_replace(".", "", $tel); // Убираем .
	$tel = str_replace(" ", "", $tel); // Убираем пробелы
	$tel = trim($tel);
	if ($tel['0'] == '8'){
		$tel = substr($tel, 1);
	}
	if ($tel['0'] != '7') $tel = "7".$tel."";
	return $tel;
}


function getconversion ($value1, $value2){
    if ($value2 == 0) return 0;
    $result = $value1/$value2*100;
    $result = round($result);
    return $result;
}


function getsizetypeimage($w_src, $h_src){

    if ($w_src > $h_src)  return  "horizont";
  if ($w_src < $h_src)  return "vertikal";
  if ($w_src == $h_src)  return"kvadrat";




}

function fCURL($url, $PARAMS = [], $headers = []){


    $ch = curl_init();

    if (!empty($PARAMS['GET'])){
        $url = $url."?".http_build_query($PARAMS['GET']);
    }


    if ($headers != []){
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    }else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json-patch+json'));
    }



    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


    //  curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
//    curl_setopt($ch, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
//    curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);


    if (!empty($PARAMS['POST'])){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMS['POST']);
    }

    if (!empty($PARAMS['PATCH'])){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMS['PATCH']);
    }



    if (!empty($PARAMS['DELETE'])){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $PARAMS['DELETE']);
    }





    $result = curl_exec($ch);

    curl_close($ch);

    //  $resultJson = $result;
    $resultJson = json_decode($result, TRUE);


    return $resultJson;



}

function validationpay($data, $type){

    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data);

    if ($type == "qiwi"){
        $data = intval($data);
        if (strlen($data) > 12) return "Qiwi кошелек не должен быть длиннее 10 символов";
        if (strlen($data) < 5) return "Qiwi кошелек не должен быть длиннее 5 символов";
    }


    if ($type == "yamoney"){
        $data = intval($data);
        if (strlen($data) > 30) return "Яндекс.Деньги не должен быть длиннее 30 символов";
        if (strlen($data) < 5) return "Яндекс.Деньги  не должен быть длиннее 5 символов";
    }

    if ($type == "card"){
        $data = intval($data);
        if (strlen($data) > 30) return "Номер карты не должен быть длиннее 30 символов";
        if (strlen($data) < 5) return "Номер карты  не должен быть длиннее 5 символов";
    }




    return true;
}


// Форматирование цен.
function format_price($value)
{
    return number_format($value, 2, ',', ' ');
}

// Сумма прописью.
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

?>