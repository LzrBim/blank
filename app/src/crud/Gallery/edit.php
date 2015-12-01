<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Gallery/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($gallery->_id, $gallery->id());

/* FIRST SECTION */
$title = 'Edit '.$gallery->_title;

$content = '';

ob_start();
 
echo $form->status($gallery->status);  

echo $form->input('title', 'Title', $gallery->title, array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Description', $gallery->description, array(
	'rows' => 3)
);


$content = ob_get_clean();

$adminView->box($title, $content);

/* SECOND SECTION FOR THUMB PREVIEW */
$title = 'Photos';
$content = '';

ob_start(); 

$imageCount = $gallery->getAllImageCount();

if($imageCount) { 
	
	echo $form->gallery_images($gallery, $gallery->images, 'galleryImage.php');
	
} 

$opts = array(
	'buttons' => array(
		array(
			'text' 	=> 'Add Photo', 
			'href' 	=> 'galleryImage.php?mode=add', 
			'class' => 'success', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-tpjc-action="formChangeMonitorButton"'
		)
	)
);

if($imageCount) { 

	$opts['buttons'][] = array(
		'text' 	=> 'Edit Images', 
		'href' 	=> 'galleryImage.php?mode=index&amp;galleryID='.$gallery->id(), 
		'class' => 'primary', 
		'size'	=> 'xs',
		'icon' 	=> 'pencil',
		'badge' => $imageCount,
		'data' 	=> 'data-tpjc-action="formChangeMonitorButton"'
	);
	
}  



$content = ob_get_clean();

$adminView->box('Images', $content,  $opts); 


/* THIRD SECTION FOR SEO */
$title = 'Search Engine Optimization';
$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $gallery->permalink); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true)); 

echo $form->buttonsAdd();

echo $form->close();




