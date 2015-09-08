<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/DocumentLibrary/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($documentLibrary->_id, $documentLibrary->getId());
echo $form->hidden('documentID', $documentLibrary->documentID);


/* FIRST SECTION */

$title = 'Edit Library Document';

$content = '';

ob_start(); 

echo $form->input('title', 'Title', $documentLibrary->title, array(
	'required' => true,
	'size'=> 'xlarge')
); 

echo $form->tag('tags', 'Tags', $documentLibrary->tag->getTagString());

echo $form->document($documentLibrary); 

echo $form->fileInput('uploadFile', 'Upload Document', array('help' => 'This will replace the existing document'));

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();