<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Item/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($item->_id, $item->id());
echo $form->hidden('imageID', $item->imageID);

/* FIRST SECTION */

$title = 'Edit '.$item->_title;

$content = '';

ob_start();

echo $form->status($item->status); 

echo $form->radio('isFeatured', 'Featured', $item->isFeatured,  array(1 => 'Yes', 0 => 'No') );

echo $form->input('title', 'Title', $item->title, array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->editor('description', 'Body', $item->description);

$selectCategoryOptions = $item->category->getSelectOptionArray($item->id());

$addCategoryButton = $adminView->get_button(array(
	'text' 	=> 'Add New Category', 
	'href' 	=> '#itemCategoryModal', 
	'class' => 'success',
	'size'	=> 'xs',
	'icon'	=> 'plus',
	'data' 	=> 'data-toggle="modal"'
));

echo $form->selectMultiple('itemCategoryID', 'Category', $selectCategoryOptions, array(
	'help' => $addCategoryButton,
	'id' => 'itemCategoryID'));

echo $form->tag('tags', 'Tags', $item->tag->getTagString());

$imageCount = $item->getAllImageCount();

if(!$imageCount) { //if there are no images display add button
	
	$imageButton = $adminView->get_button(array(
		'text' 	=> 'Add Images', 
		'href' 	=> 'itemImage.php?mode=add&amp;itemID='.$item->id(), 
		'class' => 'primary',
		'size'	=> 'sm',
		'icon' 	=> 'plus'
	));

} else {
	
	$imageButton = $adminView->get_button(array(
		'text' 	=> 'Edit Images', 
		'href' 	=> 'itemImage.php?mode=index&amp;itemID='.$item->id(), 
		'class' => 'primary',
		'size'	=> 'sm',
		'badge' => $imageCount
	));
	
}

echo $form->arbitraryRow('Images ', $imageButton); 

$content = ob_get_clean();

$adminView->box($title, $content);


/* SECOND SECTION FOR IMAGE */

$title = 'Item Main Image';

$content = '';

ob_start(); 

if($item->image->isLoaded()){
	echo $form->image($item); 
}

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$opts = array();
if($item->image->isLoaded()){
	$opts = array(
		'buttons' => array(
			array(
				'text' 	=> 'Remove Image', 
				'href' 	=> '#', 
				'class' => 'danger', 
				'size'	=> 'xs',
				'icon' 	=> 'remove',
				'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$item->id().'"'
			)
		)
	);
}

$adminView->box($title, $content,  $opts); 


/* THIRD SECTION FOR SEO */

$title = 'Search Engine Optimization';

$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $item->permalink ); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true));

echo $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/ItemCategory/modal_add.php');