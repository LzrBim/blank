<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemTag/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add Tag';

$content = '';

ob_start();

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();