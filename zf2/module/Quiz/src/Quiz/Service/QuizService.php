<?php

namespace Quiz\Service;

use MyZend\Service\Service as Service;

class QuizService extends Service {
	
	protected $document = "Quiz\Document\Quiz";
	
	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
		
	}
	

}
