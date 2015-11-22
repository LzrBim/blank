<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm(); 

echo $form->open();

echo $form->hidden('model', 'Page');

echo $form->hidden('status', 'active');

echo $form->input('title', 'Administrative Title', '', array(
	'help' => 'Just for record keeping, will not be displayed publically',
  'required' => true
));

echo $form->input('headline', 'Headline', '', array(
	'help' => 'This will appear as the large headline on the page',
  'required' => true
));

echo $form->input('metaTitle', 'Meta Title', ''); 

echo $form->input('metaDescription', 'Meta Description', '' );

echo $form->input('metaKeywords', 'Meta Keywords', '' );

echo $form->buttonsAdd();

echo $form->close();