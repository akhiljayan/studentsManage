<?php

namespace AkjnBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
//use Gregwar\CaptchaBundle\Type\CaptchaType;

class LoginForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array(
            'required' => false
        ));
        $builder->add('password', PasswordType::class, array(
            'required' => false
        ));
//        $builder->add('captcha', CaptchaType::class, array(
//            'reload' => true,
//            'as_url' => true,
//            'height' => 34
//        ));
    }

    public function getBlockPrefix() {
        return 'user';
    }

    public function configureOptions(OptionsResolver $options) {
        return array(
            'data_class' => 'AkjnBundle\Entity\User'
        );
    }

}
