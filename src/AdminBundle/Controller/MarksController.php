<?php

namespace AdminBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TomJerryBundle\Interfaces\AuditableControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use TomJerryBundle\Controller\uploaderHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of AttendenceController
 *
 * @author akhil
 */
class MarksController extends Controller {

    public function manageMarksAction(Request $request) {
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('students_marks_manage'))
                ->setMethod("POST")
                ->add('class', 'entity', array(
                    'class' => 'AdminBundle:ClassRoom',
                    'property' => 'classNum',
                    'empty_value' => 'Select Class',
                    'label' => 'Class:',
                    'attr' => array('class' => 'form-control')
                ))
                ->add('division', 'entity', array(
                    'class' => 'AdminBundle:Division',
                    'property' => 'division',
                    'empty_value' => 'Select Division',
                    'label' => 'Division:',
                    'attr' => array('class' => 'form-control')
                ))
                ->add('save', 'submit', array('label' => 'Add Marks', 'attr' => array('class' => 'btn btn-success')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $class = $form['class']->getData();
            $division = $form['division']->getData();
            return $this->redirect($this->generateUrl('add_marks_to_class', array('class' => $class->getId(), 'division' => $division->getId())));
        }
        return $this->render('AdminBundle:MainAdmin/Marks:marksManage.html.twig', array('form' => $form->createView()));
    }

    public function addMarksAction(Request $request, $class, $division) {
        $em = $this->getDoctrine()->getManager();
        $classRoomSelected = $em->getRepository("AdminBundle:ClassRoom")->findOneById($class);
        $divisionSelected = $em->getRepository("AdminBundle:Division")->findOneById($division);
        $subjects = $em->getRepository("AdminBundle:SubjectClassLink")->findBy(array('classId' => $class, 'divisionId' => $division));
        $students = $em->getRepository("AdminBundle:MasterStudents")->findBy(array('class' => $class, 'division' => $division),array('studentsName' => 'ASC'));
        return $this->render('AdminBundle:MainAdmin/Marks:addMarks.html.twig', array(
                    'students' => $students,
                    'subjects' => $subjects,
                    'class' => $classRoomSelected,
                    'division' => $divisionSelected));
    }

}
