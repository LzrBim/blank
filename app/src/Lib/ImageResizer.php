<? 
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: app/lib/ImageResizer.php
----------------------------------------------------------------------------- */
namespace App\Lib;

use Psr\Log\LoggerInterface;
use App\Lib\File;

class ImageResizer {
	
	private $logger;

	private $fileSource;
	private $fileDestination;
	private $resizeMethod;
	private $width;
	private $height;
	private $aspect;
	private $useImagick;
	
	private $imageType; /* from getimagesize, 1 = gif, 2= jpg, 3=png */
	
	public function __construct(LoggerInterface $logger){
		
		$this->logger = $logger;
		
	}

	public function init($fileSource, $fileDestination = ''){  
		
		if(empty($fileSource)){
			$this->logger->error('No fileSource supplied'); 
		} 
		
		if(!defined('ASSET_BASE_PATH')) {
			$this->logger->error('ASSET_BASE_PATH undefined');
			die('NO ASSET BASE BATH');
		}
		
		$this->fileSource = $fileSource;
		
		if(isset($fileDestination) && !empty($fileDestination)) { 
		
			$this->fileDestination = $fileDestination;
			
		} else {
			$this->fileDestination = $fileSource;
		} 
		
		/* VALIDATE DESTINATION IN BASE PATH */
		if(strpos($this->fileDestination, ASSET_BASE_PATH) === false){
			$this->logger->error('fileDestination did not contain ASSET_BASE_PATH');
			die('ImageResizer - fileDestination did not contain ASSET_BASE_PATH');
		}
		
		/* POPULATE IMAGE ATTRIBUTES */
		$this->setImageAttr();
		
		
		/* DETECT IMAGICK */
		if (extension_loaded('imagick')){
			$this->useImagick = true;
		} else {
			$this->useImagick = false;
		}
	
	}
	
	/* OPTION SETTERS 
	----------------------------------------------------------------------------- */
	
	public function setImageickOff(){
		$this->useImagick = false;
		return $this;
	}


	/* PUBLIC METHODS 
	----------------------------------------------------------------------------- */

	public function boxFit($maxWidth, $maxHeight, $forceOut = FALSE, $messages = TRUE){
		
		if(empty($this->width) || empty($this->height)){
			$this->logger->error('Empty width or height'); 
			return false;
		}
		
		if( $this->width <= $maxWidth && $this->height <= $maxHeight && !$forceOut ) {
			
			if( $this->fileSource == $this->fileDestination ) {
				$this->logger->debug("The image did not require a resize.");
				return true;
				
			} else {
				if(copy($this->fileSource, $this->fileDestination)) {
					$this->logger->debug("The image was copied to new destination, no resize was necessary");
					return true;
					
				} else {
					$this->logger->error('Copy from source to destination failed');
					return false;	
				}
			}
		} 
		
		if($this->useImagick){ /* USE IMAGICK */
		
			$this->logger->debug('boxFit using Imagick');
			
			try {
				
				$im = new Imagick();
				$im->readImage($this->fileSource);
				$im->resizeImage($maxWidth,$maxHeight, imagick::FILTER_LANCZOS, 1, true);
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				unset($im);
				
			} catch (ImagickException $e){
				
        $this->logger->error('Imagick Exception Caught: '.$e->getMessage()); 
				return false;
				
			}
			
			$this->logger->debug("The image was resized");
			return true;
			
		
		} else { /* USE GD */
		
			$this->logger->debug('boxFit using GD');
			
			$imageRatio = $this->width/$this->height;
			
			$boxRatio = $maxWidth/$maxHeight;
			
			if ($imageRatio > $boxRatio) { //adjust width
				$newWidth = $maxWidth;
				$newHeight = ($this->height/$this->width)*$maxWidth;
				$tempImage = imagecreatetruecolor($newWidth,$newHeight);
				
			} elseif ($imageRatio < $boxRatio) { //adjust height 
				$newHeight = $maxHeight;
				$newWidth = ($this->width/$this->height)*$maxHeight;
				$tempImage = imagecreatetruecolor($newWidth,$newHeight);
				
				} elseif ($imageRatio == $boxRatio) {
				$newWidth = $maxWidth;
				$newHeight = ($this->height/$this->width)*$maxWidth;
				$tempImage = imagecreatetruecolor($newWidth,$newHeight);
				
			} else {
				$this->logger->debug('Problem comparing image ratios'); 
				return false;
			}
		
			$imageFromSource = $this->imageCreate();
			
			if($imageFromSource){
				
				if(imagecopyresampled($tempImage,$imageFromSource,0,0,0,0,$newWidth,$newHeight,$this->width, $this->height)){
					
					$this->logger->debug("success","The image was resized from (".$this->width."x".$this->height.") to (".ceil($newWidth)."x".ceil($newHeight).")"); 
					
					
					if (file_exists($this->fileDestination)){
						if(!unlink($this->fileDestination)){
							$this->logger->error("Problem deleting the previous destination file");
						}
					}
					
					$success = $this->imageWrite($tempImage);
					imagedestroy($tempImage);
					imagedestroy($imageFromSource);
					return $success;
					
				} else {
					$this->logger->error("Problem copying the resampled image");
					return false;
				}
			} else {
				$this->logger->error('Problem creating GD Image');
				return false;
			}
		
		}
		
	}
	
	
	public function boxCrop($targetWidth, $targetHeight, $messages = TRUE){
		
		if(empty($this->width) || empty($this->height)){
			$this->logger->debug('Empty width or height'); 
			return false;
		}
	
		$targetAspect = round($targetWidth/$targetHeight, 2);
	
		//CASE ONE - Exact Same Ratio - Send it to box fit.
		if( $this->aspect == $targetAspect ) {
			
			$this->logger->debug('Weird, exact same ratio');
		
			return $this->boxFit($targetWidth, $targetHeight);
		
		} elseif( $this->aspect > $targetAspect ) {
			
			$w = $this->height * $targetAspect;
			$h = $this->height;
			$x = floor( ($this->width - $w ) / 2 );
			$y = 0;
			
			return $this->crop($x, $y, $w, $h, $targetWidth, $targetHeight);
			
		
		} elseif( $this->aspect < $targetAspect ) {
		
			$w = $this->width;
			$h = $this->width * (1/$targetAspect);
			$x = 0;
			$y = floor( ($this->height - $h ) / 2 );
			
			return $this->crop($x, $y, $w, $h, $targetWidth, $targetHeight);
			
		} else {
			$this->logger->debug('This should never happen'); 
			return false;
		}
			
	}

	public function crop($x, $y, $w, $h, $dst_w, $dst_h){
		
		$this->logger->debug('crop('.$x.', '.$y.', '.$w.', '.$h.', '.$dst_w.', '.$dst_h.')');
		
		if($this->useImagick){ /* USE IMAGICK */
		
			$this->logger->debug('Crop using Imagick');
			
			try {
				
				$im = new Imagick();
				$im->readImage($this->fileSource);
				$im->cropImage($dst_w, $dst_h, $x, $y);				
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				unset($im);
				
			} catch (ImagickException $e){
				
        $this->logger->error('Imagick Exception Caught: '.$e->getMessage()); 
				return false;
				
			}
			
			$this->logger->debug("The image was cropped");
			return true;
			
		
		} else { /* USE GD */
		
			$this->logger->debug('Crop using GD');

			$tempImage = imagecreatetruecolor($dst_w, $dst_h); 		
			
			$imageFromSource = $this->imageCreate();
			
			if($imageFromSource){
				
				if(imagecopyresampled($tempImage, $imageFromSource, 0, 0, $x, $y, $dst_w, $dst_h, $w, $h)){
					
					if (file_exists($this->fileDestination)){
						
						if(!unlink($this->fileDestination)){
							
							$this->logger->error("Problem deleting the previous destination file");
							return false;
							
						}
					}
					
					$success = $this->imageWrite($tempImage);
					
					imagedestroy($tempImage);
					
					imagedestroy($imageFromSource);
					
					return $success;
					
				} else {
					
					$this->logger->debug('Problem copying the resampled image');
					return false;
					
				}
				
			} else {
				
				$this->logger->debug('Error in imageCreate()');
				return false;
			}
			
		}
	
	}
	
	
	/*	PRIVATE METHODS - MOSTLY FOR GD 
	----------------------------------------------------------------------------- */
	
	private function setImageAttr(){
		
		list($this->width, $this->height, $this->imageType) = @getimagesize($this->fileSource);
		
		$this->aspect = round( ($this->width / $this->height), 2); 
		
	}

	private function imageCreate(){
		
		if($this->imageType == 1){
			return @imagecreatefromgif($this->fileSource);
			
		} else if($this->imageType == 2){
			return @imagecreatefromjpeg($this->fileSource);
			
		} else if($this->imageType == 3){
			return @imagecreatefrompng($this->fileSource);
			
		} else {
			$this->logger->error('Undefined imageType');
			return false;
		}
	}

	private function imageWrite($tempImage){
		
		if($this->imageType == 1){
			if(imagegif($tempImage, $this->fileDestination, 100)){
				return true;
			} else {
				$this->logger->debug('Could not write GIF to disk');
				return false;
			}
			
		} else if($this->imageType == 2){
			if(imagejpeg($tempImage, $this->fileDestination, 100)){
				return true;
			} else {
				$this->logger->debug('Could not write JPG to disk');
				return false;
			}
			
		} else if($this->imageType == 3){
			if(imagepng($tempImage, $this->fileDestination)){
				return true;
			} else {
				$this->logger->debug('Could not write PNG to disk');
				return false;
			}
			
		} else {
			$this->logger->debug('undefined imageType');
			return false;
		}
		
	}
	
}