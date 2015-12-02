<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/models/Staff.php
----------------------------------------------------------------------------- */

namespace App\Model;
use \App\Lib\Sanitize;

class Staff extends StaffBase {
	
	/* FETCH
	----------------------------------------------------------------------------- */
	public function fetchActiveByCategory($categoryID, $orderBy = '', $limit = '', $loadChildren = true){
		
		return $this->fetch("staffCategoryID = ".$categoryID." AND status = 'active'", $orderBy, $limit, $loadChildren);
	
	}
	
	/* FRONT FETCH
	----------------------------------------------------------------------------- */
	
	/* ADMIN FETCH
	----------------------------------------------------------------------------- */
	
	
	/* 	HELPERS
	----------------------------------------------------------------------------- */
	public function getFullName(){
		
		$str = $this->firstName;
		
		if(!empty($this->middleName)){ 
			$str .= ' '.$this->middleName; 
		} 
		
		$str .= ' '.$this->lastName;
		
		if(!empty($this->suffix)){ 
			$str .= ', '.$this->suffix; 
		} 
		return $str;
	}
	
}