<?php

namespace Auth;

use Auth\Listener\AuthListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getTarget()->getEventManager();

        $authListener = $serviceManager->get(AuthListener::class );
        $authListener->attach($eventManager);
    }
}
