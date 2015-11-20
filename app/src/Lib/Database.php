<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Database
----------------------------------------------------------------------------- */
namespace App\Lib;

class Database { 
		
	private static $instance; 
		
	private function __construct() { }   
		
	private function __clone() { } 

	public static function get_instance()  {  
		
		if(!isset(self::$instance)) {  
			
			self::$instance = @mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);  
			
			if (mysqli_connect_error()) {
				
				die('No db');
				
			}
			
			mysqli_set_charset(self::$instance , 'utf8' );
			
		}   
		
		return self::$instance; 
		
  }
}