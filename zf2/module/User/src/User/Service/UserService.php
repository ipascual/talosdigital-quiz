<?php

namespace User\Service;

use MyZend\Service\Service as Service;

use User\Document\User;
use User\Document\User\Address;
use User\Document\User\Phonenumber;
use User\Document\User\Facebook;
use User\Document\User\Validation;
use Media\Document\Picture;

class UserService extends Service {
	
	protected $document = "User\Document\User";
	
	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
	}
	
	/**
	 * Update login stadisticts of User
	 * 
	 * @param $user Document to update
	 * @return $user updated
	 */
	public function login($user) {
		//Save login
		$user->incLogins();
		$user->setLastAccess(new \DateTime());
		
		//Remove alerts older than a month
		$now = new \DateTime();
		foreach($user->getAlerts() as $alert) {
			$interval = $now->diff($alert->getTimestamp());
			$days = (int)$interval->format('%a');
			if($days > 30) {
				$user->getAlerts()->removeElement($alert);
			}	
		}
		$this->save($user);
		
		//Save		
		$this->save($user);
		
		return $user;
	}
	
	/**
	 * Find a user by Facebook Id
	 * Authentication System
	 * 
	 * @param $id String Facebook Id
	 */
	public function findByFacebookId($id) {
		$filter = array("facebook.facebook_id" => $id);
		return $this->findOneBy($filter);	
	}

	/**
	 * Find a user by Email
	 * Authentication System
	 * 
	 * @param $id String Email
	 */
	public function findByEmail($email) {
		$filter = array("email" => $email);
		return $this->findOneBy($filter);	
	}

	/**
	 * Find a user by Id
	 * Authentication System
	 * 
	 * @param $id MongoId
	 */	
    public function findById($id)
    {
		$filter = array("id" => $id);
		return $this->findOneBy($filter);
    }	

	public function searchBy($query, $filter = array(), $limit = null, $start = null, $sort = null, $dir = null) {
		$qb = $this->dm->createQueryBuilder($this->document);

		$qb->addOr($qb->expr()->field("_id")->equals(new \MongoId($query)));
		$qb->addOr($qb->expr()->field("full_name")->equals(new \MongoRegex("/". $query ."/i")));
		$qb->addOr($qb->expr()->field("firstname")->equals(new \MongoRegex("/". $query ."/i")));
		$qb->addOr($qb->expr()->field("lastname")->equals(new \MongoRegex("/". $query ."/i")));
		$qb->addOr($qb->expr()->field("email")->equals(new \MongoRegex("/". $query ."/i")));

		foreach($filter as $filt => $value) {
			$qb->addAnd($qb->expr()->field($filt)->equals($value));
		}
		
		if($limit != null) {
			$qb->limit($limit);
		}

		if($start != null) {
			$qb->skip($start);
		}
		
		if($sort != null) {
			if($dir == null) {
				$dir = "ASC";
			}
			$qb->sort($sort, $dir);
		}
		
		return $qb->getQuery()->execute();
	}
	
}
