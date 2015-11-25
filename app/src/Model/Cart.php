<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /models/Cart.php
----------------------------------------------------------------------------- */

class Cart {
	
	//ATTRIBUTES
	public $_title = 'Cart';

	//FIELDS
	private $data;

	public function __construct(){
		
		if(!empty($_SESSION['cart'])){
			$this->data = unserialize($_SESSION['cart']);
			
		} else {
			$this->data = array();
		}
		
	}

	/* LOAD
	----------------------------------------------------------------------------- */
	
	public function add($id){
		
		$this->data[] = $id;
		
		$this->save();
		
		return true;
		
	}
	
	public function remove($id){
	
		$key = array_search($id, $this->data);
		
		if($key !== false){
			unset($this->data[$key]);
		}
		
		$this->save();
		
		return true;
	}
	
	public function has_item($id){
		return in_array($id, $this->data);
	}
	
	
	public function get_total_items(){
		return count($this->data);
	}
	
	public function get_total(){
		return '$'.number_format(count($this->data) * 500);
	}
	
	
	private function save(){
		$_SESSION['cart'] = serialize($this->data);
	}
	
	
	/* HELPERS
	----------------------------------------------------------------------------- */
	
	
}
