<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/EventCategory.php
----------------------------------------------------------------------------- */

class EventCategory extends Tag {
	
	//ATTRIBUTES
	public $_title = 'Category';
	public $_table = 'eventCategory';
	public $_id = 'eventCategoryID';
	public $_modReWritePath = 'calendar/';
	
	//FIELDS
	public $eventCategoryID = 0;

}