<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Staff/add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

echo $form->status('active'); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->input('firstName', 'First Name', '', array(
			'required' => true )
		); 
		
		echo $form->input('middleName', 'Middle Name',''); 
		
		echo $form->input('lastName', 'Last Name', '', array(
			'required' => true )
		);
		
		echo $form->input('suffix', 'Suffix', ''); 
		
		?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
  
  	echo $form->input('email', 'Email', ''); 
		
		echo $form->input('title', 'Job Title', '');
		
		//echo $form->select('staffCategoryID', 'Category', $staff->category->getSelectOptionArray());
		
		echo $form->phone('mobilePhone', 'Mobile Phone', ''); ?>
    
    <div class="row">

      <div class="col-xs-8"><?
			
				echo $form->phone('officePhone', 'Office Phone', ''); ?>
      
      </div><!-- /.col -->
      
      <div class="col-xs-4"><?
			
				echo $form->input('officePhoneExtension', 'Extension', ''); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

echo $form->editor('description', 'Biography', '', array(
	'rows' => 8)
); ?>

<div class="row">

	<div class="col-sm-4"><?

		echo $form->input('facebook', 'Facebook', '');  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('twitter', 'Twitter', ''); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('linkedIn', 'Linked In', '');  ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

echo $form->fileInput('uploadFile', 'Upload Photo'); 

echo $form->buttonsAdd();

echo $form->close();