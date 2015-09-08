<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Post/edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($staff->_id, $staff->getId());
echo $form->hidden('imageID', $staff->imageID);

/* FIRST SECTION */

$title = 'Edit Staff';

$content = '';

ob_start();

echo $form->status('active'); ?>

<div class="row">

	<div class="col-sm-6"><? 
  
  	echo $form->input('firstName', 'First Name', $staff->firstName, array(
			'required' => true )
		); 
		
		echo $form->input('middleName', 'Middle Name', $staff->middleName); 
		
		echo $form->input('lastName', 'Last Name', $staff->lastName, array(
			'required' => true )
		);
		
		echo $form->input('suffix', 'Suffix', $staff->suffix); ?>
  
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
  
  	echo $form->input('email', 'Email', $staff->email); 
		
		echo $form->input('title', 'Job Title', $staff->title); 
		
		echo $form->select('staffCategoryID', 'Category', $staff->category->getSelectOptionArray($staff->staffCategoryID));
		
		echo $form->phone('mobilePhone', 'Mobile Phone', $staff->mobilePhone); ?>
    
    <div class="row">

      <div class="col-xs-8"><?
			
				echo $form->phone('officePhone', 'Office Phone', $staff->officePhone); ?>
      
      </div><!-- /.col -->
      
      <div class="col-xs-4"><?
			
				echo $form->input('officePhoneExtension', 'Extension', $staff->officePhoneExtension); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
  
  </div><!-- /.col -->
  
</div><!-- /.row --><?  

echo $form->editor('description', 'Biography', $staff->description, array(
	'rows' => 8)
); ?>

<div class="row">

	<div class="col-sm-4"><?

		echo $form->input('facebook', 'Facebook', $staff->facebook);  ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('twitter', 'Twitter', $staff->twitter); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-4"><?
	
		echo $form->input('linkedIn', 'Linked In', $staff->linkedIn);  ?>
  
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

$adminView->box($title, $content);

/* SECOND SECTION FOR IMAGE */

$title = 'Staff Photo';

$content = '';

ob_start(); 

if($staff->image->isLoaded()){
	echo $form->image($staff); 
}

echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));

$content = ob_get_clean();

$opts = array();
if($staff->image->isLoaded()){
	$opts = array(
		'buttons' => array(
			array(
				'text' 	=> 'Remove Image', 
				'href' 	=> '#', 
				'class' => 'danger', 
				'size'	=> 'xs',
				'icon' 	=> 'remove',
				'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$staff->getId().'"'
			)
		)
	);
}

$adminView->box($title, $content, $opts); 

echo $form->buttonsEdit();

echo $form->close();