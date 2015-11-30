<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/EventDate.php
----------------------------------------------------------------------------- */

class EventDate extends Core {
	
	//ATTRIBUTES
	public $_title = 'Date';
	public $_table = 'eventDate';
	public $_id = 'eventDateID';
	
	//FIELDS
	public $eventDateID = 0;
	public $eventID;
	public $recurringDate;
	
	public $collection = array();
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function fetchAllByParent($eventID){
		
		if(empty($eventID)){ 
			return;
		}
		
		$query = "SELECT * FROM ".$this->_table." WHERE eventID = ".$eventID." ORDER BY recurringDate ASC";
		
		return $this->loadCollection($query);
	}

	
	
	/* CRUD
	----------------------------------------------------------------------------- */

	public function _insert(){
		
		wLog(1, $this->recurringDate);

		$insert = sprintf("INSERT INTO ".$this->_table." 
			(eventID, recurringDate) 
			VALUES (%d, %s)",
			Sanitize::input($this->eventID, "int"),
			Sanitize::input($this->recurringDate, "datetime"));
		
		wLog(1, $insert);
		
		if($this->query($insert)){ 
		
			$this->setInsertId();
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error inserting '.$this->_title);
			return false;
		} 
		
	}
	
	protected function _update(){
		
		$update = sprintf("UPDATE ".$this->_table."
				SET eventID=%d, recurringDate=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->eventID, "int"),
			Sanitize::input($this->recurringDate, "datetime"),
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
		
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}

	
	protected function deleteAllByParent($eventID, $verbose = TRUE){
		
		$delete = sprintf("DELETE FROM ".$this->_table."
			WHERE eventID=%d",
		Sanitize::input($eventID, "int"));
		if($this->query($delete)){ 
			if($verbose){
				addMessage('success', $this->_title.' deleted successfully');
			}
			return true;
		} 
	}
	
	/* DATE HELPERS
	----------------------------------------------------------------------------- */
	
	public function get_date($format = 'm/d/Y'){
		return date($format, strtotime($this->recurringDate));
	}
	
	public function getTime($format = 'g:ia'){
		return date($format, strtotime($this->recurringDate));
	}
	
	
}