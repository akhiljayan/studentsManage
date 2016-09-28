<?php

namespace AkjnBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'label' => 'Name', 
            'mapped' => false,
            'attr' => array('class' => 'form-control')));
        $builder->add('username', 'text', array(
            'label' => 'Username', 
            'mapped' => false,
            'attr' => array('class' => 'form-control')));
        $builder->add('email', 'text', array(
            'label' => 'Email', 
            'mapped' => false,
            'attr' => array('class' => 'form-control')));
        $builder->add('mobile', 'text', array(
            'label' => 'Mobile Number', 
            'mapped' => false,
            'attr' => array('class' => 'form-control mobile-number')));
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
            'options' => array('attr' => array('class' => 'password-field form-control')),
            'required' => true,
            'label' => 'Password',
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Confirm Password'),
            'mapped' => false,
            'attr' => array('class' => 'form-control'),
        ));
        $builder->add('save', 'submit', array('attr' => array('class' => 'btn btn-success')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AkjnBundle\Entity\User'
        ));
    }

}
