<?php

namespace AkjnBundle\Security\Handler;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SessionIdleHandler {

    protected $session;
    protected $securityContext;
    protected $router;
    protected $maxIdleTime;

    public function __construct(SessionInterface $session, AuthorizationCheckerInterface $securityContext, RouterInterface $router, $maxIdleTime = 0) {
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {

            return;
        }

        if ($this->maxIdleTime > 0) {

            $this->session->start();
            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();
            if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {

                if ($lapse > $this->maxIdleTime) {
                    // Change the route if you are not using FOSUserBundle.
                 return  $event->setResponse(new RedirectResponse($this->router->generate('_logout')));                
                    $this->securityContext->setToken(null);
                    $this->session->getFlashBag()->set('info', 'You have been logged out due to inactivity.');
                }
            }
        }
    }

}
