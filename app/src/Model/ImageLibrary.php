<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/ImageLibary
----------------------------------------------------------------------------- */

class ImageLibrary extends Core {
	
	//ATTRIBUTES
	public $_title = 'Image';
	public $_id = 'imageLibraryID';
	public $_table = 'imageLibrary';
	
	//FIELDS
	public $imageLibraryID = 0;
	public $imageID = 0;
	public $title = '';
	public $status = 'active';
	
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
 		
		'uploadMode' => 'hashInsertOverWriteUpdate',
		'targetDirectory' => 'images/', 
		
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
		'thumbWidth' => 120,
		'thumbHeight' => 100,
		'hasThumbCrop' => true,
		'forceOutThumb' => false,
		'lockThumbAspectRatio' => false

	);
	
	public function __construct(){
		
		parent::__construct();
		
		$this->image = new Image($this->_imageSettings);
		$this->tag = new ImageLibraryTag();
		
	}
	
	
	/* LOAD
	----------------------------------------------------------------------------- */
	protected function loadHook(){
		
		//IMAGE
		if(!empty($this->imageID)){
			$this->image->load($this->imageID);
		} else {
			wLog(2, 'Image Library has no image - isssue');
		}

	}
	
	protected function loadChildren(){
		$this->tag->loadCollectionByParent($this->id());
	}
	
	/* CRUD
	----------------------------------------------------------------------------- */
		
	protected function _insert(){
		
		if($this->image->insert()){
			
			$this->imageID = $this->image->id();
			
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(imageID, title, status) 
				VALUES (%d, %s, %s)",
				Sanitize::input($this->imageID, "int"),
				Sanitize::input($this->title, "text"),
				Sanitize::input($this->status, "text"));
			
			if($this->query($insert)){ 
				
				$this->setInsertId();
				
				addMessage('success', $this->_title.' was saved successfully');
				
				//INSERT TAGS
				$this->tag->updateTagsByTitleCsv($this->id());
				
				return true;
				
			} else { 
				addMessage('error','Error updating '.$this->_title);
				return false;
			} 
			
		} else {
			addMessage('error','No image was uploaded');
			return false;
		}
				
	}
	
	protected function _update(){
		
		$update = sprintf("UPDATE ".$this->_table."
				SET title=%s
				WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->id(), "int"));
	
		if($this->query($update)){ 
		
			$this->image->update();
			
			//UPDATE TAGS
			$this->tag->updateTagsByTitleCsv($this->id());
		
			addMessage('success', $this->_title.' was saved successfully');
			return true;
			
		} else { 
			addMessage('error','Error saving '.$this->_title);
			return false;
		}
	}
	
	
	
	public function delete($verbose = TRUE){
		
		$this->image->delete();
		
		$this->tag->deleteAllTagLinksByParent($this->id());
		
		$this->_delete($verbose);
	}
	
	/*-----------------------------------------------------------------------------
		FETCH
	----------------------------------------------------------------------------- */
	
	public function fetchActive(){
		return $this->fetch('', 'libraryImageID DESC');
	}
	
	public function fetchActiveCount(){
		return $this->fetchCount();
	}
	
	/* SEARCH
	----------------------------------------------------------------------------- */
	
	protected function buildSearchQuery($searchPhrase){
		
		$searchParts = explode(' ',trim($searchPhrase));
		$str = '';
		foreach($searchParts as $part) {
			$str .= "iL.title LIKE '%".$part."%' or iLT.title like '%".$part."%' OR ";
			
		} 
		return substr($str, 0, -3);
	}
	
	public function fetchSearch($searchPhrase, $and = '', $orderBy = '', $limit = ''){
		
		if(empty($searchPhrase)){
			wLog(2, get_class($this).'::fetchSearch() - no searchPhrase supplied');
			return array();
		}
		$query = "SELECT iL.*, GROUP_CONCAT(iLT.title SEPARATOR ', ') as tagTitle 
			FROM `".$this->_table."` iL
			LEFT JOIN imageLibraryTagLink iLTL 
				ON iL.imageLibraryID = iLTL.parentID
			LEFT JOIN imageLibraryTag iLT
				ON iLTL.tagID = iLT.tagID
			WHERE ".$this->buildSearchQuery($searchPhrase)." ";
		
		if(!empty($and)){
			$query .= "AND ".$and." ";
		} 
		
		$query .= "GROUP BY iL.imageLibraryID ";
		
		if(!empty($orderBy)){
			$query .= "ORDER BY iL.".$orderBy." ";
		} else {
			$query .= "ORDER BY iL.".$this->_id." DESC ";
		}
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit;
		} 
		
		
		return $this->loadCollection($query);
	}
	
	public function fetchSearchCount($searchPhrase){
		
		return count($this->fetchSearch($searchPhrase));
	}
	
	
}