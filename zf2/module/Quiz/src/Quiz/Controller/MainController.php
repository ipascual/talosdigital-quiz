<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

use Quiz\Document\Quiz;
use Quiz\Document\Result;
use Quiz\Document\Quiz\Question;
use Quiz\Document\Quiz\Choice;

use Quiz\Service\QuizService;

class MainController extends AbstractActionController
{

	/**
	 * Return a list of the all quizes
	 */
	public function indexAction() {
		$quizes = $this->quizService->findAll();
		$data = array();
		foreach($quizes as $quiz) {
			$data[] = array("id" => $quiz->getId(), "name" => $quiz->getName(), "description" => $quiz->getDescription(), "duration" => $quiz->getDuration() );
		}
		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => $data,
			'error' => $error,
			'error_message' => $errorMessage
		);
		return new JsonModel($result);
	}
	
	public function startAction() {
	    $post = $this->getRequest()->getPost();
		
		$quizId = $post->get("quiz_id");
		$userId = $post->get("user_id");
		$quiz = $this->quizService->findOneBy(array("id" => $quizId));
		$totalQuestions = $quiz->getQuestions()->count();
		$user = $this->userService->findOneBy(array("id" => $userId));
		
		$result = new Result();
		$result->setQuiz($quiz);
		$result->setUser($user);
		$result->start();
		$result = $this->resultService->save($result);

		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => array("result_id" => $result->getId(), "total_questions" => $totalQuestions),
			'error' => $error,
			'error_message' => $errorMessage
		);
	    return new JsonModel($result);

	}
	
	/**
	 * Get a specific question
	 * 
	 * @var result_id
	 * @var index
	 */
	public function questionAction() {
	    $query = $this->getRequest()->getQuery();
		
		$resultId = $query->get("result_id");
		$index = $query->get("index");
		$result = $this->resultService->findOneBy(array("id" => $resultId));

		$question = $result->getQuestion($index)->toArray();
		$answer = $result->getAnswer($index);
		// Merge user answers to the question
		 foreach ($answer as $index){
		    foreach ($question["choices"] as $key => $val){
		        if (!in_array($key, $answer)){
		            $question["choices"][$key]["selected"] = false;
		        }
		    }
		    $question["choices"][$index]["selected"] = true;
		}

		 // remaining time
		$startTime = $result->getStartedAt();
		$endTime = new \DateTime();
		$interval = $endTime->diff($startTime);
		$time = $interval->i;
		
		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => array("question" => $question, "time_spent" => $time),
			'error' => $error,
			'error_message' => $errorMessage
		);
		
	    return new JsonModel($result);
	} 
	
	/**
	 * Answer a specific question
	 * 
	 * @var result_id : string
	 * @var index : integer
	 * @var answers : array
	 */
	public function answerAction() {
	    $post = $this->getRequest()->getPost();
	
		$resultId = $post->get("result_id");
		$index = $post->get("index");
		$answer = $post->get("answers");
		$result = $this->resultService->findOneBy(array("id" => $resultId));

		$result->setAnswer($index, $answer);
		$this->resultService->save($result);
		
		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => array(),
			'error' => $error,
			'error_message' => $errorMessage
		);
		
	    return new JsonModel($result);
	} 

	/**
	 * Finish the test
	 * - save result
	 * - send email with results
	 * 
	 * @var result_id : string
	 * @var index : integer
	 * @var answer : array
	 */
	public function finishAction() {
	    $post = $this->getRequest()->getPost();
		
		$resultId = $post->get("result_id");
		$index = $post->get("index");
		$answer = $post->get("answer");
		$result = $this->resultService->findOneBy(array("id" => $resultId));

		$score = $result->finish();
		$this->resultService->save($result);

		// Email data
		$fullname = $result->getUser()->getFirstname()." ".$fullname = $result->getUser()->getLastname();
		$userEmail = $result->getUser()->getEmail();
		$quizName = $result->getQuiz()->getName();
		$totalQuestions = $result->getQuiz()->getQuestions()->count();
		$startTime = $result->getStartedAt();
		$endTime = $result->getFinishedAt();
		$interval = $endTime->diff($startTime);
		$time = $interval->i;
		
		$email = $this->email->create();
		$email->setSubject($quizName." Result");
		$email->setTextContent("Candidate: ".$fullname." (".$userEmail.") has finished ".$quizName." with result: ".$score."/".$totalQuestions." Spent time: ".$time."minutes");
		$email->setHtmlContent("
		<p><b>Candidate:</b> ".$fullname." (".$userEmail.") has finished ".$quizName." with result: ".$score."/".$totalQuestions."</p>
		<p><b>Spent time:</b> ".$time." minutes</p>
		        ");
		$this->email->send($email); 
		
		$error = 0;
		$errorMessage = "";
		$result = array(
			'data' => array("score" => $score),
			'error' => $error,
			'error_message' => $errorMessage
		);
		
	    return new JsonModel($result);
	} 

}