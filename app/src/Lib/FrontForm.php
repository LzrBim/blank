<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/lib/FrontForm.php
----------------------------------------------------------------------------- */

class FrontForm extends Form {
	
	public function submitButton($opts = array()){ 
		
		ob_start(); ?>
		
    <button type="submit" class="btn btn-default">Submit</button><?

		return ob_get_clean();
	}
	
}
