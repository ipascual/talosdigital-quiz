<?php
namespace QuizTest\Tests;

use QuizTest\AbstractControllerZendtest;
use Zend\Stdlib\Parameters;

class IndexControllerTest extends AbstractControllerZendtest
{

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('quiz');
        $this->assertControllerName('Quiz\Controller\Index');
        $this->assertControllerClass('IndexController');
    }

}