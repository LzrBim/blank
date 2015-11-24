<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ImageLibrary/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

/* FIRST SECTION */

$title = 'Add Library Image';

$content = '';

ob_start(); 

echo $form->status('active'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true)
); 

echo $form->tag('tags', 'Tags');

echo $form->fileInput('uploadFile', 'Upload Image'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();