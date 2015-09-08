<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemTag/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($itemTag->_id, $itemTag->getId());

/* FIRST SECTION */

$title = 'Edit Tag';

$content = '';

ob_start();

echo $form->status($itemTag->status); 

echo $form->input('title', 'Title', $itemTag->title, array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();