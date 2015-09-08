<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Listing/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($listing->_id, $listing->getId());
echo $form->hidden('imageID', $listing->imageID);

/* FIRST SECTION */

$title = 'Edit Listing';

$content = '';

ob_start();

echo $form->status($listing->status); 

echo $form->input('title', 'Title', $listing->title, array(
  'required' => true )
); 

if($listing->noAbstract){
	
	$collapsed = true;
} else {
	$collapsed = false;
}

echo $form->checkboxCollapsible('noAbstract', 'Abstract', 'Just shorten the listing', $form->editor('abstract', 'Abstract', $listing->abstract), $collapsed, $opts = array('collapsed' => $collapsed) ); 

echo $form->editor('description', 'Body', $listing->description, array(
	'required' => true,
	'rows' => 16 )
); ?>


<div class="row">

	<div class="col-sm-6"><? 
	
		echo $form->select('listingCategoryID', 'Category', $listing->category->getSelectOptionArray());  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"> <? 
  
		echo $form->tag('tags', 'Tags', $listing->tag->getTagString()); ?>

  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

$adminView->box($title, $content);


/* SECOND SECTION FOR IMAGE */

$title = 'Listing Image';

$content = '';

ob_start(); 

if($listing->image->isLoaded()){
	echo $form->image($listing); 
}

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$opts = array();
if($listing->image->isLoaded()){
	$opts = array(
		'buttons' => array(
			array(
				'text' 	=> 'Remove Image', 
				'href' 	=> '#', 
				'class' => 'danger', 
				'size'	=> 'xs',
				'icon' 	=> 'remove',
				'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$listing->getId().'"'
			)
		)
	);
}

$adminView->box($title, $content,  $opts); 

/* THIRD SECTION FOR SEO */

$title = 'Search Engine Optimization';

$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $listing->permalink); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true)); 

echo $form->buttonsEdit();

echo $form->close();