<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/edit.php
----------------------------------------------------------------------------- */ 

use \App\Lib\AdminForm;

$form = new \App\Lib\AdminForm();

echo $form->open();

echo $form->hidden($page->_id, $page->id());

echo $form->status($page->status);  

echo $form->input('title', 'Title', $page->title, array(
  'required' => true, 
	'help' => 'For administrative use only')
);  

if($page->id() != 1 && !$page->isHardCoded){
	
	echo $form->input('slug', 'Slug', $page->slug, array('help' => 'URL: <span id="tjpc_permalink">page/<span>'.$page->slug.'</span>/</span>') ); 
	
} else {
	
	echo $form->hidden('permalink', $page->permalink );
	
}

echo $form->input('metaTitle', 'Meta Title', $page->metaTitle ); 

echo $form->input('metaDescription', 'Meta Description', $page->metaDescription );

echo $form->editor('asdf', 'desc Title', 'test' ); 

echo $form->buttonsEdit();

echo $form->close();
