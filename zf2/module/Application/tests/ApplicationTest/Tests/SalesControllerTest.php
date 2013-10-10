<?php
namespace ApplicationTest\Tests;

use Zend\Stdlib\Parameters;

use ApplicationTest\AbstractControllerZendtest;

use User\Service\UserService;
use Report\Service\CrService;
use Report\Service\PlrService;

class SalesControllerTest extends AbstractControllerZendtest
{

	public function setup()
	{
 		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
		$this->crService = new CrService($this->getServiceManager());
		$this->plrService = new PlrService($this->getServiceManager());
	}
	
    public function testLoadRecipientSettings()
    {
		$this->loginByEmail("ignacio@bcn.com");

		$this->dispatch('/sales/salesapi/load-recipients', 'GET');
		
		$this->assertResponseStatusCode(200);
		$jsonResponse = $this->getResponse()->getContent();
		$response = json_decode($jsonResponse);
		$this->assertEquals(false, $response->error);
    }
    
	// PHPUnit_Framework_Exception: PHP Fatal error:  Uncaught exception 'Exception' with message 'Serialization of 'Closure' is not allowed' in -:49
	/*public function testSaveRecipientSettings() 
	{	
		$recipients = array(
			"recipients" => "a@a.com ,b@b.com, c@c.com"
		);
		
		$this->dispatch('/sales/salesapi/save-recipients', 'POST', $recipients);
		$this->assertResponseStatusCode(200);
		$jsonResponse = $this->getResponse()->getContent();
		$response = json_decode($jsonResponse);
		$this->assertEquals(false, $response->error);
		$this->assertEquals("Recipients saved.", $response->success_message);
	}*/
	
}