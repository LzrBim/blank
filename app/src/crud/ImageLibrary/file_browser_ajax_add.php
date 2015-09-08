<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/ImageLibrary/file_browser_ajax_add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); ?>

<div class="box">
	
  <form id="editForm" target="ajaxIframe" action="<?= ADMIN_HTTP_PATH ?>js/file_browser/inc/insert_image.php" class="form-horizontal" method="post" enctype="multipart/form-data" role="form">
  <input type="hidden" name="mode" value="insert" />

	<div class="boxContent"><?
	
		echo $form->input('title', 'Title', '', array(
      'required' => true)
    ); 
    
    echo $form->tag('tags', 'Search Tags');
    
    echo $form->fileInput('uploadFile', 'Upload Image'); ?>

	</div><!-- /.boxContent -->
    
  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="submit" class="btn btn-default" onclick="window.parent.tinymce.activeEditor.windowManager.close();">Close</button>
  </div>
  </form>
    
</div><!-- /.box -->