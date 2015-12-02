<?php

namespace App\AdminView;

class ModalView {
	
	/* MAIN FUNCTION */
	public static function modal($id, $title, $content, $opts = []){ 
	
		if(empty($id)){ return ''; }
		
		if(empty($title)){ return 'N/A'; }
		
		if(empty($content)){ return ''; } 
		
		$opts = array_merge([
			'action' 		=> '',
			'formClass' => ''
		], $opts); ?>
    
    <!-- Modal -->
    <div id="<?= $id; ?>" class="modal fade" tabindex="-1" role="dialog">
    
      <form id="<?= $id; ?>Form" action="<?= $opts['action']; ?>" class="<?= $opts['formClass']; ?>" method="post">
      
      <div class="modal-dialog">
      
        <div class="modal-content">
        
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title; ?></h4>
          </div>
          
          <div class="modal-body">
          
						<div class="row">
            
              <div class="col-xs-12">
              
              	<div class="tpjc_modalAlert"></div>
              
              </div>
                
            </div><!-- /.row -->

						<?= $content; ?>            
               
          </div><!-- /.modal-body -->
          
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
      
        </div><!-- /.modal-content -->
        
      </div><!-- /.modal-dialog -->
      
      </form>
     
    </div><!-- /.modal --><? 
        
	}
	
}

/*

<div class="modal fade" tabindex="-1" role="dialog">

  <div class="modal-dialog">

		<div class="modal-content">
      
			<div class="modal-header">
        
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				
        <h4 class="modal-title">Modal title</h4>
      
			</div>
      
			<div class="modal-body">
        <p>One fine body&hellip;</p>
      </div>
      
			<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    
		</div><!-- /.modal-content -->
  
	</div><!-- /.modal-dialog -->

</div><!-- /.modal -->

*/