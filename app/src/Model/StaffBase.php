<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/models/Staff.php
----------------------------------------------------------------------------- */

namespace App\Model;

use \App\Lib\Sanitize;

class StaffBase extends BaseModel {
	
	use SluggableTrait;
	use ImageTrait;
	
	//ATTRIBUTES
	public $_title = 'Staff';
	public $_id = 'staffID';
	public $_table = 'staff';
	
	//FIELDS 
	public $staffID = 0;	
	public $imageID = 0;	
	public $staffCategoryID = 0;
	
	public $slug;
	
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
	
	public $status;
	public $rank; 
	
	public $_imageSettings = array(
 		
		'targetDir' => 'staff/', 
		
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
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		
		$this->category = new StaffCategory();
		
	}
	
	public function with($with){
		
		if(!$this->isLoaded()){	return false;	}
		
		if(!is_array($with)){	$with = array($with);	}
		
		foreach($with as $relation){
			
			if($relation == '*' || $relation == 'image'){
				
				if($this->imageID){
					$this->image->load($this->imageID);
				}
			}
			
			if($relation == '*' || $relation == 'category'){
				
				if($this->staffCategoryID){
					$this->category->load($this->staffCategoryID);
				}
				
			}
		}
		
		return $this;
			
	}
	
	
	/* FETCH
	----------------------------------------------------------------------------- */
	
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
		
		$this->setSlug($this->getFullName());
		
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
			slug=%s, 
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
			Sanitize::input($this->slug, "text"), 
			Sanitize::input($this->status, "text"),
			Sanitize::input($this->rank, "int"));
		
		return $this->queryInsert($insert);
		
	}
	
	
	public function update(){
		
		//UPDATE IMAGE
		if(!empty($this->imageID)){
			$this->image->update();
		} else {
			if($this->image->insert()){
				$this->imageID = $this->image->id();
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
	
		return $this->query($update);
		
	}
	
	public function delete($verbose = TRUE){
		
		$this->image->delete();
		
		$this->_delete($verbose);
		
	}	
	
}