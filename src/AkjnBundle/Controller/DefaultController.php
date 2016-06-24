<?php

namespace AkjnBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AkjnBundle:Default:index.html.twig');
    }
}
