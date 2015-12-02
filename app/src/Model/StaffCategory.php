<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/StaffCategory.php
----------------------------------------------------------------------------- */

namespace App\Model;
use \App\Lib\Sanitize;

class StaffCategory extends BaseModel {
	
	//ATTRIBUTES
	public $_title = 'Staff Category';
	public $_id = 'staffCategoryID';
	public $_table = 'staffCategory';
	
	//FIELDS
	public $staffCategoryID = 0;
	
}