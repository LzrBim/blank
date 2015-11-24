<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ImageLibrary/ajax_add.php
----------------------------------------------------------------------------- */ 

include(MODEL_PATH.'Page.php'); 

$page = new Page();

$form = new \App\Lib\AdminForm();?>
  
<!-- Modal -->
<div class="modal fade" id="menuItemModal" tabindex="-1" role="dialog" aria-labelledby="menuItemModal" aria-hidden="true">

	<form id="ajaxForm" name="ajaxForm" action="ajax/addMenuItem.php" class="form-horizontal" method="post" enctype="multipart/form-data">
   
  <div class="modal-dialog">
  
    <div class="modal-content">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Add A Menu Item</h4>
      </div>
      
      <div class="modal-body">
      
      	<div class="container">
        
        <div class="row">
        
        	<div class="col-xs-12">

						<?= $form->hidden('menuID', $menu->menuID); ?>
            
            <?= $form->input('title', 'Title', '', array(
              'required' => true)
            ); ; ?>
      
      			<?= $form->radio('type', 'Type', 1,  array(1 => 'Page', 2 => 'Link', 3 => 'Drop Down'), array(
							'required' => true )
            ); ?>
      			
            <div class="menuItemPageContainer menuItemContainer">
      
							<?= $form->select('pageID', 'Page', $page->getSelectOptionArray(0, true), array(
								'id' => 'pageID',
                'required' => true )
              ); ?>
              
            </div>
            
            <div class="menuItemHrefContainer menuItemContainer">
            
							<?= $form->input('href', 'Link', '', array(
								'id' => 'href',
                'required' => true )
              ); ?>
              
            </div>
        
        	</div>
        
        </div>
        
        </div>
        
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div><!-- /.modal-content -->
    
  </div><!-- /.modal-dialog -->
  </form>
 
</div><!-- /.modal -->