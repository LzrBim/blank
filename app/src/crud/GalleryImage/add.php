<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/GalleryImage/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');
echo $form->hidden('galleryID', $gallery->getId());

/* FIRST SECTION */

$title = 'Add Gallery Image';

$content = '';

ob_start(); 

echo $form->status('active'); 

echo $form->fileInput('uploadFile', 'Upload Image', array(
	'multiple' => true,
	'required' => true,
	'help' 		 => 'You can upload multiple images at a time'
)); 

echo $form->editor('description', 'Caption', repop('description'), array(
  'rows' => 2 )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();