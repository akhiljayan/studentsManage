<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AkjnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AkjnBundle\Forms\LoginForm;
use AkjnBundle\Entity\User;
use AkjnBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller implements AuditableControllerInterface {

    public function loginAction(Request $request) {
        $session = $request->getSession();
        $user = new User();

        $form = $this->createForm(LoginForm::class, $user);

//        session destroys if user already logged in 

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            return new RedirectResponse($this->generateUrl("fos_user_security_logout"));
        }

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage();
        }
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        $csrfToken = $this->container->has('security.csrf.token_manager') ? $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue() : null;
        $salt = md5(uniqid(php_uname('n')));

        return $this->render('AkjnBundle:User:login.html.twig', array('form' => $form->createView(), 'error' => $error, 'csrf_token' => $csrfToken, 'browserSalt' => $salt));
    }

    public function preLoginAction($userName) {
        $em = $this->getDoctrine()->getManager();
        if (filter_var($userName, FILTER_VALIDATE_EMAIL)) {
            $preLoginUser = $em->getRepository('AkjnBundle:User')->findOneByEmail($userName);
        } else {
            $preLoginUser = $em->getRepository('AkjnBundle:User')->findOneByUsername($userName);
        }
        if ($preLoginUser) {
            return new JsonResponse(array('uppu' => $preLoginUser->getSalt()));
        } else {
            return new JsonResponse(array('uppu' => md5(uniqid(php_uname('n')))));
        }
    }

    public function expiredAction(Request $request) {
        return $this->render('AkjnBundle:User:loginExpired.html.twig');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data) {
        return $this->container->get('templating')->renderResponse('AkjnBundle:Default:login.html.twig', $data);
    }

    public function checkAction() {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction() {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

}
