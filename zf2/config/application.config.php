<?php
return array(
    // This should be an array of module namespaces used in the application.
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
	        'config/autoload/{,*.}{global,local}.php',
	        'config/autoload/env.'.(getenv('APPLICATION_ENV') ?: 'production').'.config.php',
	    ),
	    'module_paths' => array(
	        './module',
	        './vendor',
	    ),
	),
   
);
