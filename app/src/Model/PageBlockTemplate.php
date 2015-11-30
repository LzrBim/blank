<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /models/PageBlockTemplate.php
----------------------------------------------------------------------------- */

class PageBlockTemplate extends Core {  
	
	//ATTRIBUTES
	public $_title = 'PageBlockTemplate';
	public $_id = 'pageBlockTemplateID'; 
	public $_table = 'pageBlockTemplate';

	//FIELDS
	public $pageBlockTemplateID = 0;
	public $template;
	public $status;
	
	public $_validateRules = array( 
		'rules' => array( 
			'template' => array( 'required' => true )
		)
	);
	
	
	/* CRUD
	----------------------------------------------------------------------------- */

	public function _insert(){
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(template, status) 
			VALUES (%s, %s)",
			Sanitize::input($this->template, "text"),
			Sanitize::input($this->status, "text"));
		
		if($this->query($insert)){ 
		
			$this->setInsertId();
			
			return true;
		
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		} 
	}
	
	public function _update(){
	
				
		$update = sprintf("UPDATE ".$this->_table."
			SET template=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->template, "text"),
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
		
			addMessage('success', $this->_title.' was updated successfully');
			return true;
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		}
	}	
	
}