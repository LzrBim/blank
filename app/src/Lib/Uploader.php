<? 
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/Uploader.php
----------------------------------------------------------------------------- */

namespace App\Lib;

use Psr\Log\LoggerInterface;
use Slim\Flash;
use App\Lib\File;

class Uploader {

	//PRIVATE
	private $uploadKey = 'uploadFile';
	private $targetDir = ''; 
	private $fileArrayIndex = -1;
	private $files = array('name' => '','type' => '', 'tmp_name' => '', 'error' => '', 'size' => '');
	
	private $logger;
	private $flash;
	
	//POST UPLOAD VARS
	private $fileName = ''; 				/* FULL FILE NAME WITH EXTENSION - title.jpg */
	private $fileRelativePath = ''; /* RELATIVE PATH TO FILE - assets/images/title.jpg */
	private $fileBasePath = ''; 		/* FULL PATH TO FILE INCLUDING ASSET_BASE_PATH - WEBROOT/blanksite.com/public_html/assets/images/title.jpg */
	private $fileExt = '';  				/* NO PERIOD - jpg */
	
	
	public function __construct(LoggerInterface $logger, $flash){
		
		if(!defined('ASSET_BASE_PATH')) {
			die('Uploader::__construct() - ASSET_BASE_PATH undefined');
		}
		
		$this->logger = $logger;
		
		$this->flash = $flash;
		
		$this->setDefaults();
		
		$this->formatFileArr();
		
		$this->mimes = File::getValidUploadMime();
		
	}
	
	private function setDefaults(){
		
		/* OPTIONS */
		$this->opts = array(
			'targetFileName' 	=> '', 				/* ABOVE ASSET_BASE_PATH - NO FILE EXTENSION - THIS REPLACES USER SUPPLIED FILE NAME */
			'allowedList' 		=> '', 				/* jpg,gif,png */
			'restrictToMime' 	=> true, 			/* RESTRICT TO GLOBAL MIME LIST */
			'overwrite' 			=> false,     /* SHOULD IT OVERWRITE PRE-EXISTING FILE */
			'useHashPrevent'	=> true,			/* IN THE EVENT OF PRE-EXISTING FILE - APPEND HASH, IF FALSE IT WILL FAIL */
			'maxFileSize' 		=> 40971520 	/* 40MB */	
		);
		
		/* INSERT HANDLING FOR .JPEG */
		if(!empty($this->opts['allowedList'])){
			
			if(strpos($this->opts['allowedList'], 'jpg') !== false){
				$this->opts['allowedList'] .= ',jpeg';
			}
			
		}
	}
	
	//OPTION SETTERS
	
	public function setKey($key){
	
		$this->uploadKey = $key;
		
		//reset file array
		$this->formatFileArr();
		return $this;
		
	}
	
	public function setTargetDir($targetDir){
		
		$this->targetDir = $targetDir;
		return $this;
		
	}
	
	public function setIndex($index){
	
		$this->fileArrayIndex = $index;
		//reset file array
		$this->formatFileArr();
		return $this;
		
	}
	
	public function setTargetFileName($fileName){
	
		$this->opts['targetFileName'] = $fileName;
		return $this;
		
	}
	
	public function setAllowedList($allowedList){
	
		$this->opts['allowedList'] = $allowedList;
		return $this;
		
	}
	
	public function disableMimeCheck(){
	
		$this->opts['restrictToMime'] = false;
		return $this;
		
	}
	
	public function enableOverwrite(){
	
		$this->opts['overwrite'] = true;
		return $this;
		
	}
	
	public function disableHashPrevent(){
	
		$this->opts['useHashPrevent'] = false;
		return $this;
		
	}
	
	public function setMaxFileSize($size){
	
		$this->opts['maxFileSize'] = $size;
		return $this;
		
	}
	
	/* PUBLIC GETTERS */
	public function getFileName(){ /* title.jpg */
		return $this->fileName;
	}
	
	public function getFileExtension(){ /* jpg */
		return $this->fileExt;
	}
	
	public function getFileRelativePath(){ /* /assets/images/title.jpg */
		return $this->fileRelativePath;
	}
	
	public function getFileBasePath(){ /* WEBROOT/blanksite.com/public_html/assets/images/title.jpg */
		return $this->fileBasePath;
	}
	
	
	/* UPLOAD
	----------------------------------------------------------------------------- */

	public function upload(){
		
		if(!is_writable(ASSET_BASE_PATH.$this->targetDir)){
			die('Uploader::__construct() - Error opening directory for writing');
		}
		
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
		
			$this->logger->error('Error building file name and path');
			return false;
			
		} 
		
		
		/* OPTIONAL CHECKS */
		if( !empty($this->opts['allowedList']) ) {
			
			if( !$this->_checkAllowedMimeType($this->fileExt) ) {
				
				$this->logger->warning('not in allowedList');
				return false; 
			
			}
				
		}
		
		/* MOVE THE FILE */
		if( !empty($this->fileBasePath) ){
			
			if(move_uploaded_file($this->files['tmp_name'], $this->fileBasePath)) {
				
				$this->logger->info("The file ".$this->files['name']." (".File::getFormattedFileSize($this->files['size']).") was uploaded successfully");
				return true;
				
			} else {
				$this->logger->warning("Error during move_uploaded_file");
				return false;
			}
			
		} else {
			$this->logger->warning("Error resolving base path");
			return false;
		} 
			
		
	}
	
	private function formatFileArr(){
		
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
	
 	
	
	
	
	/* 	PUBLIC HELPERS
	----------------------------------------------------------------------------- */
	
	public function isUploaded(){
		
		if(array_key_exists($this->uploadKey, $_FILES)){
					
			if(is_uploaded_file($this->files['tmp_name'] )){
				return true;
			} else {
				return false;
			}

		} else {
			$this->logger->debug('FILE was not populated with key specified');
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
			
			$this->flash->addMessage("error", "Invalid Mime Type.<br>You uploaded a file of type:  ".$this->files['type']);
			$this->logger->error("Invalid Mime Type - this shouldnt happen");
			
			return false;
		
		} else { /* USE WHATEVER THE STRING FILE EXTENSION IS */
		
			$ext = File::getFileExtension($this->files['name']);
			
			if($ext){
				$this->fileExt = $ext;	
				
			} else {
				$this->flash->addMessage("error", "The file you uploaded had no file extension");
				$this->logger->error("The file had no extension");
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
			}
		}
		
		/* BUILD FILE NAME VARS */
		$this->fileBasePath = ASSET_BASE_PATH.$this->targetDir.$this->fileName; 
		
		
		/* OVERWRITE CASES */
		if(!$this->opts['overwrite'] && file_exists($this->fileBasePath) ) {
			
			if($this->opts['useHashPrevent']){
				
				$hash = time();
				
				$this->fileName = $hash.'-'.$this->fileName;
				$this->fileRelativePath =  'assets/'.$this->targetDir.$this->fileName;
				$this->fileBasePath = ASSET_BASE_PATH.$this->targetDir.$this->fileName; 
				
			} else {
				$this->flash->addMessage('error',"This file already exists");
				$this->logger->error("This file already exists and overwrite is set to false");
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
				$this->flash->addMessage("error", "Invalid File Extension.<br>Acceptable file types are:  "
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
			   	$this->flash->addMessage('error', $uploadErrors[$errorCode]);
			   	return false;
					
			} else {
				$this->flash->addMessage('error', 'An unknown php error code was found');
				$this->logger->error('An unknown php error code was found');
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
			$this->flash->addMessage("warning","Sorry, the file you uploaded is too large.<br>Your file was:  "
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
			$this->logger->error('Mime Extension not found: '.$mimeType.'.  Please double check file has a valid extension.');
			return false;
		}	
	}
	
	private function _checkGlobalMimeType(){
			
		if(array_key_exists($this->files['type'], $this->mimes)) {
			return true;
			
		} else {
			$this->flash->addMessage("error", "Invalid File Type.<br>You uploaded a file of type:  ".$this->files['type']);
			$this->logger->error('Invalid Mime Type: '.$this->files['type']);
			return false;
		}	
		
	}	
} 