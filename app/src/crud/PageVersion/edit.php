<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersion/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 
$pageView = new PageView();

echo $form->open();

/* FIRST SECTION */
$title = 'Edit Page Version';
$content = '';

ob_start(); 

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden('status', $pageVersion->status);
echo $form->hidden('pageID', $pageVersion->pageID);
echo $form->hidden($pageVersion->_id, $pageVersion->getId());


//HEADLINE
echo $form->input('headline', 'Headline', $pageVersion->headline);  ?>

<div id="sortable"><?

foreach($pageVersion->blocks as $block){ 

	$pageView->panel($block, $pageVersion->getId());
	
} ?>

</div><!-- /#sortable -->

<div id="addedBlockContainer"></div><? 

$content = ob_get_clean(); 

$opts = array(
	'buttons' => array(
		array(
			'text' 	=> 'Add New Block', 
			'href' 	=> '#addPageVersionBlockModal', 
			'class' => 'success', 
			'size'	=> 'xs',
			'icon' 	=> 'plus',
			'data' 	=> 'data-toggle="modal"'
		),
		array(
			'text' 	=> 'Insert Repeating Block', 
			'href' 	=> '#insertPageVersionBlockModal', 
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

echo $form->input('title', 'Version Note', $pageVersion->title);  

$content = ob_get_clean();

$adminView->box($title, $content); 

echo $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/PageVersionBlock/modal_add.php');
include(APP_PATH.'crud/PageVersionBlock/modal_insert.php');


