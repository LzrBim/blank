<?php
namespace App\AdminController;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Valitron\Validator;
use Slim\Flash;
use App\Lib\ImageResizer;

class BaseController {
	
	protected $view;
	protected $logger;
	protected $flash;
	
	public function __construct(Twig $view, LoggerInterface $logger, $services = array()){
		
		$this->view = $view;
		$this->logger = $logger;
		
		$this->validator = new Validator($_POST);
		
		if(isset($services['flash'])){
			
			$this->flash = $services['flash'];
			
			//ALWAYS APPEND MESSAGES
			$this->view->offsetSet('flash', $this->flash->getMessages());
		
		}
		
		if(isset($services['uploader'])){
			
			$this->uploader = $services['uploader'];

		}
		
		if(isset($services['imageResizer'])){
			
			$this->imageResizer = $services['imageResizer'];

		}
			
   
	}
	
	public function uploadImage($model, $action, $fileArrayIndex = -1){
		
		$targetDir = $model->image->settings['targetDir'];
		
		$this->uploader->setTargetDir($targetDir);
		$this->uploader->setIndex($fileArrayIndex);
		
		//ON UPDATE, MAKE IT OVERWRITE TO MAINTAIN LINKS
		if($action == 'update'){
			
			if(!empty($model->image->fileNameMain)){ 
			
				$this->uploader->enableOverwrite();
			
				$info = pathinfo($model->image->fileNameMain);
				
				$this->uploader->setTargetFileName = $info['filename'];
				
				$this->uploader->setAllowedList = $info['extension'];
			}
			
		} 
		
		if(!$this->uploader->isUploaded()){
			$this->logger->debug('Nothing uploaded');
			return 0;
		}


		if($this->uploader->upload()){
			
			$this->logger->debug('Uploaded a file');
			
			$model->image->fileNameMain = $targetDir.$this->uploader->getFileName();
		
			$info = pathinfo($this->uploader->getFileBasePath());
			
			$originalName = $info['filename'].'_o.'.$info['extension'];
			$thumbName = $info['filename'].'_t.'.$info['extension'];
			$systemName = $info['filename'].'_st.'.$info['extension'];
			
			$originalPath = $info['dirname'].'/'.$originalName;
			$thumbPath = $info['dirname'].'/'.$thumbName;
			$systemPath = $info['dirname'].'/'.$systemName;
			
			// box fit the original to a manageable size from settings and rename it _o suffix 
			$this->imageResizer->init($this->uploader->getFileBasePath(), $originalPath);
			
			$this->logger->debug('Original resized');
			
			if($this->imageResizer->boxFit($model->image->settings['originalWidth'], $model->image->settings['originalHeight'], FALSE, TRUE)){
				
				$model->image->fileNameOriginal = $model->image->settings['targetDir'].$originalName;
				
				// box fit the main image
				$this->imageResizer->init($this->uploader->getFileBasePath());
				
				if($this->imageResizer->boxFit($model->image->settings['mainWidth'], $model->image->settings['mainHeight'], FALSE, FALSE)){
				
					if($model->image->settings['hasThumb']){
										
						$this->imageResizer->init($this->uploader->getFileBasePath(), $thumbPath);
						
						if($this->imageResizer->boxCrop($model->image->settings['thumbWidth'], $model->image->settings['thumbHeight'], FALSE)){
							
							$model->image->fileNameThumb = $model->image->settings['targetDir'].$thumbName;
							
						} 
					}
					
					// Generate the system thumb  100 x 100
					$this->imageResizer->init($this->uploader->getFileBasePath(), $systemPath);
					
					if($this->imageResizer->boxCrop(150, 150, FALSE, FALSE)){
						
						$model->image->fileNameSystem = $model->image->settings['targetDir'].$systemName;
						
					}
				
				}
			}
			
			if($action == 'insert'){
				
				return $model->image->insert();
				
			} else {
				
				return $model->image->update();
				
			}
			
		} else {
			
			$this->flash->addMessage('error', 'Image upload failed');
			
		}
	
		return 0;
		
	}
	
}
