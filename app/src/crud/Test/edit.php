<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Test/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); ?>

<form id="editForm" class="form-horizontal" method="post" enctype="multipart/form-data">
	<input type="hidden" name="mode" value="update" />
	<input type="hidden" name="testID" value="<?= $model->testID; ?>" />

  <div class="groupContainer">
    <div class="group">

		<?= $form->status($model->status); ?>
  
		<?= $form->input('title', 'Title', $model->title, array(
      'required' => true,
      'size'=> 'xxlarge')
    ); ?>
    
     <?= $form->datepicker('dateAdded', 'Date', $model->dateAdded, array(
      'size' => 'small')
    ); ?>
    
    <?= $form->radio('radio1', 'Radio 1', $model->radio1,  array('on' => 1, 'off' => 0) ); ?>
    
    <?= $form->checkbox('check1', 'Check 1', $model->check1); ?>
       
    <?= $form->checkboxes('CheckBoxes X Y Z', array(
      array('x', 'CheckBox x', $model->x),		
      array('y', 'CheckBox y', $model->y),		
      array('z', 'CheckBox z', $model->z)		
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
    
    <?= $form->password('password1', 'Password', $model->password1); ?>
    
    <?= $form->textarea('textarea1', 'Textarea', $model->textarea1, array(
      'required' => true,
      'help' => 'no puncturation',
      'size'=> 'xxlarge')
    ); ?>
    
    <?= $form->editor('description1', 'Description Editor', $model->description1, array(
      'required' => true,
      'help' => 'no puncturation',
      'size'=> 'large')
    ); ?>
    
     <?= $form->editor('description2', 'Description Editor2', $model->description2, array(
      'required' => true,
      'help' => 'nostuff',
      'size'=> 'xlarge')
    ); ?>
    
    <?= $form->fileInput('uploadFile', 'File'); ?>
    
    
    <?= $form->buttonsEdit(); ?>
    
  </div>
  
</div>

</form>