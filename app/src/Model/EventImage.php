<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/EventImage.php
----------------------------------------------------------------------------- */

class EventImage extends Core {
	
	//ATTRIBUTES
	public $_title = 'Event Slider';
	public $_id = 'eventImageID';
	public $_table = 'eventImage';
	
	//FIELDS
	public $eventImageID = 0;
	public $eventID = 0;
	public $imageID = 0;
	public $description;
	public $status = '';
	public $rank = 100;
		
	private $_imageSettings = array(
 		
		'uploadMode' => 'hashInsertOverWriteUpdate',
		'targetDirectory' => 'event/', 
		
		/* ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 640,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => true,
		'thumbWidth' => 200,
		'thumbHeight' => 200,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => false

	);
	
	public $_validateRulesInsert = array(
		'rules' => array( 
			'uploadFile' => array( 'required' => true )
		)
	);
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		
	}
	
	
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}

	}
	
	/* FETCH
	----------------------------------------------------------------------------- */
		
	public function fetchActiveByParent($parentID, $orderBy = 'rank ASC', $limit = ''){
		
		if(empty($parentID)){
			return array();
		}
		
		$where ="eventID = ".$parentID." AND status = 'active'";
		
		return $this->fetch($where, $orderBy, $limit);	
	}	
	
	public function fetchActiveByParentCount($parentID){
		
		if(empty($parentID)){
			return 0;
		}
		
		$where = "eventID = ".$parentID." AND status = 'active'";
		
		return $this->fetchCount($where);
		
	}
	
	public function fetchAllByParent($parentID, $orderBy = 'rank ASC', $limit = ''){
		
		if(empty($parentID)){
			return array();
		}
		
		$where ="eventID = ".$parentID;
		
		return $this->fetch($where, $orderBy, $limit);	
		
	}
	
	public function fetchAllByParentCount($parentID){
		
		if(empty($parentID)){
			return 0;
		}
		
		$where = "eventID = ".$parentID;
		
		return $this->fetchCount($where);
		
	}
		
	
	/* CRUD
	----------------------------------------------------------------------------- */
	public function insertMultiple($uploadCount){
		
		$successCount = 0;
		for($x = 0; $x < $uploadCount; $x++){
			
			$obj = new EventImage();
			
			$obj->loadByData($_POST);
			
			if(empty($obj->eventID)){
				addMessage('error','Error saving '.$obj->_title);
				wLog(4, 'No eventID supplied');
				return false;
			}
		
			
			if($obj->image->insert($x)){
				
				$obj->imageID = $obj->image->getId();
				
				$insert = sprintf("INSERT INTO ".$this->_table." 
					(eventID, imageID, description, status, rank) 
					VALUES (%d, %d,  %s, %s, %d)",
					Sanitize::input($obj->eventID, "int"),
					Sanitize::input($obj->imageID, "int"),
					Sanitize::input($obj->description, "editor"),
					Sanitize::input($obj->status, "text"),
					Sanitize::input($obj->rank, "int"));
				
				if($obj->query($insert)){ 
					
					$obj->setInsertId();
					$successCount++;
										
				} else { 
					addMessage('error','Error saving '.$obj->_title);
					
				} 
				
			} else {
				
			}
						
		}
		addMessage('success', 'Uploaded ('.$successCount.') files');
		return true;
				
	}
	
	
	protected function _insert(){
		
		if(empty($this->eventID)){
			addMessage('error','Error saving '.$this->_title);
			wLog(4, 'No eventID supplied');
			return false;
		}
		
		if($this->image->insert()){
			
			$this->imageID = $this->image->getId();
			
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(eventID, imageID, description, status, rank) 
				VALUES (%d, %d,  %s, %s, %d)",
				Sanitize::input($this->eventID, "int"),
				Sanitize::input($this->imageID, "int"),
				Sanitize::input($this->description, "editor"),
				Sanitize::input($this->status, "text"),
				Sanitize::input($this->rank, "int"));
			
			if($this->query($insert)){ 
				
				$this->setInsertId();
				
				addMessage('success', $this->_title.' was saved successfully');
				
				return true;
				
			} else { 
				addMessage('error','Error saving '.$this->_title);
				return false;
			} 
			
		} else {
			return false;
		}
				
	}
	
	protected function _update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->getId();
			}
		}
		
		$update = sprintf("UPDATE ".$this->_table."
				SET imageID=%d, description=%s, status=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->status, "text"),  
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
		
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	
	
	public function delete($verbose = TRUE){
		if($this->image->delete()){
			if($this->_delete($verbose)){
				return true;
			}
		}
		return false;
	}
	
}