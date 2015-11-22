<?  
/*-----------------------------------------------------------------------------
  * SITE: 
	* FILE: /app/lib/form.php
----------------------------------------------------------------------------- */

namespace App\Lib;

/*$opts = array(
	'required' 		=> true,
	'id' 					=> x,
	'class' 			=> '', 
	'labelClass' 	=> '',
	'rows'				=> 10, //default textarea rows
	'placeholder' => '',
	'help' 				=> 'how to use this form item',
	'helpClass' 	=> 'help-block', //default is help-block
	'data'				=> '' //like 'data-attr-id=5' - full string
);*/



class Form {
	
	protected $_defaultWrapperClass = 'form-group';
	protected $_defaultLabelClass = '';
	protected $_defaultInputClass = 'form-control';
	protected $_defaultHelpClass = 'help-block';
	protected $_defaultTextareaRows = 8;
	
	
	protected function _render($label, $control, $opts){
				
		ob_start(); ?>
		
		<div class="<?= $this->_defaultWrapperClass; ?>">
			<?= $label; ?>
			<?= $control; ?>
		</div><? 
		
		$content = ob_get_contents();	
		ob_end_clean();
		return $content;
		
	}
	
	protected function _buildLabel($title, $opts){ 
		
		return '<label '.$this->_getLabelAttr($opts).'>'.$title.'</label>';
		
	}
	
	protected function _buildInput($type, $name, $title, $value = '', $opts = array()){ 
	
		//echo '<pre>'.print_r($opts).</pre>';
	
		if(empty($type) || !in_array($type,array('text','file','file','password'))){ return 'Invalid input type supplied'; } 
		
		if(empty($name)){ return 'No input name supplied'; } 
		
		if(empty($type)){ return 'No type supplied'; } 
		
		$label = $this->_buildLabel($title, $opts);
		$control = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" '.$this->_getInputAttr($opts).' >'.$this->_appendHelp($opts);
		
		return $this->_render($label, $control, $opts);
		
	}
	
	
	/* DISPLAY FUNCTIONS
	----------------------------------------------------------------------------- */
	/*
	$form = new Form();
	$form->open(array(
		'id'			=> '',
		'action'	=> '',
		'method'	=> ''
	));
	
	*/	
	
	public function open($opts = array()){ 
	
		$defaults = array(
			'id'		=> 'contactForm',
			'action'	=> $_SERVER['REQUEST_URI'],
			'method'=> 'post'
		);
		
		$opts = array_merge($defaults, $opts);
	
		return '<form id="'.$opts['id'].'" action="'.$opts['action'].'"  method="'.$opts['method'].'">';
		
	}
	
	public function close(){ 
	
		return '</form>';
		
	}
	
	public function arbitraryRow($title, $control, $opts = array()){ 
	
		$label = $this->_buildLabel($title, $opts);
		return $this->_render($label, $control, $opts);
		
	}
	
	/*
	DISPLAY A TEXT INPUT
	
	echo $form->input('fieldName', 'title', '', array(
		'required' 		=> true,
		'placeholder' => ''
		)
  );
	*/
	public function input($name, $title, $value = '', $opts = array()){ 
	
		return $this->_buildInput('text', $name, $title, $value, $opts);
		
	}
	
	
	/*
	DISPLAY A TEXTAREA
	
	echo $form->textarea('fieldName', 'title', '', array(
		'required' 		=> true,
		'placeholder' => ''
		)
  );
	*/
	public function textarea($name, $title, $value = '', $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
		
		$label = $this->_buildLabel($title, $opts);
		
		$control = '<textarea name="'.$name.'" '.$this->_getInputAttr($opts).' '.$this->_appendTextareaAttr($opts).' >'.$value.'</textarea>'.$this->_appendHelp($opts);
		
		return $this->_render($label, $control, $opts);
		
	}  
	
	
	/*
	DISPLAY A RADIO BUTTON
	
	echo $form->radio('name', 'Title', 0,  array(1 = 'on', 0 => 'off') ); 
	*/
	public function radio($name, $title, $value = '', $choices = array(), $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
  
		$label = $this->_buildLabel($title, $opts);
	
		ob_start();
	
		if(!empty($choices)){
			
			foreach($choices as $key => $val){ ?>
				<label for="<?= $name; ?>_<?= $key; ?>" class="radio-inline" >
					<input type="radio" name="<?= $name; ?>" id="<?= $name; ?>_<?= $key; ?>" value="<?= $key; ?>" <? 
					if($value == $key){
						echo 'checked';
					} ?>>
					<?= $val; ?>
				</label><?
			}
			
		} else {
			echo 'Choice array was empty';
		} 
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
	}
	
	/*
	DISPLAY A CHECKBOX - Value is always 1

	echo $form->checkbox('name', 'Title', false);
	*/
	public function checkbox($name, $title, $checked = false, $opts = array() ){ 
  
		if(empty($name)){ return 'No input name supplied'; } 
	
		ob_start(); ?>
    
    <div class="checkbox">
			<label <?= $this->_getLabelAttr($opts); ?>>
				<input name="<?= $name; ?>" value="1"  type="checkbox" <?										 
				if($checked){
					echo 'checked';
				} ?>> <?= $title; ?>
			
      </label>
		</div><? 
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $control;
	}
	
	public function checkboxCollapsible($name, $title, $label, $content, $checked = false, $opts = array() ){ 
  
		if(empty($name)){ return 'No input name supplied'; } 
		
		if(isset($opts['target'])){ 
			$target = $opts['target'];
		} else {
			$target = 'collapsible_'.rand(1,100);
		} 
		
		ob_start(); ?>
    
    <div class="<?= $this->_defaultWrapperClass; ?>"><?
		
			if(!empty($title)){ ?>
    
    		<label><?= $title; ?></label><?
				
			} ?>
      
      <div class="checkboxCollapsible">
      
        <div class="checkbox">
          <label <?= $this->_getLabelAttr($opts); ?>>
            <input name="<?= $name; ?>" value="1"  type="checkbox" data-toggle="collapse" data-target="#<?= $target; ?>" <?										 
            if($checked){
              echo 'checked';
            } ?>> <?= $label; ?>
          
          </label>
        </div> 
        
        <div id="<?= $target; ?>" class="collapse<? if(isset($opts['collapsed']) && $opts['collapsed'] == false){ echo ' in'; } ?>">
          <?= $content; ?>
        </div>
        
      </div>
      
		</div><? 
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $control;
	}
	
	/*
	DISPLAY A ROW OF CHECKBOXES 

	echo $form->checkboxes('Group Title', array(
		array('name', 'title', true),
		array('name', 'title', false),
	));
	
	$choices = array(
		array('name', 'title', isChecked = false),
	);
	
	*/
	public function checkboxes($title, $choices = array(), $opts = array()){ 
		
		$label = $this->_buildLabel($title, $opts);
	
		ob_start(); 
		
		if(!empty($choices)){
			
			foreach($choices as $arr){
				list($name, $optTitle, $isChecked) = $arr; ?>
				<label for="<?= $name; ?>" class="checkbox-inline">
					<input type="checkbox" name="<?= $name; ?>" id="<?= $name; ?>" value="1" <? 
					if($isChecked){
						echo 'checked';
					} ?>>
					<?= $optTitle; ?>
				</label><?
			}
		} else {
			echo 'Choice array was empty';
		}
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
	 
	}
	
	/*
	DISPLAY A MULTIPLE SELECT CHECKBOX
	
	echo $form->checkboxMultiple('name', 'Title', array(
		array('Option 1 Title', 2, true),		
		array('Option 2 Title', 3, false),		
		array('Option 3 Title', 4, false)		
	));
	
	$choices = array(
		array('Title', value, isChecked = false),
	);
	
	*/
	public function checkboxMultiple($name, $title, $choices = array(), $opts = array()){ 
  
		if(empty($name)){ return 'No input name supplied'; } 
		
		$label = $this->_buildLabel($title, $opts);
	
		ob_start(); 
		
		if(!empty($choices)){
			foreach($choices as $arr){
				list($optTitle, $value, $isChecked) = $arr; ?>
				<label for="<?= $name; ?>_<?= $value; ?>" class="checkbox inline">
					<input type="checkbox" name="<?= $name; ?>[]" id="<?= $name; ?>_<?= $value; ?>" value="<?= $value; ?>" <? 
					if($isChecked){
						echo 'checked';
					} ?>>
					<?= $optTitle; ?>
				</label><?
			}
		} else {
			echo 'Choice array was empty';
		}
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
		
	}
	 
	/*
	DISPLAY A SELECT
	
	$form->select('name', 'Title', array(
		array('Please select an option', '', true),
		array('Option 1', 1, false),		
		array('Option 2', 2, false),		
		array('Option 3', 3, false)		
	), array(
		'required' => true,
		'help' => 'pick something')
	);
	
	$choices = array(
		array('name', 'title', 'value', isSelected = false),
	);
	*/
	public function select($name, $title, $choices = array(), $opts = array()){ 
  
		$label = $this->_buildLabel($title, $opts);
	
		ob_start(); 
		
		if(!empty($choices)){ ?>
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
    } else {
      echo 'No choices yet.';
    }
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
		 
	}
	
	public function groupedSelect($name, $title, $choices = array(), $opts = array()){ 
  
		if(empty($name)){ return 'No input name supplied'; } 
		
		$label = $this->_buildLabel($title, $opts);
	
		ob_start(); 
		
		if(!empty($choices)){ ?>
      <select name="<?= $name; ?>" <?= $this->_getInputAttr($opts); ?>><? 
			
			if(isset($opts['emptyOption']) && !empty($opts['emptyOption'])) { ?>
				<option value=""><?= $opts['emptyOption']; ?></option><?
			}
			
      foreach($choices as $group => $optionsArr){ ?>
				
				<optgroup label="<?= $group; ?>"><? 
				
				foreach($optionsArr as $arr){
			
					list($optTitle, $value, $isSelected) = $arr; ?>
						<option value="<?= $value; ?>" <? 
						if($isSelected){
							echo 'selected';
						} ?>>
						<?= $optTitle; ?>
					</option><?
				} ?>
        
        </optgroup><? 
				
      } ?>
      </select><? 
			
    } else {
      echo 'Option array was empty';
    }
		
		echo $this->_appendHelp($opts); 
		
    $control = ob_get_contents();	
		ob_end_clean();
      
		return $this->_render($label, $control, $opts);
		 
	}
	
	public function phone($name, $title, $value = '', $opts = array()){ 
	
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tpjc_phoneMask';																 																												 
		} else {
			$opts['inputClass'] = 'tpjc_phoneMask';
		}
		
		return $this->input($name, $title, $value, $opts);  
		
	}
	
	public function datepicker($name, $title, $value = '', $opts = array()){ 
	
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tpjc_datePicker';																 																												 
		} else {
			$opts['inputClass'] = 'tpjc_datePicker';
		}
		
		/* BECAUSE MYSQL STORES DATE FORMAT IN YYYY-MM-DD LET'S TRANSLATE IT TO ENGLISH	*/
		if(Sanitize::isMySqlDate($value)){
			$value = date('m/d/Y', strtotime($value));
		} else {
			$value = '';
		}
		return $this->input($name, $title, $value, $opts);  
		
	}
	
	
	public function timepicker($name, $title, $value = '', $opts = array()){ 
	
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tpjc_timePicker';																 																												 
		} else {
			$opts['inputClass'] = 'tpjc_timePicker';
		}
		
		/* Convert a date?	*/
		$value = date('g:i a', strtotime($value));
		
		$control = '';
		
		ob_start(); ?>
		
			<div class="input-group bootstrap-timepicker">
					<input type="text" name="<?= $name; ?>" class="form-control tpjc_timePicker">
					<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
			</div><?= $this->_appendHelp($opts); ?> <? 
		
		$control = ob_get_contents();
		ob_end_clean();
		
		return $this->arbitraryRow($title, $control, $opts);  
		
	}

	
	public function tag($name, $title, $value = '', $opts = array()){ 
	
		if(array_key_exists('data', $opts) && !empty($opts['data'])) {
			$opts['data'] .= ' data-role=tagsinput';																 																												 
		} else {
			$opts['data'] = 'data-role=tagsinput';
		}

		return $this->input($name, $title, $value, $opts);  
		
	}
	
	/*
	$choices = array(
		array('title', 'value', isSelected = false),
	);
	*/
	public function selectMultiple($name, $title, $choices = array(), $opts = array()){ 
	
		if(empty($name)){ return 'No input name supplied'; } 
	
		/* THIS IS TIED TO THE CHOSEN JQUERY PLUGIN*/
		if(array_key_exists('inputClass', $opts) && !empty($opts['inputClass'])) {
			$opts['inputClass'] .= ' tpjc_multiSelect';																 																												 
		} else {
			$opts['inputClass'] = 'tpjc_multiSelect';
		} 
    
		$label = $this->_buildLabel($title, $opts);
	
		ob_start();  ?>
    
    <select name="<?= $name; ?>[]" <?= $this->_getInputAttr($opts); ?> multiple="multiple"><? 
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
	
	
	
	/*
	DISPLAY A FILE INPUT
	
	echo $form->fileInput('uploadFile', 'File');
	*/
	
	public function fileInput($name, $title, $opts = array()){ 

		if(empty($name)){ return 'No name supplied'; } 
		
		$label = $this->_buildLabel($title, $opts);
		
		if(isset($opts['multiple']) && $opts['multiple']){
			$control = '<input type="file" name="'.$name.'[]" multiple="multiple" />';
		} else {
			$control = '<input type="file" name="'.$name.'" />';
		}
		
		$control .= $this->_appendHelp($opts);
		
		return $this->_render($label, $control, $opts);
		
	}
	
	
	/*
	DISPLAY A PASSWORD INPUT
	
	echo $form->password('password', 'Password');
	*/
	
	public function password($name, $title, $opts = array()){ 
		
		return $this->_buildInput('password', $name, $title, '', $opts);
	}
	
	/*
	DISPLAY A HIDDEN INPUT
	
	echo $form->hidden('mode', 'insert');
	*/
	
	public function hidden($name, $value = '', $opts = array()){ 
	
		if(empty($name)){
			return '';
			wLog(2, 'no name supplied');
		}
		
		$class = '';
		if(!empty($opts) && isset($opts['inputClass'])){
			$class = 'class="'.$opts['inputClass'].'"';
		}
	
		return '<input type="hidden" '.$class.' name="'.$name.'" value="'.$value.'" />';
		
	}
	
	
	/* COMMON SETS   
	----------------------------------------------------------------------------- */
	
	public function yesNoRadio($name, $title, $val = 1){ 
  
		return $this->radio($name, $title, $val, $choices = array(
			1 => 'Yes' ,
			0 => 'No'
		));
	 
	}
	
	public function status($status = 'inactive'){ 
  
		return $this->radio('status', 'Status', $status, $choices = array(
			'active' => 'Active' ,
			'inactive' => 'Inactive'
		));
	 
	} 
	
	
	
	/* ATTRIBUTE HELPERS   
	----------------------------------------------------------------------------- */
	
	protected function _getLabelAttr($opts = array()){
		
		$attr = '';
		$classes = array();
		$classes[] = $this->_defaultLabelClass;
		
		if(!empty($opts)){
		
			if(!empty($opts['id'])){
				$attr .= 'for="'.$opts['id'].'" '; 
			} 
			
			if(!empty($opts['required']) && $opts['required']){
				$classes[] = 'required';
			}
			
			if(!empty($opts['labelClass'])){
				$classes[] = trim($opts['labelClass']); 
			}

		}
		
		if(count($classes)){
			$attr .= 'class="'.implode(' ', $classes).'" '; 
		}
		
		return $attr; 
	}
	
	protected function _getInputAttr($opts = array()){
		
		$attr = '';
		$classes = array();
		$classes[] = $this->_defaultInputClass;
		
		if(!empty($opts['id'])){
			$attr .= 'id="'.$opts['id'].'" '; 
		} 
		
		if(!empty($opts['placeholder'])){
			$attr .= 'placeholder="'.$opts['placeholder'].'" '; 
		} 
		
		if(!empty($opts['inputClass'])){
			$classes[] = trim($opts['inputClass']); 
		}

		if(count($classes)){
			$attr .= 'class="'.implode(' ', $classes).'" '; 
		}
		
		if(!empty($opts['data'])){
			$attr .= $opts['data'].' '; 
		} 
		
		return $attr;
	}
	
	protected function _appendTextareaAttr($opts = array()){
		
		$attr = '';
		if(!empty($opts['rows'])){
			$attr .= 'rows="'.$opts['rows'].'" '; 
		} else {
			$attr .= 'rows="'.$this->_defaultTextareaRows.'" '; 
		}
		
		return $attr;
	}
	
	protected function _appendHelp($opts = array()){
		
		$helpStr = '';
		
		if(!empty($opts['helpClass'])){
			$helpClass = $opts['helpClass'];	
		} else {
			$helpClass = $this->_defaultHelpClass;
		}
		
		if(!empty($opts['help'])){
			$helpStr = '<span class="'.$helpClass.'">'.$opts['help'].'</span>'; 
		}
		//wLog(1, $helpStr);
		return $helpStr;
	}
	
	
} /* EOF Form.php */