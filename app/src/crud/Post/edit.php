<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Post/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($post->_id, $post->getId());
echo $form->hidden('imageID', $post->imageID);

/* FIRST SECTION */

$title = 'Edit Post';

$content = '';

ob_start();

echo $form->status($post->status); 

echo $form->input('title', 'Title', $post->title, array(
  'required' => true )
); 

if($post->noAbstract){
	
	$collapsed = true;
} else {
	$collapsed = false;
}

echo $form->checkboxCollapsible('noAbstract', 'Abstract', 'Just shorten the post', $form->editor('abstract', 'Abstract', $post->abstract), $collapsed, $opts = array('collapsed' => $collapsed) ); 

echo $form->editor('description', 'Body', $post->description, array(
	'required' => true,
	'rows' => 16 )
); ?>


<div class="row">

	<div class="col-sm-6"><? 
	
		echo $form->select('postCategoryID', 'Category', $post->category->getSelectOptionArray(), array(
			'emptyOption' => ' '
		));  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"> <? 
  
		echo $form->tag('tags', 'Tags', $post->tag->getTagString()); ?>

  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

$adminView->box($title, $content);


/* SECOND SECTION FOR IMAGE */

$title = 'Post Image';

$content = '';

ob_start(); 

if($post->image->isLoaded()){
	echo $form->image($post); 
}

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$opts = array();
if($post->image->isLoaded()){
	$opts = array(
		'buttons' => array(
			array(
				'text' 	=> 'Remove Image', 
				'href' 	=> '#', 
				'class' => 'danger', 
				'size'	=> 'xs',
				'icon' 	=> 'remove',
				'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$post->getId().'"'
			)
		)
	);
}

$adminView->box($title, $content,  $opts); 

/* THIRD SECTION FOR SEO */

$title = 'Search Engine Optimization';

$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $post->permalink); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true)); 

echo $form->buttonsEdit();

echo $form->close();