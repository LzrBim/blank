<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PagePromoBlock/add.php
----------------------------------------------------------------------------- */ 

use App\Lib\AdminForm;

$form = new \App\Lib\AdminForm();

echo $form->open();

echo $form->hidden('mode', 'insert');

echo $form->hidden('status', 'active');

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->editor('description1', 'Body', '', array(
	'required' => true,
	'rows' => 18 )
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();