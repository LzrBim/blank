<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/EventDate/modal_add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

ob_start(); 

echo $form->hidden('eventID', $event->getId()); ?>
	
<div class="row">

  <div class="col-xs-6">
    <div style="margin-right:15px;">
    <?= $form->datepicker('date', 'Date', '', array('required' => true ) ); ?>
    </div>
  </div><!-- /.col -->
  
  <div class="col-xs-6">
  
    <?= $form->timepicker('time', 'Time', '7:00 PM', array('required' => true ) ); ?>
  
  </div><!-- /.col -->

</div><!-- /.row --><?

$content = ob_get_clean();

$adminView->displayModal('addEventDateModal', 'Add A Date', $content, 'ajax/addEventDate.php');