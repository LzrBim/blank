<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Page/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');

/* FIRST SECTION */

$title = 'Edit Settings';

$content = '';

ob_start(); ?>

<div class="row">

	<div class="col-sm-6"><?
	
		echo $form->input('organization', 'Organization', $settings->get('organization'), array(
			'required' => true )
		); 
		
		echo $form->input('email', 'Email', $settings->get('email'), array(
			'required' => true )
		); 
		
		echo $form->input('phone', 'Phone', $settings->get('phone'), array(
			'required' => true )
		); ?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
		echo $form->input('address', 'Address', $settings->get('address'), array(
			'required' => true )
		); 
		
		echo $form->input('city', 'City', $settings->get('city'), array(
			'required' => true )
		); ?>
		
		<div class="row">

			<div class="col-sm-6"><?
				echo $form->input('state', 'State', $settings->get('state'), array(
					'required' => true )
				); ?>
			
			</div><!-- /.col -->
			
			<div class="col-sm-6"><?
			
				echo $form->input('zip', 'Zip', $settings->get('zip'), array(
					'required' => true )
				);  ?>
			
			</div><!-- /.col -->
			
		</div><!-- /.row -->

  </div><!-- /.col -->
  
</div><!-- /.row --><? 

$content = ob_get_clean();

$adminView->box($title, $content);


/* SECOND SECTION FOR SEO */

$title = 'Search Engine Optimization Settings';

$content = '';

ob_start(); 

echo $form->input('metaTitle', 'Site Title', $settings->get('metaTitle') ); 

echo $form->textarea('metaDescription', 'Meta Description', $settings->get('metaDescription'), array('rows' => 3) );

echo $form->textarea('metaKeywords', 'Meta Keywords', $settings->get('metaKeywords'), array('rows' => 3) );

$content = ob_get_clean();

$adminView->box($title, $content); 

echo $form->buttonsEdit();

echo $form->close();