<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PagePromoBlock/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();

echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add New Block';

$content = '';

ob_start();

//echo $form->status('active'); 

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