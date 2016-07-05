<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterStudentsType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('address', 'textarea', array(
            'attr' => array('class' => 'form-control'),
            'required' => false,
            'label' => 'Address'
        ));
        $builder->add('landPhoneNumber', 'text', array(
            'attr' => array('class' => 'form-control'),
            'required' => false,
            'label' => 'Land Phone Number'
        ));
        $builder->add('parentsEMail', 'text', array(
            'attr' => array('class' => 'form-control'),
            'required' => false,
            'label' => 'Parents Email'
        ));
        $builder->add('admissionNumber', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => 'Admission Number'
        ));
        $builder->add('studentsName', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => 'Students Name'
        ));
        $builder->add('fathersName', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => 'Fathers Name'
        ));
        $builder->add('mothersName', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => 'Mothers Name'
        ));
        $builder->add('parentsMobNumber', 'text', array(
            'attr' => array('class' => 'form-control'),
            'label' => 'Parents Mobile No'
        ));
        $builder->add('class', 'entity', array(
            'class' => 'AdminBundle:ClassRoom',
            'property' => 'classNum',
            'empty_value' => 'Select Class',
            'disabled' => true,
            'label' => 'Class:',
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('division', 'entity', array(
            'class' => 'AdminBundle:Division',
            'property' => 'division',
            'disabled' => true,
            'empty_value' => 'Select Division',
            'label' => 'Division:',
            'attr' => array('class' => 'form-control')
        ));
        $builder->add('gender', 'choice', [
            'choices' => array('M' => 'M', 'F' => 'F'),
            'empty_value' => '-Gender-',
            'attr' => array('class' => 'form-control')
        ]);
        $builder->add('save', 'submit', array('attr' => array('class' => 'btn btn-success')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\MasterStudents'
        ));
    }

}
