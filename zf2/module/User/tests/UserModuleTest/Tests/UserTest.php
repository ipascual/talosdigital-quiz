<?php
namespace UserModuleTest\Tests;

use UserModuleTest\AbstractTestCase;

use User\Document\User;
use User\Document\User\Address;
use User\Document\User\Phonenumber;
use User\Document\User\Seller;
use User\Document\User\Facebook;
use User\Document\User\Validation;
use Media\Document\Picture;
use Geolocation\Document\Geolocation;
use Geolocation\Document\Country;

use User\Helper\UserHelper;
use MyZend\Helper\UrlHelper;
use User\Service\UserService;
use Geolocation\Service\GeolocationService;

class UserTest extends AbstractTestCase {
	
	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
		$this->userHelper = new UserHelper($this->getServiceManager());
		$this->geolocationService = new GeolocationService($this->getServiceManager());
		$this->urlHelper = new UrlHelper($this->getServiceManager());
	}

	/**
	 * Create User
	 */
	public function testCreateUser() {
		$users = $this->userService->findAll();
		$countStart = count($users);

		//Data
		$data = array();
		/*
		 $data["address"]["street"] = '42 Wyatt Rd.';
		 $data["address"]["city"] = 'London';
		 $data["address"]["state"] = 'Greater London';
		 $data["address"]["postal_code"] = 'SW2 2LL';

		 //Phone
		 $data["phonenumber"]["phonenumber"] = "3103733333";

		 //Picture
		 $data["picture"]["picture"] = "london_picture.jpeg";
		 $data["picture"]["label"] = "My profile picture";

		 //Facebook
		 $data["facebook"]["username"x] = "ernacho";
		 $data["facebook"]["email"] = "ernacho@facebook.com";
		 */
		//User attributes
		$data["full_name"] = "Ignacio Pascual";
		$data["email"] = "ipascual@example.com";
		$data["password"] = "123456";
		$data["gender"] = "male";
		$data["locale"] = "en_US";
		$data["languages"] = "Spanish, English";
		//User Service
		$this->userHelper->createUser($data);

		//Assert
		$this->assertCount($countStart + 1, $this->userService->findAll());
		$user = $this->userService->findOneBy(array("email"=>$data["email"]));
		$this->assertEquals($data["email"], $user->getEmail());
		$this->assertEquals($data["full_name"], $user->getFullName());
		//$this->assertEquals($data["facebook"]["username"], $user->getFacebook()->getUsername());
		//$this->assertEquals($data["picture"]["picture"], $user->getPicture()->getPicture());
	}

	/**
	 * User can change password
	 */
	public function testUserChangePwd() {
		//get var user-id
		$data["id"] = "50116a08724f9a2a0a000000";
		$data["pwd"] = "999999";
		//get user
		$filter = array("id"=>$data["id"]);

		//search the user
		$user = $this->userService->findOneBy($filter);

		//update values
		$user->setPassword($data["pwd"]);

		//save user
		$this->userService->save($user);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($data["pwd"], $user->getPassword());
	}

	/**
	 * User change about me
	 */
	public function testUserChangeAbout() {
		//get var user-id
		$data["id"] = "50116983724f9a280a000000";
		$data["about"] = "bla bla bla bla ..........";
		//get user
		$filter = array("id"=>$data["id"]);

		//search the user
		$user = $this->userService->findOneBy($filter);

		//update values
		$user->setAboutMe($data["about"]);

		//save user
		$this->userService->save($user);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($data["id"], $user->getId());
	}

	/**
	 * User change geolocation
	 */
	public function testUserChangeGeolocation() {
		//get user
		$id = "50116983724f9a280a000000";
		$filter = array("id" => $id);

		//search the user
		$user = $this->userService->findOneBy($filter);
		//update values
		$a = $user->getAddresses()->get(0);

		//save user
		$this->userService->save($user);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($id, $user->getId());
	}

	/**
	 * User change email
	 */
	public function testUserChangeEmail() {

		//get var user-id
		$data["id"] = "50c2668f8f604cbf0a000000";
		$data["email"] = "newemail@test.com";

		//get user
		$filter = array("id"=>$data["id"]);

		//search the user
		$user = $this->userService->findOneBy($filter);
		$email = $user->getEmail();

		//update values
		$user->setEmail($data["email"]);
		//save user
		$this->userService->save($user);
		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($filter["id"], $user->getId());

	}

	/**
	 * User can change Birthday
	 */
	public function testChangeBirthday() {
		//get var id
		$data["id"] = "50116a08724f9a2a0a000000";

		//get user
		$filter = array("id"=>$data["id"]);

		//search the user
		$user = $this->userService->findOneBy($filter);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($filter["id"], $user->getId());
	}

	/**
	 * Load user by email
	 */
	public function testLoadUserByEmail() {
		//get the email
		$filter = array("email"=>"cmontoya@example.com");
		//search the user
		$user = $this->userService->findOneBy($filter);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($filter["email"], $user->getEmail());
	}

	/**
	 * Load user by facebook email
	 */
	public function testLoadUserByFacebookEmail() {
		$filter = array("facebook.email"=>"rebecap@facebook.com");

		$user = $this->userService->findOneBy($filter);

		//one or more results
		$this->assertNotEquals(0, count($user));

		//the result have to match
		$this->assertEquals($filter["facebook.email"], $user->getFacebook()->getEmail());
	}

	/**
	 * Add a Phonenumber to a User
	 */
	public function testAddPhonenumber() {
		//Load User
		$filter = array("email"=>"rebecap@fincaelmanantial.com");
		$user = $this->userService->findOneBy($filter);

		//Add a new phone
		$data["phonenumber"] = "310001111111";
		$phone = new Phonenumber($data);
		$user->getPhonenumbers()->add($phone);

		//Add a new phone
		$data["phonenumber"] = "310002222222";
		$phone = new Phonenumber($data);
		$user->getPhonenumbers()->add($phone);

		$this->userService->save($user);

		//Assert
		$user = $this->userService->findOneBy($filter);
		$this->assertCount(3, $user->getPhonenumbers());
		$this->assertEquals($data["phonenumber"], $user->getPhonenumbers()->get(2)->getPhonenumber());
	}

	/**
	 * Delete a Phonenumber to a User
	 */
	public function testDeletePhonenumber() {
		//Load User
		$filter = array("email"=>"rebecap@fincaelmanantial.com");
		$user = $this->userService->findOneBy($filter);

		//Add a new phone
		$phoneIndex = 0;
		$phone = $user->getPhonenumbers()->get($phoneIndex);
		$user->getPhonenumbers()->removeElement($phone);
		$this->userService->save($user);

		//Assert
		$user = $this->userService->findOneBy($filter);
		$this->assertCount(0, $user->getPhonenumbers());
		$this->assertNull($user->getPhonenumbers()->get($phoneIndex));
	}

	/**
	 * Update User, test1
	 * Update Main data and Facebook username
	 */
	public function testUpdateUser1() {
		//Assert
		$users = $this->userService->findAll();
		$countStart = count($users);

		//Load User
		$filter = array("email"=>"cmontoya@example.com");
		$user = $this->userService->findOneBy($filter);

		//Update Main data and Facebook
		$user->setFullName("Ignacio Pascual - new");
		$user->getFacebook()->setUsername("ipascual-new");
		$this->userService->save($user);

		//Assert
		$this->assertCount($countStart, $this->userService->findAll());

		$user = $this->userService->findOneBy($filter);
		$this->assertEquals($filter["email"], $user->getEmail());
		$this->assertEquals("Ignacio Pascual - new", $user->getFullName());
		$this->assertEquals("ipascual-new", $user->getFacebook()->getUsername());
	}

	/**
	 * Update User, test2
	 * Update Profile Picture
	 */
	public function testUpdateUser2() {
		//Assert
		$countStart = $this->userService->findAll()->count();

		//Load User
		$user = $this->userService->findOneBy(array("email"=>"cmontoya@example.com"));

		$newData["label"] = "My NEW profile picture";

		//Update Picture Label
		$picture = $user->getPicture();
		if (!$picture) {
			$user->setPicture(new Picture($newData));
		}
		$picture->setLabel($newData["label"]);
		$this->userService->save($user);

		//Assert
		$this->assertCount($countStart, $this->userService->findAll());
		$user = $this->userService->findOneBy(array("email"=>"cmontoya@example.com"));
		$this->assertEquals($newData["label"], $user->getPicture()->getLabel());
	}

	/**
	 * Update User, test3
	 * Update Seller Details
	 */
	public function testUpdateUser3() {
		//Assert
		$users = $this->userService->findAll();
		$countStart = count($users);

		//Load User
		$filter = array("email"=>"cmontoya@example.com");
		$user = $this->userService->findOneBy($filter);

		//Update Seller
		if ($user->getSeller() == null) {
			$user->setSeller(new Seller());
		}
		$user->getSeller()->setPrice1(30);
		$user->getSeller()->setPrice2(60);
		$user->getSeller()->setPrice3(90);
		$this->userService->save($user);

		//Assert
		$this->assertCount($countStart, $this->userService->findAll());

		$user = $this->userService->findOneBy($filter);
		$this->assertEquals(30, $user->getSeller()->getPrice1());
		$this->assertEquals(60, $user->getSeller()->getPrice2());
		$this->assertEquals(90, $user->getSeller()->getPrice3());
	}

	/**
	 * Validate Email User
	 */
	public function testValidateEmailUser() {
		//Load User
		$filter = array("email"=>"cmontoya@example.com");
		$user = $this->userService->findOneBy($filter);

		//Assert
		$this->assertCount(0, $user->getValidation());

		//Validate
		$data["status"] = "verified";
		$data["code"] = "11111111111111111";
		$data["validated_at"] = new \DateTime();
		$validation = new Validation($data);

		$user->getValidation()->set("email", $validation);
		$this->userService->save($user);

		//Assert
		$user = $this->userService->findOneBy($filter);
		$this->assertNotEmpty($user->getValidation()->get("email"));
		$this->assertEquals($data["code"], $user->getValidation()->get("email")->getCode());
		$this->assertTrue($user->isValidated("email"));
	}

	/**
	 * User can Verify facebook
	 public function testVerifyFacebookUser() {

	 //Load User
	 $filter = array("email"=>"ignacio@bcnciudadmaravilla.com");
	 $user = $this->userService->findOneBy(array("email"=>$filter));

	 //Assert
	 $this->assertEquals(0, count($user));

	 //Validate
	 $data["status"] = "verified";
	 $data["code"] = "777777";
	 $data["validated_at"] = new \DateTime();
	 //$validation = new Validation();
	 //$user->getValidation();
	 //	d($user->getValidation());
	 //$validation->getset("facebook",$data);
	 //save user
	 //		$this->userHelper->createUser($data);
	 $this->userService->set($user);

	 //Assert
	 $this->assertNotEmpty($user->getValidation()->get("email"));
	 $this->assertEquals($data["code"], $user->getValidation()->get("email")->getCode());
	 $this->assertTrue($user->isValidated("email"));
	 }
	 */

	 public function testSaveBillingAddress() {
		
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a000000"));
	 	$geolocation = $this->geolocationService->findOneBy(array("id" => "51eff5c08f604c8019000001"));
	 	
		$address = new Address();
		$address->setFirstname('Test');
		$address->setLastname('Test');
		$address->setCompanyName('Test Inc.');
		$address->setStreet("Test Street, 20");
		$address->setPostCode('23432');
		$address->setGeolocation($geolocation);
		
	 	$user->setAddress("Billing", $address);
	 	$this->userService->save($user);
	 	$this->assertNotNull($user->getAddress("Billing"));
	 }
	 
	 public function testLoadBillingAddress() {
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a800000"));
	 	$user2 = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a000000"));
	 	
	 	$this->assertNotNull($user->getAddress("Billing"));
	 	$this->assertNull($user2->getAddress("Billing"));
	 }
	 
	 public function testReplaceHomeAddress() {
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a800000"));
	 	$geolocation = $this->geolocationService->findOneBy(array("id" => "51eff5c08f604c8019000001"));
	 	
	 	$newAddress = new Address();
	 	$newAddress->setFirstname('Test');
	 	$newAddress->setLastname('Test');
	 	$newAddress->setCompanyName('Test Inc.');
	 	$newAddress->setStreet("Test Street, 20");
	 	$newAddress->setPostCode('23432');
	 	$newAddress->setGeolocation($geolocation);
	 	
	 	$addressBefore = $user->getAddress("Home");
	 	$this->assertNotNull($addressBefore);
	 	
		$user->setAddress("Home", $newAddress);
		$this->userService->save($user);

		$addressAfter = $user->getAddress("Home");
		$this->assertNotNull($addressAfter);
		
		$this->assertNotEquals($addressBefore, $addressAfter);
	 }
	 
	 public function testSetSettings() {
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a800000"));
		
		$data = array("a@a.com", "b@b.com", "c@c.om");
		$user->setSettings("sales/order_recipient_emails", $data);
		$this->userService->save($user);
		$this->assertNotNull($user->getSettings("sales/order_recipient_emails"));
		$this->assertEquals($data, $user->getSettings("sales/order_recipient_emails"));
	 }

	 public function testSetSettings2() {
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a800000"));
		
		$user->setSettings("sales/notify_new_orders", true);
		$this->userService->save($user);

		$this->assertNotNull($user->getSettings("sales/notify_new_orders"));
		$this->assertEquals(true, $user->getSettings("sales/notify_new_orders"));
	 }
	 
	 public function testGetSettings() {
	 	$user = $this->userService->findOneBy(array("id" => "50c266838f604cbb0a800000"));
		
		$notify = $user->getSettings("sales/notify_new_payment");

		$this->assertEquals(true, $user->getSettings("sales/notify_new_payment"));
	 }
	 	 
}
