<?php
namespace Quiz\Service;

use MyZend\Service\Service as Service;

class ResultService extends Service {
	
	protected $document = "Quiz\Document\Result";
	
	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
		
	}
	

}
