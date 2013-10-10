<?php
namespace Quiz\Document\Quiz;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\EmbeddedDocument */
class Choice extends Document
{
 	public function __construct($data = null)
    {
    	parent::__construct($data);
    }

    /** 
	 * A description of the choise
	 * @var String
	 * 
	 * @ODM\String
	*/
    protected $description;

    /** 
	 * The state of the choice that defines whether the choise if "correct" or "incorrect"
	 * 
	 * @var String
	 * 
	 * @ODM\String
	*/
    protected $state;
    const CHOICE_STATE_CORRECT = "correct";
    const CHOICE_STATE_INCORRECT = "incorrect";

	public function toArray($attributes = array(), $formatter = null) {
		$result = parent::toArray($attributes, $formatter);
		unset($result["state"]);
		
		return $result;
	}
	
}
