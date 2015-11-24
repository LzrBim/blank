<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemCategory/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add '.$itemCategory->_title;

$content = '';

ob_start();

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->editor('description', 'Category Description', repop('description'), array(
	'help' => 'This will only be shown on the category list page' )
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();