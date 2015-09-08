<? 
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: app/lib/ImageResizer.php
----------------------------------------------------------------------------- */

class ImageResizer {

	private $fileSource;
	private $fileDestination;
	private $resizeMethod;
	private $width;
	private $height;
	private $aspect;
	private $useImagick;
	
	private $imageType; /* from getimagesize, 1 = gif, 2= jpg, 3=png */

	public function __construct($fileSource, $fileDestination = ''){  
		
		if(empty($fileSource)){
			wLog(3, 'No fileSource supplied'); 
			return false;
		} 
		
		if(!defined('ASSET_BASE_PATH')) {
			wLog(4, 'ASSET_BASE_PATH undefined');
			die('Uploader::__construct() - ASSET_BASE_PATH undefined');
		}
		
		$this->fileSource = $fileSource;
		
		if(isset($fileDestination) && !empty($fileDestination)) { 
		
			$this->fileDestination = $fileDestination;
			
		} else {
			$this->fileDestination = $fileSource;
		} 
		
		/* VALIDATE DESTINATION IN BASE PATH */
		if(strpos($this->fileDestination, ASSET_BASE_PATH) === false){
			wLog(4, 'fileDestination did not contain ASSET_BASE_PATH');
			die('ImageResizer::__construct() - fileDestination did not contain ASSET_BASE_PATH');
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
			wLog(3, get_class($this).'->'.__FUNCTION__.'() - Empty width or height'); 
			return false;
		}
		
		if( $this->width <= $maxWidth && $this->height <= $maxHeight && !$forceOut ) {
			
			if( $this->fileSource == $this->fileDestination ) {
				if($messages){ addMessage("success","The image did not require a resize."); }
				return true;
				
			} else {
				if(copy($this->fileSource, $this->fileDestination)) {
					if($messages){ addMessage("success","The image was copied to new destination, no resize was necessary"); }
					return true;
					
				} else {
					wLog(2, 'Copy from source to destination failed'); 
					addMessage("error","Copy from source to destination failed");	
					return false;	
				}
			}
		} 
		
		if($this->useImagick){ /* USE IMAGICK */
		
			wLog(1, 'boxFit using Imagick');
			
			try {
				
				$im = new Imagick();
				$im->readImage($this->fileSource);
				$im->resizeImage($maxWidth,$maxHeight, imagick::FILTER_LANCZOS, 1, true);
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				unset($im);
				
			} catch (ImagickException $e){
				
        wLog(3, 'Imagick Exception Caught: '.$e->getMessage()); 
				addMessage("error",$e->getMessage());
				return false;
			}
			
			addMessage("success","The image was resized");
			return true;
			
		
		} else { /* USE GD */
		
			wLog(1, 'boxFit using GD');
			
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
				wLog(2, 'Problem comparing image ratios'); 
				addMessage("error","Problem comparing image ratios");
				return false;
			}
		
			$imageFromSource = $this->imageCreate();
			
			if($imageFromSource){
				
				if(imagecopyresampled($tempImage,$imageFromSource,0,0,0,0,$newWidth,$newHeight,$this->width, $this->height)){
					
					if($messages){ 
						addMessage("success","The image was resized from (".$this->width."x".$this->height.") to (".ceil($newWidth)."x".ceil($newHeight).")"); 
					}
					
					if (file_exists($this->fileDestination)){
						if(!unlink($this->fileDestination)){
							addMessage("warning","Problem deleting the previous destination file");
						}
					}
					
					$success = $this->imageWrite($tempImage);
					imagedestroy($tempImage);
					imagedestroy($imageFromSource);
					return $success;
					
				} else {
					wLog(2, 'Problem copying the resampled image');
					addMessage("error","Problem copying the resampled image");
					return false;
				}
			} else {
				addMessage("error","Problem creating GD Image");
				wLog(2, 'Problem creating GD Image');
				return false;
			}
		
		}
		
	}
	
	
	public function boxCrop($targetWidth, $targetHeight, $messages = TRUE){
		
		if(empty($this->width) || empty($this->height)){
			wLog(3, 'Empty width or height'); 
			return false;
		}
	
		$targetAspect = round($targetWidth/$targetHeight, 2);
	
		//CASE ONE - Exact Same Ratio - Send it to box fit.
		if( $this->aspect == $targetAspect ) {
			
			wLog(1,'Weird, exact same ratio');
		
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
			wLog(4, 'This should never happen'); 
			return false;
		}
			
	}

	public function crop($x, $y, $w, $h, $dst_w, $dst_h){
		
		wLog(1, 'crop('.$x.', '.$y.', '.$w.', '.$h.', '.$dst_w.', '.$dst_h.')');
		
		if($this->useImagick){ /* USE IMAGICK */
		
			wLog(1, 'Crop using Imagick');
			
			try {
				
				$im = new Imagick();
				$im->readImage($this->fileSource);
				$im->cropImage($dst_w, $dst_h, $x, $y);				
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				unset($im);
				
			} catch (ImagickException $e){
				
        wLog(3, 'Imagick Exception Caught: '.$e->getMessage()); 
				addMessage("error",$e->getMessage());
				return false;
			}
			
			addMessage("success","The image was cropped");
			return true;
			
		
		} else { /* USE GD */
		
			wLog(1, 'Crop using GD');

			$tempImage = imagecreatetruecolor($dst_w, $dst_h); 		
			
			$imageFromSource = $this->imageCreate();
			
			if($imageFromSource){
				
				if(imagecopyresampled($tempImage, $imageFromSource, 0, 0, $x, $y, $dst_w, $dst_h, $w, $h)){
					
					if (file_exists($this->fileDestination)){
						
						if(!unlink($this->fileDestination)){
							
							addMessage("warning","Problem deleting the previous destination file");
							
							wLog(3, 'Problem deleting the previous destination file');
							return false;
						}
					}
					
					$success = $this->imageWrite($tempImage);
					
					imagedestroy($tempImage);
					
					imagedestroy($imageFromSource);
					
					return $success;
					
				} else {
					
					addMessage("error","Problem copying the resampled image");
					wLog(3, 'Problem copying the resampled image');
					return false;
					
				}
				
			} else {
				
				addMessage("error","Problem creating GD Image");
				wLog(3, 'Error in imageCreate()');
				return false;
			}
			
		}
	
	}
	
	public function watermark(){
			
		$watermarkFile = BASE_PATH.'public_html/images/placeholder/cavan-images-watermark.png';	
		
		/* CALCULATE GRAVITY */
		list($watermarkWidth, $watermarkHeight, $watermarkType) = getimagesize($watermarkFile);
		
		$x = $this->width - $watermarkWidth;
		$y = ($this->height  - $watermarkHeight) - 40;			

		//wLog(1, 'Base image width='.$this->width.', height='.$this->height);
		//wLog(1, 'Watermark image width='.$watermarkWidth.', height='.$watermarkHeight);	
		//wLog(1, 'Watermark x='.$x.', y='.$y);

		/* RENDER */
		
		if($this->useImagick){ /* USE IMAGICK */
		
			wLog(1, 'Watermark using Imagick');
			
			try {
				
				$im = new Imagick();
				$im->readImage($this->fileSource);
				$watermarkIm = new Imagick();
				$watermarkIm->readImage($watermarkFile);
				$im->compositeImage($watermarkIm, imagick::COMPOSITE_OVER, $x, $y);
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				$watermarkIm->clear();
				$watermarkIm->destroy();
				unset($im);
				unset($watermarkIm);
				
			} catch (ImagickException $e){
				
        wLog(3, 'Imagick Exception Caught: '.$e->getMessage()); 
				addMessage("error",$e->getMessage());
				return false;
			}
			
			addMessage("success","The image was watermarked");
			return true;
			
		
		} else { /* USE GD */
		
			wLog(1, 'Watermark using GD');
		
			$image = $this->imageCreate();
			$watermarkImage = imagecreatefrompng($watermarkFile);
			
			imagealphablending($image, true);
			imagealphablending($watermarkImage, true);
			
			imagecopy($image, $watermarkImage, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight);
			
			$success = $this->imageWrite($image);
			
			imagedestroy($image);
			imagedestroy($watermarkImage);
			
			return $success;
			
		}
	
	}
	
	/*$textX = $x + 312;
		$textY = $y + 62;*/
	
	public function text($string = '', $x, $y){
		
		$font = BASE_PATH.'public_html/images/placeholder/ARIALN.TTF';
		$fontSize = 13;
		
		/* RENDER */		
		if($this->useImagick){ /* USE IMAGICK */
		
			wLog(1, 'Text using Imagick');
			
			try {
				
				$im = new Imagick();
				$draw = new ImagickDraw();				
				$im->readImage($this->fileSource);
				
				$draw->setFillColor('white');
				$draw->setFont($font);
				$draw->setFontSize($fontSize);	
				$draw->setTextAlignment (imagick::ALIGN_LEFT);			
				
				$im->annotateImage($draw, $x, $y, 0, $string);
				
				$im->writeImage($this->fileDestination);
				$im->clear();
				$im->destroy();
				unset($im);
				
			} catch (ImagickException $e){
				
        wLog(3, 'Imagick Exception Caught: '.$e->getMessage()); 
				addMessage("error",$e->getMessage());
				return false;
			}
			
			addMessage("success","The image text was overlaid");
			return true;
			
		
		} else { /* USE GD */
		
			wLog(1, 'Text using GD');
		
			$image = $this->imageCreate();
			$white = imagecolorallocate($image, 255, 255, 255);
			
			imagettftext($image, $fontSize, 0, $x, $y, $white, $font, $string);
			
			$success = $this->imageWrite($image);
			
			imagedestroy($image);
			
			return $success;
			
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
			wLog(3, 'Undefined imageType');
			return false;
		}
	}

	private function imageWrite($tempImage){
		
		if($this->imageType == 1){
			if(imagegif($tempImage, $this->fileDestination, 100)){
				return true;
			} else {
				addMessage("error","Could not write GIF to disk");
				wLog(3, 'Could not write GIF to disk');
				return false;
			}
			
		} else if($this->imageType == 2){
			if(imagejpeg($tempImage, $this->fileDestination, 100)){
				return true;
			} else {
				addMessage("error","Could not write JPG to disk");
				wLog(3, 'Could not write JPG to disk');
				return false;
			}
			
		} else if($this->imageType == 3){
			if(imagepng($tempImage, $this->fileDestination)){
				return true;
			} else {
				addMessage("error","Could not write PNG to disk");
				wLog(3, 'Could not write PNG to disk');
				return false;
			}
			
		} else {
			wLog(3, 'undefined imageType');
			return false;
		}
		
	}
	
}