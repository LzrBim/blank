<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Newsletter/edit.php
----------------------------------------------------------------------------- */ 

$newsletterView = new NewsletterView(); 
$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($newsletter->_id, $newsletter->id());

/* SECOND SECTION */
$title = 'Edit Newsletter';
$content = '';

ob_start();  ?>

<div id="sortable"><?

foreach($newsletter->blocks as $block){ 

	$newsletterView->panel($block, $newsletter->id());
	
} ?>

</div><!-- /#sortable -->

<div id="addedBlockContainer"></div><? 

$content = ob_get_clean(); 

$opts = array(
	'buttons' => array(
		array(
			'text' 	=> 'Add New Block', 
			'href' 	=> '#addNewsletterBlockModal', 
			'class' => 'success', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-toggle="modal"'
		),
		array(
			'text' 	=> 'Insert Existing Block', 
			'href' 	=> '#insertNewsletterBlockModal', 
			'class' => 'primary', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-toggle="modal"'
		)
	)
);


$adminView->box($title, $content, $opts); 

/* SECOND SECTION */
$title = 'Edit Settings';
$content = '';

ob_start();

echo $form->radio('status', 'Status', $newsletter->status, $choices = array(
			'active' => 'Active',
			'archived' => 'Archived'
		));

echo $form->input('title', 'Title', $newsletter->title, array(
  'required' => true )
);  

$content = ob_get_clean();


$adminView->box($title, $content); 

echo $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/Newsletter/modal_email_test.php');
include(APP_PATH.'crud/NewsletterBlock/modal_add.php');
include(APP_PATH.'crud/NewsletterBlock/modal_insert.php');


