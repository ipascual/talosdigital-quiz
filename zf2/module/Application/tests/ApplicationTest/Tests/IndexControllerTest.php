<?php
namespace ApplicationTest\Tests;

use Zend\Stdlib\Parameters;

use ApplicationTest\AbstractControllerZendtest;

use User\Service\UserService;
use Report\Service\CrService;
use Report\Service\PlrService;

class IndexControllerTest extends AbstractControllerZendtest
{

	public function setup() {
 		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
		$this->crService = new CrService($this->getServiceManager());
		$this->plrService = new PlrService($this->getServiceManager());
	}
	
	/*
	 *  Home page loads
	 */
	
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
    }
	
	/*
	   Contact message send
	   Full Name: Test
       Email: qa@talosdigital.com
       Subject: Test
       Message: Test
	 */
	public function testContactMessageSend(){
		$this->dispatch('/contact', 'POST', array(
			"name" => "Test",
			"email" => "qa@talosdigital.com",
			"message" => "Test",
			"subject" => "Test"
		));
		
		$this->assertResponseStatusCode(200);
        $this->assertModuleName('cms');
        $this->assertControllerName('Cms\Controller\Contact');
        $this->assertControllerClass('ContactController');
		
		/*
		 * verify Ajax returns
		 * 
		 */
	}
	
	/*
	 * Support page loads
	 * 
	 */
	public function testSupportPageCanBeAccessed(){
		$this->dispatch('/support');

        $this->assertResponseStatusCode(200);
	    $this->assertModuleName('cms');
        $this->assertControllerName('Cms\Controller\Index');
        $this->assertControllerClass('IndexController');
    }
	
	/*
	 * Support page loads
	 * 
	 */
	
	public function testLoginPageCanBeAccessed(){
		$this->dispatch('/auth/login');
		
	    $this->assertResponseStatusCode(200);
	}
	
	/*
	 * Signup page loads
	 */
	public function testSignUpCreateUser(){
		$count = $this->userService->findAll()->count();
		$this->dispatch('/auth/login', 'POST', array(
			'formAction' => 'formSignup',
			'firstname' => 'Test',
			'lastname' => '520',
			'email' => 'test520@test.com',
			'password' => '123456'
		));
		
		$createdUser = $this->userService->findOneBy(array('email' => 'test520@test.com'));
		$this->assertEquals("Test 520", $createdUser->getFullName());
		$this->assertEquals("test520@test.com", $createdUser->getEmail());
		$this->assertCount($count + 1, $this->userService->findAll()); 
	}

	
	public function testUpdateProfileAccount(){
		$count = $this->userService->findAll()->count();
		$this->loginByEmail("tkachenko.vitaly.job@gmail.com");
		$this->dispatch('/user/profile/myaccount', 'POST', array(
			'firstname' => 'Test',
			'lastname' => '520',
			'company_name' => 'Talos Digital',
			'company_address' => '944 5th Avenue',
			'company_city' => 'New York',
			'company_state' => 'New York',
			'company_name' => 'Talos Digital',
			'company_postal_code' => '',
			'company_country' => 'United States',
			'phonenumber' => '123456789',
			'email' => 'tkachenko.vitaly.job@gmail.com'
		));
		$this->assertResponseStatusCode(200);
		
		$this->userService->flush();
		$createdUser = $this->userService->findOneBy(array('email' => 'tkachenko.vitaly.job@gmail.com'));

		$this->assertEquals("Test 520", $createdUser->getFullName());
		$this->assertEquals("tkachenko.vitaly.job@gmail.com", $createdUser->getEmail());
		$this->assertCount($count, $this->userService->findAll()); 
	}
	
	public function testModifySettings(){
		$count = $this->userService->findAll()->count();
		$this->loginByEmail("tkachenko.vitaly.job@gmail.com");
		$this->dispatch('/user/profile/settings','POST', array(
			'currency' => 'EUR',
			'wacc' => 15,
			'area_unit' => 'sqf'
		));
		$this->userService->flush();
		$createdUser = $this->userService->findOneBy(array('id' => '51f6d2b18f604cee0a000000'));
		$this->assertEquals("EUR", $createdUser->getCurrency());
		$this->assertEquals("15", $createdUser->getWacc());
		$this->assertEquals("sqf", $createdUser->getAreaUnit());
		$this->assertCount($count, $this->userService->findAll()); 
	}
	
	public function testSharePlr(){
		$count = $this->userService->findAll()->count();
		$this->dispatch('/r/plr/5186fc808f604c7915000001');
		$this->assertResponseStatusCode(302);
	}
	
	public function testCreatePlr(){
		$count = $this->plrService->findAll()->count();
		$this->loginByEmail("user@tester.com");
		
		$this->dispatch('/report/plrwizard/index','GET', array());
		$this->assertResponseStatusCode(302);
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
	//	$this->assertResponseStatusCode(200);
		$url = parse_url($headers['Location']);
		parse_str($url["query"], $params);
		$plr_id = $params["plr_id"];
		
		$plr = $this->plrService->findOneBy(array('id' => $plr_id));
		$this->assertNull($plr);
		
		$data1 = array(
			"step" => 1,
			"plr_id" => $plr_id,
			"name" => "Test_Prop",
			"building_name" => "Test_Building",
			"building_type" => "commercial",
			"building_grade" => "premium",
			"geolocation" => "30 George Street, Sydney, New South Wales, Australia",
			"address" => "30 George Street",
			"city" => "Sydney",
			"country" => "Australia",
			"state" => "New South Wales",
			"post_code" => "2000",
			"type" => "tr"
		);
		
		$this->dispatch($headers['Location'],'GET', array());
		$this->assertResponseStatusCode(302);
		$this->dispatch('/report/plrwizard-api/wizard','POST', $data1);
		
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
		
		$this->assertResponseStatusCode(302);
		$this->plrService->flush();

		$plr = $this->plrService->findOneBy(array('id' => $plr_id));
		$this->assertNotNull($plr);
		$this->assertEquals("Test_Prop", $plr->getName());
		$this->assertNotNull($plr->getProperty());
		$this->assertNotNull($plr->getProperty()->getGeolocation());
		$this->assertCount($count + 1, $this->plrService->findAll()); 
		
		$data2 = array(
				'step' => 2,
				"plr_id" => $plr_id,
				'premises' => '419',
				'area' => '56',
				'area_unit' => 'sqm',
				'commencing_base_rental' => '500',
				'lease_commencement_date' => '2014-01-14',
				'lease_term' => 10,
				'lease_term_period' => 'years',
				'recovery_type' => 'net',
				'outgoings' => array (
    							'0' => 
    								array (
     								 'label' => 'Land tax',
      								 'amount' => '20',
      							  	 'growth' => '5',
    								),
    							'1' => 
    								array (
      								'label' => 'Some other tax',
      								'amount' => '50',
      								'growth' => '7',
    								),
  				),
				'direct_recoveries' => '20',
				'expense_growth_assumption' => '1',
				'review' => 
  								array (
    								'frequency' => 12,
    								'type' => "fixed",
    								'amount' => 5,
    								'margin' => 0,
  								),
		);
		
		
		
		$this->dispatch('/report/plrwizard-api/wizard','POST', $data2);
		$this->plrService->flush();
		
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
		$this->assertResponseStatusCode(302);
		
		
		$plr = $this->plrService->findOneBy(array('id' => $plr_id));
		$this->assertNotNull($plr);
		$this->assertEquals("Test_Prop", $plr->getName());
		$this->assertNotNull($plr->getLeaseOutgoings());
		$this->assertNotNull($plr->getRentalReview());
		$this->assertCount($count + 1, $this->plrService->findAll()); 
		
		$data3 = array(
				'step' => 3,
				"plr_id" => $plr_id,
				'other_charges' => 
  								array (
    								'0' => 
    									array (
      										'label' => 'Parking',
										    'number' => '55',
										    'amount' => '25',
										    'review' => 
      											array (
        											'frequency' => 12,
											        'type' => "fixed",
											        'amount' => 5,
											        'margin' => 0,
      											),
    									),
  								),
			 	'incentives' => 
  							array (
    							'0' => 
								    array (
								      'active' => '1',
								      'data' => array(
								      		'0' => array(
										      'type' => 'abatement',
										      'label' => 'Rental Abatement',
										      'start_date' => '2014-01-14',
										      'end_date' => '2014-02-14',
										      'amount' => '500',
								      		),
								      	),
								    ),
    							'1' => 
								    array (
								      'active' => '1',
								       'data' => array(
								       		'0' => array(
										      'type' => 'cash',
										      'label' => 'Cash Contribution',
										      'start_date' => '2014-01-14',
										      'amount' => '3500',
								       		),
								    	),
								    ),
    							'2' => 
								    array (
								      'active' => '1',
								      'data' => array(
								      		'0' => array(
										      'type' => 'rent_free',
										      'label' => 'Rent Free',
										      'amount' => '3',
								      		),
									    ),
								    ),
  							), 

		);

			
		$this->dispatch('/report/plrwizard-api/wizard','POST', $data3);
		$this->plrService->flush();
		
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
		$this->assertResponseStatusCode(302);
		
		$plr = $this->plrService->findOneBy(array('id' => $plr_id));
		$this->assertNotNull($plr);
		$this->assertEquals("Test_Prop", $plr->getName());
		
		$this->assertNotNull($plr->getOtherCharges());
		$this->assertNotNull($plr->getIncentives());
		$this->assertCount($count + 1, $this->plrService->findAll()); 
		
		$data4 = array(
				"step" => 4,
				"plr_id" => $plr_id,
				"agency" => array(
						"agent_full_name" => "Test Inc",
						"name" => "Test",
						"email" => "test@test.com",
						"phone" => "12345",
						"geolocation" => "",
						"address" => "",
						"city" => "",
						"country" => "",
						"state" => "",
						"post_code" => ""
				)
					
		);
		
		$this->dispatch('/report/plrwizard-api/wizard','POST', $data4);
		$this->plrService->flush();
		
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
		$this->assertResponseStatusCode(302);
		
		$plr = $this->plrService->findOneBy(array('id' => $plr_id));
		$this->assertNotNull($plr);
		$this->assertEquals("Test_Prop", $plr->getName());
		
		$this->assertNotNull($plr->getProperty());
		$this->assertNotNull($plr->getProperty()->getAgency());
		$this->assertCount($count + 1, $this->plrService->findAll());
		
	}	
		
	public function testCreateCr(){
		$count = $this->crService->findAll()->count();
		$this->loginByEmail("ignacio@bcn.com");
		$this->dispatch('/report/crmanage/edit','GET', array());
	/*	$this->assertResponseStatusCode(302);
		$headers = $this->getResponse()->getHeaders()->toArray();
		$this->dispatch($headers['Location']);
		$cr_id = explode("=",$headers['Location']);
		$cr_id = $cr_id[1];
		$this->dispatch('/report/crmanage/edit','POST', array(
			'cr_id' => $cr_id,
			'name' => 'Test CR',
			'plrs' => array("518547818f604cd30c000004", "5186fc808f604c7915000000", "518d93448f604cfb17000000"),
		));
		$this->crService->flush();
		$this->assertCount($count + 1, $this->crService->findAll()); */
	}
}