<?
/* ----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/Help.php
----------------------------------------------------------------------------- */
namespace App\Lib;

class Help {
	
	public static function flatErrors($errors){
		
		$str = '';
		foreach($errors as $field => $messages){
			foreach($messages as $message){
				$str .= $message."<br>";	
			}
		}
		return $str;
		
	}
	
	
	public static function clip($string, $len){
		
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

	public static function stripHttpFromUrl($url){
		return preg_replace('/^(http|ftp|news)s?:\/+/i', '', $url);
	}

	public static function camelCaseToHuman($str){
	 return ucfirst(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $str));
	}

	public static function humanTime($sqlDate, $dayLimit = 20, $dateFallBack = true) { 
		
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
	public static function phoneFormat($phone, $format = 0){
		
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

	public static function copyTitle(&$title){
		
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
	
}
