<?php
namespace Quiz\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\Document(collection="quiz_result") */
class Result extends Document
{
 	public function __construct($data = null)
    {
    	parent::__construct($data);
    }

    /** 
	 * Id
	 * @var MongoId
	 * 
	 * @ODM\Id 
	*/
    protected $id;

    /** @ODM\ReferenceOne(targetDocument="User\Document\User") */
    protected $user;
    
	/** @ODM\ReferenceOne(targetDocument="Quiz\Document\Quiz") */
	protected $quiz;
	
	/** 
	 * Number of correct questions
	 * @var Int
	 * 
	 *  @ODM\Int
	 */
	protected $score;

	/** 
	 * Order of the questions and results
	 * @var Array
	 * 
	 *  @ODM\Hash
	 */
	protected $results;

	/** 
	 * Started Date
	 * @var DateTime
	 * 
	 *  @ODM\Date 
	 */
	 protected $started_at;

	/** 
	 * Finished Date
	 * @var DateTime
	 * 
	 *  @ODM\Date 
	 */
	 protected $finished_at;


	/** 
	 * Created Date
	 * @var DateTime
	 * 
	 *  @ODM\Date 
	 */
	 protected $created_at;
	
	/** ================================================================== **/
	
	/** @ODM\PrePersist */
    public function prePersist()
    {
        $this->created_at = new \DateTime();
		$this->setDefaultIntegrity();
    }

	/** ================================================================== **/
	
	/**
	 * Generate question index order and result.
	 * 
	 */
	public function start() {
		$this->setScore(0);
		$this->setStartedAt(new \DateTime());
		for($i=0; $i<$this->getQuiz()->getQuestions()->count(); $i++) {
			$this->results[] = array("index" => $i, "answer" => array());	
		}
	}
	
	public function getQuestion($index) {
		$quizIndex = $this->results[$index]["index"];
		return $this->getQuiz()->getQuestions()->get($quizIndex);
	}

	public function getAnswer($index) {
		return $this->results[$index]["answer"];
	}
	
	public function setAnswer($index, $answer) { 
		if(!is_array($answer)) {
			throw new \Exception("Answer needs to be an array of indexes.");
		}
		$this->results[$index]["answer"] = $answer;
	}

	/**
	 * Calculate final score
	 */	
	public function finish() {
		$score = 0;
		
		foreach($this->results as $result) {
			$question = $this->getQuestion($result["index"]);
			$rightAnswers = array();
			foreach($question->getChoices() as $index => $choice) {
				if($choice->getState() == $choice::CHOICE_STATE_CORRECT) {
					$rightAnswers[] = $index;
				}
			}
			if($result["answer"] == $rightAnswers) {
				$score++;
			}
		}
		$this->setScore($score);
		$this->setFinishedAt(new \DateTime());
		
		return $score;
	}

}
