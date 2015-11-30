<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/modal_add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

use App\Model\PageVersionBlock;
use App\Model\Gallery;
use App\Model\Video;
use App\Model\Faq;
use App\AdminView\ModalView;

ob_start(); 

echo $form->hidden('mode', 'insert');

echo $form->hidden('pageVersionID', $pageVersion->id()); ?>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#block" data-toggle="tab">Blocks</a></li>
  <li><a href="#gallery" data-toggle="tab">Gallery</a></li>
  <li><a href="#video" data-toggle="tab">Video</a></li>
  <li><a href="#faq" data-toggle="tab">FAQ</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div role="tabpanel" class="tab-pane active" id="block"><? 
  
    $pageVersionBlock = new PageVersionBlock();
		
    echo $form->radioButtonGroup('templateID', 'Template', 1, $pageVersionBlock->getTemplateOptions()); 
			
    echo $form->checkboxCollapsible('isRepeating', 'Make block re-useable', 'Yes', 
    $form->input('title', 'Title', ''), false, $opts = array('collapsed' => true, 'target' => 'tpjc_repeatingCheckBox') );  ?>
    
  </div><!-- /.block tab panel -->
  
  
  <div role="tabpanel" class="tab-pane" id="gallery"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		$gallery = new Gallery();
		echo $form->selectSearch('galleryID', 'Photo Gallery', $gallery->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a gallery'
		)); ?>
  
  </div><!-- /.gallery tab panel -->
  
  
  <div role="tabpanel" class="tab-pane" id="video"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		$video = new Video();
		echo $form->selectSearch('videoID', 'Video', $video->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a video'
		)); ?>
     
  </div><!-- /.video tab panel -->
  
  <div role="tabpanel" class="tab-pane" id="faq"><? 
		
		//selectSearch($name, $title, $choices = array(), $opts = array())
		/*$faqTag = new FaqTag();
		echo $form->selectSearch('faqTagID', 'FAQ', $faqTag->getSelectOptionArray(), array(
			'emptyOption' => 'Please select a tag'
		));*/ ?>
     
  </div><!-- /.video tab panel -->
  
</div><!-- /.tab-content --><?  

$content = ob_get_clean();

ModalView::modal('addPageVersionBlockModal', 'Add A Block', $content, ['action' => '/admin/pageVersionBlock/insert']);