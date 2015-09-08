<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/AdminUser/editPassword.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'updatePassword');
echo $form->hidden($user->_id, $user->getId());

/* FIRST SECTION */

$title = 'Change Password';

$content = '';

ob_start(); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->password('password1', 'New Password', array(
			'required' => true,
			'id' => 'password1')
		); 
	
		echo $form->password('password2', 'Confirm Password', array(
			'required' => true )
		); ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><?  

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsEdit();

echo $form->close();