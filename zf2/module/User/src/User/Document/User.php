<?php

namespace User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\Document(collection="user_user") */
class User extends Document {

	public function __construct($data = null)
    {
    	parent::__construct($data);
        $this->addresses = new ArrayCollection();
        $this->phonenumbers = new ArrayCollection();
		$this->validation = new ArrayCollection();
		$this->alerts = new ArrayCollection();
		$this->messages = new ArrayCollection();
		$this->subscriptions = new ArrayCollection();
    }

    /**
	 * Id
	 * @var MongoId
	 *
	 * @ODM\Id
	*/
    protected $id;

	/**
	 * First name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $firstname;

	/**
	 * Middle name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $middlename;

	/**
	 * Last name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $lastname;


	/**
	 * Full Name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $full_name;

	/**
	 * The User Gender
	 * @var String
	 *
	 * @ODM\String
	*/
	protected $gender;

	/**
	 * Birthday
	 * @var Date
	 *
	 *  @ODM\Date
	 */
	protected $birthday;

	/**
	 * User Locale Preference (e.g. en_US)
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $locale;

	/**
	 * User Currency Preference (e.g. USD)
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $currency;

	/**
	 * User Area Unit Preference sqm or sqf
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $area_unit;

	/**
	 * WACC default value
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $wacc;

	/**
	 * Email
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $email;

	/**
	 * Main email option
	 * @var Boolean
	 *
	 * @ODM\Boolean
	 */
	protected $mail_opt;

    /**
	 * Password
	 * @var String
	 *
	 * @ODM\String
	 */
    protected $password;

	/**
	 * Role (guest, user, admin)
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $role;

	/**
	 * Settings
	 * 
	 * Save different settings from other modules related to the user.
	 * 
	 * @var Array
	 *
	 * @ODM\Hash
	 */
	protected $settings;

    /**
     * @return \Zend\Permissions\Acl\Role\RoleInterface[]
     */
	public function getRoles() {
		$roles = array($this->role);
		if($this->role == "admin") {
			$roles[] = "user";
		}
		return $roles;
	}

	/**
	 * Company Name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $company_name;

    /** @ODM\EmbedMany(targetDocument="User\Document\User\Address") */
    protected $addresses = array();

    /** @ODM\EmbedMany(targetDocument="User\Document\User\Phonenumber") */
    protected $phonenumbers = array();

    /** @ODM\EmbedOne(targetDocument="User\Document\User\Facebook") */
    protected $facebook;

    /** @ODM\EmbedMany(targetDocument="User\Document\User\Validation", strategy="set") */
    protected $validation = array();

    /** @ODM\Hash */
    protected $notifications = array();

	/**
	 * Stadistics about the user
	 *
	 *
	 */

    /** @ODM\Hash */
    protected $stats = array();

	/**
	 * Created Date
	 * @var DateTime
	 *
	 *  @ODM\Date
	 */
	 protected $created_at;

	/**
	 * Updated Date
	 * @var DateTime
	 *
	 * @ODM\Date
	 */
	protected $updated_at;

	/**
	 * Last login date
	 * @var DateTime
	 *
	 * @ODM\Date
	 */
	protected $last_access;

	/**
	 * Number of successful logins
	 * @var Integer
	 *
	 * @ODM\Int
	 */
	protected $logins;

	/**
	 * Languages
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $languages;

	/**
	 * About me
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $about_me;


	/** ================================================================== **/

	/** @ODM\PrePersist */
    public function prePersist()
    {
        $this->created_at = new \DateTime();
    }

	/** @ODM\PreUpdate */
    public function preUpdate()
    {
        $this->updated_at = new \DateTime();
    }


	/** ================================================================== **/

	/**
	 * Overriding toArray method
	 */
	public function toArray($attributes = array(), $formatter = null) {
		$values = parent::toArray($attributes, $formatter);
		unset($values["password"]);
		unset($values["settings"]);
		
		return $values;
	}
	
	/**
	 * Check if the user is validated in the system on a method
	 * @var $method : "facebook" | "phone" | "email"
	 */
	public function isValidated($method) {
		if($this->getValidation()) {
			if($this->getValidation()->get($method)) {
				if($this->getValidation()->get($method)->getStatus() == "verified") {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if the user has the mandatory fields
	 *
	 */
	public function hasMandatoryFields() {
		$pass = true;

		if(! $this->getEmail()) {
			$pass = false;
		}
		if(! count($this->getPhonenumber())) {
			$pass = false;
		}
		return $pass;
	}

	/**
	 * Return the public seller profile URL
	 */
	public function getUrl() {
		$url = "/travel-planner/";

		//Use seller URL
		$seller = false;
		if($this->getSeller()) {
			if($this->getSeller()->getUrl()) {
				$url .= $this->getSeller()->getUrl();
				$seller = true;
			}
		}
		//No seller URL
		if(! $seller) {
			$url .= $this->id;
		}

		return $url;
	}

	/**
	 * Return all notifications
	 *
	 */
	 public function getAlertsBy($status = "all", $limit = -1) {
	 	//Order alerts
		$iterator = $this->alerts->getIterator();
		$iterator->uasort(function($first, $second) {
			if ($first->getTimestamp() < $second->getTimestamp()) {
				return true;
			}
			return false;
		});

		//Filter by status and limit
		$alerts = array();
		foreach($iterator as $key => $alert) {

			//Limit
			if(count($alerts) == $limit) {
				break;
			}

			//Filter by status
			if($status == "all") {
				$alerts[$key] = $alert;
			}
			elseif($status == $alert->getStatus()) {
				$alerts[$key] = $alert;
			}
		}

		return $alerts;

	}

	public function getLocale() {
		if(! $this->locale) {
			$this->locale = "en_US";
		}
		return $this->locale;
	}

	public function getCurrency() {
		if(! $this->currency) {
			$this->currency = "USD";
		}
		return $this->currency;
	}

	public function getAreaUnit() {
		if(! $this->area_unit) {
			$this->area_unit = "sqm";
		}
		return $this->area_unit;
	}

	public function getWacc() {
		if(! $this->wacc) {
			$this->wacc = (float)10.00;
		}
		return $this->wacc;
	}
	
	public function setAddress($label, $address){
		$found = false;
				
		// update
		foreach($this->getAddresses() as $userAddress) {
			if($userAddress->getLabel() == $label) {
				// remove
				$index = $this->getAddresses()->indexOf($userAddress);
				$this->getAddresses()->remove($index);
				// add
				$newAddress = clone $address;
				$newAddress->setLabel($label);
				$this->getAddresses()->set($index, $newAddress);
				$found = true;
			}
		}
		// create
		if(! $found){
			$newAddress = clone $address;
			$newAddress->setLabel($label);
			$this->getAddresses()->add($newAddress);
		}
	}
	
	public function getAddress($label){
		foreach($this->getAddresses() as $userAddress) {
			if($userAddress->getLabel() == $label) {
				return $userAddress;
			}
		
		}
		return null;
	}
	
	public function setSettings($key, $value) {
		$this->settings[$key] = $value;
	}
	
	public function getSettings($key) {
		if(isset($this->settings[$key])) {
			return $this->settings[$key];
		}
		return null;
	}
		
}