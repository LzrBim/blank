<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/AdminUser/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($user->_id, $user->getId());

/* FIRST SECTION */

$title = 'Edit User';

$content = '';

ob_start();

echo $form->status('active'); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->input('firstName', 'First Name', $user->firstName, array(
			'required' => true )
		); 
	
		echo $form->input('lastName', 'Last Name', $user->lastName, array(
			'required' => true )
		); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
  
  	echo $form->input('email', 'Email', $user->email);  ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><?  

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();