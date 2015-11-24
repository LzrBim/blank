<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/GalleryImage/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($eventImage->_id, $eventImage->getId());
echo $form->hidden('eventImageID', $eventImage->imageID);

/* FIRST SECTION */

$title = 'Edit Event Slider Image';

$content = '';

ob_start(); 

echo $form->status($eventImage->status); 

echo $form->image($eventImage); 

echo $form->fileInput('uploadFile', 'Upload Image', array(
	'help' => 'This will replace the existing image'
));

echo $form->editor('description', 'Caption', $eventImage->description, array(
	'rows'=> 2)
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();