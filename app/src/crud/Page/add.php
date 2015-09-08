<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();

echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add Page';

$content = '';

ob_start();

echo $form->input('title', 'Administrative Title', repop('title'), array(
	'help' => 'Just for record keeping, will not be displayed publically',
  'required' => true
));

echo $form->input('headline', 'Headline', repop('headline'), array(
	'help' => 'This will appear as the large headline on the page',
  'required' => true
));

/*echo $form->editor('description', 'Body', '', array(
	'required' => true,
	'rows' => 18 )
);*/

$content = ob_get_clean();

$adminView->box($title, $content);


/* SECOND SECTION FOR SEO*/

$title = 'Search Engine Optimization';

$content = '';

ob_start(); 

echo $form->input('metaTitle', 'Meta Title', repop('metaTitle')); 

echo $form->input('metaDescription', 'Meta Description', repop('metaDescription') );

echo $form->input('metaKeywords', 'Meta Keywords', repop('metaKeywords') );

$content = ob_get_clean();

$adminView->box($title, $content, array('collapsed' => false)); 

echo $form->buttonsAdd();

echo $form->close();