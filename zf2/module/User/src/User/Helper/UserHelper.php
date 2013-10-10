<?php
namespace User\Helper;

use Zend\Crypt\Password\Bcrypt;
use Ut\Service\Service as Service;

use User\Document\User;
use User\Document\User\Address;
use User\Document\User\Phonenumber;
use User\Document\User\Facebook;
use User\Document\User\Validation;
use Media\Document\Picture;

use User\Auth\AuthStorageDb;

use Geolocation\Helper\GeolocationHelper;

use User\Service\UserService;

class UserHelper {

	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
		$this->userService = new UserService($sm);
		$this->authStorageDb = new AuthStorageDb(); //need to encrypt password
		$this->authStorageDb->setServiceManager($sm);
	//	$this->facebook = $sm->get('facebook');
		$this->email = $sm->get('email');
		$this->geolocationHelper = new GeolocationHelper($sm);
	}
	
	/**
	 *
	 * Check if the Facebook User exits on the database.
	 * If not exists then it creates it and return it.
	 *
	 */
	public function facebookConnect($fbUser) {
		//is already register by facebook?
		$filter = array("facebook.facebook_id" => $fbUser["id"]);
		$user = $this->userService->findOneBy($filter);
		if(!$user) {
			//is already register by normal login
			$filter = array("email" => $fbUser["email"]);
			$user = $this->userService->findOneBy($filter);
		}

		//Create new
		if(!$user) {
			//Save new user
			$data["full_name"] = $fbUser["name"];
			$data["birthday"] = $fbUser["birthday"];
			$data["email"] = $fbUser["email"];

			//Save entity
			$user = $this->createUser($data);
		}

		//Update user
		$user->setFullName($fbUser["name"]);
		$user->setBirthday($fbUser["birthday"]);

		//Save Facebook data
		if(! $user->getFacebook()) {
			$data["facebook"]["username"] = $fbUser["username"];
			$data["facebook"]["facebook_id"] = $fbUser["id"];
			$data["facebook"]["email"] = $fbUser["email"];

			$fb = new Facebook($data["facebook"]);
			$user->setFacebook($fb);
		}
		
		//Save Facebook Validated
		if(! $user->getValidation()->get("facebook")) {
			//Set facebook validated
			$validated = new Validation();
			$validated->setValidatedAt(new \MongoDate());
			$validated->setStatus("verified");
			$user->getValidation()->set("facebook", $validated);
		}

		$this->userService->save($user);

		return $user;
	}

	/**
	 * Unlink Facebook Account
	 */
	 public function unlinkFacebook($user) {
	 	//Remove validation
		if($facebook = $user->getValidation()->get("facebook")) {
			//Set facebook validated
			$user->getValidation()->removeElement($facebook);
		}

		//Remove facebook credentials
		if($facebook = $user->getFacebook()) {
			$user->setFacebook(null);
		}

		//Save
		$this->userService->save($user);
	 }

	/**
	 * Create the user with default role.
	 */
	public function createUser($data) {
		//New User
		$user = new User($data);

		//Full name
		if(! isset($data["full_name"])) {
			$user->setFullName($data["firstname"] . " " .$data["lastname"]);
		}

		//Facebook
		if(isset($data["facebook"])) {
			$fb = new Facebook($data["facebook"]);
			$user->setFacebook($fb);
		}

		//Password
		$this->authStorageDb->setPassword($user);

		//Mail options
		$user->setMailOpt(true);

		//Set Role (required for security)
		$user->setRole("user");

		//Save
		$user = $this->userService->save($user);
		
		return $user;
	}

	/**
	 * Save Mandatory fields from web form
	 *
	 * @param $user User Document
	 * @param $post Form data
	 */
	 public function updateMandatoryFields($user, $post) {
	 	//Email
		$user->setEmail($post["email"]);
		//Birthday
		$user->setBirthday($post["birthday"]);
		//PhoneNumbers
		foreach($post["phonenumbers"] as $key => $phoneData) {
			$phoneNumber = $user->getPhonenumber($key);
			$isNew = false;

			//New phonenumber
			if(! $phoneNumber) {
				$isNew = true;
				$phoneNumber = new Phonenumber();
			}
			//Set phone data
			$phoneNumber->setOptions($phoneData);

			//Add
			if($isNew) {
				$user->addPhonenumber($phoneNumber);
			}
		}

		//Save
		return $this->userService->save($user);
	 }

	 /**
	  * Update user main address
	  */
	  public function updateAdress($user, $data) {
	  	$address = new Address($data);

		$user->getAddresses()->clear();
		$user->getAddresses()->add($address);

		return $this->userService->save($user);
	  }

	/**
	 * Create and send a verification email
	 */
	public function sendVerifyEmail($user) {
		$uuid = uniqid();

		//Validated Object
		$emailValidation = new Validation();
		if($user->getValidation()->get("email")) {
			$emailValidation = $user->getValidation()->get("email");
		}
		$emailValidation->setCode($uuid);
		$emailValidation->setStatus("pending");
		$emailValidation->setEmail($user->getEmail());
		$emailValidation->incTry(1);

		//Save to User
		$user->getValidation()->set("email", $emailValidation);
		$this->userService->save($user);

		//Send the verification email
		$params["userId"] = $user->getId();
		$params["code"] = $uuid;
		$params["method"] = "email";
		$vars["link"] = "/user/profile/verify?" . http_build_query($params);
		//send email
		$email = $this->email->create($vars);
		$email->setTemplateName("user/verify-email");
		$email->addTo($user);
		$this->email->send($email);
	}

	/**
	 * Create and send a verification email
	 */
	public function verifyEmail($userId, $code) {
		$user = $this->userService->findOneBy(array("id" => $userId));
		if ($linkCode = $user->getValidation()->get("email")->getCode()) {
			if (($user->getId() == $userId) && ($linkCode == $code)) {
				//Save details
				$validation = $user->getValidation()->get("email");
				$validation->setValidatedAt(new \MongoDate());
				$validation->setStatus("verified");
				$this->userService->save($user);
				//Verified message
				return true;
			}
		}
		//User does not have verfify code or it is wrong
		return false;
	}


	/**
	 * Create and send a forgot password email
	 */
	public function forgotPassword($user) {

		//Generaate and save new password
		$newPassword = $this->authStorageDb->getRandomPassword(8);
		$user->setPassword($newPassword);
		$user = $this->authStorageDb->setPassword($user);
		$this->userService->save($user);

		//Send the verification email
		$vars["password"] = $newPassword;
		$email = $this->email->create($vars);
		$email->setTemplateName("user/forgot-password");
		$email->addTo($user);
		$this->email->send($email);
	}

	public function managePicture($mode, $options, $file) {

		//Load entity
		$user = $this->userService->findOneBy(array("id" => $options["entity_id"]));

		//save
		if($mode == "save") {
			//Add picture
			$data["id"] = substr($file->name, 0, 24); //get the media file Id
			$data["picture"] = $file->name;
			$data["url"] = $file->url;
			$data["thumb_url"] = $file->thumbnail_url;
			$picture = new Picture($data);
			//Add to artist
			$user->setPicture($picture);
		}
		//delete
		if($mode == "delete") {
			//Delete picture
			$user->setPicture(null);
		}

		//Save changes
		$this->userService->save($user);
	}

	public function changePassword($user, $password, $newPassword) {
		if($this->authStorageDb->verifyPassword($user, $password)) {
			$user->setPassword($newPassword);
			$this->authStorageDb->setPassword($user);
			return true;
		}
		return false;
	}

	public function refreshStats($user)
	{
		$stats = $user->getStats();

		//Completed profile
		$ranking = 0;
		if($user->getBirthday()) {
			$ranking += 10;
		}
		if($user->getLocationFrom()) {
			$ranking += 10;
		}
		if($user->getLocationCurrent()) {
			$ranking += 10;
		}
		if($user->getGender()) {
			$ranking += 10;
		}
		if($user->getLanguages()) {
			$ranking += 10;
		}
		if($user->getAboutMe()) {
			$ranking += 10;
		}
		if($user->getAboutMe()) {
			$ranking += 10;
		}
		if($user->getTravelStyle()) {
			$ranking += 10;
		}

		$stats["profile_completed"] = number_format(($ranking * 100) / 80);

		$user->setStats($stats);

		$this->userService->save($user);
	}
	
	public function mapBillingDetails($data, $address = null) {
		// geolocation
		$location = "";
		if(isset($data['address'])){
			$location .= $data['address'];
		}	
		if(isset($data['city'])){
			$location .= "," .$data['city'];
		}	
		if(isset($data['state'])){
			$location .= "," .$data['state'];
		}
		if(isset($data['state_short'])){
			$location .= "," .$data['state_short'];
		}	
		if(isset($data['post_code'])){
			$location .= "," .$data['post_code'];
		}	
		if(isset($data['country'])){
			$location .= "," .$data['country'];
		}	
		
		if(! $address){
			$address = new Address();
		}
		
		if(isset($data["firstname"])){
			$address->setFirstname($data["firstname"]);
		}
		if(isset($data["lastname"])){
			$address->setLastname($data["lastname"]);
		}
		if(isset($data["company_name"])){
			$address->setCompanyName($data["company_name"]);
		}
		if(isset($data["address"])){
			$address->setStreet($data["address"]);
		}
		if(isset($data["post_code"])){
			$address->setPostCode($data["post_code"]);
		}

		// set geolocation up
		try {
			$geolocation = $this->geolocationHelper->lookupGeolocation($location);
			$address->setGeolocation($geolocation);
		}
		catch(\Exception $e) {
			$geolocation = null;
		}
		
		return $address;
	}
	
}
