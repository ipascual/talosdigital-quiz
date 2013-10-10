<?php

namespace User\Document\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Validation extends Document
{
    /** @ODM\Date */
    protected $validated_at;

    /** @ODM\String */
    protected $code;

    /** @ODM\String */
    protected $status;

    /** @ODM\Increment */
    protected $try;

	/*
	 *  Email Verification
	 */
	
    /** @ODM\String */
    protected $email;
	
	/*
	 * Telephone Verification
	 */

    /** @ODM\String */
    protected $phone_country;
	
    /** @ODM\String */
    protected $phone_number;
	
    /** @ODM\String */
    protected $phone_call_id;


	/** ================================================================== **/
	
	/** @ODM\PrePersist */
    public function prePersist()
    {
    }

	/** @ODM\PreUpdate */
    public function preUpdate()
    {
    }


	/** ================================================================== **/


}
