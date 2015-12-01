<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Newsletter/snippet.php
----------------------------------------------------------------------------- */ 

$newsletterView = new NewsletterView(); 

$form = new \App\Lib\AdminForm();

echo $form->open();

/* HIDDEN*/
echo $form->hidden($newsletter->_id, $newsletter->id());

/* SECOND SECTION */
$title = 'Edit Newsletter';
$content = '';

ob_start();  ?>
  
  <textarea id="snippet"  name="snippet" onclick="this.focus();this.select()" readonly="readonly" style="width:100%; margin-bottom:20px; font-size:12px;" rows="20"><?
	
	echo trim($newsletterView->newsletter($newsletter));?>
	
	</textarea><? 

$content = ob_get_clean(); 

$adminView->box($title, $content); 

echo $form->close();

