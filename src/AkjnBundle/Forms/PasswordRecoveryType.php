<?php

namespace AkjnBundle\Forms;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordRecoveryType extends AbstractType {
    
   
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('dob', 'date', array('required' => false, 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'attr' => array('class' => 'vd_date_required', 'maxlength' => 10)));
        $builder->add('email', 'email', array('attr' => array('class' => 'vd_email_required'), 'required' => true));
        $builder->add('securityQuestion', 'entity', array('class' => 'AkjnBundle:SecurityQuestion', 'property' => 'Question', 'empty_value' => "Select Question", 'attr' => array('class' => 'required')));
        $builder->add('securityAnswer', 'text', array('attr' => array('class' => 'vd_name_required')));
        $builder->add('submit', 'submit', array( 'attr' => array('class' => 'ui-button','value' => 'Recover Now')));
        
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
        return 'AkjnUserPasswordRecovery';
    }
    
}
