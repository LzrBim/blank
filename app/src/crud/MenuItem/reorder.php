<?
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/crud/Menu/edit.php
----------------------------------------------------------------------------- */ 

$form = new \App\Lib\AdminForm();

echo $form->open();
echo $form->hidden('mode', 'update');
echo $form->hidden($menu->_id, $menu->getId());
echo $form->hidden('menuTreeID', $menu->menuTreeID);

/* SECTION 1 - MENU ORDER VIA NESTABLE */

$title = 'Menu Order';

$content = '';

/*
$json = '[{"id":5,"children":[{"id":6},{"id":7}]},{"id":8},{"id":9,"children":[{"id":10}]}]';
$arr = json_decode($json);
$mptt->add(0, 'Main Menu');
nestable_to_insert($mptt, $arr);
echo $mptt->to_list(0, 'ul');
$mptt->array_to_list($mptt->get_tree(0));*/

/*$tree = new MenuTree(); 
$node = $tree->add(0, 0);
$tree->add($node,1);
$tree->add($node,2);
$child = $tree->add($node,3);
$tree->add($child,4);
$tree->add($node,4);
$arr = $tree->get_tree($menu->menuTreeID);

die();
*/

$tree = $menu->menuTree->get_tree($menu->menuTreeID);

ob_start(); 

/* SORTABLE LIST */ ?>

<div class="row">
	
  <div class="col-md-9">
  <input type="hidden" id="nestableInput" name="tree" value="" /><?
	
	if(!empty($tree)){ ?>
  
    <div class="dd" id="nestable"><? 
    	echo $menu->array_to_nestable($tree); ?>
    </div><?
		
	} else { ?>
  
		<p id="noResults" class="alert alert-warning">No results found.  Click Add Menu Item on the right to get started.</p>
    
    <script>
		setTimeout(function() {
			$("#noResults").fadeOut(600);
		}, 5000);
		</script>
    
		
		<div class="dd" id="nestable">
    	<ol class="dd-list">
      
      </ol>
    </div>
		<?
	} ?>
  
	<br />
  </div><!-- /.col -->
  <div class="col-md-3">
  
  	<a data-toggle="modal" href="#menuItemModal" class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i> Add Menu Item</a>
    
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

echo  $form->buttonsEdit();

echo $form->close();

include(APP_PATH.'crud/MenuItem/modal_add.php');