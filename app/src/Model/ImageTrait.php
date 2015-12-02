<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/Model/ImageTrait.php
----------------------------------------------------------------------------- */

namespace App\Model;

use App\Lib\Sanitize;

trait ImageTrait {
	
	/* 	REMOVE IMAGE
	----------------------------------------------------------------------------- */
	public function removeImage(){
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=0 WHERE ".$this->_id."=%d",
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
		
			return $this->image->delete();
			
		} else { 
		
			return false;
			
		}
	}
	
}