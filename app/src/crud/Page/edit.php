<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($page->_id, $page->getId());

/* FIRST SECTION */
$title = 'Edit Page';
$content = '';

ob_start();

echo $form->status($page->status);  

echo $form->input('title', 'Title', $page->title, array(
  'required' => true, 
	'help' => 'For administrative use only')
);  


if($page->getId() != 1 && !$page->isHardCoded){
	echo $form->input('permalink', 'Permalink', $page->permalink, array('help' => 'URL: <span id="tjpc_permalink">'.HTTP_PATH.'page/<span>'.$page->permalink.'</span>/</span>') ); 
} else {
	echo $form->hidden('permalink', $page->permalink );
}

echo $form->input('metaTitle', 'Meta Title', $page->metaTitle ); 

echo $form->input('metaDescription', 'Meta Description', $page->metaDescription );

$content = ob_get_clean();

$adminView->box($title, $content, array('helpDoc' => 'pageEditHelp')); 

echo $form->buttonsEdit();

echo $form->close();

echo $adminView->displayHelpModal('pageEditHelp', 'Page Edit Help', 'Page/edit_help.php');