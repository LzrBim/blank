<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/Staff.php
----------------------------------------------------------------------------- */

class Staff extends CorePerma {
	
	//ATTRIBUTES
	public $_title = 'Staff';
	public $_id = 'staffID';
	public $_table = 'staff';
	protected $_modReWritePath = 'staff/'; 
	
	//FIELDS 
	public $staffID = 0;
	public $imageID = 0;
	public $staffCategoryID = 0;
	public $firstName;
	public $middleName;
	public $lastName;
	public $suffix;
	public $title;
	public $description;
	public $mobilePhone;
	public $officePhone;
	public $officePhoneExtension;
	public $email;  
	public $facebook;
	public $twitter;
	public $linkedIn;
	public $permalink;
	public $status;
	public $rank; 
	
	private $_imageSettings = array(
 		
		'uploadMode' => 'hashInsertOverWriteUpdate',
		'targetDirectory' => 'staff/', 
		
		/* ORIGINAL FILE SETTINGS */
		'originalWidth' => 800,
		'originalHeight' => 600,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 640,
		'mainHeight' => 400,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => false,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => true,
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => false

	);
	
	//CHILDREN
	public $category;
	
	//VALIDATION
	public $_validateRules = array(
		'rules' => array( 
			'firstName' => array( 'required' => true ),
			'lastName' => array( 'required' => true ),
			'email' => array( 'email' => true )
		)
	);
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		
		$this->category = new StaffCategory();
		
	}
	
	
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}
		
		if(!empty($this->staffCategoryID)){
			
			$this->category->load($this->staffCategoryID);
			
		}

	}
	
	/* FETCH
	----------------------------------------------------------------------------- */
	
	public function fetchActiveByCategory($categoryID, $orderBy = '', $limit = '', $loadChildren = true){
		
		return $this->fetch("staffCategoryID = ".$categoryID." AND status = 'active'", $orderBy, $limit, $loadChildren);
	
	}
	
	
	protected function buildSearchQuery($searchPhrase){
		
		$searchParts = explode(' ',trim($searchPhrase));
		$str = '';
		foreach($searchParts as $part) {
			$str .= 'firstName LIKE "%'.$part.'%" OR lastName LIKE "%'.$part.'%" OR ';
			
		} 
		return substr($str, 0, -3);
	}
	
	
	/* CRUD
	----------------------------------------------------------------------------- */

	public function insert(){
		
		if($this->image->insert()){
			$this->imageID = $this->image->getId();
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." SET 
			imageID=%d, 
			staffCategoryID=%d, 
			firstName=%s, 
			middleName=%s, 
			lastName=%s, 
			suffix=%s, 
			title=%s, 
			description=%s, 
			email=%s, 
			mobilePhone=%s, 
			officePhone=%s, 
			officePhoneExtension=%s,
			facebook=%s, 
			twitter=%s, 
			linkedIn=%s, 
			permalink=%s, 
			status=%s, 
			rank=%d;", 
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->staffCategoryID, "int"),
			Sanitize::input($this->firstName, "text"),
			Sanitize::input($this->middleName, "text"),
			Sanitize::input($this->lastName, "text"), 
			Sanitize::input($this->suffix, "text"),
			Sanitize::input($this->title, "text"), 
			Sanitize::input($this->description, "editor"),
			Sanitize::input($this->email, "text"),
			Sanitize::input($this->mobilePhone, "phone"), 
			Sanitize::input($this->officePhone, "phone"), 
			Sanitize::input($this->officePhoneExtension, "text"),			 
			Sanitize::input($this->facebook, "text"), 
			Sanitize::input($this->twitter, "text"), 
			Sanitize::input($this->linkedIn, "text"), 
			Sanitize::input($this->permalink, "text"), 
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->rank, "int"));
		
		if($this->query($insert)){ 
		
			$this->setInsertId();
			
			//ADD PERMALINK
			$this->setPermalink($this->getFullName());
			
			$update = sprintf("UPDATE ".$this->_table." SET permalink=%s WHERE staffID=%d",
					Sanitize::input($this->permalink, "text"),  
					Sanitize::input($this->staffID, "int"));
			
			if($this->query($update)){ 	
			
				addMessage('success', $this->_title.' was saved successfully');
				return true;
				
			} else {
				addMessage('error', 'Error while saving '.$this->_title);
				return false;
			}
			
		} 
		
		return false;
		
	}
	
	
	public function update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->getId();
			}
		}
		
		$update = sprintf("UPDATE ".$this->_table." SET 
			imageID=%d, 
			staffCategoryID=%d, 
			firstName=%s, 
			middleName=%s, 
			lastName=%s, 
			suffix=%s,
			title=%s, 
			description=%s, 
			email=%s,
			mobilePhone=%s, 
			officePhone=%s, 
			officePhoneExtension=%s, 
			facebook=%s, 
			twitter=%s, 
			linkedIn=%s, 
			status=%s 
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->staffCategoryID, "int"),
			Sanitize::input($this->firstName, "text"),
			Sanitize::input($this->middleName, "text"),
			Sanitize::input($this->lastName, "text"), 
			Sanitize::input($this->suffix, "text"), 
			Sanitize::input($this->title, "text"), 
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->email, "text"), 
			Sanitize::input($this->mobilePhone, "phone"), 
			Sanitize::input($this->officePhone, "phone"), 
			Sanitize::input($this->officePhoneExtension, "text"), 
			Sanitize::input($this->facebook, "text"), 
			Sanitize::input($this->twitter, "text"), 
			Sanitize::input($this->linkedIn, "text"),
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->staffID, "int"));
	
		if($this->query($update)){ 
			addMessage('success', $this->_title.' was saved successfully');
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	public function delete($verbose = TRUE){
		$this->image->delete();
		$this->_delete($verbose);
	}
	
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
	
	
	/* 	AJAX INTERACTIONS
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
	
}