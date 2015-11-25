<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/PageVersionBlock/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($pageVersionBlock->_id, $pageVersionBlock->getId());
echo $form->hidden('status', $pageVersionBlock->status);

if($pageVersionBlock->isRepeating){
	
	echo $form->input('title', 'Title', $pageVersionBlock->title, array(
		'required' => true )
	);
	
} else {
	echo $form->hidden('title', $pageVersionBlock->title);
}

if($pageVersionBlock->templateID == 1){
	
		echo $form->editor('description1', $pageVersionBlock->getTemplateTitle(1), $pageVersionBlock->description1, array(
  	'required' => true, 
		'rows' => 20)
	); 

} elseif($pageVersionBlock->templateID == 2){ ?>

	<div class="row">

    <div class="col-sm-6"><?
			echo $form->editor('description1', 'Left Column', $pageVersionBlock->description1, array(
				'required' => true,
				'rows' => 20 )
			); ?>
    
    </div><!-- /.col -->
    
    <div class="col-sm-6"><?
			echo $form->editor('description2', 'Right Column', $pageVersionBlock->description2, array(
				'required' => true,
				'rows' => 20)
			); ?>
    
    </div><!-- /.col -->
    
  </div><!-- /.row --><?	

	
} elseif($pageVersionBlock->templateID == 3){
	
	echo $form->input('headline1', $pageVersionBlock->getTemplateTitle(3), $pageVersionBlock->headline1, array(
  	'required' => true )
	); 
}

echo $form->buttonsEdit();

echo $form->close();