<?php

namespace AkjnBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Authorisation controller.
 *
 */
class AuthorisationController extends Controller {

    /**
     * authorised redirection
     *
     */
    public function indexAction() {

        $loggedUser = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($loggedUser) && $loggedUser instanceof UserInterface) {
            $roll = $loggedUser->getRoles();
            if ($roll[0] === 'ROLE_REGISTERED_USER') {
                return $this->redirect($this->generateUrl('_home'));
            }
        }
        return $this->redirect($this->generateUrl('_home'));
    }

}
