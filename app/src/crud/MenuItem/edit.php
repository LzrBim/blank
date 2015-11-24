<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Menu/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($menu->_id, $menu->getId());

/* SECTION 1 - MENU ORDER VIA NESTABLE */

$title = 'Menu Order';

$content = '';

include(MODEL_PATH.'Tree.php');
$mptt = new Tree(); 



/*$node = $mptt->add(0, 'Main');
$mptt->add($node, 'Child 1');
$child = $mptt->add($node, 'Child 2');
	$mptt->add($child, 'Child 2-1');
	$mptt->add($child, 'Child 2-2');
$mptt->add($node, 'Child 3');
$mptt->add($node, 'Child 4');*/


function nestable_to_insert($tree, $arr, $id = 0){
	
	foreach($arr as $key => $obj){
		
		$nodeID = $tree->add($id, 'Child - '.$obj->id);
		
		if(isset($obj->children)){
			nestable_to_insert($tree ,$obj->children, $nodeID);
		}
	}
}


$json = '[{"id":5,"children":[{"id":6},{"id":7}]},{"id":8},{"id":9,"children":[{"id":10}]}]';
$arr = json_decode($json);

//echo '<pre>';
//print_r($arr);
//echo '</pre>';

$mptt->add(0, 'Main Menu');
nestable_to_insert($mptt, $arr);

echo $mptt->to_list(0, 'ul');

/*echo '<pre>';
 print_r($mptt->get_tree(0));
echo '</pre>';*/

$mptt->array_to_list($mptt->get_tree(0));

ob_start(); 






/* SORTABLE LIST */ ?>

<div class="row">
	<div class="col-md-9">
	
  <input type="hidden" id="nestableInput" name="tree" value="" />  
  
  <div class="dd" id="nestable">
    <ol class="dd-list">
      <li class="dd-item" data-id="5">
      	<div class="dd-handle">Item 5 </div>
      	<ol class="dd-list">
      		<li class="dd-item" data-id="6">
          	<div class="dd-handle">Item 6</div>
          </li>
      		<li class="dd-item" data-id="7"><div class="dd-handle">Item 7</div></li>
      		<li class="dd-item" data-id="8"><div class="dd-handle">Item 8</div></li>
      	</ol>
      </li>
      <li class="dd-item" data-id="9"><div class="dd-handle">Item 9</div></li>
      <li class="dd-item" data-id="10"><div class="dd-handle">Item 10</div></li>
    </ol>
  </div>

  </div><!-- /.col -->
  <div class="col-md-3">
  
  	<a data-toggle="modal" href="#menuItemModal" class="btn btn-success pull-right">Add Menu Item</a>
    
  </div>
</div><?  

$content = ob_get_clean();

$adminView->box($title, $content);

/* SECOND SECTION FOR MENU ATTRIBUTES */

$title = 'Edit Menu Attributes';

$content = '';

ob_start();

echo $form->status($menu->status);  

echo $form->input('title', 'Title', $menu->title, array(
  'required' => true )
); 

$content = ob_get_clean();

$adminView->box($title, $content, $opts = array(
	'collapsed' => true																									 
));

echo $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/MenuItem/modal_add.php');