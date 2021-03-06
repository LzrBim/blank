<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/NewsletterBlock/modal_insert.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();?>
  
<!-- Modal -->
<div class="modal fade" id="insertNewsletterBlockModal" tabindex="-1" role="dialog" aria-labelledby="insertNewsletterBlockModal" aria-hidden="true">

	<form action="ajax/insertNewsletterBlock.php" class="form-horizontal" method="post">
  
  <?= $form->hidden('newsletterID', $newsletter->id()); ?>
  
  <div class="modal-dialog">
  
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Insert A Newsletter Block</h4>
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
							
							$newsletterBlock = new NewsletterBlock(); 
							
							echo $form->selectSearch('newsletterBlockID', 'Newsletter Block', $newsletterBlock->getInsertBlockSelectArray($newsletter->id())); ?>
                         
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