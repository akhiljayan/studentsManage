<?php

namespace AkjnBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;



class LogoutController extends BaseController {

    public function indexAction() {


        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $userManager = $this->container->get('fos_user.user_manager');
            $user->setIsLogged(false);
            $userManager->updateUser($user);
            $this->get('security.token_storage')->setToken(null);
            $this->get('request')->getSession()->invalidate();            
        }
        return $this->redirect($this->generateUrl('login'));
    }

}
