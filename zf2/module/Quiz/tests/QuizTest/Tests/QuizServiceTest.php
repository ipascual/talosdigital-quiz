<?php
namespace QuizTest\Tests;

use QuizTest\AbstractTestCase;

use User\Document\User;
use Quiz\Document\Quiz;
use Quiz\Document\Quiz\Question;
use Quiz\Document\Quiz\Choice;
use Quiz\Document\Result;

use User\Service\UserService;
use Quiz\Service\QuizService;
use Quiz\Service\ResultService;

class QuizServiceTest extends AbstractTestCase {
	
	public function setup() {
		parent::setup();
		
		// Services
		$this->userService = new UserService($this->getServiceManager());
		$this->quizService = new QuizService($this->getServiceManager());
		$this->resultService = new ResultService($this->getServiceManager());
	}
    
	/**
	 * Create a question
	 */
	public function testCreateQuestion() {
		$quiz = new Quiz();
		
	    //Quiestions
	    $question1 = new Question();
	    $question1->setDescription('Which of the following PHP tags do NOT require additional changes of php.ini?');
	    
	    //Choices
	    $choice1 = new Choice();
	    $choice1->setDescription('< ?php     ?>');
	    $choice1->setState(Choice::CHOICE_STATE_CORRECT);
	    $question1->getChoices()->add($choice1);
	  
	    $choice2 = new Choice();
	    $choice2->setDescription('< ?        ?>');
	    $choice2->setState(Choice::CHOICE_STATE_INCORRECT);
	    $question1->getChoices()->add($choice2);
	    
	    $choice3 = new Choice();
	    $choice3->setDescription('< %        %>');
	    $choice3->setState(Choice::CHOICE_STATE_INCORRECT);
	    $question1->getChoices()->add($choice3);
	    
	    $choice4 = new Choice();
	    $choice4->setDescription('< script language=�php�>    </script>');
	    $choice4->setState(Choice::CHOICE_STATE_CORRECT);
	    $question1->getChoices()->add($choice4);
	    
	    $question1->setResult(Question::QUESTION_STATE_PASSED);
	    $question1->setType(Question::QUESTION_TYPE_MULTIPLE);
		
		$quiz->getQuestions()->add($question1);
	    $quiz = $this->quizService->save($quiz);
		
	    // asserts
	    $this->assertNotNull($quiz);
	    $this->assertInstanceOf('Quiz\Document\Quiz\Question', $question1);
	    $this->assertNotEmpty($quiz->getQuestions()); 
		$choice = $quiz->getQuestions()->get(0)->getChoices()->get(0);
	    $this->assertEquals('< ?php     ?>', $choice->getDescription());
	    $this->assertEquals(Choice::CHOICE_STATE_CORRECT, $choice->getState());  
	}
	
	/**
	 * Create a quiz
	 */
	public function testCreateQuiz(){
	    
	    //Quiestions
	    $question1 = new Question();
	    $question1->setDescription('Which of the following PHP tags do NOT require additional changes of php.ini?');
	     
	    //Choices
	    $choice1 = new Choice();
	    $choice1->setDescription('< ?php     ?>');
	    $choice1->setState(Choice::CHOICE_STATE_CORRECT);
	    $question1->getChoices()->add($choice1);
	     
	    $choice2 = new Choice();
	    $choice2->setDescription('< ?        ?>');
	    $choice2->setState(Choice::CHOICE_STATE_INCORRECT);
	    $question1->getChoices()->add($choice2);
	     
	    $choice3 = new Choice();
	    $choice3->setDescription('< %        %>');
	    $choice3->setState(Choice::CHOICE_STATE_INCORRECT);
	    $question1->getChoices()->add($choice3);
	     
	    $choice4 = new Choice();
	    $choice4->setDescription('< script language=�php�>    </script>');
	    $choice4->setState(Choice::CHOICE_STATE_CORRECT);
	    $question1->getChoices()->add($choice4);
	     
	    $question1->setResult(Question::QUESTION_STATE_PASSED);
	    $question1->setType(Question::QUESTION_TYPE_MULTIPLE);
	    //$question1 = $this->questionService->save($question1);
	    
	    //Quiz
	    $quiz = new Quiz();
	    $quiz->setName('PHP quiz');
	    $quiz->setDescription('This is a test quiz which was created in order to use it in Unit tests.');
	    $quiz->getQuestions()->add($question1);
	    $quiz->setStatus(Quiz::QUIZ_STATUS_COMPLETE);
	    $quiz->setIsPassed(TRUE);
	    $this->quizService->save($quiz);
	    //asserts
	    $this->assertNotNull($quiz);
	    $this->assertInstanceOf('Quiz\Document\Quiz', $quiz);
	    $this->assertNotEmpty($quiz->getQuestions());
	    $this->assertEquals(Quiz::QUIZ_STATUS_COMPLETE, $quiz->getStatus());
	    $this->assertEquals('PHP quiz', $quiz->getName());
	}
	
	/**
	 * Add a question to the quiz
	 */
	public function testAddQuestionToQuiz(){
	    
		$quiz = $this->quizService->findOneBy(array("id" => "524ecb4cb9d68d486a8b4567"));
		$this->assertInstanceOf('Quiz\Document\Quiz', $quiz);
		//Choices
		$data['description'] = '< ?      ?>';
		$data['state'] =Choice::CHOICE_STATE_CORRECT;
		$choice1 = new Choice($data);
		
		$data['description'] = '< %      %>';
		$data['state'] =Choice::CHOICE_STATE_CORRECT;
		$choice2 = new Choice($data);
		
		$data['description'] = '< &	     &>';
		$data['state'] =Choice::CHOICE_STATE_INCORRECT;
		$choice3 = new Choice($data);
		
		$data['description'] = '< !--	-->';
		$data['state'] =Choice::CHOICE_STATE_INCORRECT;
		$choice4 = new Choice($data);
		
		//Question
		$data['description'] = 'Which of the following tags are valid for PHP?';
		$data['result'] = Question::QUESTION_STATE_PASSED;
		$data['type'] = Question::QUESTION_TYPE_MULTIPLE;
		$question = new Question($data);
		$this->assertInstanceOf('Quiz\Document\Quiz\Question', $question);
		$question->getChoices()->add($choice1);
		$question->getChoices()->add($choice2);
		$question->getChoices()->add($choice3);
		$question->getChoices()->add($choice4);
		$this->quizService->save($question);
		
		$beforeQuestions = $quiz->getQuestions()->count();
		$this->assertEquals(4, $beforeQuestions);
	
		$quiz->getQuestions()->add($question);
		$this->assertNotNull($quiz->getQuestions());
		$afterQuestions = $quiz->getQuestions()->count();
		$this->assertEquals(5, $afterQuestions);
	} 

	/**
	 * Question json does not output the correct choice.
	 */	
	public function testGetQuestionJson() {
		$quiz = $this->quizService->findOneBy(array("id" => "524ecb4cb9d68d486a8b4567"));
		$this->assertInstanceOf('Quiz\Document\Quiz', $quiz);
		
		$question = $quiz->getQuestions()->get(0);
		$json = json_encode($question->toArray());
		$this->assertFalse(strpos($json, "state"));
	}
	
	public function testStartQuiz() {
		$quiz = $this->quizService->findOneBy(array("id" => "524ecb4cb9d68d486a8b4567"));
		$user = $this->userService->findOneBy(array("id" => "51effc658f604c012b000007"));
		
		$result = new Result();
		$result->setQuiz($quiz);
		$result->setUser($user);
		$result->start();
		$result = $this->resultService->save($result);
		$this->assertNotNull($result);
				die();
		
	}
	
	public function testGetQuestion() {
		$result = $this->resultService->findOneBy(array("id" => "524ecfbdb9d68d8b218b456b"));
		
		$question = $result->getQuestion(0);
		$this->assertNotNull($question);
		$this->assertInstanceOf('Quiz\Document\Quiz\Question', $question);
	}
	
	public function testAddAnswerToQuestion() {
		$result = $this->resultService->findOneBy(array("id" => "524ecfbdb9d68d8b218b456b"));

		$answer = array(2);
		$result->setAnswer(0, $answer);
		
		$answer = $result->getAnswer(0);
		$this->assertNotNull($answer);
		$this->assertEquals(2, $answer[0]);
	}
	
	public function testFinish() {
		$result = $this->resultService->findOneBy(array("id" => "524ecfbdb9d68d8b218b456b"));

		$answer = array(0);
		$result->setAnswer(0, $answer);

		$answer = array(0);
		$result->setAnswer(1, $answer);

		$answer = array(0);
		$result->setAnswer(2, $answer);

		$answer = array(1, 3);
		$result->setAnswer(3, $answer);
		
		$score = $result->finish();
		$this->assertNotNull($score);
		$this->assertEquals(4, $score);
	}	
	
}