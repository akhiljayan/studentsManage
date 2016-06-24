<?php

// AuthenticationListener.php
namespace AkjnBundle\Listener;

use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface; 
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
 
class AuthenticationListener implements EventSubscriberInterface
{
    
    
/**
* getSubscribedEvents
*
* @author 
* @return array
*/
public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        );
    }
 
/**
* onAuthenticationFailure
*
* @author 
* @param AuthenticationFailureEvent $event
*/
public function onAuthenticationFailure( AuthenticationFailureEvent $event )
{
// executes on failed login
}
 
/**
* onAuthenticationSuccess
*
* @author 
* @param InteractiveLoginEvent $event
*/
public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
        // executes on successful login
    }
}

