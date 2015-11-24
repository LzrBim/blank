<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/DocumentLibrary/file_browser_ajax_add.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();?>

<div class="box">

	<form id="editForm" target="ajaxIframe" action="<?= ADMIN_HTTP_PATH ?>js/file_browser/inc/insert_document.php" class="form-vertical" method="post" enctype="multipart/form-data">
  <input type="hidden" name="mode" value="insert" />

	<div class="boxContent">
  
    <?= $form->input('title', 'Title', '', array(
      'required' => true,
      'size'=> 'xlarge')
    ); ?>
    
    <?= $form->fileInput('uploadFile', 'Upload Document'); ?>
    
	</div><!-- /.boxContent -->
    
  <div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="submit" class="btn btn-default" onclick="window.parent.tinymce.activeEditor.windowManager.close();">Close</button>
  </div>
  </form>
 
</div><!-- /.box -->