<?php
return array(
    'modules' => array(
        'Application',
		'DoctrineModule',
 		'DoctrineMongoODMModule',
        'MyZend',
        'User',
        'Email',
        'Quiz',
    ),

    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local,testing}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),

);
