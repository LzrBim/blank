<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/AdminForm.php
----------------------------------------------------------------------------- */
namespace App\Lib;

use \App\Lib\Form;

class AdminForm extends Form {
	
	public function open($opts = array()){ 
	
		$defaults = array(
			'id'		=> 'editForm',
			'action'	=> $_SERVER['REQUEST_URI'],
			'method'=> 'post'
		);
		
		$opts = array_merge($defaults, $opts);
	
		return '<form id="'.$opts['id'].'" action="'.$opts['action'].'"  method="post" enctype="multipart/form-data" role="form">';
		
	}
	
	public function close(){ 
	
		return '</form>';
		
	}
	
	/*
	DISPLAY A TINYMCE TEXTAREA
	
	echo $form->editor('name', 'Title', '', array(
		'required' => true,
		'help' => 'Body of the document',
		'rows'=> 6)
	);
	*/

	public function editor($name, $title, $value = '', $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
	
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tinyMCE';																 																												 
		} else {
			$opts['inputClass'] = 'tinyMCE';
		}
		
		$label = $this->_buildLabel($title, $opts);
		$control = '<textarea name="'.$name.'" '.$this->_getInputAttr($opts).' '.$this->_appendTextareaAttr($opts).' >'.$value.'</textarea>'.$this->_appendHelp($opts);
		
		return $this->_render($label, $control, $opts);
		
	}
	
	
	public function simpleEditor($name, $title, $value = '', $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
	
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' simpleTinyMCE';																 																												 
		} else {
			$opts['inputClass'] = 'simpleTinyMCE';
		}
		
		$label = $this->_buildLabel($title, $opts);
		$control = '<textarea name="'.$name.'" '.$this->_getInputAttr($opts).' '.$this->_appendTextareaAttr($opts).' >'.$value.'</textarea>'.$this->_appendHelp($opts);
		
		return $this->_render($label, $control, $opts);
		
	}
	
	/* DISPLAY IMAGE
	----------------------------------------------------------------------------- */
	
	//SPECIAL OPTIONS:
	/*
	$form->image($model, array(
		'mainImageTitle' 	=> '',	
		'thumbImageTitle' => '',	
		'noImageMessage' 	=> ''
	));
	*/
	
	public function image($model, $opts = array()){ 
	
		
	
		if(empty($model) || !is_object($model)){ 
		
			wLog(3, 'Error determining image settings');
			return 'Error determining image settings';
			
		} 
		
		$defaults = array(
			'mainImageTitle' 	=> 'Main Image',	
			'thumbImageTitle' => 'Thumbnail Image',	
			'noImageMessage' 	=> 'No image uploaded',
			'noImageMessageLevel' 	=> 'warning'
		);
		
		$opts = array_merge($defaults, $opts);		
		
		$html = '';
		
		if($model->image->hasMainImage() || $model->image->hasThumbImage()) {
		
			if($model->image->hasMainImage()) {
				
				$mainSrc = $model->image->getMainSrc();
			
				ob_start(); ?>
	
				<div id="editImage_main" class="hoverControlsContainer">
        
        	<img src="<?= $mainSrc; ?>?t=<?= time(); ?>" class="img-responsive" />	
            
					<div class="hoverControls">
						<a data-model="<?= get_class($model); ?>" data-id="<?= $model->getId(); ?>" data-type="main" class="btn btn-default tpjc_cropButton" href="#">Crop Image</a> 
					</div>
				</div><?
				
				$control = ob_get_contents();	
				ob_end_clean();
				
				$label = $this->_buildLabel($opts['mainImageTitle'], $opts);
				$html .= $this->_render($label, $control, $opts);
				
			} 
			
			if($model->image->hasThumbImage()) {
				
        $mainSrc = $model->image->getThumbSrc();
				
				ob_start(); ?>
		
				<div id="editImage_thumb" class="hoverControlsContainer">
					<img src="<?= $mainSrc; ?>?t=<?= time(); ?>" class="img-responsive" />
					<div class="hoverControls">
						<a data-model="<?= get_class($model); ?>" data-id="<?= $model->getId(); ?>" data-type="thumb" class="btn btn-default tpjc_cropButton" href="#">Crop Image</a>
					</div>
				</div><?
				
				$control = ob_get_contents();	
				ob_end_clean();
				
				$label = $this->_buildLabel($opts['thumbImageTitle'], $opts);
				$html .= $this->_render($label, $control, $opts);
				
			} 

		} else { 
		
			$control = '<p class="alert alert-'.$opts['noImageMessageLevel'].'">'.$opts['noImageMessage'].'</p>';
			
			$label = $this->_buildLabel('', $opts);
			
			$html .= $this->_render($label, $control, $opts);
		}
		
		
		return $html;
	
	}
	
	/* DISPLAY IMAGES   
	----------------------------------------------------------------------------- */
	public function gallery_images($parent, $collection, $editPage){ 
	
		if(empty($collection)){ return '<p>No images yet</p>'; } 
		
		ob_start(); ?>
    
    <div class="clearfix" style="margin-bottom:15px;">
    <ul class="systemThumbs"><?  
		
		foreach($collection as $collectionImage){ ?>
    
    	<li id="galleryImage_<?= $collectionImage->getId(); ?>">
			
				<div class="hoverControlsContainer">
			
					<img src="<?= $collectionImage->image->getSystemSrc(); ?>?t=<?= time(); ?>" class="img-responsive" />
          
        	<div class="hoverControls">
          
						<a class="btn btn-default btn-xs" data-tpjc-action="formChangeMonitorButton" href="<?= $editPage; ?>?mode=edit&<?= $parent->_id; ?>=<?= $parent->getId(); ?>&<?= $collectionImage->_id; ?>=<?= $collectionImage->getId(); ?>"><i class="glyphicon glyphicon-pencil"></i></a> 
            
            <a class="btn btn-default btn-xs tpjc_removeGalleryImage" data-tpjc-action="remove_gallery_image" data-tpjc-id="<?= $collectionImage->getId(); ?>" data-tpjc-model="<?= get_class($collectionImage); ?>" href="#"><i class="glyphicon glyphicon-trash"></i></a>  
            
					</div>
          
				</div>
				
			</li><? 
			
		} ?>
    
    </ul>
		</div><? 
				
		$control = ob_get_contents();	
		
		ob_end_clean();	
		
		return $control;
	
	}
	
	public function document($model, $opts = array()){ 
	
		$label = $this->_buildLabel('Document', $opts);
		
		if($model->document->hasFile()) { 
		
			$control = $model->document->get_document_summary();
			
		} else { 
			$control = '<p>No document found</p>';
		}
		
		
		return $this->_render($label, $control, $opts);
	}
	
	/* BUTTONS SETS   
	----------------------------------------------------------------------------- */
	
	public function buttonsAdd($showSaveAndAdd = true){ 
		
		ob_start(); ?>
		
    <div class="row">
    	<div class="col-xs-12">
        <div class="form-actions">
          <button type="submit" id="quickSave" name="quickSave" class="btn btn-large btn-primary">Save</button>
          <button type="submit" id="saveAndAdd" name="saveAndAdd" class="btn btn-large btn-default">Save &amp; Add Another</button>	
        </div>
			</div>
		</div><?
			
		
		$control = ob_get_contents();	
		ob_end_clean();
		return $control;
	}
	
	public function buttonsEdit($showSaveAndGoBack = true){ 
	
		ob_start(); ?>
    
    <div class="row">
    	<div class="col-xs-12">
        <div class="form-actions">
          <button type="submit" id="quickSave" name="quickSave" class="btn btn-large btn-primary">Save</button>
      		<button type="submit" id="goBack" name="goBack" class="btn btn-large btn-default">Save &amp; Go Back</button>	
        </div>
			</div>
		</div><?
				
		$control = ob_get_contents();	
		ob_end_clean();
		return $control;
	} 
	
	/*
	DISPLAY A RADIO BUTTON
	
	echo $form->radio('name', 'Title', 0,  array(1, 'title', t, 0 => 'off') ); 
	
	*/
	public function radioButtonGroup($name, $title, $value = '', $choices = array(), $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
		
  
		$label = $this->_buildLabel($title, $opts);
	
		ob_start();
	
		if(!empty($choices)){ ?>
    
    	<br /><div class="btn-group" data-toggle="buttons"><? 
			
			foreach($choices as $key => $val){ ?>
          
       
        <label for="<?= $name; ?>_<?= $key; ?>" class="btn btn-default<? 
					if($value == $key){
						echo ' active';
					} ?>"> 
          <input type="radio" name="<?= $name; ?>" value="<?= $key; ?>" <? 
					if($value == $key){
						echo 'checked="checked"';
					} ?>> <?= $val; ?>
        </label><?
			} ?>
      
      </div><!-- /.btn-group --><?
			
		} else {
			echo 'Choice array was empty';
		} 
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
	}
	
	public function selectSearch($name, $title, $choices = array(), $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
	
		/* THIS IS TIED TO THE CHOSEN JQUERY PLUGIN*/
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tpjc_searchSelect';																 																												 
		} else {
			$opts['inputClass'] = 'tpjc_searchSelect';
		} 
    
		$label = $this->_buildLabel($title, $opts);
	
		ob_start();  ?>
    
    <select name="<?= $name; ?>" <?= $this->_getInputAttr($opts); ?>><? 
		
		if(isset($opts['emptyOption']) && !empty($opts['emptyOption'])) { ?>
      <option value="" selected="selected"><?= $opts['emptyOption']; ?></option><?
    }
			
    foreach($choices as $arr){
      list($optTitle, $value, $isSelected) = $arr; ?>
        <option value="<?= $value; ?>" <? 
        if($isSelected){
          echo 'selected';
        } ?>>
        <?= $optTitle; ?>
      </option><?
    } ?>
    </select><? 
    		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);

	}
	
}


