<?php
namespace Quiz\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\Document(collection="quiz_quiz") */
class Quiz extends Document
{
 	public function __construct($data = null)
    {
    	parent::__construct($data);
    	
    	$this->questions = new ArrayCollection();
    }

    /** 
	 * Id
	 * @var MongoId
	 * 
	 * @ODM\Id 
	*/
    protected $id;

    /**
     * Quiz name
     * @var String
     *
     * @ODM\String
     */
    protected $name;
    
    /**
     * Quiz description
     * @var String
     *
     * @ODM\String
     */
    protected $description;
    
	/** @ODM\EmbedMany(targetDocument="Quiz\Document\Quiz\Question") */
	protected $questions = array();
	
	/**
	 * Report status
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $status;
	const QUIZ_STATUS_PENDING = "pending";
	const QUIZ_STATUS_COMPLETE = "complete";
	
	/**
	 * Quiz time
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $duration;
	
	/**
	 * Is a quiz was passed
	 * @var Boolean
	 *
	 * @ODM\Boolean
	 */
	protected $is_passed;

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
	
	/** ================================================================== **/
	
	/** @ODM\PrePersist */
    public function prePersist()
    {
        $this->created_at = new \DateTime();
		$this->setDefaultIntegrity();
    }

	/** @ODM\PreUpdate */
    public function preUpdate()
    {
        $this->updated_at = new \DateTime();
		$this->setDefaultIntegrity();
    }

	/** ================================================================== **/

}
