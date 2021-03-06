<?php

namespace Auth\Listener;

use Auth\Controller\AuthController;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;

class AuthListener implements ListenerAggregateInterface
{
    private $listeners = [];

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedManager = $events->getSharedManager();

        $this->listeners[] = $sharedManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'checkIdentity'],
            $priority
        );

        $this->listeners[] = $sharedManager->attach(
            AbstractRestfulController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'checkToken'],
            $priority
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    public function checkIdentity(EventInterface $event)
    {
        $controller = $event->getTarget();
        $authService = $event->getApplication()->getServiceManager()->get(AuthenticationService::class);
        $controllerName = $event->getRouteMatch()->getParam('controller', null);

        if (!$authService->getIdentity() && $controllerName != AuthController::class) {
            return $controller->redirect()->toRoute('login');
        }

        return 0;
    }

    public function checkToken(EventInterface $event)
    {
        return 0;
    }
}