<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/NewsletterBlock/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden('mode', 'update');
echo $form->hidden($newsletterBlock->_id, $newsletterBlock->id());
echo $form->hidden('status', $newsletterBlock->status);

/* SECOND SECTION */
$title = 'Edit Newsletter Block';
$content = '';

ob_start();

echo $form->input('title', 'Title', $newsletterBlock->title, array(
  	'required' => true )
	);

if($newsletterBlock->templateID == 1){
	
	echo $form->textarea('headline1', 'Headline', $newsletterBlock->headline1, array(
  	'required' => true )
	); 

} elseif($newsletterBlock->templateID == 2){
	
	echo $form->editor('description1', 'Wide Section', $newsletterBlock->description1, array(
  	'required' => true, 
		'rows' => 20)
	); 
	
} elseif($newsletterBlock->templateID == 3){ ?>

	<div class="row">

    <div class="col-sm-6"><?
			echo $form->editor('description1', 'Left Column', $newsletterBlock->description1, array(
				'required' => true,
				'rows' => 20 )
			); ?>
    
    </div><!-- /.col -->
    
    <div class="col-sm-6"><?
			echo $form->editor('description2', 'Right Column', $newsletterBlock->description2, array(
				'required' => true,
				'rows' => 20)
			); ?>
    
    </div><!-- /.col -->
    
  </div><!-- /.row --><?	

}


$content = ob_get_clean();

$adminView->box($title, $content); 

echo $form->buttonsEdit();

echo $form->close();