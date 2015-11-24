<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersion/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm(); 

echo $form->open();

echo $form->hidden('status', 'inactive');

echo $form->hidden('pageID', $page->pageID);

echo $form->input('headline', 'Headline', ''); 

echo $form->input('title', 'Version Note', 'First Draft', array('help' => 'For administrative use only, will not be displayed publically.')); 

echo $form->buttonsAdd();

echo $form->close();