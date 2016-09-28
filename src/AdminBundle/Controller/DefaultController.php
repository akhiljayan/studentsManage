<?php

namespace AdminBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            }else{
                return $this->render('AdminBundle:MainAdmin:indexUser.html.twig');
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
    
    
    
    public function mastersDivisionAction() {
        return $this->render('AdminBundle:MainAdmin/Division:manageDivision.html.twig');
    }

    public function mastersDivisionListAction() {
        $em = $this->getDoctrine()->getManager();
        $division = $em->getRepository("AdminBundle:Division")->findBy(array(), array('division' => 'ASC'));
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Division:listDivision.html.twig', array("division" => $division)));
    }

    public function mastersDivisionAddNewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $divEntity = new \AdminBundle\Entity\Division();
        $divType = new \AdminBundle\Form\DivisionType();
        $form = $this->createForm($divType, $divEntity, array("action" => $this->generateUrl("add_new_division_form")));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($divEntity);
            $em->flush();
            return $this->render('AdminBundle:MainAdmin/Division:manageDivision.html.twig');
        }
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Division:addDivisionForm.html.twig', array('form' => $form->createView())));
    }

    public function mastersDivisionEditAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $divEntity = $em->getRepository("AdminBundle:Division")->findOneById($id);
        $divType = new \AdminBundle\Form\DivisionType();
        $form = $this->createForm($divType, $divEntity, array("action" => $this->generateUrl("edit_new_division_form", array('id' => $id))));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($divEntity);
            $em->flush();
            return $this->render('AdminBundle:MainAdmin/Division:manageDivision.html.twig');
        }
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Division:addDivisionForm.html.twig', array('form' => $form->createView())));
    }

}
