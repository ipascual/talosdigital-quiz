<?php

namespace User\Validator;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

//Documents
use User\Document\User\User;

class UserValidator {
    
    public function __construct($sm) {
        //User identity
	    if($sm->get('zfcuser_auth_service')->hasIdentity()) {
	        $this->user = $sm->get('zfcuser_auth_service')->getIdentity();
	    }
        
        //Service
        //...
        
        //Helper
        //...
    }
    
    /**
     * Check if the current user can do the functionality required on an object.
     * 
     * @param $functionality the action required
     * @param $object target object
     * 
     * @return true || Exception
     */
    public function isAllow($functionality, $object = null)
    {
        switch ($functionality) {
            case "manage_picture":
                if($this->user->getId() == $object->getId()) {
                    return true;
                }
                break;
        }
        throw new \Exception("There was a problem with your submission");
    }   
    
}
