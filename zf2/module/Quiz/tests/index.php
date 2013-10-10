<?php
//$command = "phpunit";
//$command = "phpunit --filter UserServiceTest::testCreateQuiz --debug";
//$command = "phpunit --filter UserTest::testUserChangeAddress";
//$command = "phpunit --filter UserTest::testUserChangeAddress --debug";
//$command = "phpunit --filter QuizServiceTest::testCreateQuiz --debug";
$command = "phpunit --filter QuizServiceTest";

echo "<h1> $command </h1>";
echo "<pre>";
system($command);
echo "</pre>";
