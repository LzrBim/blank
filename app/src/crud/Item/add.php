<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Item/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add '.$item->_title;

$content = '';

ob_start();

echo $form->status('active'); 

echo $form->radio('isFeatured', 'Featured', 0,  array(1 => 'Yes', 0 => 'No') );

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Body', repop('description'));

$addCategoryButton = $adminView->get_button(array(
	'text' 	=> 'Add New Category', 
	'href' 	=> '#itemCategoryModal"', 
	'class' => 'success', 	//default, primary, success, info, warning, danger, link
	'size' 	=> 'xs', 	//lg, sm, xs
	'icon' 	=> 'plus',
	'data'	=> 'data-toggle="modal"'
));


echo $form->selectMultiple('itemCategoryID', 'Category', $item->category->getSelectOptionArray(), array(
	'help' => $addCategoryButton,
	'id' => 'itemCategoryID'
));

echo $form->tag('tags', 'Tags');

echo $form->fileInput('uploadFile', 'File'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();

include(APP_PATH.'crud/ItemCategory/modal_add.php');