<?php

namespace AkjnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AkjnBundle\Forms;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AkjnBundle\Security\Keys\SecuredLoginKeys;
use Sinner\Phpseclib\Crypt\Crypt_RSA;
use AkjnBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PasswordController extends Controller implements AuditableControllerInterface {

    public function passwordchangeviewAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->createForm(new Forms\ChangepasswordForm());
        return $this->render('AkjnBundle:User:changePassword.html.twig', array('form' => $form->createView()));
    }

    public function passwordchangeAction(Request $request) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user)) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->createForm(new Forms\ChangepasswordForm());
        $form->handleRequest($request);
        $strMsg = "Something went error!!";
        $status="";
        if ($request->isMethod('POST')) {
            $upws = $form['upws']->getData();

            $cipher = new Crypt_RSA ();
            $PRK = new SecuredLoginKeys ();
            $cipher->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
            $cipher->loadKey($PRK->getPrivateKey(), CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
            $UsernamePassword = $cipher->decrypt(base64_decode($upws));
            $Credentials = explode("|", $UsernamePassword);
            $username = $Credentials[0] ? $user = $this->container->get('security.context')->getToken()->getUser() : $Credentials[0];
            $password = $Credentials[1];
            $randomNo = $Credentials[2];
            $newPassword = $Credentials[3];

            $encoderFactory = $this->container->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);
            $validPassword = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
            if (!$validPassword) {
                // throw new AccessDeniedException('Unable to authenticate using your current password');
                $strMsg = "Current password is invalid.";
                $status="FAILED";
            } else {
                $userManager = $this->container->get('fos_user.user_manager');
                $user->setPlainPassword($newPassword);
                $userManager->updateUser($user);
                $strMsg = " Password has beeen Changed.";
                $status="SUCCESS";
            }
        }
        
        //return $this->render('AkjnBundle:User:changePassword.html.twig', array('form' => $form->createView(), 'message' => $strMsg));
        return new JsonResponse(array('STATUS'=>$status,'MESSAGE'=>$strMsg));
    }

}
