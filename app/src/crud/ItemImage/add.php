<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemImage/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');
echo $form->hidden('itemID', $item->getId());

/* FIRST SECTION */

$title = 'Add Item Image';

$content = '';

ob_start(); 

echo $form->status('active'); 

echo $form->fileInput('uploadFile', 'Upload Image', array(
	'required' => true																													 
)); 

echo $form->editor('description', 'Caption', repop('description'), array(
  'rows' => 2 )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();