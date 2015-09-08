<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/NewsletterBlock/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();

echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add Newsletter Block';

$content = '';

ob_start();

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

$newsletterBlock = new NewsletterBlock(); 

echo $form->select('templateID', 'Template', $newsletterBlock->getTemplateOptions()); 
    

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();