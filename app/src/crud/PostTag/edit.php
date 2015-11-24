<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PostTag/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($postTag->_id, $postTag->getId());

$title = 'Edit Tag';

$content = '';

ob_start();

echo $form->status($postTag->status); 

echo $form->input('title', 'Title', $postTag->title, array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();