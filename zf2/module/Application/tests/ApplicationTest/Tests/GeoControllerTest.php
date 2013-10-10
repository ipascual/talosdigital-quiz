<?php
namespace ApplicationTest\Tests;

use ApplicationTest\AbstractTestCase;

use Geolocation\Service\GeolocationService;

class GeoControllerTest extends AbstractTestCase {
	
	public function setup() {
		parent::setup();
		
		$this->geolocationService = new GeolocationService($this->getServiceManager());
	}
	
	public function testRegionShortUpdateChangeAbout() {
		$geolocations = $this->geolocationService->findAll();
		$geolocations->count();
	}
}
