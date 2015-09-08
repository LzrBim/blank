<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/NewsletterBlock/modal_add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

ob_start(); 

echo $form->hidden('newsletterID', $newsletter->getId()); ?>
	
<div class="row">

  <div class="col-xs-6">
    <div style="margin-right:15px;">
    <? $newsletterBlock = new NewsletterBlock(); ?>
    <?= $form->select('templateID', 'Template', $newsletterBlock->getTemplateOptions()); ?>
    
    </div>
  </div><!-- /.col -->
  
  <div class="col-xs-6">
  
    <?= $form->input('title', 'Title', '', array('required' => true ) ); ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><?

$content = ob_get_clean();

$adminView->displayModal('addNewsletterBlockModal', 'Add A Newsletter Block', $content, 'ajax/addNewsletterBlock.php');