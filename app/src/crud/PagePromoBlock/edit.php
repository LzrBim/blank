<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PagePromoBlock/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($pagePromoBlock->_id, $pagePromoBlock->id());
echo $form->hidden('imageID', $pagePromoBlock->imageID);

/* FIRST SECTION */
$title = 'Edit Page Block';
$content = '';

ob_start();

echo $form->status($pagePromoBlock->status);

if($pagePromoBlock->isType()){
	
	if($pagePromoBlock->type == 'editor'){ 
	
		echo $form->editor('description1', $pagePromoBlock->title, $pagePromoBlock->title	);
		
		
	} else if($pagePromoBlock->type == 'text'){
		
		echo $form->textarea('description1', $pagePromoBlock->title, $pagePromoBlock->description1, array(
			'rows'=> 8)
		);
		
	 
	} else if($block->type == 'list'){
		
		echo $form->textarea('description1', $pagePromoBlock->title, $pagePromoBlock->description1, array(
			'rows' => 8,
			'help' => 'This is a list so place a new line after each list item')
		);
		
	} else if($block->type == 'headline'){
		
		echo $form->input('headline1', 'Headline 1', $pagePromoBlock->headline1 );
		
	} else {
		
		wLog(3, 'Invalid promo type '.$block->type);
		
	}
	
} elseif(!empty($pagePromoBlock->pageBlockTemplateID)) {
	
	$pageBlockTemplate = new PageBlockTemplate();
	
	if($pageBlockTemplate->load($pagePromoBlock->pageBlockTemplateID)){
	
		$template = new Template();
		$template->setTemplate($pageBlockTemplate->template);
		
		$vars = $template->getVars();
		
		foreach($vars as $var){
			
			$title = ucfirst(substr($var, 0, -1));
			
			if($title == 'Headline' || $title == 'Href'){
				
				if($title == 'Href'){
					$title = 'Link';
				}
				
				echo $form->input($var, $title, $pagePromoBlock->$var);
				
			} else {
				echo $form->editor($var, $title, $pagePromoBlock->$var);
			}
			
			
			
		}
		
	} else {
		wLog(3, 'Invalid template: '.$pagePromoBlock->pageBlockTemplateID);
	}

} else { 

	wLog(3, 'Invalid Promo: '.$pagePromoBlockID);
	return '';
	
}


$content = ob_get_clean();

$adminView->box($title, $content); 

if($pagePromoBlock->type == 'promo' || $pagePromoBlock->type == 'promo2'){

	/* SECOND SECTION FOR IMAGE */
	
	$title = 'Block Image';
	
	$content = '';
	
	ob_start(); 
	
	if($pagePromoBlock->image->isLoaded()){
		echo $form->image($pagePromoBlock); 
	}
	
	echo $form->fileInput('uploadFile', 'Upload Image', array('help' => 'This will replace the existing image'));
	
	$content = ob_get_contents();
	
	ob_end_clean();
	
	$opts = array();
	if($pagePromoBlock->image->isLoaded()){
		$opts = array(
			'buttons' => array(
				array(
					'text' 	=> 'Remove Image', 
					'href' 	=> '#', 
					'class' => 'danger', 
					'size'	=> 'xs',
					'icon' 	=> 'remove',
					'data' 	=> 'data-tpjc-action="removeImage" data-tpjc-id="'.$pagePromoBlock->id().'"'
				)
			)
		);
	}
	
	$adminView->box($title, $content,  $opts); 

}

echo $form->buttonsEdit();

echo $form->close();