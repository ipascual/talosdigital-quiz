<?php
$command = "phpunit --filter UserTest";

//$command = "phpunit --filter UserServiceTest";
//$command = "phpunit --filter UserModuleTest::testUserChangeAddress";
//$command = "phpunit --filter UserModuleTest::testUserChangeAddress --debug";
//$command = "phpunit --filter IndexControllerTest ";

echo "<h1> $command </h1>";
echo "<pre>";
system($command);
echo "</pre>";
