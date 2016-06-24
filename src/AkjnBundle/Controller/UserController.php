<?php

namespace AkjnBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends BaseController {

    private $noOfInvalidAttempts = 10;
    //private $userUnlockDiff = .50;

    public function showChangePasswordFormAction() {
        $csrfToken = $this->container->has('security.csrf.token_manager') ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
        return $this->container->get('templating')->renderResponse(
                        'AkjnBundle:User:changePassword.html.twig', array('csrf_token' => $csrfToken));
    }

    public function changePasswordAction(Request $request) {


        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $password = $request->request->get('password');
        $newpassword = $request->request->get('newpassword');

        $userexists = $this->getDoctrine()
                ->getRepository('AkjnBundle:User')
                ->findOneBy(array('password' => $password));

        if (!$userexists) {
            $userManager = $this->container->get('fos_user.user_manager');
            $user->setPlainPassword("hashsalted:" . $newpassword);
            $user->setCredentialsExpireAt();
            $user->setCredentialsExpired(false);
            $userManager->updateUser($user);
            // return new JsonResponse("Change Password Seems to be successful");
            $this->get('session')->getFlashBag()->add('status', 'Change Password Seems to be successful.');
            return $this->redirect($this->generateUrl('change_user_password'));
        } else {
            $csrfToken = $this->container->has('security.csrf.token_manager') ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
            return $this->container->get('templating')->renderResponse(
                            'AkjnBundle:User:changePassword.html.twig', array('csrf_token' => $csrfToken)
            );
        }
    }

    public function credentialsExpiredAction() {

        $this->get('session')->getFlashBag()->add('status', 'Your login credentials seems to be expired.Please change your username and password .');
        $csrfToken = $this->container->has('security.csrf.token_manager') ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
        return $this->container->get('templating')->renderResponse(
                        'AkjnBundle:User:changePassword.html.twig', array('csrf_token' => $csrfToken));
    }

    public function addFailedLoginAttemptsAction($username) {
        $userManager = $this->container->get('fos_user.user_manager');
        if ($this->container->hasParameter('login_invalid_attempts')) {
            
            $this->noOfInvalidAttempts = $this->container->getParameter('login_invalid_attempts');
        }
        $user = $this->getDoctrine()
                ->getRepository('AkjnBundle:User')
                ->findOneBy(array('username' => $username));
        if ($user) {
            if ($user->getAttempted() < $this->noOfInvalidAttempts) {
                $user->setAttempted($user->getAttempted() + 1);
                $user->setAttemptedAt();
                $userManager->updateUser($user);
            } else {
                $user->setLocked(true);
                $userManager->updateUser($user);
            }
        }
        return $this->redirect($this->generateUrl('login'));
    }

    public function changeLockStatusAction($username) {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->getDoctrine()
                ->getRepository('AkjnBundle:User')
                ->findOneBy(array('username' => $username));
        $user->setLocked(false);
        $user->setAttempted(0);
        $userManager->updateUser($user);

        return $this->redirectToRoute('_index');
    }
}
