<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/
----------------------------------------------------------------------------- */

class Slider extends Core {
	
	//ATTRIBUTES
	public $_title = 'Slider';
	public $_id = 'sliderID';
	public $_table = 'slider';
	
	//FIELDS
	public $sliderID = 0;
	public $imageID = 0;
	public $title = '';
	public $description = '';
	public $status = 'active';
	public $rank = 100;
	
	public $_validateRules = array(
		'rules' => array( 
			'title' => array( 'required' => true )
		)
	);
	
	public $_validateRulesInsert = array(
		'rules' => array( 
			'uploadFile' => array( 'required' => true )
		)
	);
	
	private $_imageSettings = array(
 		
		'uploadMode' => 'hash',  //hashInsertOverWriteUpdate, overwrite,	hash
		'targetDirectory' => 'slider/', 
		'targetFileName' => '',
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 1140,
		'mainHeight' => 600,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => true,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => false,
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => false

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
	
	
	/* CRUD
	----------------------------------------------------------------------------- */
	public function insert(){
		
		/* IMAGE IS MANDATORY */
		if($this->image->insert()){
			
			$this->imageID = $this->image->id();
			
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(imageID, title, description, status, rank) 
				VALUES (%d, %s, %s, %s, %d)",
				Sanitize::input($this->imageID, "int"),
				Sanitize::input($this->title, "text"),
				Sanitize::input($this->description, "editor"), 
				Sanitize::input($this->status, "text"), 
				Sanitize::input($this->rank, "int"));
			
			if($this->query($insert)){ 
				
				$this->setInsertId();
				
				addMessage('success', $this->_title.' was saved successfully');
				
				return true;
				
			} else { 
				addMessage('error','Error updating '.$this->_title);
				return false;
			} 
			
		} else {
			addMessage('error','Slider image is required');
			return false;
		}
				
	}
	
	public function update(){
		
		$update = sprintf("UPDATE ".$this->_table."
				SET title=%s, description=%s, status=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->description, "editor"), 
			Sanitize::input($this->status, "text"),  
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
		
			$this->image->update();
															
			addMessage('success', $this->_title.' was saved successfully');
			
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	
	
	public function delete($verbose = TRUE){
		$this->image->delete();
		$this->_delete($verbose);
	}
	
	
	/*-----------------------------------------------------------------------------
	HELPERS
	----------------------------------------------------------------------------- */
	
	
}