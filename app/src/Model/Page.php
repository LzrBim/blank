<?
/*-----------------------------------------------------------------------------
 * SITE: 
 * FILE: /models/Page.php
----------------------------------------------------------------------------- */

class Page extends PageBase {  
	
	//CHILDREN	
	public $pageVersion;
	public $pageBlock = array();
	
	//PICKUPS
	public $headline;
	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function __construct(){
		
		parent::__construct();
		
		$this->version = new PageVersion();
		
		$this->promoBlock = new PagePromoBlock();
		
	}
	
	protected function loadChildren(){
		
		$this->version->loadActiveByPage($this->getId());
		
		$this->promoBlocks = $this->promoBlock->fetchByPageAndSiteWide($this->getId());
		
		$this->_loadedChildren = true;
		
	}
	
	protected function loadHook(){
		if($this->getId() == 1){
			$this->permalink = HTTP_PATH;
		}
	}
	
	/* PUBLIC FETCH
	----------------------------------------------------------------------------- */
	public function fetchActiveContent($orderBy = '', $limit = ''){
	
		$query = "SELECT page.pageID, page.title, page.permalink, pageVersion.headline
			FROM page, pageVersion
			WHERE page.pageID = pageVersion.pageID
			AND pageVersion.status = 'active' 
			AND page.isHardCoded IS NULL
			AND page.status = 'active'";
		
		if(!empty($limit)){
			$query .= "LIMIT ".$limit;
		}
		
		return $this->loadCollection($query);

	}
	/* ADMIN FETCH 
	----------------------------------------------------------------------------- */

	/* CRUD
	----------------------------------------------------------------------------- */

	
	public function _insert(){
		
		$this->setPermalink($this->title);
		
		$insert = sprintf("INSERT INTO ".$this->_table." 
			(title, permalink, metaTitle, metaDescription, metaKeywords, status, dateAdded) 
			VALUES (%s, %s, %s, %s, %s, %s, %s)",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->metaTitle, "text"),
			Sanitize::input($this->metaDescription, "text"),
			Sanitize::input($this->metaKeywords, "text"),
			Sanitize::input($this->status, "text"),
			'NOW()');
		
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
			SET title=%s, permalink=%s, metaTitle=%s, metaDescription=%s, metaKeywords=%s, status=%s
			WHERE ".$this->_id."=%d",
			Sanitize::input($this->title, "text"),
			Sanitize::input($this->permalink, "text"),
			Sanitize::input($this->metaTitle, "text"),
			Sanitize::input($this->metaDescription, "text"), 
			Sanitize::input($this->metaKeywords, "text"),
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
	
	/* 	EXTEND CORE-PERMA FOR HARD CODED
	----------------------------------------------------------------------------- */

	public function getPermalink(){
		if($this->isHardCoded){
			return $this->permalink.'/'; 
		} else {
		
			return $this->_modReWritePath.$this->permalink.'/'; 
		}
		
	}
	
	/* 	CHILD HELPERS
	----------------------------------------------------------------------------- */
	public function getPromoBlock($pagePromoBlockID){
		
		foreach($this->promoBlocks as $promoBlock){
			
			if($promoBlock->getId() == $pagePromoBlockID){
				
				return $promoBlock;
			}
		}
		
		wLog(3, 'Page Promo Block not found: '.$pagePromoBlockID);
		
		return '';
		
	}
	
	public function getPromoBlockChildrenCount(){
		
		$count = 0;
		
		if(count($this->promoBlocks)){
			
			foreach($this->promoBlocks as $promoBlock){
				
				if($promoBlock->pageID == $this->getId()){
					$count++;
				}
			}
		}
		
		return $count;
		
	}
	
	/* FRONT HELPERS
	----------------------------------------------------------------------------- */
	public function isActive(){
		
		if($this->status != 'active'){
			return false;
		}
		
		
		if(!$this->isHardCoded){
			
			if($this->version->isLoaded()){
				
				return true;
				
			}
		}
		
	}
	
	
	
	public function fetchFrontSearch($searchPhrase, $limit = ''){
		
		if(empty($searchPhrase)){
			wLog(3, 'no searchPhrase supplied');
			return array();
		}
		
		$searchParts = explode(' ',trim($searchPhrase));
		$str = '';
		foreach($searchParts as $part) {
			$str .= 'searchConcat LIKE "%'.$part.'%" OR headline LIKE "%'.$part.'%" OR ';
			
		} 
		$str = substr($str, 0, -3);
		
		
		$query = "SELECT page.pageID, page.title, page.permalink, pageVersion.pageVersionID, pageVersion.headline, 
				CONCAT(
					IFNULL(GROUP_CONCAT(pageVersionBlock.headline1), ''),
					IFNULL(GROUP_CONCAT(pageVersionBlock.headline2), ''),
					IFNULL(GROUP_CONCAT(pageVersionBlock.description1), ''), 
					IFNULL(GROUP_CONCAT(pageVersionBlock.description2), '')
				) AS searchConcat
			FROM page, pageVersion, pageVersionBlockLink
			LEFT JOIN pageVersionBlock 
				ON pageVersionBlock.pageVersionBlockID = pageVersionBlockLink.pageVersionBlockID
			WHERE page.pageID = pageVersion.pageID
			AND pageVersionBlockLink.pageVersionID = pageVersion.pageVersionID 
			AND pageVersion.status = 'active' 
			AND page.isHardCoded IS NULL
			AND page.status = 'active'
			GROUP BY page.pageID
			HAVING ".$str;
		
		/*if(!empty($limit)){
			$query .= "LIMIT ".$limit;
		}*/
		
		return $this->loadCollection($query);
	}
	
	public function fetchFrontSearchCount($searchPhrase){
		
		return count($this->fetchFrontSearch($searchPhrase));
		
	}

	
	/* ADMIN HELPERS
	----------------------------------------------------------------------------- */
	public function isActiveButUnpublished(){
		
		if(!$this->isHardCoded){
			
			$this->version->loadActiveByPage($this->getId());
			if(!$this->version->isLoaded()){
				return true;
			}
			
		}
		return false;
	
	}
	
	
	
}