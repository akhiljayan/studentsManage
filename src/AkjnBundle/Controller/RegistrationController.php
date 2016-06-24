<?php

namespace AkjnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AkjnBundle\Entity\Registration;
use AkjnBundle\Entity\User;
use AkjnBundle\Forms\RegistrationType;

/**
 * Registration controller.
 *
 */
class RegistrationController extends Controller {

    /**
     * Lists all Registration entities.
     *
     */
    public function indexAction() {
        return $this->render('AkjnBundle:Registration:home.html.twig');
    }

    public function findAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AkjnBundle:Registration')->findAll();

        return $this->render('AkjnBundle:Registration:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Registration entity.
     *
     */
    public function createAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration);
        $form->handleRequest($request);

        if ($form->isValid()) {

            /* transaction begins */
            $em->getConnection()->beginTransaction();
            try {
                /* Getting new Registration Number from Registration Number Master */
                $registrationNM = $em->find('AkjnBundle:RegistrationNumberMaster', 1, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
                /* Creating new user */
                $fosUserManager = $this->get('fos_user.user_manager');
                $newUser = $fosUserManager->createUser();
                $newUser->setUsername($registrationNM->getUserId());
                $newUser->setPlainPassword("hash:" . $form['password']->getData());
                $newUser->setEnabled(true);
                $newUser->setRoles(array('ROLE_REGISTERED_USER'));
                $newUser->setEmail($form['email']->getData());
                $fosUserManager->updateUser($newUser);
                /* Creating new Registration */
                $registration->setIpAddress($request->getClientIp());
                $registration->setSecurityAnswer(md5($form['securityAnswer']->getData()));
                $registration->setUserId($newUser);

                /* Incrementing Registration Number Master */
                $registrationNM->setUserId($registrationNM->getUserId() + 1);

                $em->persist($registration);
                $em->persist($registrationNM);
                $em->flush();
                $em->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                throw $e;
            }
            return $this->redirect($this->generateUrl('registration_show', array('name' => $registration->getDisplayName(), 'appln_no' => $newUser->getUsername())));
        }

        return $this->render('AkjnBundle:Registration:new.html.twig', array(
                    'entity' => $registration,
                    'form' => $form->createView(),
        ));
    }

    public function saveAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration);
        $form->handleRequest($request);
        $k = $form['email']->getData();
        $r = $this->checkEmail($k);
        if ($r['status'] == false) {


            if ($form->isValid()) {


                /* transaction begins */
                $em->getConnection()->beginTransaction();
                try {
                    /* Getting new Registration Number from Registration Number Master */
                    $registrationNM = $em->find('AkjnBundle:RegistrationNumberMaster', 1, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
                    /* Creating new user */
                    $fosUserManager = $this->get('fos_user.user_manager');
                    $newUser = $fosUserManager->createUser();
                    $newUser->setUsername($registrationNM->getUserId());
                    $newUser->setPlainPassword("hash:" . $form['password']->getData());
                    $newUser->setEnabled(true);
                    $newUser->setRoles(array('ROLE_REGISTERED_USER'));
                    $newUser->setEmail($form['email']->getData());
                    $newUser->setCredentialsExpireAt();
                    $fosUserManager->updateUser($newUser);
                    /* Creating new Registration */
                    $registration->setIpAddress($request->getClientIp());
                    $registration->setSecurityAnswer(md5($form['securityAnswer']->getData()));
                    $registration->setUserId($newUser);

                    /* Incrementing Registration Number Master */
                    $registrationNM->setUserId($registrationNM->getUserId() + 1);

                    $em->persist($registration);
                    $em->persist($registrationNM);
                    $em->flush();
                    $em->commit();
                } catch (\Exception $e) {
                    $em->getConnection()->rollback();
                    throw $e;
                }
                return $this->redirect($this->generateUrl('registration_show', array('name' => $registration->getDisplayName(), 'appln_no' => $newUser->getUsername())));
            }
        } else {
            $this->get('session')->getFlashBag()->add('status', 'Email Already Exists.');
            return $this->redirect($this->generateUrl('registration_new'));
        }
        return $this->render('AkjnBundle:Registration:new.html.twig', array(
                    'entity' => $registration,
                    'form' => $form->createView(),
        ));
    }

    public function createNewTJAdminUserAction(Request $request) {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $userType = new \AkjnBundle\Forms\UserType();
            $form = $this->createForm($userType, $user, array('action' => $this->generateUrl('registration_new_tj_admin_user')));
            $form->handleRequest($request);
            if ($form->isValid()) {
                $username = $form['userName']->getData();
                $pass = $form['password']->getData();
                $fosUserManager = $this->get('fos_user.user_manager');
                $newUser = $fosUserManager->createUser();
                $newUser->setUsername($username);
                $newUser->setPlainPassword($pass);
                $newUser->setEnabled(true);
                $newUser->setRoles(array('ROLE_TMJR_ADMIN'));
                $newUser->setEmail($form['email']->getData());
                $newUser->setCredentialsExpireAt();
                $fosUserManager->updateUser($newUser);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'New admin user created successfully with username "' . $username . '" and password "' . $pass . '".');
                return $this->redirect($this->generateUrl('login'));
            }
            return $this->render('AkjnBundle:Registration:newTJAdminUser.html.twig', array(
                        'form' => $form->createView(),
            ));
        }else{
            var_dump("No Access");
            die;
        }
    }

    /**
     * Displays a form to create a new Registration entity.
     *
     */
    public function newAction() {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration);

        return $this->render('AkjnBundle:Registration:new.html.twig', array(
                    'entity' => $registration,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Ajax email avilability checking....
     *
     */
    public function emailAvailabilityCheckAction(Request $request, $email) {
        $status = $this->checkEmail($email);
        return new \Symfony\Component\HttpFoundation\JsonResponse($status);
    }

    private function checkEmail($email) {
        $this->getDoctrine()->getManager();
        $user = new User();
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);
        if ($user === null) {
            $status = array("status" => FALSE);
        } else {
            $status = array("status" => TRUE);
        }
        return $status;
    }

    /**
     * Finds and displays a Registration entity.
     *
     */
    public function showAction($name, $appln_no = "") {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('AkjnBundle:Registration')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Registration entity.');
//        }   

        return $this->render('AkjnBundle:Registration:show.html.twig', array(
                    'name' => $name,
                    'appln_no' => $appln_no
        ));
    }

    /**
     * Displays a form to edit an existing Registration entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AkjnBundle:Registration')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Registration entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AkjnBundle:Registration:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Registration entity.
     *
     * @param Registration $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Registration $entity) {
        $form = $this->createForm(new RegistrationType(), $entity, array(
            'action' => $this->generateUrl('registration_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Registration entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AkjnBundle:Registration')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Registration entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('registration_edit', array('id' => $id)));
        }

        return $this->render('AkjnBundle:Registration:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Registration entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AkjnBundle:Registration')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Registration entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('registration'));
    }

    /**
     * Creates a form to delete a Registration entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('registration_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
