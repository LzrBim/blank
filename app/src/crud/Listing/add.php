<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Listing/add.php
----------------------------------------------------------------------------- */ 

$form = new AdminForm(); 

echo $form->open();
echo $form->hidden('mode', 'insert');

echo $form->hidden('price', '', array('inputClass' => 'tpjc_priceMaskDestination'));


$title = 'Add Listing';

$content = '';

ob_start(); ?>

<div class="row">

	<div class="col-sm-6"><?

		echo $form->status('active');
	
		echo $form->checkboxes('Type', array(
			array('isSale', 'For Sale', true),
			array('isRent', 'For Rent', false),
		));?>
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><?
	
		$listing = new Listing();

		echo $form->select('categoryID', 'Category', $listing->getCategorySelectOptionArray(), array(
			'required' => true )
		); ?>
			
  </div><!-- /.col -->
  
</div><!-- /.row --><? 

echo $form->input('title', 'Title', repop('title'), array(
  'required' => true )
); 

echo $form->editor('description', 'Description', repop('description'), array(
	'required' => true,
	'rows' => 6)
); ?>

<div class="row">

	<div class="col-sm-6"><? 
	
		echo $form->input('address', 'Address', repop('address'), array(
			'required' => true )
		);
		
		echo $form->input('city', 'City', repop('city'), array(
			'required' => true )
		); ?>
    
    <div class="row">

      <div class="col-sm-6"><? 
		
				echo $form->input('state', 'State', 'NY', array(
					'required' => true )
				); ?>
      
      </div><!-- /.col -->
      
      <div class="col-sm-6"><?
      
				echo $form->input('zip', 'Zip', repop('zip'), array(
					'required' => true )
				); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
  
  </div><!-- /.col -->
  
  <div class="col-sm-6"><? 
	
		echo $form->input('priceAnon', 'Price', repop('priceAnon'), array(
			'required' => true,
			'inputClass' => 'tpjc_priceMask')
		); ?>
    
    <div class="row">

      <div class="col-sm-6"><? 
		
				echo $form->input('beds', 'Beds', repop('beds'), array(
					'required' => true )
				); ?>
					
      </div><!-- /.col -->
      
      <div class="col-sm-6"><?
      
				echo $form->input('baths', 'Baths', repop('baths'), array(
					'required' => true )
				); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
    
    <div class="row">

      <div class="col-sm-6"><? 
		
				echo $form->input('sqft', 'Sq. Ft.', repop('sqft'), array(
					'required' => true )
				); ?>
      
      </div><!-- /.col -->
      
      <div class="col-sm-6"><?
      
				echo $form->input('lotSize', 'Lot Size', repop('lotSize'), array(
					'required' => true )
				); ?>
      
      </div><!-- /.col -->
      
    </div><!-- /.row -->
		
  </div><!-- /.col -->
  
</div><!-- /.row --><? 



echo $form->fileInput('uploadFile', 'Upload Image'); 

$content = ob_get_clean();

$adminView->box($title, $content);

echo $form->buttonsAdd();

echo $form->close();