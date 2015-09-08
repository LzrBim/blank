<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ItemCategory/modal_add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); ?>
  
<!-- Modal -->
<div class="modal fade" id="itemCategoryModal" tabindex="-1" role="dialog" aria-labelledby="itemCategoryModal" aria-hidden="true">

	<form action="itemCategory.php" class="form-horizontal" method="post">
  
  <?= $form->hidden('mode', 'insert'); ?>
  <?= $form->hidden('model', 'ItemCategory'); ?>
  <?= $form->hidden('status', 'active'); ?>
   
  <div class="modal-dialog">
  
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add A Category</h4>
      </div>
      
      <div class="modal-body">
      
      	<div class="container">
      
          <div class="row">
  
            <div class="col-sm-12">
            
            	<div class="modalMessageContainer"></div>
              
							<?= $form->input('title', 'Title', '', array(
                'required' => true )
              ); ?>
            
            </div><!-- /.col -->
            
          </div><!-- /.row -->
          
        </div><!-- /.container -->
      
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div><!-- /.modal-content -->
    
  </div><!-- /.modal-dialog -->
  </form>
 
</div><!-- /.modal -->