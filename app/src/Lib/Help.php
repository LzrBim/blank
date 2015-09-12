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
	
	public static function getMode(&$mode){
		
		if(isset($_REQUEST['mode']) && !empty($_REQUEST['mode'])){
			$mode = Sanitize::clean($_REQUEST['mode']);
		} else {
			$mode = 'index';
		}
	}
	
	public static function thisFile(){
		return basename($_SERVER['SCRIPT_FILENAME']);   
	}
	
}
