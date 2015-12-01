<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Video/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($video->_id, $video->id());

/* FIRST SECTION */
$title = 'Edit '.$video->_title;

$content = '';

ob_start();
 
echo $form->status($video->status);  

echo $form->input('title', 'Title', $video->title, array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->textarea('embed', 'YouTube Embed', $video->embed, array(
	'rows' => 3)
);

echo $form->image($video, array(
	'mainImageTitle' => 'Thumbnail',
	'noImageMessage' => 'Using YouTube default thumbnail',
	'noImageMessageLevel' => 'info'
)); 

echo $form->fileInput('uploadFile', 'Thumbnail', array(
	'help' => 'This will replace the existing image'
));

$content = ob_get_clean();

$adminView->box($title, $content);

/* THIRD SECTION FOR SEO */
$title = 'Search Engine Optimization';
$content = '';

ob_start(); 

echo $form->input('permalink', 'Permalink', $video->permalink); 

$content = ob_get_clean();

$adminView->box($title, $content,  array('collapsed' => true)); 

echo $form->buttonsAdd();

echo $form->close();




