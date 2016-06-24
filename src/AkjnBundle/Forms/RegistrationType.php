<?php

namespace AkjnBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('displayName', 'text', array('required' => false, 'attr' => array('class' => 'vd_name_required')));
        $builder->add('dob', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'vd_date_required', 'maxlength' => 10)));
        $builder->add('email', 'email', array('attr' => array('class' => 'vd_email_required'), 'required' => false));
        $builder->add('mobileNumber', 'text', array('required' => false, 'attr' => array('class' => 'vd_mobile_required', 'maxlength' => 10)));
        $builder->add('password', 'password', array('required' => false, 'attr' => array('class' => 'vd_name_required')));
        $builder->add('checkpassword', 'password', array('mapped' => false, 'required' => false));
        $builder->add('securityQuestion', 'entity', array('class' => 'AkjnBundle:SecurityQuestion', 'property' => 'Question', 'empty_value' => "Select Question", 'attr' => array('class' => 'required')));
        $builder->add('securityAnswer', 'text', array('attr' => array('class' => 'vd_name_required')));
        $builder->add('captcha', 'captcha');
        $builder->add('reset', 'reset', array('attr' => array('class' => 'btn btn-default')));
        $builder->add('submit', 'button', array('label' => 'Register', 'attr' => array('class' => 'btn btn-primary')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AkjnBundle\Entity\Registration'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'AkjnReg';
    }

}
