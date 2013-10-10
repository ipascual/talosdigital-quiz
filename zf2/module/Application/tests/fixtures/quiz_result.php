<?php
$data[] = array(
  '_id' => new MongoId("524ecfbdb9d68d8b218b456b"),
  'user' => 
  array (
    '$ref' => 'user_user',
    '$id' => new MongoId("51effc658f604c012b000007"),
    '$db' => $databaseName,
  ),
  'quiz' => 
  array (
    '$ref' => 'quiz_quiz',
    '$id' => new MongoId("524ecb4cb9d68d486a8b4567"),
    '$db' => $databaseName,
  ),
  'results' => 
  array (
    '0' => 
    array (
      'index' => new MongoInt32(0),
      'answer' => 
      array (
      ),
    ),
    '1' => 
    array (
      'index' => new MongoInt32(1),
      'answer' => 
      array (
      ),
    ),
    '2' => 
    array (
      'index' => new MongoInt32(2),
      'answer' => 
      array (
      ),
    ),
    '3' => 
    array (
      'index' => new MongoInt32(3),
      'answer' => 
      array (
      ),
    ),
  ),  
  'created_at' => new MongoDate(1380896701, 0),
);