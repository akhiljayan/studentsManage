<?php

namespace AkjnBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType {
    
    private $superRole;

    public function __construct($superRole) {
        $this->superRole = $superRole;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name', 'text', array(
            'required' => false,
            'mapped' => false,
            'label' => 'Name',
            'attr' => array('class' => 'vd_name_required registration-input')));
        
        $builder->add('userName', 'text', array(
            'required' => true,
            'mapped' => false,
            'label' => 'username',
            'attr' => array('class' => 'vd_name_required registration-input')));
        
        $builder->add('email', 'email', array(
            'attr' => array('class' => 'vd_email_required registration-input'), 
            'required' => true,
            'mapped' => false));
        
        $builder->add('mobileNumber', 'text', array(
            'required' => false, 
            'mapped' => false,
            'attr' => array('class' => 'vd_mobile_required  registration-input', 'maxlength' => 10)));
        
        if ($this->superRole) {
            $builder->add('role', 'choice', array(
                'choices' => array(''=>'--Select A Role--','ROLE_SM_SUPERUSER' => 'Superuser', 'ROLE_SM_ADMIN' => 'Admin', 'ROLE_SM_USER' => 'Normal User'),
                'label' => 'User Role',
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
            ));
        } else {
            $builder->add('role', 'choice', array(
                'choices' => array(''=>'--Select A Role--','ROLE_SM_ADMIN' => 'Admin', 'ROLE_SM_USER' => 'Normal User'),
                'label' => 'User Role',
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
            ));
        }
        
        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'options' => array('attr' => array('class' => 'password-field  registration-input')),
            'required' => true,
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
            'mapped' => false,
        ));
        
        $builder->add('reset', 'reset', array('attr' => array('class' => 'registration-submit')));
        $builder->add('submit', 'submit', array('label' => 'Register', 'attr' => array('class' => ' registration-submit')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AkjnBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'AkjnReg';
    }

}
