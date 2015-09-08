<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/EventDate/modal_edit.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

ob_start(); 

echo $form->hidden('eventDateID', 0); ?>
	
<div class="row">

  <div class="col-xs-6">
    <div style="margin-right:15px;">
    <?= $form->datepicker('date', 'Date', '', array('required' => true ) ); ?>
    </div>
  </div><!-- /.col -->
  
  <div class="col-xs-6">
  
    <?= $form->timepicker('time', 'Time', '', array('required' => true ) ); ?>
  
  </div><!-- /.col -->

</div><!-- /.row --><?

$content = ob_get_clean();

$adminView->displayModal('editEventDateModal', 'Edit Date', $content, 'ajax/editEventDate.php');