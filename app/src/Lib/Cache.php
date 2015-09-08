<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /lib/Cache.php
----------------------------------------------------------------------------- */

class Cache {

	private $item;
	private $path;
	private $fileName;
	private $cacheTime;

	public function __construct($item = ''){

		$this->cacheTime = 3600;

		if(!defined('ASSET_BASE_PATH')){
			wLog(5, 'ASSET_BASE_PATH is not defined');
			die('ASSET_BASE_PATH is not defined');
		}

		$this->path = ASSET_BASE_PATH.'cache/';

		$this->item = $item;
		
		if(empty($this->item)){

			$this->item = $_SERVER['REQUEST_URI'];
		}

		$this->fileName = $this->_getHash($this->item);

	}

	public function isCached(){

		if(file_exists($this->_getFilePath()) && !$this->_isExpired()){

			return true;
		}
		
		return false;

	}

	public function getCache(){

		return file_get_contents($this->_getFilePath());

	}

	public function _isExpired() {

		if(filemtime($this->_getFilePath()) >= (time() + $this->cacheTime) ){

			return true;

		}

		return false;
		
	  }

	public function eraseCache($item) {
	
		if(file_exists($this->_getFilePath())){

			if(unlink($this->_getFilePath())){
				
				wLog(1, 'Cache: '.$this->fileName.' was erased');

				return true;

			}
		}
		return false;

	}

	public function flushCache() {
	
		$files = glob($this->path.'*'); 
		$count = 0;
		
		foreach($files as $file){ 
			if(is_file($file)){
				unlink($file); 
				$count++;
			}				
		}
		
		if($count){
			wLog(1, 'Flushed '.$count.' items from cache');
		}

	}

	 // This is the function you store information with
	public function setCache($data) {

		$h = fopen($this->_getFilePath(),'w');

		if($h){

			if (fwrite($h,$data)===false) {
				wLog(3, 'Error writing cache');
			}

			fclose($h);
			return true;

		}

		return false;

	}
	
	/* 	PRIVATE
	----------------------------------------------------------------------------- */

	private function _getFilePath() {
		return $this->path.$this->fileName;
	}	

	private function _getHash($item) {
		return sha1($item);
	}

}