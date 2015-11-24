<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Post/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add Post';

$content = '';

ob_start();

echo $form->status('active'); 

/*echo $form->radio('isFeatured', 'Featured', 0,  array('Yes' => 1, 'No' => 0) );*/

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->checkboxCollapsible('noAbstract', 'Abstract', 'Just shorten the post', $form->editor('abstract', 'Abstract', ''), true, $opts = array('collapsed' => true) ); 


echo $form->editor('description', 'Post', repop('description'), array(
	'required' => true,
	'rows' => 16)
); ?>

<div class="row">

	<div class="col-sm-6"><? 
	
		echo $form->select('postCategoryID', 'Category', $post->category->getSelectOptionArray(), array(
			'emptyOption' => ' '
		));  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"> <? 
  
		echo $form->tag('tags', 'Tags'); ?>

  </div><!-- /.col -->
  
</div><!-- /.row --><? 

//$tagChoices = $post->tag->build_select_option_array();	
//echo $form->selectMultiple('tagID', 'Tags', $tagChoices);

echo $form->fileInput('uploadFile', 'Upload Image'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();