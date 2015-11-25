<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/FaqTag.php
----------------------------------------------------------------------------- */

namespace App\Model;

class FaqTag extends Tag {
	
	//ATTRIBUTES
	public $_title = 'Tag';
	public $_table = 'faqTag';
	public $_linkTable = 'faqTagLink';
	public $_id = 'tagID';
	public $_modReWritePath = 'faq/tag/';
	
	//FIELDS
	public $tagID = 0; 

}