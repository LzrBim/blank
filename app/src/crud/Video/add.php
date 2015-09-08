<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Video/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add '.$video->_title;

$content = '';

ob_start();

echo $form->status('active'); 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true,
  'size'=> 'xlarge')
); 

echo $form->textarea('embed', 'YouTube Embed', '', array(
	'rows' => 3)
);

echo $form->fileInput('uploadFile', 'Thumbnail');

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();