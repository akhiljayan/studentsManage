<?php

namespace AdminBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AkjnBundle\Forms\LoginForm;
use AkjnBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TomJerryBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use TomJerryBundle\Controller\uploaderHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller {

    public function mainRedirectAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($user->hasRole('ROLE_SM_SUPERUSER')) {
                return $this->render('AdminBundle:MainAdmin:index.html.twig');
            } elseif ($user->hasRole('ROLE_SM_ADMIN')) {
                return $this->render('AdminBundle:MainAdmin:index.html.twig');
            }
        } else {
            return $this->generateUrl("_logout");
        }
    }

    public function mastersClassAction() {
        return $this->render('AdminBundle:MainAdmin/ClassRooms:manageClasses.html.twig');
    }

    public function mastersClassListAction() {
        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository("AdminBundle:ClassRoom")->findBy(array(), array('classNum' => 'ASC'));
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/ClassRooms:listClasses.html.twig', array("classes" => $classes)));
    }

    public function mastersClassAddNewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $classEntity = new \AdminBundle\Entity\ClassRoom();
        $classType = new \AdminBundle\Form\ClassRoomType();
        $form = $this->createForm($classType, $classEntity, array("action" => $this->generateUrl("add_new_class_form")));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($classEntity);
            $em->flush();
            return $this->render('AdminBundle:MainAdmin/ClassRooms:manageClasses.html.twig');
        }
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/ClassRooms:addClassForm.html.twig', array('form' => $form->createView())));
    }

    public function mastersClassEditAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $classEntity = $em->getRepository("AdminBundle:ClassRoom")->findOneById($id);
        $classType = new \AdminBundle\Form\ClassRoomType();
        $form = $this->createForm($classType, $classEntity, array("action" => $this->generateUrl("edit_new_class_form", array('id' => $id))));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($classEntity);
            $em->flush();
            return $this->render('AdminBundle:MainAdmin/ClassRooms:manageClasses.html.twig');
        }
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/ClassRooms:addClassForm.html.twig', array('form' => $form->createView())));
    }

}
