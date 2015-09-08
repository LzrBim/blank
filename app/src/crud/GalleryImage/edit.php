<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/GalleryImage/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($galleryImage->_id, $galleryImage->getId());
echo $form->hidden('imageID', $galleryImage->imageID);

/* FIRST SECTION */

$title = 'Edit Gallery Image';

$content = '';

ob_start(); 

echo $form->status($galleryImage->status); 

echo $form->image($galleryImage); 

echo $form->fileInput('uploadFile', 'Upload Image', array(
	'help' => 'This will replace the existing image'
));

echo $form->editor('description', 'Caption', $galleryImage->description, array(
	'rows'=> 2)
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();