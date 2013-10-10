<?php
namespace User;

return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Manage' 		=> 'User\Controller\ManageController'
        ),
    ),

    'router' => array(
        'routes' => array(
            /* /module / controller / action */
            'user' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
					),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'quiz' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller]/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
							),
                        ),
                    ),
				),
			),
        ),
	),
	
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Document')
				
            ),
            'odm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Document' => __NAMESPACE__ . '_driver',
				)
            )
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
		'strategies' => array(
            'ViewJsonStrategy',
        ),        
    ),
    
	'email' => array(
		"template_path_stack" => array(
				__DIR__ . "/../view/email/"
		),
	),
	
	'bjyauthorize' => array(
	    'guards' => array(
	        'BjyAuthorize\Guard\Controller' => array(
	        ),
	    ),
	),
	
);