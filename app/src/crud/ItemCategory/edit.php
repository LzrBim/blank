<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemCategory/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($itemCategory->_id, $itemCategory->getId());

/* FIRST SECTION */

$title = 'Edit Category';

$content = '';

ob_start();

echo $form->status($itemCategory->status); 

echo $form->input('title', 'Title', $itemCategory->title, array(
  'required' => true )
); 

echo $form->editor('description', 'Category Description', $itemCategory->description, array(
	'help' => 'This will only be shown on the category list page' )
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

include(APP_PATH.'crud/ItemCategory/modal_add.php');