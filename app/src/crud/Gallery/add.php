<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Gallery/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add '.$gallery->_title;

$content = '';

ob_start();

echo $form->status('active'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Description', '', array(
	'rows' => 3)
);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();