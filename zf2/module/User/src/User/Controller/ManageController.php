<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

use User\Document\User;

class ManageController extends AbstractActionController
{

 	public function signupAction() {
 		$post = $this->getRequest()->getPost()->toArray();
		
		$user = new User($post);
		$this->userService->save($user);
		
		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => array("user_id" => $user->getId()),
			'error' => $error,
			'error_message' => $errorMessage
		);
		
		return new JsonModel($result);
	}
	
}
