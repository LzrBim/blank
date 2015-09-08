<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PostCategory/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add Category';

$content = '';

ob_start();

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->editor('description', 'Body', repop('description'), array(
	'help' => 'This will only be shown on the category list page' )
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();

//include(APP_PATH.'crud/ItemCategory/modal_add.php');