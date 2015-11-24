<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/FaqTag/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($faqTag->_id, $faqTag->getId());

$title = 'Edit Tag';

$content = '';

ob_start();

echo $form->status($faqTag->status); 

echo $form->input('title', 'Title', $faqTag->title, array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();