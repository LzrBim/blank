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
	
}
