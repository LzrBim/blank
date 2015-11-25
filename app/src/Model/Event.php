<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/Event.php
----------------------------------------------------------------------------- */

class Event extends CorePerma implements GalleryInterface {
	
	//ATTRIBUTES
	public $_title = 'Event';
	public $_id = 'eventID';
	public $_table = 'event';
	public $_modReWritePath = 'calendar/';
	
	//FIELDS
	public $eventID = 0;
	public $imageID = 0;
	public $eventCategoryID;
	public $title;
	public $description;
	public $permalink;
	public $status;
	
	//CHILDREN
	public $eventDate;
	public $eventCategory;
	//public $eventGallery;
	//public $eventActor;
	
	//ONE TO MANY COLLECTIONS
	public $dates = array();
	public $images = array();
	public $actors = array();
	
	//PICKUPS
	public $startDate;
	public $endDate;
	public $singleDate;
	
	//VALIDATION
	public $_validateRules = array(
		'rules' => array( 
			'title' => array( 'required' => true ),
			'description' => array( 'required' => true ),
		)
	);
	
	private $_imageSettings = array(
 		
		'uploadMode' => 'hash', 
		'targetDirectory' => 'event/', 
		'targetFileName' => '', 
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 640,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => true,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => true,
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => true

	);
	
	/* LOAD
	----------------------------------------------------------------------------- */
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		
		$this->eventDate = new EventDate();
		
		$this->eventCategory = new EventCategory();
		
		$this->eventImage = new EventImage();
		
	}
	
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}
		
	}
	
	protected function loadChildren(){
		
		$this->images = $this->eventImage->fetchAllByParent($this->getId());
		
		$this->dates = $this->eventDate->fetchAllByParent($this->getId());
		
		if(!empty($this->eventCategoryID)){
		
			$this->eventCategory->load($this->eventCategoryID);
		
		}
		
	}
	
	/* PUBLIC FETCH
	----------------------------------------------------------------------------- */
	
	public function fetchByMonth($month, $year){
		
		$query = "SELECT event.*, eventDate.recurringDate as singleDate
			FROM event, eventDate
			WHERE event.eventID = eventDate.eventID  
			AND YEAR(eventDate.recurringDate) = ".$year." AND MONTH(eventDate.recurringDate) = ".$month."
			AND event.status = 'active'
			ORDER BY eventDate.recurringDate";
		
		return $this->loadCollection($query, false);
	}
	
	
	public function fetchFeatured(){
		
		$where = "isFeatured = 1 AND status = 'active'";
		
		return $this->fetch($where, 'rank ASC');
		
	}
	
	public function fetchFeaturedCount(){
		
		$where = "isFeatured = 1 AND status = 'active'";
		
		return $this->fetchCount($where);
		
	}
	
	
	/* ADMIN FETCH
	----------------------------------------------------------------------------- */

	public function fetchAll($orderBy = '', $limit = '', $loadChildren = true){
		
		$query = "SELECT
			".$this->_table.".*,
			MIN(eventDate.recurringDate) as startDate,
			MAX(eventDate.recurringDate) as endDate
			
			FROM event
			
			LEFT JOIN eventDate
			ON event.eventID = eventDate.eventID
			
			GROUP BY event.eventID ";
		
	
		if(!empty($orderBy)){
			$query .= "ORDER BY ".$orderBy." ";
		} else {
			$query .= "ORDER BY startDate ASC ";
		}
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit." ";
		}
		
		//wLog(1, $query);
		
		return $this->loadCollection($query, $loadChildren);
		
	}

	
	
	public function fetchAllByCategory($eventCategoryID, $orderby = '', $limit = ''){
		
		if(empty($eventCategoryID)){ 
			wLog(3, 'No eventCategoryID supplied');
			return array();
		}
		
		$where = 'eventCategoryID = ".$eventCategoryID';
		
		return $this->fetch($where);
		
	}
	
	public function fetchAllByCategoryCount($eventCategoryID){
		
		if(empty($eventCategoryID)){ 
			wLog(3, 'No eventCategoryID supplied');
			return 0;
		}
		
		$where = "eventCategoryID = ".$eventCategoryID;
		
		return $this->fetchCount($where);
	}
	
	
	
	/*-----------------------------------------------------------------------------
		CRUD
	----------------------------------------------------------------------------- */
	
	public function _insert(){
		
		if($this->image->insert()){
			$this->imageID = $this->image->imageID;
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." SET
			
			imageID=%d,
			eventCategoryID=%d,
			title=%s,
			description=%s, 
			status=%s;", 

			Sanitize::input($this->imageID, 'int'),
			Sanitize::input($this->eventCategoryID, 'int'),
			Sanitize::input($this->title, 'text'),
			Sanitize::input($this->description, 'editor'),
			Sanitize::input($this->status, 'text'));
		
		if($this->query($insert)){ 
			
			$this->setInsertId();
			
			//ADD PERMALINK
			$this->setPermalink($this->title);
			
			$update = sprintf("UPDATE ".$this->_table." SET permalink=%s WHERE ".$this->_id."=%d",
				Sanitize::input($this->permalink, "text"),  
				Sanitize::input($this->getId(), "int"));
			
			if($this->query($update)){ 
				
				addMessage('success', $this->_title.' was saved successfully');			
				
				return true;
				
			} else {
				addMessage('error', 'Error adding permalink');
				return false;
			}
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	public function _update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->getId();
			}
		}
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=%d,
			eventCategoryID=%d,
			title=%s,
			description=%s,
			permalink=%s,
			status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->eventCategoryID, "int"),
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor"),
			Sanitize::input($this->permalink, "text"), 
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			
			addMessage('success', $this->_title.' was updated successfully');
			
			return true;
			
		} else { 
			addMessage('error','Error updating '.$this->_title);
			return false;
		}
	}
	
	public function delete($verbose = true){
		
		$this->_delete($verbose);
		
		$this->eventDate->deleteAllByParent($this->getId());
		
		$this->image->delete($verbose);
		
	}
	
	/* AJAX INTERACTIONS	
	----------------------------------------------------------------------------- */
	
	public function removeImage(){
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=0 WHERE ".$this->_id."=%d",
			Sanitize::input($this->getId(), "int"));
	
		if($this->query($update)){ 
			return $this->image->delete();
		} else { 
			return false;
		}
	}
	
	/* DATE HELPERS
	----------------------------------------------------------------------------- */
	public function getDateRangeAdmin($format = 'm/d/Y'){
		
		$content = '';
		
		if(!empty($this->startDate)){
			
			$content .= date($format, strtotime($this->startDate));
			
			if(!empty($this->endDate) && (date($format, strtotime($this->startDate)) != date($format, strtotime($this->endDate)) )) {
			
				$content .= ' - '.date($format, strtotime($this->endDate));
			
			}
		
		} else {
			$content = 'No dates added';
		}
		
		return $content;
	
		
	}
	
	/* 	EVENT DATE FUNCTIONS
	----------------------------------------------------------------------------- */
	public function getDateCount(){
		
		return count($this->dates);
		
	}
	
	public function get_start_date(){
		
		if($this->getDateCount()){
			return $this->dates[0]->recurringDate;
		}
		
		return false;
		
	}
	
	public function get_end_date(){
		
		$count = $this->getDateCount();
		
		if($count){
			return $this->dates[$count-1]->recurringDate;
		}
		
		return false;
		
	}
	
	

	/* GALLERY INTERFACE
	----------------------------------------------------------------------------- */	
	public function getActiveImageCount(){
		
		return count($this->images);
	
	}

	public function getAllImageCount(){ 
	
		return $this->eventImage->fetchCount('eventID='.$this->eventID);
		
	}
	
	public function getFirstImage(){ 
	
		if($this->getActiveImageCount()){
			return $this->images[0];
		} 
	}

}