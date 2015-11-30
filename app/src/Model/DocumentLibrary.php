<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/DocumentLibary
----------------------------------------------------------------------------- */

class DocumentLibrary extends Core {
	
	//ATTRIBUTES
	public $_title = 'Document';
	public $_id = 'documentLibraryID';
	public $_table = 'documentLibrary';
	
	//FIELDS
	public $documentLibraryID = 0;
	public $documentID = 0;
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
	
	private $_documentSettings = array(
 		
		'targetDirectory' => 'documents/', 
		
	);
	
	public function __construct(){
		
		parent::__construct();
		
		$this->document = new Document($this->_documentSettings);
		
		$this->tag = new DocumentLibraryTag();	
		
	}
	
	
	/*-----------------------------------------------------------------------------
		LOAD
	----------------------------------------------------------------------------- */
	protected function loadHook(){
		
		//DOCUMENT
		if(!empty($this->documentID)){
			$this->document->load($this->documentID);
		}

	}
	
	protected function loadChildren(){
		$this->tag->loadCollectionByParent($this->id());
	}
	
	/*-----------------------------------------------------------------------------
		CRUD
	----------------------------------------------------------------------------- */
		
	protected function _insert(){
		
		if($this->document->insert()){
			
			$this->documentID = $this->document->id();
			
			$insert = sprintf("INSERT INTO ".$this->_table." 
				(documentID, title, status) 
				VALUES (%d, %s, %s)",
				Sanitize::input($this->documentID, "int"),
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
		
			//UPDATE IMAGE
			$this->document->update();
			
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
		$this->document->delete();
		$this->tag->deleteAllTagLinksByParent($this->id());
		$this->_delete($verbose);
	}
	
	/* FETCH
	----------------------------------------------------------------------------- */
	
	
	
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
			LEFT JOIN documentLibraryTagLink iLTL 
				ON iL.documentLibraryID = iLTL.parentID
			LEFT JOIN documentLibraryTag iLT
				ON iLTL.tagID = iLT.tagID
			WHERE ".$this->buildSearchQuery($searchPhrase)." ";
		
		if(!empty($and)){
			$query .= "AND ".$and." ";
		} 
		
		$query .= "GROUP BY iL.documentLibraryID ";
		
		
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