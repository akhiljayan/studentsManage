<?php

namespace AkjnBundle\Security\Firewall;

use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Security\UserProvider as FOSUserProvider;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use AkjnBundle\Validator\LoginCaptchaValidator;

class SecuredLoginFormAuthenticator extends FOSUserProvider implements SimpleFormAuthenticatorInterface, AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface {

    private $encoderFactory;
    protected $browserSalt = "Test";
    protected $userCaptcha = null;
    protected $userSession = null;
    private $noOfInvalidAttempts=10;
    private $userUnlockDiff=.50;

    /**
     *
     * @access protected
     * @var \FOS\UserBundle\Security\UserProvider $userProvider
     */
    protected $userProvider;

    /**
     *
     * @access protected
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    protected $router;

    /**
     *
     * @param array $routeReferer
     */
    protected $routeReferer;

    /**
     *
     * @param array $emr
     */
    protected $container;

    public function __construct(EncoderFactoryInterface $encoderFactory, Router $router, $routeReferer, $serviceContainer) {
        $this->encoderFactory = $encoderFactory;
        $this->router = $router;
        $this->routeReferer = $routeReferer;
        $this->container = $serviceContainer;
    }

    public function createToken(Request $request, $username, $password, $providerKey) {
        $this->browserSalt = $request->get('_uppu');
        $this->userCaptcha = $request->get('user', null, true);
        $this->userSession = $request->getSession();
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {

        $captchInput = $this->userCaptcha['captcha'];
        $val = new LoginCaptchaValidator($this->container, $this->userSession, 'gcb_captcha', 'Invalid Code', 'bypass', 0);
        if ($this->container->hasParameter('login_invalid_attempts')) {            

            $this->noOfInvalidAttempts= $this->container->getParameter('login_invalid_attempts');
        }
        if ($this->container->hasParameter('user_unlock_diff_in_hours')) {
       
            $this->userUnlockDiff=$this->container->getParameter('user_unlock_diff_in_hours');
        }
        if (false === $val->validate($captchInput)) {
            throw new AuthenticationException('Captcha is invalid');
        }

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        $userManager = $this->container->get('fos_user.user_manager');
        if ($user->isEnabled()) {

            $passwordValid = $this->encoderFactory->getEncoder($user)->isPasswordValid($token->getCredentials(), $user->getPassword(), $this->browserSalt);

            if ($passwordValid) {
                if ($this->ckeckAccountStatus($user) == true)
                $user->setLocked(false);
                $user->setAttempted(0);
                $userManager->updateUser($user);
                return new UsernamePasswordToken($user, 'bar', $providerKey, $user->getRoles());
            } else {
                $attempted = $user->getAttempted();
                
                $warning_point = (70 / 100) * $this->noOfInvalidAttempts;
                $attempts_remaining = $this->noOfInvalidAttempts - $attempted;
                if ($attempted >= $warning_point && $attempts_remaining > 0) {
                    throw new AuthenticationException("Invalid username or password.You have $attempts_remaining attempts remaining");
                } elseif ($attempts_remaining <= 0) {
                    throw new LockedException('Your Account has been temporarly locked.Please try after ' . $this->userUnlockDiff . ' hour(s)');
                } else {

                    throw new AuthenticationException('Invalid username or password');
                }
            }
        } else {
            throw new AuthenticationException('User not activated');
        }
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        if ($this->routeReferer['enabled']) {

            $key = '_security.main.target_path';

            if ($this->container->get('session')->has($key)) {
                $url = $this->container->get('session')->get($key);
                $this->container->get('session')->remove($key);
            } else {
                $url = $this->router->generate('_index');
            }

            $response = new RedirectResponse($url);

            if ($request->isXmlHttpRequest() || $request->request->get('_format') === 'json') {
                $response = new Response(json_encode(array('status' => 'success')));
                $response->headers->set('Content-Type', 'application/json');
            }
        } else {
            if ($request->isXmlHttpRequest() || $request->request->get('_format') === 'json') {
                $response = new Response(
                        json_encode(
                                array(
                                    'status' => 'failed',
                                    'errors' => array()
                                )
                        )
                );

                $response->headers->set('Content-Type', 'application/json');
            } else {
                $response = new RedirectResponse(
                        $this->router->generate(
                        // $this->routeLogin['name'], $this->routeLogin['params']
                        )
                );
            }
        }
        return $response;
    }

    /**
     *
     * @access public
     * @param  \Symfony\Component\HttpFoundation\Request                                                     $request
     * @param  \Symfony\Component\Security\Core\Exception\AuthenticationException                            $exception     *                                             
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        if ($request->request->has('_peru')) {
            $username = $request->request->get('_peru');
        } else {
            $username = '';
        }

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        // Send response back to browser depending on wether this is XML request or not.
        if ($request->isXmlHttpRequest() || $request->request->get('_format') === 'json') {
            $response = new Response(
                    json_encode(
                            array(
                                'status' => 'failed',
                                'errors' => array($exception->getMessage())
                            )
                    )
            );

            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new RedirectResponse($this->router->generate("add_failed_login_attempts", array("username" => $username)));
            // $response = new RedirectResponse($this->router->generate("_index"));
        }

        return $response;
    }

    public function ckeckAccountStatus($user) {

        $expired = $user->isCredentialsExpired();
        if (!is_null($user->getAttemptedAt())) {
            $attemptedTime = $user->getAttemptedAt()->format('Y-m-d H:i:s');
            $datetime1 = strtotime($attemptedTime);
            $current = new \DateTime();
            $datetime2 = strtotime($current->format('Y-m-d H:i:s'));
            $interval = abs($datetime2 - $datetime1);
            $minutes = (int) round($interval / 60);

            
            $diff = $this->userUnlockDiff * 60;
            if ($user->isAccountNonLocked() === false) {
                if ($minutes < $diff) {
                    throw new LockedException('Your Account has been temporarly locked.Please try after ' . $this->userUnlockDiff . ' hour(s)');
                } else {
                    return true;
                }
            }
            if ($expired === true) {
                $this->router->generate("credentials_expired");
            }
        }
    }

}
