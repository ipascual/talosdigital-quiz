<?php
namespace User;

use Zend\Module\Consumer\AutoloaderProvider,
	Zend\EventManager\StaticEventManager,
	Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
	Zend\ModuleManager\ModuleManager,
	Zend\Stdlib\Hydrator\ClassMethods;

use User\Service\UserService;
use Geolocation\Service\GeolocationService;
use Report\Service\PlrService;
use Report\Service\CrService;

use User\Helper\UserHelper;
use Notification\Helper\NotificationHelper;
use Geolocation\Helper\GeolocationHelper;

use Locale;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
	const EVENT_USER_AFTER_SIGNUP = "event_user_after_signup";
	
    /**
     * Get Autoloader Configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array();
	}

    public function getControllerPluginConfig()
    {
        return array();
    }

	
	public function init($moduleManager)
	{
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);

		// Emails
		$emailHelper = new \User\Helper\EmailHelper();
        $sharedEvents->attach("User", self::EVENT_USER_AFTER_SIGNUP, array($emailHelper, 'userAfterSignup'));
	}

	public function preDispatch($event)
	{

    	//Unauthorized request after success login
    	$session = $event->getApplication()->getServiceManager()->get('session');
		if($lastRequest = $session->get("lastRequest")) {
			$route = $event->getRouteMatch()->getMatchedRouteName();

			if($route != "zfcuser/logout" && $route != "zfcuser/login") {
				$event->getTarget()->getRequest()->setMethod($lastRequest["request"]->getMethod());
				$event->getTarget()->getRequest()->setPost($lastRequest["request"]->getPost());
				$event->getTarget()->getRequest()->setQuery($lastRequest["request"]->getQuery());
				
				//Delete request
				$session->set("lastRequest", null);				
			}
		}

        //ServiceManager
		$sm = $event->getApplication()->getServiceManager();

        //Services
		$event->getTarget()->userService = new UserService($sm);
		
        //Helpers
        //...
        
        //Validator
        //...
        
		//Vendor Helpers
        //$event->getTarget()->facebook = $sm->get('facebook');
        $event->getTarget()->email = $sm->get('email');
        $event->getTarget()->session = $sm->get('session');
    }

}