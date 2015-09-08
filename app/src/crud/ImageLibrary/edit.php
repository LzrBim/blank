<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ImageLibrary/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($imageLibrary->_id, $imageLibrary->getId());
echo $form->hidden('imageID', $imageLibrary->imageID);

/* FIRST SECTION */

$title = 'Edit Library Image';

$content = '';

ob_start(); 

echo $form->input('title', 'Title', $imageLibrary->title, array(
	'required' => true )
); 

echo 'str ='.$imageLibrary->tag->getTagString();

echo $form->tag('tags', 'Tags', $imageLibrary->tag->getTagString());

echo $form->image($imageLibrary); 

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();