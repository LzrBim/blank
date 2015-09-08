<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Faq/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($faq->_id, $faq->getId());

/* FIRST SECTION */

$title = 'Edit Faq';

$content = '';

ob_start();

echo $form->status($faq->status); 

echo $form->input('title', 'Question', $faq->title, array(
  'required' => true )
); 

echo $form->simpleEditor('description', 'Answer', $faq->description, array(
	'required' => true,
	'rows' => 8 )
); 
  
$selectTagOptions = $faq->tag->getSelectOptionArray($faq->getId());

$addTagButton = $adminView->get_button(array(
	'text' 	=> 'Add New Tag', 
	'href' 	=> '#faqTagModal', 
	'class' => 'success',
	'size'	=> 'xs',
	'icon'	=> 'plus',
	'data' 	=> 'data-toggle="modal"'
));

echo $form->selectMultiple('tagID', 'Tags', $selectTagOptions, array(
	'help' => $addTagButton,
	'id' => 'faqTagID'));

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/FaqTag/modal_add.php');