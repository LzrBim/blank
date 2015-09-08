<? 
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/Uploader.php
----------------------------------------------------------------------------- */

/* USAGE:

Simple
$uploader = new Uploader('uploadFile', 'images/');

Full
$uploader = new Uploader('uploadFile', 'images/', array( 
	'targetFileName' 	=> '', 				// ABOVE ASSET_BASE_PATH - NO FILE EXTENSION
	'allowedList' 		=> '', 				// jpg,gif,png 
	'restrictToMime' 	=> true, 			// RESTRICT TO GLOBAL MIME LIST
	'overwrite' 			=> false,     // SHOULD IT OVERWRITE PRE-EXISTING FILE
	'useHashPrevent'	=> true,			// IN THE EVENT OF PRE-EXISTING FILE - APPEND HASH, IF FALSE IT WILL FAIL
	'maxFileSize' 		=> 40971520 	// 40MB
)); 

*/

class Uploader {

	private $uploadKey; 
	
	private $targetDirectory; 
	
	private $opts = array(
		'targetFileName' 	=> '', 				/* ABOVE ASSET_BASE_PATH - NO FILE EXTENSION - THIS REPLACES USER SUPPLIED FILE NAME */
		'allowedList' 		=> '', 				/* jpg,gif,png */
		'restrictToMime' 	=> true, 			/* RESTRICT TO GLOBAL MIME LIST */
		'overwrite' 			=> false,     /* SHOULD IT OVERWRITE PRE-EXISTING FILE */
		'useHashPrevent'	=> true,			/* IN THE EVENT OF PRE-EXISTING FILE - APPEND HASH, IF FALSE IT WILL FAIL */
		'maxFileSize' 		=> 40971520 	/* 40MB */	
	);
	
	private $fileArrayIndex;
	
	private $files = array('name' => '','type' => '', 'tmp_name' => '', 'error' => '', 'size' => '');
	
	//POST UPLOAD VARS
	private $fileName = ''; 				/* FULL FILE NAME WITH EXTENSION - title.jpg */
	private $fileRelativePath = ''; /* RELATIVE PATH TO FILE - assets/images/title.jpg */
	private $fileHttpPath = ''; 		/* FULL PATH TO FILE INCLUDING ASSET_HTTP_PATH - http://www.thirdperspective.com/assets/images/title.jpg */
	private $fileBasePath = ''; 		/* FULL PATH TO FILE INCLUDING ASSET_BASE_PATH - WEBROOT/blanksite.com/public_html/assets/images/title.jpg */
	private $fileExt = '';  				/* NO PERIOD - jpg */
	
	private $mimes = array(
											 
		'image/gif' => 'gif',
		'image/jpeg' => 'jpg', 
		'image/pjpeg' => 'jpg',
		'image/png' => 'png', 
		'image/tiff' => 'tif',
		
		'application/msword' => 'doc',
		'application/pdf' => 'pdf',
		'application/vnd.ms-powerpoint' => 'ppt',
		'application/vnd.ms-excel' => 'xls',
		'application/rtf' => 'rtf',
		
		'text/plain' => 'txt',
		'text/csv' => 'csv',
		'text/html' => 'html',
		'text/vcard' => 'vcf',
		'text/xml' => 'xml',
		
		'video/mpeg' => 'mpeg',
		'video/mp4' => 'mp4',
		'video/quicktime' => 'mov',
		'video/x-ms-wmv' => 'wmv',
		'video/msvideo' => 'wmv',
		'video/avi' => 'avi', 
		'video/x-msvideo' => 'wmv',
		'video/x-flv' => 'flv',
		'application/x-shockwave-flash' => 'swf',
		
		'application/xml' => 'xml',
		'application/zip' => 'zip',
		'application/gzip' => 'gz',
		
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx',
		'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx',
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
		'application/vnd.openxmlformats-officedocument.presentationml.slide' => 'sldx',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx'
	
	);
	
	
	public function __construct($uploadKey, $targetDirectory, $opts = array(), $fileArrayIndex = 0){
		
		if(!defined('ASSET_BASE_PATH')) {
			wLog(4, 'ASSET_BASE_PATH undefined');
			die('Uploader::__construct() - ASSET_BASE_PATH undefined');
		}
		
		if(!defined('ASSET_HTTP_PATH')) {
			wLog(4, 'ASSET_HTTP_PATH undefined');
			die('Uploader::__construct() - ASSET_HTTP_PATH undefined');
		}
		
		if(empty($uploadKey) || empty($targetDirectory)){
			wLog(4, 'Empty arguments');
			die('Uploader::__construct() - empty arguments');
		}
		
		if(!is_writable(ASSET_BASE_PATH.$targetDirectory)){
			wLog(4, 'Error opening directory for writing - '.$this->targetDirectory);
			die('Uploader::__construct() - Error opening directory for writing');
		}
		
		$this->uploadKey = $uploadKey;
		
		$this->targetDirectory = $targetDirectory;
		
		/* HANDLE OPTIONS */
		if(!empty($opts)){
			$this->opts = array_merge($this->opts, $opts);
		}
		
		/* INSERT HANDLING FOR .JPEG */
		
		if(!empty($this->opts['allowedList'])){
			
			if(strpos($this->opts['allowedList'], 'jpg') !== false){
				$this->opts['allowedList'] .= ',jpeg';
			}
			
		}
		
		/* HANDLE MULTIPLE */
		$this->fileArrayIndex = $fileArrayIndex;
		
		/* SETUP THE FILE ARRAY */
		if(array_key_exists($this->uploadKey, $_FILES)){
			
			foreach($this->files as $key => $val){
				
				if(is_array($_FILES[$this->uploadKey][$key])){
					$this->files[$key] = $_FILES[$this->uploadKey][$key][$this->fileArrayIndex];
					
				} else {
					
					$this->files[$key] = $_FILES[$this->uploadKey][$key];
				}
			}
		}		
		
		
	}
	
	/* MAIN ROUTINE */
	public function upload(){
		
		/* MANDATORY CHECKS */
		if( !$this->_checkPhpErrorCode() ) { return false; }
		
		if( !$this->_checkFileSize() ) { return false; }
				
		/* OPTIONAL CHECKS */
		if( $this->opts['restrictToMime'] ) {
			
			if( !$this->_checkGlobalMimeType() ){ 
				return false; 
			} 
				
		}
							
		/* POPULATES fileName, fileXXXXPaths, fileExt */
		if( !$this->_buildFileNameAndPath() ){ 
			wLog(3, 'Error building file name and path');
			return false; 
		} 
		
		
		/* OPTIONAL CHECKS */
		if( !empty($this->opts['allowedList']) ) {
			
			if( !$this->_checkAllowedMimeType($this->fileExt) ) { 
				wLog(2, 'not in allowedList');
				return false; 
			
			}
				
		}
		
		/* MOVE THE FILE */
		if( !empty($this->fileBasePath) ){
			
			if(move_uploaded_file($this->files['tmp_name'], $this->fileBasePath)) {
				
				addMessage("success", "The file ".$this->files['name']." (".File::getFormattedFileSize($this->files['size']).") was uploaded successfully");
				wLog(1, "The file ".$this->files['name']." (".File::getFormattedFileSize($this->files['size']).") was uploaded successfully");
				return true;
				
			} else {
				addMessage('There was an error while moving the uploaded file');
				wLog(4, "Error during move_uploaded_file");
				return false;
			}
			
		} else {
			addMessage("error", "Error resolving base path");
			wLog(4, "Error resolving base path");
			return false;
		} 
			
		
	}
	
 	/* PUBLIC GETTERS */
	public function getFileName(){ /* title.jpg */
		return $this->fileName;
	}
	
	public function getFileExtension(){ /* jpg */
		return $this->fileExt;
	}
	
	public function getRelativeFilePath(){ /* assets/images/title.jpg */
		return $this->fileRelativePath;
	}
	
	public function getHttpFilePath(){ /* http://www.thirdperspective.com/assets/images/title.jpg */
		return $this->fileHttpPath;
	}
	
	public function getBaseFilePath(){ /* WEBROOT/blanksite.com/public_html/assets/images/title.jpg */
		return $this->fileBasePath;
	}
	
	
	
	/* 	PUBLIC HELPERS
	----------------------------------------------------------------------------- */
	
	public function isUploaded(){
		
		if(array_key_exists($this->uploadKey, $_FILES)){
					
			if(is_uploaded_file($this->files['tmp_name'] )){
				return true;
			} else {
				//wLog(1, print_r($_FILES, true));
				return false;
			}

		} else {
			wLog(1, 'FILE was not populated with key specified');
		}
	}
	
	public static function is_multiple_upload(){
		
		if(count($_FILES)){
			
			foreach($_FILES as $key => $val){
				
				if(is_array($_FILES[$key]['name'])){
					return count($_FILES[$key]['name']);
				} else {
					return 0;
				}
			}			
		}		
	}
 
	/* 	PRIVATE
	----------------------------------------------------------------------------- */

	
	private function _buildFileNameAndPath(){
		
		/* SET fileExt */
		$this->fileExt = $this->_mimeToExtension($this->files['type']);
		
		if(empty($this->fileExt) && $this->opts['restrictToMime']){
			addMessage("error", "Invalid Mime Type.<br>You uploaded a file of type:  ".$this->files['type']);
			wLog(4, "Invalid Mime Type - this shouldnt happen");
			return false;
		
		} else { /* USE WHATEVER THE STRING FILE EXTENSION IS */
		
			$ext = File::getFileExtension($this->files['name']);
			
			if($ext){
				$this->fileExt = $ext;	
				
			} else {
				addMessage("error", "The file you uploaded had no file extension");
				wLog(2,"The file had no extension");
				return false;
			}
		}
		
		/* SET fileName */
		if(!empty($this->opts['targetFileName'])){ /* file name has been specified, but we need to add the file extension */
			
			$this->fileName = $this->opts['targetFileName'].'.'.$this->fileExt;
			
		} else {
			
			if(!empty($this->files['name'])){
				
				/*this function can return an empty string*/
				$this->fileName = Sanitize::fileName($this->files['name']);
				
			} else {
				$this->fileName = time().'.'.$this->fileExt;
				wLog(1, 'No name was supplied by local filesystem, generating hash');
			}
		}
		
		/* BUILD FILE NAME VARS */
		$this->fileRelativePath =  ASSET_RELATIVE_PATH.$this->targetDirectory.$this->fileName;
		$this->fileHttpPath = ASSET_HTTP_PATH.$this->targetDirectory.$this->fileName; 
		$this->fileBasePath = ASSET_BASE_PATH.$this->targetDirectory.$this->fileName; 
		
		
		/* OVERWRITE CASES */
		if(!$this->opts['overwrite'] && file_exists($this->fileBasePath) ) {
			
			if($this->opts['useHashPrevent']){
				
				wLog(2, "hashPrevent");
				$hash = time();
				
				$this->fileName = $hash.'-'.$this->fileName;
				$this->fileRelativePath =  ASSET_RELATIVE_PATH.$this->targetDirectory.$this->fileName;
				$this->fileHttpPath = ASSET_HTTP_PATH.$this->targetDirectory.$this->fileName; 
				$this->fileBasePath = ASSET_BASE_PATH.$this->targetDirectory.$this->fileName; 
				
			} else {
				addMessage("error", "This file already exists");
				wLog(2, "This file already exists and overwrite is set to false");
				return false;
			}	
		}
		
		return true;
		
	}
	
	private function _checkAllowedMimeType($ext){
		
		if(!empty($this->opts['allowedList'])){
			
			$allowedArr = array_map('trim', explode(',', $this->opts['allowedList']));
			
			if(in_array($ext, $allowedArr)) {
				return true;
				
			} else {
				addMessage("error", "Invalid File Extension.<br>Acceptable file types are:  "
					.implode(', ',$allowedArr)."<br>You uploaded a file of type:  ".$this->files['type']);
				return false; 
			}
		
		} 
		return true;			
		
	}
	
  
	private function _checkPhpErrorCode(){

		$uploadErrors = array(
			UPLOAD_ERR_INI_SIZE 	=> 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			UPLOAD_ERR_FORM_SIZE 	=> 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
			UPLOAD_ERR_PARTIAL 		=> 'The uploaded file was only partially uploaded.',
			UPLOAD_ERR_NO_FILE 		=> 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR 	=> 'Missing a temporary folder.',
			UPLOAD_ERR_CANT_WRITE 	=> 'Failed to write file to disk.',
		);
		$errorCode = $this->files['error'];
		
		if($errorCode !== UPLOAD_ERR_OK) {
			
			if(isset($uploadErrors[$errorCode])) {
			   	addMessage('error', $uploadErrors[$errorCode]);
			   	return false;
					
			} else {
				addMessage('error', 'An unknown php error code was found');
				wLog(2, 'An unknown php error code was found');
				return false;
				
			}
		} else {
			return true;
		}
	}


	private function _checkFileSize(){ 
	
		if($this->files['size'] < $this->opts['maxFileSize']){
			return true;
			
		} else {
			addMessage("warning","Sorry, the file you uploaded is too large.<br>Your file was:  "
				.File::getFormattedFileSize($this->files['size'])
				.", and the limit is set to:  "
				.File::getFormattedFileSize($this->opts['maxFileSize']));
		}
	}
	
	private function getUrlMimeType($url) {
			$buffer = file_get_contents($url);
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			return $finfo->buffer($buffer);
	}
	
	
	private function _mimeToExtension($mimeType){	
		
		if(array_key_exists($mimeType, $this->mimes)) {
			
			return $this->mimes[$mimeType];
			
		} else {
			addMessage("error","Error converting mime type to extension");
			wLog(2, 'Mime Extension not found: '.$mimeType);
			return false;
		}	
	}
	
	private function _checkGlobalMimeType(){
			
		if(array_key_exists($this->files['type'], $this->mimes)) {
			return true;
			
		} else {
			addMessage("error","Invalid Mime Type.<br>You uploaded a file of type:  ".$this->files['type']);
			wLog(2, 'Invalid Mime Type: '.$this->files['type']);
			return false;
		}	
		
	}	
} 