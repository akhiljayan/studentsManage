<?php

namespace AkjnBundle\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as BaseMessageDigestPasswordEncoder;

/**
 * Extends MessageDigestPasswordEncoder to support custom merging of password and salt strings.
 *
 * @author Vipin Bose <bose.vpin@nic.in>
 */
class SecuredLoginPasswordEncoder extends BaseMessageDigestPasswordEncoder {

    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt) {
        if ($this->isPasswordTooLong($raw)) {
            throw new BadCredentialsException('Invalid password.');
        }

        if (!in_array('sha256', hash_algos(), true)) {
            throw new \LogicException(sprintf('The algorithm "%s" is not supported.', 'sha256'));
        }


        if (substr($raw, 0, 5) === "hash:") {
            /* Handling hash coming directly from browser */
            $salted = $this->mergePasswordAndSalt(substr($raw, 5), $salt);
            $digest = bin2hex(hash('sha256', $salted, true));
        } elseif (substr($raw, 0, 11) === "hashsalted:") {
            /* Handling hash coming directly from browser */
            /* Do Nothing as the string is already hashed, salt merged and then again hashed coming directly from browser... this is the case of change userpassword */
            $digest = substr($raw, 11);
        } else {
            /* handling symfony builting packages */
            $salted = $this->mergePasswordAndSalt(( bin2hex(hash('sha256', $raw, true))), $salt);
            $digest = bin2hex(hash('sha256', $salted, true));
        }
        
        for ($i = 1; $i < 7; $i++) {
            $digest = bin2hex(hash('sha256', $digest, true));
        }
        return $digest;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($digestBrowser, $digestDB, $salt) {

        $digestUser = $digestBrowser;

        for ($i = 1; $i < 7; $i++) {
            $digestUser = bin2hex(hash('sha256', $digestUser, true));
        }

        return $this->comparePasswords($digestUser, $digestDB);
    }

}
