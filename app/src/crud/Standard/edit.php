<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Standard/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($standard->_id, $standard->id());

/* FIRST SECTION */

$title = 'Edit '.$standard->_title;

$content = '';

ob_start();

echo $form->input('title', 'Title', $standard->title, array(
  'required' => true )
); 

echo $form->editor('description', 'Description', $standard->description);

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();