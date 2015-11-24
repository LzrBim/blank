<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

echo $form->hidden('mode', 'insert');
echo $form->hidden('status', 'active');

$title = 'Add Block';

$content = '';

ob_start();

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

$pageVersionBlock = new PageVersionBlock(); 

echo $form->select('templateID', 'Template', $pageVersionBlock->getTemplateOptions()); 
    

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();