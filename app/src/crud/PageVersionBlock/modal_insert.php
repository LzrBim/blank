<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/modal_insert.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();?>
  
<!-- Modal -->
<div class="modal fade" id="insertPageVersionBlockModal" tabindex="-1" role="dialog" aria-labelledby="insertPageVersionBlockModal" aria-hidden="true">

	<form action="ajax/insertPageVersionBlock.php" class="form-horizontal" method="post">
  
  <?= $form->hidden('pageVersionID', $pageVersion->id()); ?>
  
  <div class="modal-dialog">
  
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Insert A Block</h4>
      </div>
      
      <div class="modal-body">
      
      	<div class="container">
        
        	<div class="row">
        
        		<div class="col-xs-12">
      
      				<div class="modalMessageContainer"></div>
              
            </div>
            
          </div><!-- /.row -->
          
          <div class="row">

            <div class="col-xs-12"><? 
							
							$pageVersionBlock = new PageVersionBlock(); 
							
							echo $form->selectSearch('pageVersionBlockID', 'PageVersion Block', $pageVersionBlock->getInsertBlockSelectArray($pageVersion->getId())); ?>
                         
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