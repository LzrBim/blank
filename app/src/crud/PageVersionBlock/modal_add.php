<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/modal_add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

ob_start(); 

echo $form->hidden('mode', 'insert');
echo $form->hidden('pageVersionID', $pageVersion->getId()); ?>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist" style="margin-left:-15px; margin-right:-15px;">

  <li class="active"><a href="#block" data-toggle="tab">Blocks</a></li>
  <li><a href="#gallery" data-toggle="tab">Gallery</a></li>
  <li><a href="#video" data-toggle="tab">Video</a></li>
  <li><a href="#faq" data-toggle="tab">FAQ</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content" style="margin-left:-15px; margin-right:-15px; padding:15px;background:#FFF; border-left:1px solid #ddd; border-bottom:1px solid #ddd; border-right:1px solid #ddd;">

  <div role="tabpanel" class="tab-pane active" id="block" style="padding:0 15px;"><? 
  
    $pageVersionBlock = new PageVersionBlock();
    echo $form->radioButtonGroup('templateID', 'Template', 1, $pageVersionBlock->getTemplateOptions()); 
			
    echo $form->checkboxCollapsible('isRepeating', 'Make block re-useable', 'Yes', 
    $form->input('title', 'Title', ''), false, $opts = array('collapsed' => true, 'target' => 'tpjc_repeatingCheckBox') );  ?>
    
  </div><!-- /.block tab panel -->
  
  
  <div role="tabpanel" class="tab-pane" id="gallery" style="padding:0 15px;"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		$gallery = new Gallery();
		echo $form->selectSearch('galleryID', 'Photo Gallery', $gallery->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a gallery'
		)); ?>
  
  </div><!-- /.gallery tab panel -->
  
  
  <div role="tabpanel" class="tab-pane" id="video" style="padding:0 15px;"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		$video = new Video();
		echo $form->selectSearch('videoID', 'Video', $video->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a video'
		)); ?>
     
  </div><!-- /.video tab panel -->
  
  <div role="tabpanel" class="tab-pane" id="faq" style="padding:0 15px;"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		$faqTag = new FaqTag();
		echo $form->selectSearch('faqTagID', 'FAQ', $faqTag->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a tag'
		)); ?>
     
  </div><!-- /.video tab panel -->
  
</div><!-- /.tab-content --><?  

$content = ob_get_clean();

$adminView->displayModal('addPageVersionBlockModal', 'Add A Block', $content, 'pageVersionBlock.php');