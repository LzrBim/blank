<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Event/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add '.$event->_title;

$content = '';

ob_start();

echo $form->status('inactive'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Body', repop('description'), array( 'required' => true));

echo $form->select('eventCategoryID', 'Category', $event->eventCategory->getSelectOptionArray(), array(
	'id' => 'eventCategoryID'
));

echo $form->fileInput('uploadFile', 'Thumbnail Image'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();

//include(APP_PATH.'crud/EventDate/modal_add.php');