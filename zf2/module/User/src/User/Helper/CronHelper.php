<?php

namespace User\Helper;

use Ut\Service\Service as Service;

use User\Document\User;
use User\Document\User\Seller;
use User\Document\User\Address;
use User\Document\User\Phonenumber;
use User\Document\User\Facebook;
use User\Document\User\Validation;
use Media\Document\Picture;

use User\Service\UserService;
use Destination\Service\DestinationService;

use Zend\Crypt\Password\Bcrypt;
use User\Auth\AuthStorageDb;

class CronHelper {

	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
		$this->userService = new UserService($sm);
		$this->destinationService = new DestinationService($sm);
		$this->facebook = $sm->get('facebook');
		$this->email = $sm->get('email');
	}

	public function run() {
		$this->sellerUrl();
		
	}
	
	public function sellerUrl() {
		$users = $this->userService->findAll();
		foreach($users as $user) {
			$filter = array("owner.id" => $user->getId());
			$destinations = $this->destinationService->findBy($filter);
			if(count($destinations) > 0) {
				$seller = $user->getSeller();
				if(! $seller) {
					$seller = new Seller();
				}
				if(! $seller->getUrl()) { 
			    	$url = $user->getFullName();
			    	$url = strtolower(cleanString(($url)));
			    	$url = str_replace(" ", "", $url);
			    	//Check unique
			    	$filter = array("seller.url" => $url);
			    	$userCheck = $this->userService->findOneBy($filter);
				    if($userCheck) {
				    	$url = $url.uniqid();
				    }
					//Save
					$seller->setUrl($url);
				    $user->setSeller($seller);
					$this->userService->save($user);
				}
			}
		}
	}	
	
}