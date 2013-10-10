<?php
return array(
    'modules' => array(
        'MyZend',

		//Database
		'DoctrineModule',
 		'DoctrineMongoODMModule',

		//User
 		'ZfcBase',
 		'ZfcUser',
 		'ZfcUserDoctrineMongoODM',
 		//'BjyAuthorize',
 		//'Facebook',
 		'User',
    	'Sales',
    	'Geolocation',
    	'Email',
    	'Media',
    	'Notification',
 		'Sales',
    	'Subscription'
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
