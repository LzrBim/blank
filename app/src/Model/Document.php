<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: app/models/Document.php
----------------------------------------------------------------------------- */

class Document extends Core {
	
	/* ATTRIBUTES */
	public $_title = 'Document';
	public $_id = 'documentID';
	public $_table = 'document';
	
	/* FIELDS */
	public $documentID = 0;
	public $fileName = '';
	public $dateAdded = '';
	public $dateModified = ''; 
	
	/* UPLOADER SETTINGS */	
	private $_uploaderSettings = array(
		'targetDocumentName' => '',
		'allowedList' => 'doc,pdf,ppt',
		'restrictToMime' => true,
		'overwrite' => false,
		'maxDocumentSize' => 20971520 //20mb
	);
	
	/* SETTINGS */
	private $_settings = array(
 		
		'targetDirectory' => 'documents/', 
		'targetDocumentName' => '', 
		'inputName' => 'uploadFile',
		
	);
		
	
	public function __construct($settings, $uploaderSettings = array()){
			
		$this->_settings = array_merge($this->_settings, $settings);
		
		$this->_uploaderSettings = array_merge($this->_uploaderSettings, $uploaderSettings);
		
		parent::__construct();
		
	}
	
	public function _insert(){
		
		if($this->upload('insert')){
				
			$insert = sprintf("INSERT INTO ".$this->_table." 
					(fileName, dateAdded) 
					VALUES (%s,  NOW())",
				Sanitize::input($this->fileName, "text"));
			
			if($this->query($insert)){ 
				$this->setInsertId();
				return true;
				
			} else { 
				addMessage('error','Error inserting '.$this->_title);
				return false;
			} 
		}
		return false;
	}
	
	public function _update(){
	
		if(!empty($this->fileNameMain)){ /* If there is already an file, run an overwrite */
			
			$info = pathinfo($this->fileNameMain);
		
			$this->_uploaderSettings['targetDocumentName'] = $info['filename'];
			$this->_uploaderSettings['allowedList'] = $info['extension'];
		}
			
		
		if($this->upload('update')){
		
			$update = sprintf("UPDATE ".$this->_table."
					SET fileName=%s
					WHERE ".$this->_id."=%d",
				Sanitize::input($this->fileName, "text"),
				Sanitize::input($this->id(), "int"));
		
			if($this->query($update)){ 
				addMessage('success', $this->_title.' was saved successfully');
				return true;
				
			} else { 
				addMessage('error','Error saving '.$this->_title);
				return false;
			}
		} 
	}
	
	public function delete($verbose = TRUE){
		
		$this->_delete_file();
	
		$this->_delete($verbose = TRUE);
	}
	
	private function _delete_file(){
		
		$path = $this->getBasePath();
		
		if($path && file_exists($path)){
			if(!unlink($path)){
				wLog(3, 'error while deleting document');
			}
		}
	}
	
	
	/* HELPERS */
	
	public function upload($action = 'insert', $verbose = true, $showWarning = false){
		
		if($action == 'update'){
			$this->_uploaderSettings['overwrite'] = true;
		}
		
		$uploader = new Uploader($this->_settings['inputName'], $this->_settings['targetDirectory'], $this->_uploaderSettings);
		
		if($uploader->isUploaded()){

			if($uploader->upload()){
				
				$this->fileName = $this->_settings['targetDirectory'].$uploader->getFileName();		
				
				return true;
				
			} else {
				return false;
			}
		}	else {
			if($verbose && $showWarning){
				addMessage('warning','No Document was uploaded');
			}
			return true;
		}
	}
	
	
	
	/* DISPLAY
	----------------------------------------------------------------------------- */
	
	/*
	USAGE
	display_link(array(
		title = '',
		class = '',
		target = ''
	));
	*/
	
	public function display_link($opts = array()){ 
	
		if (file_exists(ASSET_BASE_PATH.$this->fileName)) {
			
			$filePath = ASSET_HTTP_PATH.$this->fileName;
			$title = basename($filePath);
			$class = '';
			$target = '';
			
			if(!empty($opts)){
		
				if( !empty($opts['title']) ){
					$title = $opts['title'];
				}

				if( !empty($opts['class']) ){
					$class = 'class="'.$opts['class'].'"';
				} 
				
				if( !empty($opts['target']) ){
					$class = 'target="'.$opts['class'].'"';
				}
			}
						
      echo '<a data-documentID="'.$this->documentID.'" href="'.$filePath.'" '.$class.' '.$target.' />'.$title.'</a>';
			
		} else {
			echo '<!-- No Document Found -->';
		}
	}	
	
	/* for 3e admin view */
	public function get_document_summary(){ 
	
		if (file_exists(ASSET_BASE_PATH.$this->fileName)) {
			
			$fileBasePath = ASSET_BASE_PATH.$this->fileName;
			$filePath = ASSET_HTTP_PATH.$this->fileName; 
			
			ob_start(); ?>
      
      <table cellpadding="2" cellspacing="0" border="0">
      <tr>
      <td class="vat"><?= $this->getFileIcon(); ?></td>
      <td class="vat"><a href="<?= $filePath; ?>" target="_blank" /><?= basename($this->fileName); ?></a> <span class="sm" style="margin-left:10px;"><?= File::getFormattedFileSize(filesize($fileBasePath)); ?><br /><?= $filePath; ?></span></td>
      </tr>
      <tr>
      </tr>
      </table><?  
		
			return ob_get_clean();
			
		} else {
			echo 'No document found.';
		}
	}
	
	/*-----------------------------------------------------------------------------
	  GET FILE INFORMATION
	----------------------------------------------------------------------------- */
	
	public function getHref($useAbsolutePath = true){ 
	
		if (file_exists(ASSET_BASE_PATH.$this->fileName)) {
			
			if($useAbsolutePath){
				return ASSET_HTTP_PATH.$this->fileName;
				
			} else {
				return ASSET_RELATIVE_PATH.$this->fileName;
				
			}
		}
		return '';
	}
	
	public function getFileIcon(){ 
		return '<i class="fileType fileType-'.File::getFileExtension($this->fileName).'"></i>';  
	}
	
	/*-----------------------------------------------------------------------------
	  HELPERS
	----------------------------------------------------------------------------- */
	
	
	public function hasFile(){ 
		if($this->fileName && file_exists(ASSET_BASE_PATH.$this->fileName)){
			return true;
		} else {
			return false;
		}
	}
	
	/*-----------------------------------------------------------------------------
	  GET BASE PATH OF IMAGE / 3E ONLY
	----------------------------------------------------------------------------- */
	
	public function getBasePath($type){
		
		if (!empty($fileName) && file_exists(ASSET_BASE_PATH.$fileName)) {
			
			return ASSET_BASE_PATH.$fileName;
			
		} else {
			wLog(3, 'file did not exist in base path');
			return '';
		}
	}

	
}