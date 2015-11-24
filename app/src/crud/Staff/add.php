<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Staff/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'insert');

$title = 'Add Staff';

$content = '';

ob_start();

echo $form->status('active'); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->input('firstName', 'First Name', repop('firstName'), array(
			'required' => true )
		); 
		
		echo $form->input('middleName', 'Middle Name', repop('middleName')); 
		
		echo $form->input('lastName', 'Last Name', repop('lastName'), array(
			'required' => true )
		);
		
		echo $form->input('suffix', 'Suffix', repop('suffix')); 
		
		?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
  
  	echo $form->input('email', 'Email', repop('email')); 
		
		echo $form->input('title', 'Job Title', repop('title'));
		
		echo $form->select('staffCategoryID', 'Category', $staff->category->getSelectOptionArray());
		
		echo $form->phone('mobilePhone', 'Mobile Phone', repop('mobilePhone')); ?>
    
    <div class="row">

      <div class="col-xs-8"><?
			
				echo $form->phone('officePhone', 'Office Phone', repop('officePhone')); ?>
      
      </div><!-- /.col -->
      
      <div class="col-xs-4"><?
			
				echo $form->input('officePhoneExtension', 'Extension', repop('officePhoneExtension')); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

echo $form->editor('description', 'Biography', repop('description'), array(
	'rows' => 8)
); ?>

<div class="row">

	<div class="col-sm-4"><?

		echo $form->input('facebook', 'Facebook', repop('facebook'));  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('twitter', 'Twitter', repop('twitter')); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('linkedIn', 'Linked In', repop('linkedIn'));  ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

echo $form->fileInput('uploadFile', 'Upload Photo'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();