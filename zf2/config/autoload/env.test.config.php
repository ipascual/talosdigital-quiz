<?php
/**
 * Configuration Override on environment
 *
 */
return array(
    'doctrine' => array(
        'configuration' => array(
            'odm_default' => array(
                'default_db'         => 'talosdigital-quiz-test',
            )
        )
	),
	'email' => array(
		"active" => false
	)
);
