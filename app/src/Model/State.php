<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/State.php
----------------------------------------------------------------------------- */

class State extends Core {
	
	//ATTRIBUTES
	public $_title = 'State';
	public $_id = 'stateID';
	public $_table = 'state';
	
	//FIELDS
	public $stateID = 0;
	public $abbreviation;
	public $title;

	
	/* LOAD
	----------------------------------------------------------------------------- */
	
	
	/* FETCH
	----------------------------------------------------------------------------- */

	
	/* CRUD
	----------------------------------------------------------------------------- */
		
		
	/* CRUD
	----------------------------------------------------------------------------- */
	
	/*
		$choices = array(
			array('title', 'value', isSelected = false),
		);
	*/
	public function getSelectOptionArray($stateID = 0){
		
		$choices = array();
		$states = $this->fetchAll('title');			
	
		foreach($states as $state){
			
			$selected = ($state->stateID == $stateID)? true : false ;
			
			$choices[] = array($state->title, $state->id(), $selected);
		}
		return $choices;
		
	}
	
}