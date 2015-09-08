<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/File.php
----------------------------------------------------------------------------- */

class File {
	
	public static function extractZip($source, $dst){ 
	
		if(!defined('ASSET_BASE_PATH')) {
			wLog(4, 'ASSET_BASE_PATH undefined');
			die('File::extractZip requires ASSET PATH');
		} 
		
		/* VALIDATE DESTINATION IN BASE PATH */
		if(strpos($dst, ASSET_BASE_PATH) === false){
			wLog(4, 'dstFileName did not contain ASSET_BASE_PATH');
			die('File::extractZip dst did not contain ASSET PATH');
		}
		
		$zip = new ZipArchive;
		
		$extracted = array();
		
		if ($zip->open($source) === TRUE) {
			
			for($i = 0; $i < $zip->numFiles; $i++) {
                        
        $zip->extractTo($dst, array($zip->getNameIndex($i)));
                       
       	$extracted[] = $zip->getNameIndex($i);
                       
   		}
			
			$zip->close();
				
		} else {
			write_log(2, 'extract_zip() could not open file');
		}
		
		return $extracted;
		
	}
	
	
	
	public static function getFileExtension($fileName){
	
		if(!empty($fileName)){
			$ext = strtolower(substr(strrchr($fileName,'.'),1));
		} else {
			wLog(3, 'Could not determine extension - fileName='.$fileName);
			return '';
		}
		
		if(strlen($ext) >= 2 && strlen($ext) <= 4 ){
			return $ext;
		} else {
			wLog(3, 'Invalid fileName extension length - fileName='.$fileName);
			return '';
		}
		
	}
	
	/*public static function getFileNameWithoutExtension($fileName){
		
		if(!empty($fileName)){
			$pos = strripos($fileName, '.');
			if($pos !== false){
				return substr($fileName, 0, $pos);
			}
		}
		return '';	
	}
	*/
	public static function getFormattedFileSize($fileSize_bytes){
		if(is_numeric($fileSize_bytes)){
			$decr = 1024; 
			$step = 0;
			$prefix = array('Bytes','KB','MB','GB','TB','PB');
			while(($fileSize_bytes / $decr) > 0.9){
				$fileSize_bytes = $fileSize_bytes / $decr;
				$step++;
			}
			return round($fileSize_bytes,2).' '.$prefix[$step];
		} else {
			return 'NaN';
		}
	}
	
}