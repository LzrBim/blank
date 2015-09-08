<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemImage/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($itemImage->_id, $itemImage->getId());
echo $form->hidden('imageID', $itemImage->imageID);

/* FIRST SECTION */

$title = 'Edit Porfolio Image';

$content = '';

ob_start(); 

echo $form->status($itemImage->status); 

echo $form->image($itemImage); 

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

echo $form->editor('description', 'Caption', $itemImage->description, array(
	'rows'=> 2)
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();