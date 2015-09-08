<?
/* ----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/common.php
----------------------------------------------------------------------------- */

function thisFile(){
	return basename($_SERVER['SCRIPT_FILENAME']);   
}

function redirect($path, $relative = true){
	
	session_write_close();
	
	if(!$relative){
		$path = HTTP_PATH.$path;
	}
	
	//wLog(1, 'redirect('.$path.')');
	 
	header('Location: '.$path);
	exit(); 
}
 

function error403(){
	header('HTTP/1.0 403 Forbidden');
}

function error404(){
	header("HTTP/1.0 404 Not Found");
}

/* 
----------------------------------------------------------------------------- */

function addMessage($type, $msg) {
	$message = new Message();
	$message->add($msg, $type);
	$message->save_all_messages();
	unset($message);
}

function getMessage() {
	$message = new Message();
	$html = $message->get();
	unset($message);
	return $html;
} 

function getRawMessage() {
	$message = new Message();
	$str = $message->getRaw();
	unset($message);
	return $str;
}

function clearMessages() {
	$message = new Message();
	$message->clear_messages();
	unset($message);
}

function wLog($level, $msg) {
	$tLog = Log::get_instance();
	$tLog->write($level, $msg);
}

function writePostLog(){
	$postStr = "\n";
	foreach($_POST as $key => $val){
		$postStr .= $key." = ".$val."\n";
	}
	
	wLog(1, 'POST - '.$postStr);
}

/* 3E COMMON FUNCTIONS
----------------------------------------------------------------------------- */

function getMode(&$mode){
	if(isset($_REQUEST['mode']) && !empty($_REQUEST['mode'])){
		$mode = Sanitize::clean($_REQUEST['mode']);
	} else {
		$mode = 'index';
	}
}

/* REQUEST */
function getRequest(&$var, $title, $method = ''){
	
	if($method == 'get'){
		(isset($_GET[$title]) ? $var = $_GET[$title] : $var = NULL );
	
	} elseif($method == 'post'){
		(isset($_POST[$title]) ? $var = $_POST[$title] : $var = NULL );
		
	} else {
		(isset($_REQUEST[$title]) ? $var = $_REQUEST[$title] : $var = NULL );
	}
}


function getRequestClean(&$var, $title, $method = ''){
	
	if($method == 'get'){
		(isset($_GET[$title]) ? $var = Sanitize::clean($_GET[$title]) : $var = NULL );
	
	} elseif($method == 'post'){
		(isset($_POST[$title]) ? $var = Sanitize::clean($_POST[$title]) : $var = NULL );
		
	} else {
		(isset($_REQUEST[$title]) ? $var = Sanitize::clean($_REQUEST[$title]) : $var = NULL );
	}
}

function getRequestParanoid(&$var, $title, $method = ''){
	
	if($method == 'get'){
		(isset($_GET[$title]) ? $var = Sanitize::paranoid($_GET[$title]) : $var = 0 );
	
	} elseif($method == 'post'){
		(isset($_POST[$title]) ? $var = Sanitize::paranoid($_POST[$title]) : $var = 0 );
		
	} else {
		(isset($_REQUEST[$title]) ? $var = Sanitize::paranoid($_REQUEST[$title]) : $var = 0 );
	}
}

function getRequestPermalink(&$var, $title, $method = ''){
	
	if($method == 'get'){
		(isset($_GET[$title]) ? $var = Sanitize::permalink($_GET[$title]) : $var = '' );
	
	} elseif($method == 'post'){
		(isset($_POST[$title]) ? $var = Sanitize::permalink($_POST[$title]) : $var = '' );
		
	} else {
		(isset($_REQUEST[$title]) ? $var = Sanitize::permalink($_REQUEST[$title]) : $var = '' );
	}
}

function getRequestSearch(&$var, $title, $method = ''){
	
	if($method == 'get'){
		(isset($_GET[$title]) ? $var = Sanitize::search($_GET[$title]) : $var = '' );
	
	} elseif($method == 'post'){
		(isset($_POST[$title]) ? $var = Sanitize::search($_POST[$title]) : $var = '' );
		
	} else {
		(isset($_REQUEST[$title]) ? $var = Sanitize::search($_REQUEST[$title]) : $var = '' );
	}
}



function repop($title){
	if(isset($_REQUEST[$title])){
		return $_REQUEST[$title];
	} 
}

/* QS HELPERS
----------------------------------------------------------------------------- */

function requestAsCSV($name){
	$value = array();
	if(isset($_REQUEST[$name])){
		if(is_array($_REQUEST[$name])){
			$value = $_REQUEST[$name];
		} else {
			$value = array($_REQUEST[$name]);
		}
	}
	return implode(',', $value);
}


function cleanUrlString($str, $len = 80, $separator = '-'){
	
	$unwanted = array(
		'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
		'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
		'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
		'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
		'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
		'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
		'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
	);
	
	//cryllic 
	$str = trim(strtr($str, $unwanted)); 
	
	//lower case
	$str = strtolower($str);
	
	//remove shiz
	$str = trim(preg_replace("/[^a-z0-9]/", " ", $str));
	
	$str = preg_replace("/\s+/", $separator, $str);

	$str = substr($str, 0, $len);
	
	if(strlen($str) == $len){
		
		$pos = strrpos($str, $separator);
		
		if($pos !== false) { 
			$str = substr($str, 0, $pos);
		} 
	}
	return $str;
}

function clipString($string, $len){
	$string = trim($string);
	if(strlen($string) >= $len){
		$string = substr($string, 0, $len);
		$pos = strrpos($string, ' ');
		if($pos !== false) { 
			$string = substr($string, 0, $pos);
		}
		$string .= "...";
	}
	return $string;
}

function stripHttpFromUrl($url){
	return preg_replace('/^(http|ftp|news)s?:\/+/i', '', $url);
}

function camelCaseToHuman($str){
 return ucfirst(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $str));
}

function humanTime($sqlDate, $dayLimit = 20, $dateFallBack = true) { 
	$currentTime = time();
	$articleTime = strtotime($sqlDate);
	if( ($currentTime - $articleTime) < 120 ) { //less than 1 min show 1 minute
		$final_date = "1 minute ago";
		return $final_date;
	} elseif( ($currentTime - $articleTime) < (60*60) ) { //less than 1 hr show minute(s)
		$minutes = floor( ($currentTime - $articleTime) / 60 );
		$final_date = "".$minutes." minutes ago";
		return $final_date;
	} elseif( ($currentTime - $articleTime) < (60*60*24) ) { //less than 24 hrs show hours
		$hours = floor( ($currentTime - $articleTime) / (60*60) );
		if($hours > 1){
			$final_date = "".$hours." hours ago";
		} else {
			$final_date = "".$hours." hour ago";
		}
		return $final_date;
	} elseif( ($currentTime - $articleTime) < (60*60*24*$dayLimit) ) { //less than day limit?
		$days = ceil( ($currentTime - $articleTime) / (60*60*24) );
		if($days == 1){
			$final_date = "".$days." day ago";
		} else {
			$final_date = "".$days." days ago";
		}
		return $final_date;
	} else { 
		if($dateFallBack){
			return date('F j, Y', $articleTime);
		} else {
			return '';
		}
	}
}

/*
 formats:
 0 = (xxx) xxx-xxxx
 1 = xxx-xxx-xxxx
 2 = xxx.xxx.xxxx
*/
function phoneFormat($phone, $format = 0){
	
	$phone = preg_replace("/[^0-9]/", "", $phone);
 
	if(strlen($phone) == 7){
		return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
	
	} elseif(strlen($phone) == 10) {
		
		if(!empty($format) && $format == 1){
			return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1.$2.$3", $phone);
			
		} elseif(!empty($format) && $format == 2){
			return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
			
		} else { //DEFAULT
			return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
			
		}
		
	}	else {
		return $phone;
		
	}
}

function reTitleCopied(&$title){
	
	if(strpos($title, '(copy') === false){
											
		$title .= ' (copy)';
	
	} else {
		
		if(strpos($title, '(copy)') !== false){
			$title = substr($title, 0, -1).' 2)';
	
		} else {
			
			function incrementCopyCount($matches){
				return $matches[1].($matches[2]+1).$matches[3];
			}
			$title = preg_replace_callback("/(\(copy ){1}(\d)(\)){1}/", "incrementCopyCount", $title);
			
		}
	}
}
	







