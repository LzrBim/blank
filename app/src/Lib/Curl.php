<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/Curl.php
----------------------------------------------------------------------------- */

class Curl {
	

	public static function getFile($url, $dstFileName){    
	
		if(!defined('ASSET_BASE_PATH')) {
			wLog(4, 'ASSET_BASE_PATH undefined');
			die('CURL::getFile requires ASSET PATH');
		} 
		
		/* VALIDATE DESTINATION IN BASE PATH */
		if(strpos($dstFileName, ASSET_BASE_PATH) === false){
			wLog(4, 'dstFileName did not contain ASSET_BASE_PATH');
			die('CURL::getFile requires ASSET PATH');
		}

		$fh = fopen($dstFileName, "w");
		
		if($fh){ 
		
			$ch = curl_init();
			
			if($ch){
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_URL, $url);    
				curl_setopt($ch, CURLOPT_FILE, $fh);   
				 
				if(curl_exec($ch) !== FALSE){  
				
					curl_close($ch);  
					fclose($fh);
					wLog(1, 'Curl::getFile sucess'); 
					return true;
					
				} else { 
					wLog(3, curl_errno($ch).curl_error($ch));
					curl_close($ch);   
					fclose($fh);  
					return false;
				}
				
			} else {
				fclose($fh);
				wLog(3, 'Error during cURL init()');
				return false;
			}
		} else {
			wLog(3, 'Error opening temp file to write zip to');
			return false;
		}
	}
}