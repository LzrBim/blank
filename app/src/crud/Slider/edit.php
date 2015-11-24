<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Slider/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($slider->_id, $slider->getId());
echo $form->hidden('imageID', $slider->imageID);

/* FIRST SECTION */

$title = 'Edit Slider Image';

$content = '';

ob_start();

echo $form->status($slider->status);

echo $form->input('title', 'Title', $slider->title, array(
	'required' => true )
); 

echo $form->simpleEditor('description', 'Caption', $slider->description, array(
	'rows' => 2
)); 

echo $form->fileInput('uploadFile', 'Upload Image');

echo $form->image($slider); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();