<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/PagePromoBlock.php
----------------------------------------------------------------------------- */

namespace App\Model;

class PagePromoBlock extends BaseModel {
	
	//ATTRIBUTES
	public $_title = 'Page Block';
	public $_id = 'pagePromoBlockID';
	public $_table = 'pagePromoBlock';
	
	//FIELDS
	public $pagePromoBlockID;
	public $pageBlockTemplateID;
	public $type; //hardcoded types: editor, text, list, headline
	public $pageID = 0;
	public $imageID;
	public $title;
	public $headline1;
	public $headline2;
	public $headline3;
	public $description1;
	public $description2;
	public $description3;
	public $href1;
	public $href2;
	public $href3;
	public $status;

	private $_imageSettings = array(
 		
		'uploadMode' => 'hash', 
		'targetDirectory' => 'block/', 
		'targetFileName' => '', 
		'inputName' => 'uploadFile',
		
		/*ORIGINAL FILE SETTINGS */
		'originalWidth' => 1660,
		'originalHeight' => 1140,
		
		/* MAIN IMAGE SETTINGS */
		'hasMain' => true,
		'mainWidth' => 800,
		'mainHeight' => 800,
		'hasMainCrop' => true,
		'forceOutMain' => false,
		'lockMainAspectRatio' => true,
		
		/* THUMB IMAGE SETTINGS */
		'hasThumb' => false,

	);
	
	public function __construct(){
		
		parent::__construct();
		
		//THIS MAY NEVER HAPPEN B/C WE DON'T INSERT VIA CMS
		$this->image = new Image($this->_imageSettings);
		
	}
	
	protected function loadHook(){
		
		//SWITCH TYPE OF IMAGE SETTINGS BASED ON BLOCK TYPE
		if($this->pageBlockTemplateID == 'promo'){
			
			$this->_imageSettings['mainWidth'] = 200; 
			$this->_imageSettings['mainHeight'] = 200; 
		
		} elseif($this->pageBlockTemplateID == 'promo2'){
			
			$this->_imageSettings['mainWidth'] = 300; 
			$this->_imageSettings['mainHeight'] = 100; 
			
		}
		
		//RELOAD
		$this->image = new Image($this->_imageSettings);
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		}

	}
		
	
	/* 	FETCH ADMIN
	----------------------------------------------------------------------------- */

	public function fetchAllByPage($pageID, $orderBy = ''){
		
		if(empty($pageID)){
			wLog(3, 'No pageID supplied');
			return false;
		}
		
		$where = "status = 'active' AND pageID = ".$pageID;
		
		
		return $this->fetch($where, $orderBy);
	}
	
	public function fetchAllByPageCount($pageID){
		
		if(empty($pageID)){
			wLog(3, 'No pageID supplied');
			return false;
		}
		
		return count($this->fetchAllByPage($pageID));
	}
	
	/* 	FETCH FRONT
	----------------------------------------------------------------------------- */
	
	public function fetchByPageAndSiteWide($pageID){
		
		if(empty($pageID)){
			wLog(3, 'No pageID supplied');
			return false;
		}
		
		$where = "status = 'active' AND ( pageID = 0";
		
		if(!empty($pageID)){
			$where .= ' OR pageID = '.$pageID;
		}
		
		$where .= ' )';
		
		return $this->fetch($where);
	}

	
	/* CRUD
	----------------------------------------------------------------------------- */

	
	public function insert(){
		
		if($this->image->insert()){
			$this->imageID = $this->image->id();
		}
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(templateID, pageID, imageID, title, headline1, headline2, headline3, description1, description2, description3, href1, href2, href3, status) 
			VALUES (%d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,  %s)",
			Sanitize::input($this->templateID, "int"),
			Sanitize::input($this->pageID, "int"),
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->title, "text"), 
			Sanitize::input($this->headline1, "text"), 
			Sanitize::input($this->headline2, "text"), 
			Sanitize::input($this->headline3, "text"),
			Sanitize::input($this->description1, "editor"), 
			Sanitize::input($this->description2, "editor"), 
			Sanitize::input($this->description3, "editor"), 
			Sanitize::input($this->href1, "text"), 
			Sanitize::input($this->href2, "text"), 
			Sanitize::input($this->href3, "text"), 
			Sanitize::input($this->status, "text"));
		
		if($this->query($insert)){ 
			$this->setInsertId();
			return true;
			
		} else { 
			addMessage('error','Error Updating '.$this->_title);
			return false;
		} 
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
		
		$update = sprintf("UPDATE ".$this->_table."
			SET imageID=%d, headline1=%s, headline2=%s, headline3=%s, description1=%s, description2=%s, description3=%s, href1=%s, href2=%s, href3=%s, status=%s 
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->imageID, "int"),
			Sanitize::input($this->headline1, "text"), 
			Sanitize::input($this->headline2, "text"), 
			Sanitize::input($this->headline3, "text"),
			Sanitize::input($this->description1, "editor"), 
			Sanitize::input($this->description2, "editor"), 
			Sanitize::input($this->description3, "editor"), 
			Sanitize::input($this->href1, "text"), 
			Sanitize::input($this->href2, "text"),
			Sanitize::input($this->href3, "text"),
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
	
	/* 	HELPERS
	----------------------------------------------------------------------------- */

	public function getParentPageTitle(){
		
		if($this->pageID != 0 ){
			
			$page = new page();
			$page->load($this->pageID);
			return $page->title;
			
		}
		
		return 'All Pages';
		
	}
	
	public function isType(){
		
		if(!empty($this->type)){
			
			return true;
			
		}
		
		return false;
		
	}
	
	public function toArray(){
		return array(
			'title' 				=> $this->title,
			'headline1' 		=> $this->headline1,
			'headline2' 		=> $this->headline2,
			'headline3' 		=> $this->headline3,
			'description1' 	=> $this->description1,
			'description2' 	=> $this->description2,
			'description3' 	=> $this->description3,
			'href1' 				=> $this->href1,
			'href2' 				=> $this->href2,
			'href3' 				=> $this->href3
		);
	
	}

}