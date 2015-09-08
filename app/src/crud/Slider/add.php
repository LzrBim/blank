<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Slider/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

/* FIRST SECTION */

$title = 'Add Slider Image';

$content = '';

ob_start(); 

echo $form->status('active'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->simpleEditor('description', 'Caption', repop('description'), array(
	'rows' => 2
)); 

echo $form->fileInput('uploadFile', 'Upload Image'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();