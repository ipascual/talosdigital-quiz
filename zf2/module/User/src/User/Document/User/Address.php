<?php

namespace User\Document\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Address extends Document
{
	public function __construct($data = null)
    {
		parent::__construct($data);
	}
	
	/** @ODM\String */
	protected $label;

	/** @ODM\String */
	protected $firstname;

	/** @ODM\String */
	protected $lastname;

	/** @ODM\String */
	protected $company_name;

    /** @ODM\String */
    protected $street;

    /** @ODM\String */
    protected $post_code;

    /** @ODM\ReferenceOne(targetDocument="Geolocation\Document\Geolocation") */
    protected $geolocation;

}
