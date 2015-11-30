<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/modal_insert.php
----------------------------------------------------------------------------- */ 

use App\Model\PageVersionBlock;
use App\AdminView\ModalView;

$form = new \App\Lib\AdminForm(); 

ob_start(); ?>
       
<div class="row">

  <div class="col-xs-12"><? 
    
    $form->hidden('pageVersionID', $pageVersion->id()); 
    
    $pageVersionBlock = new PageVersionBlock(); 
    
    echo $form->selectSearch('pageVersionBlockID', 'PageVersion Block', $pageVersionBlock->getInsertBlockSelectArray($pageVersion->id())); ?>
               
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

ModalView::modal('insertPageVersionBlockModal', 'Insert Existing Block', $content, [
	'action' => '/admin/pageVersionBlock/insertLink'
]);