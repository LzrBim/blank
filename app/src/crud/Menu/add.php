<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Menu/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add Menu';

$content = '';

ob_start();

echo $form->status('active'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();