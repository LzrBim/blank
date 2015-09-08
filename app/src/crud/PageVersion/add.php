<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();

echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'inactive');
echo $form->hidden('pageID', $pageID);

$title = 'Add Page Version';

$content = '';

ob_start();

echo $form->input('headline', 'Headline', repop('headline')); 

echo $form->input('title', 'Version Note', 'First Draft', array('help' => 'For administrative use only, will not be displayed publically.')); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();