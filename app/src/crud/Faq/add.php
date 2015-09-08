<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Faq/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add Faq';

$content = '';

ob_start();

echo $form->status('active'); 

echo $form->input('title', 'Question', repop('title'), array(
  'required' => true )
); 

echo $form->simpleEditor('description', 'Answer', repop('description'), array(
	'required' => true,
	'rows' => 8)
);

$selectTagOptions = $faq->tag->getSelectOptionArray();

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

echo $form->buttonsAdd();

echo $form->close();

include(APP_PATH.'crud/FaqTag/modal_add.php');