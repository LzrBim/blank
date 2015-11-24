<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/Model/SluggableTrait.php
----------------------------------------------------------------------------- */

namespace App\Model;

use App\Lib\Sanitize;

trait SluggableTrait {
	
	/* LOAD
	----------------------------------------------------------------------------- */
	public function loadBySlug($slug, $with = array()){
		
		if(empty($slug)){
			return false;
		}
		
		$query = "SELECT * FROM ".$this->_table." 
			WHERE slug = '".$slug."' 
			AND status = 'active'";
			
		$result = $this->query($query);
		
		if($result && $this->numRows($result)){
			
			$this->loadByData($this->fetchAssoc($result));
			
		} 
		
		return $this;
		
	}
	
	
	/* slug	
	----------------------------------------------------------------------------- */
	
	protected function buildSlug($str){
		
		return Sanitize::sluggify($str);

	}
	
	protected function setSlug($str, $preventCollision = true){
		
		if(empty($str)){
			return false;
		}
		
		$this->slug = $this->buildSlug($str);
		
		if(empty($this->slug)){
			return false;
		}
		
		if($preventCollision){
		
			if($this->slugExists($this->slug)){
				
				$this->slug = $this->slug.'-'.$this->id();
				
			}
			
		}
	}
	
	
	protected function slugExists($slug){
		
		$where = "slug = '".$slug."'";
		return $this->fetchCount($where);
		
	}
	
}