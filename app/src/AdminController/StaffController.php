<?php
/*-----------------------------------------------------------------------------
 * SITE:
 * FILE: /app/src/AdminController/StaffController.php
----------------------------------------------------------------------------- */
namespace App\AdminController;

use App\Model\Staff;


class StaffController extends BaseController {
	
	public function index($request, $response, $args){
	
		$this->logger->debug("Admin Staff Index");
		
		$staff = new Staff();
		
		$staffs = $staff->fetchAll();
        
		$this->view->render($response, 'admin/staff/index.twig', [
			'title' => 'Staff',
			'staffs' => $staffs,
			'jsStaff' => 'index',
			'jsOptions' => array(
				'model' => 'Staff'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function add($request, $response, $args){
	
		$this->logger->debug("Admin - Staff Add");		
		
		ob_start();
		
		include('../app/src/crud/Staff/add.php');
		
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'Staff',
			'form' => $form,
			'jsStaff' => 'add',
			'jsOptions' => array(
				'model' => 'Staff'
			)																			 
		]);	
		
		return $response;
	
	}
	
	
	public function insert($request, $response, $args){
	
		$this->logger->debug("Admin Staff Insert");
		
		$staff = new Staff();
		
		$staff->loadByData($request->getParsedBody());
		
		$staff->imageID = $this->uploadImage($staff, 'insert');
											
		if($staff->insert()){
			
			$this->logger->debug("Staff saved");
			$this->flash->addMessage('success', 'Staff saved');
			
			return $response->withRedirect('/admin/staff/edit/'.$staff->id());
			
		} else {
			
			$this->logger->debug("Error saving staff");
			$this->flash->addMessage('error', 'Error saving staff');
			
			return $response->withRedirect('/admin/staff/add');
			
		}
	
	}
	
	
	public function edit($request, $response, $args){
	
		$this->logger->debug("Admin Staff Edit");
		
		$staff = new Staff();
		$staff->load($args['id'])->with('*');
		ob_start();
		include('../app/src/crud/Staff/edit.php');
		$form = ob_get_clean();
        
		$this->view->render($response, 'admin/edit.twig', [
			'title' => 'Staffs',
			'form' => $form,
			'jsStaff' => 'edit',
			'jsOptions' => array(
				'model' => 'Staff'
			)																			 
		]);	
		
		return $response;
	
	}
	
	public function update($request, $response, $args){
	
		$this->logger->debug("Admin Staff Update");
		
		$staff = new Staff();
		$staff->loadByData($request->getParsedBody());
		
		$this->uploadImage($staff, 'update');
        
		if($staff->update()){
			
			$this->logger->debug("Staff updated successfully");
			$this->flash->addMessage('success', 'Staff saved');
			return $response->withRedirect('/admin/staff/edit/'.$staff->id());
			
		} else {
			
			$this->flash->addMessage('error', 'Error saving staff');
			return $response->withRedirect('/admin/staff/edit/'.$staff->id());
			
		}
		
	}
	
	
	public function delete($request, $response, $args){
	
		$this->logger->debug("Admin Staff Edit");
		
		$staff = new Staff();
		$staff->load($args['id']);
		$staff->delete();
		
		$this->flash->addMessage('success', 'Staff deleted');
		
		return $response->withRedirect('/admin/staff/index');
	
	}
	
	
}
