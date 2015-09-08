<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/Model/SluggableTrait.php
----------------------------------------------------------------------------- */

namespace App\Model;

trait SluggableTrait {
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function loadBySlug($permalink, $childArgs = array()){
		
		if(empty($permalink)){
			wLog(3, 'No permalink supplied');
			return false;
		}
		
		$query = "SELECT * FROM ".$this->_table." 
			WHERE permalink = '".$permalink."' 
			AND status = 'active'";
			
		$result = $this->query($query);
		
		if($result && $this->numRows($result)){
			
			return $this->loadByData($this->fetchAssoc($result), $childArgs);
			
		} else {
			wLog(1, 'Permalink not loaded ='.$permalink);
			return false;
		}
	}
	
	
	/* PERMALINK	
	----------------------------------------------------------------------------- */
	
	protected function buildPermalink($str){
		
		return cleanUrlString($str);

	}
	
	protected function setPermalink($str, $preventCollision = true){
		
		if(empty($str)){
			wLog(4, 'Permalink init string empty');
			return false;
		}
		
		$this->permalink = $this->buildPermalink($str);
		
		if(empty($this->permalink)){
			wLog(4, 'Permalink empty');
			return false;
		}
		
		if($preventCollision){
		
			if($this->permalinkExists($this->permalink)){
				$this->permalink = $this->permalink.'-'.$this->getId();
			}
			
		}
	}
	
	
	public function getPermalink(){
		
		if(!isset($this->_modReWritePath) || empty($this->_modReWritePath)){
			wLog(4, 'no _modReWritePath set');
		}
		
		return $this->_modReWritePath.$this->permalink.'/'; 
	}
	
	
	protected function permalinkExists($permalink){
		
		$where = "permalink = '".$permalink."'";
		return $this->fetchCount($where);
		
	}
	
}