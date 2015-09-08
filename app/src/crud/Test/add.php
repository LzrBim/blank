<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Test/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); ?>

<div class="container padded">

  <div class="box">
  
   	<form id="editForm" class="form-horizontal" method="post" enctype="multipart/form-data">
   	<input type="hidden" name="mode" value="insert" />
  
   	<div class="box-header">
      <span class="title">Add Test</span>
    
      <ul class="box-toolbar">
      <li><a data-toggle="collapse" data-target="#demo1"><i class="glyphicon glyphicon-minus"></i></a></li>
      </ul>
      
    </div><!-- /.box-header -->
    
    <div id="demo1" class="box-content collapse in">
  
			<?= $form->status('active'); ?>
    
      <?= $form->input('title', 'Title', '', array(
        'required' => true)
      ); ?>
      
      <?= $form->datepicker('dateAdded', 'Date', '', array(
        'size' => 'small')
      ); ?>
      
      <?= $form->radio('radio1', 'Radio 1', 0,  array('on' => 1, 'off' => 0) ); ?>
      
      <?= $form->checkbox('check1', 'Check 1'); ?>
         
      <?= $form->checkboxes('CheckBoxes X Y Z', array(
        array('x', 'CheckBox x', false),		
        array('y', 'CheckBox y', false),		
        array('z', 'CheckBox z', false)		
      )); ?>
      
      <?= $form->checkboxMultiple('multiCheck1', 'Checkbox Multiple', array(
        array('Option 1 Title', 2, false),		
        array('Option 2 Title', 3, false),		
        array('Option 3 Title', 4, false)		
      )); ?>
      
      <?= $form->select('select1', 'Select Title', array(
        array('Please select an option', '', true),
        array('Option 1', 1, false),		
        array('Option 2', 2, false),		
        array('Option 3', 3, false)		
      ), array(
        'required' => true,
        'help' => 'pick something',
        'size'=> 'large')
      ); ?>
      
      <?= $form->selectMultiple('multiSelect1', 'Multi Select 1', array(
        array('Option 1', 1, false),		
        array('Option 2', 2, false),		
        array('Option 3', 3, false)		
      ), array(
        'required' => true,
        'help' => 'pick something')
      ); ?>
      
      <?= $form->password('password1', 'Password'); ?>
      
      <?= $form->textarea('textarea1', 'Textarea', '', array(
        'required' => true,
        'help' => 'no puncturation')
      ); ?>
      
      <?= $form->editor('description1', 'Description Editor', '', array(
        'required' => true,
        'help' => 'no puncturation',
        'size'=> 'large')
      ); ?>
      
       <?= $form->editor('description2', 'Description Editor2', '', array(
        'required' => true,
        'help' => 'nostuff',
        'size'=> 'xlarge')
      ); ?>
      
      <?= $form->fileInput('uploadFile', 'File'); ?>
    
    </div><!-- /.box-content --> 
      
    <?= $form->buttonsAdd(); ?>
      
		</form>
  
	</div><!-- /.box -->

</div><!-- /.container -->