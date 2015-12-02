<?php

namespace App\AdminView;

use App\Model\Gallery;
use App\Model\Video;
use App\Model\Faq;

class PageView {
	
	/* 	FRONT END - MANUAL BLOCKS
	----------------------------------------------------------------------------- */

	public function getPromoBlock($page, $pagePromoBlockID){
		
		$promo = $page->getPromoBlock($pagePromoBlockID);
		
		if($promo && $promo->isType()){
	
			if($promo->type == 'editor' || $promo->type == 'headline'){ 
			
				return $promo->description1;
				
				
			} else if($promo->type == 'text'){
				
				return nl2br($promo->description1);
				
			 
			} else if($promo->type == 'list'){
				
				$str = str_replace(array("\r\n", "\r", "\n"), "</li><li>", $block->description1); 
				
				return '<ul><li>'.$str.'</li></ul>';
			 
			} else {
				
				wLog(3, 'Invalid promo type '.$promo->type);
				
			}
			
		} elseif(!empty($promo->pageBlockTemplateID)) {
			
			$pageBlockTemplate = new PageBlockTemplate();
			
			if($pageBlockTemplate->load($promo->pageBlockTemplateID)){
			
				$template = new Template();
				$template->setTemplate($pageBlockTemplate->template);
				
				$template->setData($promo->toArray());
				return $template->render();
				
			} else {
				wLog(3, 'Invalid template: '.$promo->pageBlockTemplateID);
			}
		
		} else { 
		
			wLog(3, 'Invalid Promo: '.$pagePromoBlockID);
			return '';
			
		}
		
	}
	
	/* 	FRONT END DISPLAY
	----------------------------------------------------------------------------- */
	public function pageVersionBlock($pageVersionBlock){ 
	
		ob_start(); 
		
		if(!$pageVersionBlock->isModule()){
			
			//One Column
			if($pageVersionBlock->templateID == 1){ 
			
				echo $pageVersionBlock->description1;
				
			//Two Column Free Form
			} elseif($pageVersionBlock->templateID == 2){ ?>
      
      	<div class="container">
				
					<div class="row">
							
						<div class="col-xs-6"><?
							
							echo $pageVersionBlock->description1; ?>
						
						</div>
						
						<div class="col-xs-6"><?
							
							echo $pageVersionBlock->description2; ?>
						
						</div>
						
					</div>
					
				</div><?  
				
			//Section Headline
			} elseif($pageVersionBlock->templateID == 3){   ?>
			
				<h1><?= $pageVersionBlock->headline1; ?></h1><?
				
			} 
			
		} else { //MODULES

			//GALLERY
			if(!empty($pageVersionBlock->galleryID)){ 
			
				$gallery = new Gallery;
				
				$gallery->load($pageVersionBlock->galleryID);
								
				echo $galleryView->detail($gallery);
				
			//VIDEO
			} elseif(!empty($pageVersionBlock->videoID)){  
			
				$video = new Video;
				
				$video->load($pageVersionBlock->videoID);
				
				$videoView = new VideoView();
				
				echo $videoView->detail($video);
				
			//FAQ BY TAG
			} elseif(!empty($pageVersionBlock->faqTagID)){  
			
				$faq = new Faq();
				$faqs = $faq->fetchActiveByTag($pageVersionBlock->faqTagID);
				
				$faqView = new FaqView();
				echo $faqView->detail($faqs);
				
			
			} else {
				wLog(3, 'pageVersionBlock Module not found');
			}
		
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
  	
  }
	
	/* PAGE VERSION BLOCK PANELS
	----------------------------------------------------------------------------- */
	public function panel($pageVersionBlock, $pageVersionID){ 
	
		if(empty($pageVersionID)){ die('no PageVersionID'); } ?>
  
  	<div id="pageVersionBlockID_<?= $pageVersionBlock->id(); ?>" class="panel panel-default pageVersionBlockPanel">
  
      <div class="panel-heading"><?= $pageVersionBlock->getPanelTitle(); ?>
      
        <div class="pull-right"><? 
				
					if(!$pageVersionBlock->isModule()){ ?>
          	<a class="btn btn-xs btn-default" 
          		href="/admin/pageVersionBlock/edit/<?= $pageVersionBlock->id(); ?>?pageVersionID=<?= $pageVersionID; ?>">
          			<i class="glyphicon glyphicon-pencil"></i> Edit Block</a><?
					
					} ?>
          
          <a class="btn btn-xs btn-default" 
          data-tpjc-action="remove_page_version_block" 
          data-tpjc-page-version-id="<?= $pageVersionID; ?>" 
          data-tpjc-page-version-block-id="<?= $pageVersionBlock->id(); ?>" 
          href="/admin/pageVersionBlock/delete/<?= $pageVersionBlock->id(); ?>?pageVersionID=<?= $pageVersionID; ?>">
          <i class="glyphicon glyphicon-trash"></i> Remove Block</a>
          
          <a class="btn btn-xs btn-default tpjc_dragHandle"><i class="glyphicon glyphicon-sort"></i></a>
          
        </div><!-- /.pull-right -->
        
      </div><!-- /.panel-heading -->
      
      <div class="panel-body"><?
      
				if(!$pageVersionBlock->isModule()){
					
					echo $this->pageVersionBlock($pageVersionBlock);
				
				//DO CUSTOM DISPLAYS FOR MODULES INSIDE PANELS
				} else { 
					
					if(!empty($pageVersionBlock->galleryID)){ 
			
						$gallery = new Gallery;
						$gallery->load($pageVersionBlock->galleryID);
						
						$galleryView = new GalleryView();
						
						echo $galleryView->adminPanel($gallery);
						
					//VIDEO
					} elseif(!empty($pageVersionBlock->videoID)){  
					
						$video = new Video;
						$video->load($pageVersionBlock->videoID);
						
						$videoView = new VideoView();
						echo $videoView->adminPanel($video);
						
					//FAQ BY TAG
					} elseif(!empty($pageVersionBlock->faqTagID)){  
					
						$faq = new Faq();
						$faqs = $faq->fetchActiveByTag($pageVersionBlock->faqTagID);
						
						$faqView = new FaqView();
						echo $faqView->adminPanel($faqs);
						
					
					} else {
						echo 'Module not found';
					}
					
				} ?>
        
      </div>
      
    </div><? 
		
	}
	
	
}