<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TomJerryBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use TomJerryBundle\Controller\uploaderHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of UserController
 *
 * @author akhil
 */
class UserController extends Controller {

    public function userManagementManageAction() {
        return $this->render('AdminBundle:MainAdmin/UserManage:manageUserManagement.html.twig');
    }

    public function userManagementListAction() {
        $em = $this->getDoctrine()->getManager();
        $logedUser = $this->container->get('security.context')->getToken()->getUser();
        $qb = $em->createQueryBuilder();
        $users = $qb->select('u')
                ->from('AkjnBundle:User', 'u')
                ->where('u.id != :logedId')
                ->setParameters(array('logedId' => $logedUser->getId()))
                ->getQuery()
                ->getArrayResult();
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/UserManage:usersList.html.twig', array("users" => $users)));
    }

    public function enableDisableUserAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $flag = $request->request->get('flag');
        $user = $em->getRepository("AkjnBundle:User")->findOneById($id);
        if ($flag == 'on') {
            $user->setEnabled(true);
        } else {
            $user->setEnabled(false);
        }
        $em->persist($user);
        $em->flush();
        return new JsonResponse(true);
    }

    public function addNewUserFormAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->hasRole('ROLE_SM_SUPERUSER')) {
            $superRole = true;
        } else {
            $superRole = false;
        }
        $userEntity = new \AkjnBundle\Entity\User();
        $userType = new \AkjnBundle\Form\UserType($superRole);
        $form = $this->createForm($userType, $userEntity, array("action" => $this->generateUrl("add_new_user_form")));
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($user->hasRole('ROLE_SM_SUPERUSER') || $user->hasRole('ROLE_SM_ADMIN')) {
                $fosUserManager = $this->get('fos_user.user_manager');
                $newUser = $fosUserManager->createUser();
                $newUser->setUsername($form['username']->getData());
                $newUser->setPlainPassword($form['password']->getData());
                $newUser->setEnabled(true);
                $newUser->setRoles(array($form['role']->getData()));
                $newUser->setEmail($form['email']->getData());
                $newUser->setName($form['name']->getData());
                $newUser->setMobile($form['mobile']->getData());
                $fosUserManager->updateUser($newUser);

                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'New user created successfully with username "' . $form['username']->getData() . '" with given password.');
                return $this->redirect($this->generateUrl('user_management'));
            } else {
                //No access
            }
        } else {
            if ($user->hasRole('ROLE_SM_SUPERUSER') || $user->hasRole('ROLE_SM_ADMIN')) {
                return new JsonResponse($this->renderView('AdminBundle:MainAdmin/UserManage:usersAddForm.html.twig', array("form" => $form->createView())));
            } else {
                return new JsonResponse('<h4 class="alert alert-info"> Access Denied!!! </h4>');
            }
        }
    }

}
