<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/models/PostTag.php
----------------------------------------------------------------------------- */

class PostTag extends Tag {
	
	//ATTRIBUTES
	public $_title = 'Tag';
	public $_table = 'postTag';
	public $_linkTable = 'postTagLink';
	public $_id = 'tagID';
	public $_modReWritePath = 'blog/tag/';
	
	//FIELDS
	public $tagID = 0; 

}