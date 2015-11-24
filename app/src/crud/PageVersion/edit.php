<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersion/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

$pageView = new PageView();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden('status', $pageVersion->status);
echo $form->hidden('pageID', $pageVersion->pageID);
echo $form->hidden($pageVersion->_id, $pageVersion->getId());

echo $form->input('headline', 'Headline', $pageVersion->headline);  ?>

<div id="sortable"><?

foreach($pageVersion->blocks as $block){ 

	$pageView->panel($block, $pageVersion->getId());
	
} ?>

</div><!-- /#sortable -->

<div id="addedBlockContainer"></div><? 

echo $form->input('title', 'Version Note', $pageVersion->title);  

echo $form->buttonsEdit();

echo $form->close();

include('crud/PageVersionBlock/modal_add.php');

include('crud/PageVersionBlock/modal_insert.php');


