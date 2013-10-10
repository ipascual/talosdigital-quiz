<?php
namespace Quiz;

return array(
    'controllers' => array(
        'invokables' => array(
            'Quiz\Controller\Main'	=> 'Quiz\Controller\MainController',
        ),
    ),

    'router' => array(
        'routes' => array(
            /* /module / controller / action */
            'quiz' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/quiz',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Quiz\Controller',
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
                    __NAMESPACE__ . '\Document' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),

	'bjyauthorize' => array(
	    'guards' => array(
	        'BjyAuthorize\Guard\Controller' => array(
	            array('controller' => 'Quiz\Controller\Main', 'roles' => array('guest', 'user')),            
	        ),
	    ),
	),

    'view_manager' => array(
        'template_path_stack' => array(
            'quiz' => __DIR__ . '/../view',
        ),
		'strategies' => array(
            'ViewJsonStrategy',
        ),        
    ),
);