<?php
namespace Quiz\Document\Quiz;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\EmbeddedDocument */
class Question extends Document
{
 	public function __construct($data = null)
    {
    	parent::__construct($data);
    	
    	$this->choices = new ArrayCollection();
    }

    /**
     * The text of a question
     * @var String
     *
     * @ODM\String
     */
    protected $description;
    
    
    /** @ODM\EmbedMany(targetDocument="Quiz\Document\Quiz\Choice") */
    protected $choices = array();
    
    /**
     * Defines whether the question was passed or not
     * @var String
     *
     * @ODM\String
     */
    protected $result;
    const QUESTION_STATE_PASSED = "passed";
    const QUESTION_STATE_NOPASSED = "no_passed";
    
    /**
     * Defines whether the question has a single answer or multiple
     * @var String
     *
     * @ODM\String
     */
    protected $type;
    const QUESTION_TYPE_SINGLE = "single";
    const QUESTION_TYPE_MULTIPLE = "multiple";	
	
}
