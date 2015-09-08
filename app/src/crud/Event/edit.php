<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Event/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($event->_id, $event->getId());
echo $form->hidden('imageID', $event->imageID);

/* FIRST SECTION */

$title = 'Edit '.$event->_title;

$content = '';

ob_start();

echo $form->status($event->status); 

echo $form->input('title', 'Title', $event->title, array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Body', $event->description);

$selectCategoryOptions = $event->eventCategory->getSelectOptionArray($event->getId());

$addCategoryButton = $adminView->get_button(array(
	'text' 	=> 'Add New Category', 
	'href' 	=> '#eventCategoryModal', 
	'class' => 'success',
	'size'	=> 'xs',
	'icon'	=> 'plus',
	'data' 	=> 'data-toggle="modal"'
));

echo $form->select('eventCategoryID', 'Category', $selectCategoryOptions, array(
	'help' => $addCategoryButton,
	'id' => 'eventCategoryID'));


$content = ob_get_clean();

$adminView->box($title, $content);


/* EVENT DATES
----------------------------------------------------------------------------- */

$title = 'Event Dates';

$content = '';

ob_start(); 

if(!$event->getDateCount()){  ?>
		
  <div id="noEventDates">No dates have been added.</div><?
  
} ?>

<div id="eventDateContainer" class="clearfix"></div><? 

$content = ob_get_clean();

$opts = array(
	'buttons' => array(
		array(
			'text' 	=> 'Add Date', 
			'href' 	=> '#addEventDateModal', 
			'class' => 'success', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-toggle="modal"'
		)
	)
);


$adminView->box($title, $content,  $opts); 



/* SECOND SECTION FOR IMAGE */

$title = 'Event Thumbnail';

$content = '';

ob_start(); 

if($event->image->isLoaded()){
	echo $form->image($event); 
}

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$opts = array();
if($event->image->isLoaded()){
	$opts = array(
		'buttons' => array(
			array(
				'text' 	=> 'Remove Image', 
				'href' 	=> '#', 
				'class' => 'danger', 
				'size'	=> 'xs',
				'icon' 	=> 'remove',
				'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$event->getId().'"'
			)
		)
	);
}

$adminView->box($title, $content,  $opts); 


/* THIRD SECTION FOR GALLERY IMAGES */

$title = 'Event Slider Images';

$content = '';

ob_start(); 

echo $form->gallery_images($event, $event->images, 'eventImage.php'); 

$opts = array(
	'buttons' => array(
		array(
			'text' 	=> 'Add Image', 
			'href' 	=> 'eventImage.php?mode=add&eventID='.$event->getId(), 
			'class' => 'success', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-tpjc-action="formChangeMonitorButton"'
		)
	)
);

$imageCount = $event->getAllImageCount();

if($imageCount) { 

	$opts['buttons'][] = array(
		'text' 	=> 'Edit Images', 
		'href' 	=> 'eventImage.php?mode=index&amp;eventID='.$event->getId(), 
		'class' => 'primary', 
		'size'	=> 'xs',
		'icon' 	=> 'pencil',
		'badge' => $imageCount,
		'data' 	=> 'data-tpjc-action="formChangeMonitorButton"'
	);
	
}  

$content = ob_get_clean();

$adminView->box($title, $content,  $opts); 


/* FOURTH SECTION FOR SEO */

$title = 'Search Engine Optimization';

$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $event->permalink ); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true));

echo $form->buttonsAdd();

echo $form->close();

//TWO MODALS
include(APP_PATH.'crud/EventDate/modal_add.php');
include(APP_PATH.'crud/EventDate/modal_edit.php');

