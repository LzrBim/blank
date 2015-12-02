<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/File.php
----------------------------------------------------------------------------- */

namespace App\Lib;

class File {
	
	public static function extractZip($source, $dst){ 
	
		if(!defined('ASSET_BASE_PATH')) {
			die('File::extractZip requires ASSET PATH');
		} 
		
		/* VALIDATE DESTINATION IN BASE PATH */
		if(strpos($dst, ASSET_BASE_PATH) === false){
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
			
			return false;
			
		}
		
		return $extracted;
		
	}
	
	
	
	public static function getFileExtension($fileName){
	
		if(!empty($fileName)){
			$ext = strtolower(substr(strrchr($fileName,'.'),1));
		} else {
			return '';
		}
		
		if(strlen($ext) >= 2 && strlen($ext) <= 4 ){
			return $ext;
		} else {
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
	
	public static function getValidUploadMime(){
		
		return array(
											 
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
	}
	
	
	
}