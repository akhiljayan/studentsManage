<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function mainRedirectAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($user->hasRole('ROLE_SM_SUPERUSER')) {
                return $this->render('AdminBundle:MainAdmin:index.html.twig');
            } elseif ($user->hasRole('ROLE_SM_ADMIN')) {
                return $this->render('AdminBundle:MainAdmin:index.html.twig');
            }
        } else {
            return $this->generateUrl("_logout");
//            return $this->indexAction($request);
        }
    }
}
