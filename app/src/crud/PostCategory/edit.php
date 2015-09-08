<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($postCategory->_id, $postCategory->getId());

/* FIRST SECTION */

$title = 'Edit Category';

$content = '';

ob_start();

echo $form->input('title', 'Title', $postCategory->title, array(
  'required' => true )
); 

echo $form->editor('description', 'Body', $postCategory->description, array(
	'help' => 'This will only be shown on the category list page' )
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();