<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/AdminUser/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add User';

$content = '';

ob_start();

echo $form->status('active'); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->input('firstName', 'First Name', repop('firstName')); 
		
		echo $form->input('lastName', 'Last Name', repop('lastName')); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
  
  	echo $form->input('email', 'Email', repop('email'), array(
			'required' => true )
		); 
		
		$field = $form->input('password', 'Specify a password', '');
		
		echo $form->checkboxCollapsible('useTemp', 'Password', 'Send user a temporary password via email', $field, true, $opts = array(
			'id' => 'tpjc_useTemp'
		)) ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();

