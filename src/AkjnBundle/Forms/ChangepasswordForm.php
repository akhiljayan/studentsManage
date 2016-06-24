<?php

namespace AkjnBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
class ChangepasswordForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('required' => true));       
        $builder->add('password', PasswordType::class, array('required' => true));
        $builder->add('newpassword', PasswordType::class, array('required' => true, 'mapped'=>false));
        $builder->add('confirmpassword', PasswordType::class, array('required' => true, 'mapped'=>false));         
        $builder->add('save', SubmitType::class);
        $builder->add('reset', ResetType::class);
    }

    public function getBlockPrefix() {
        return 'user_password';
    }

    public function configureOptions(array $options) {
        return array(
     //       'data_class' => 'AkjnBundle\Entity\User'
        );
    }
}
