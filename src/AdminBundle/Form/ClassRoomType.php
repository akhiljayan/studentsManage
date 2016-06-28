<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassRoomType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('classNum', 'text', array('label' => 'Class', 'attr' => array('class' => 'form-control')));
        $builder->add('classString', 'text', array('label' => 'Class in words', 'attr' => array('class' => 'form-control')));
        $builder->add('roomNumber', 'text', array('label' => 'Room Number', 'attr' => array('class' => 'form-control')));
        $builder->add('classAttendenceTable', 'text', array('required' => false, 'label' => 'Attendence table name', 'attr' => array('class' => 'form-control')));
        $builder->add('save', 'submit', array('attr' => array('class' => 'btn btn-success')));
//        $builder->add('reset', 'reset', array('attr' => array('class' => 'form-control btn btn-danger')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\ClassRoom'
        ));
    }

}
