<?php
return array(
    'myzend'   => array(
    	"php_settings" => array(
    		'error_reporting'				=> E_ALL & ~E_DEPRECATED & ~E_STRICT,
        	'display_startup_errors'        => true,
        	'display_errors'                => true,
        	'max_execution_time'            => 600,
        ),
    ),
    'session' => array(
    	'cookie_lifetime' => time() + (10 * 365 * 24 * 60 * 60), 
		'remember_me_seconds' => time() + (10 * 365 * 24 * 60 * 60),
		'name' => 'myzend'
	),
);