<?php

namespace AdminBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TomJerryBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use TomJerryBundle\Controller\uploaderHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of EventsController
 *
 * @author akhil
 */
class EventsController extends Controller {

    public function eventNotificationToAllAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('event_notification_to_all'))
                ->setMethod("POST")
                ->add('eventTitle', 'text', array(
                    'label' => 'Event Title',
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('content', 'textarea', array(
                    'label' => 'Event Text Content',
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('sendsms', 'submit', array('attr' => array('class' => 'btn btn-success pull-right send-sms'), 'label' => 'Send SMS'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            var_dump("SMS Service not available yet!!!");
            die;
        }
        return $this->render('AdminBundle:MainAdmin/Events:eventNotificationToAll.html.twig', array('form' => $form->createView()));
    }

    public function eventNotificationLimitedAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('event_notification_limited'))
                ->setMethod("POST")
                ->add('class', 'entity', array(
                    'class' => 'AdminBundle:ClassRoom',
                    'property' => 'classNum',
                    'empty_value' => 'Select Class',
                    'label' => 'Class:',
                    'attr' => array('class' => 'form-control')
                ))
                ->add('division', 'entity', array(
                    'class' => 'AdminBundle:Division',
                    'property' => 'division',
                    'empty_value' => 'Select Division',
                    'label' => 'Division:',
                    'attr' => array('class' => 'form-control')
                ))
                ->add('eventTitle', 'text', array(
                    'label' => 'Event Title',
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('content', 'textarea', array(
                    'label' => 'Event Text Content',
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('sendsms', 'submit', array('attr' => array('class' => 'btn btn-success send-sms pull-right'), 'label' => 'Send SMS'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            var_dump("SMS Service not available yet!!!");
            die;
        }
        return $this->render('AdminBundle:MainAdmin/Events:eventNotificationLimited.html.twig', array('form' => $form->createView()));
    }

}
