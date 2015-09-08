<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Menu/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($menu->_id, $menu->getId());
echo $form->hidden('menuTreeID', $menu->menuTreeID);

/* FIRST SECTION */

$title = 'Edit Menu';

$content = '';

ob_start(); 

echo $form->status($menu->status);

echo $form->input('title', 'Title', $menu->title, array(
	'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();