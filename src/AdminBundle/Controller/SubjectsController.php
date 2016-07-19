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
class SubjectsController extends Controller {

    public function manageSubjectsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('masters_add_subjects'))
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
                ->add('save', 'submit', array('label' => 'Add Subjects', 'attr' => array('class' => 'btn btn-success')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $class = $form['class']->getData();
            $division = $form['division']->getData();
            return $this->redirect($this->generateUrl('add_subject_table', array('class' => $class->getId(), 'division' => $division->getId())));
        }
        return $this->render('AdminBundle:MainAdmin/Subjects:subjectsManage.html.twig', array('form' => $form->createView()));
    }

    public function redirectSubjectsLinkAction($class, $division) {
        $em = $this->getDoctrine()->getManager();
        $classRoomSelected = $em->getRepository("AdminBundle:ClassRoom")->findOneById($class);
        $divisionSelected = $em->getRepository("AdminBundle:Division")->findOneById($division);
        $subjects = $em->getRepository("AdminBundle:Subjects")->findAll();
        $subjectsLink = $em->getRepository("AdminBundle:SubjectClassLink")->findBy(array('classId' => $class, 'divisionId' => $division));
        return $this->render('AdminBundle:MainAdmin/Subjects:linkSubjectsToClass.html.twig', array(
                    'subjects' => $subjects,
                    'subjectsLinks' => $subjectsLink,
                    'class' => $classRoomSelected,
                    'division' => $divisionSelected));
    }

    public function addSubjectsToClassAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $class = $request->request->get('class');
        $division = $request->request->get('division');
        $classRoomSelected = $em->getRepository("AdminBundle:ClassRoom")->findOneById($class);
        $divisionSelected = $em->getRepository("AdminBundle:Division")->findOneById($division);

        $subjects = $em->getRepository("AdminBundle:Subjects")->findAll();
        $subjectsLink = $em->getRepository("AdminBundle:SubjectClassLink")->findBy(array('classId' => $class, 'divisionId' => $division));
        return $this->render('AdminBundle:MainAdmin/Subjects:linkSubjectsToClass.html.twig', array(
                    'subjects' => $subjects,
                    'subjectsLinks' => $subjectsLink,
                    'class' => $classRoomSelected,
                    'division' => $divisionSelected));
    }

    public function addNewSubjectToMasterAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $subjectName = $request->request->get('subject');
        $subjectNameUpper = strtoupper($subjectName);
        $subjectNameUpperTrimd = str_replace(' ', '', $subjectNameUpper);


        $subjects = $em->getRepository("AdminBundle:Subjects")->findAll();
        foreach ($subjects as $subject) {
            $existingSubjectLower = $subject->getSubjectName();
            $existingSubjectUpper = strtoupper($existingSubjectLower);
            $existingSubjectUpperTrimd = str_replace(' ', '', $existingSubjectUpper);
            if ($subjectNameUpperTrimd === $existingSubjectUpperTrimd) {
                return new JsonResponse(false);
            }
        }
        $newSubject = new \AdminBundle\Entity\Subjects();
        $newSubject->setSubjectName($subjectName);
        $em->persist($newSubject);
        $em->flush();
        return new JsonResponse(true);
    }

    public function subjectsListMasterTableAction($classId, $dividionId) {
        $em = $this->getDoctrine()->getManager();
        $subjects = $em->getRepository("AdminBundle:Subjects")->findAll();
        $subjectsLinks = $em->getRepository("AdminBundle:SubjectClassLink")->findBy(array('classId' => $classId, 'divisionId' => $dividionId));

        if ($subjects) {
            foreach ($subjects as $subject) {
                $masterSubject[] = $subject->getId();
            }
        } else {
            $masterSubject = array();
        }

        if ($subjectsLinks) {
            foreach ($subjectsLinks as $subjectsLink) {
                $existingSubjects[] = $subjectsLink->getSubjectId()->getId();
            }
        } else {
            $existingSubjects = array();
        }
        $subjectsToPassIds = array_diff($masterSubject, $existingSubjects);
        if (count($subjectsToPassIds) != 0) {
            foreach ($subjectsToPassIds as $subjectsToPassId) {
                $mainSub = $em->getRepository("AdminBundle:Subjects")->findOneById($subjectsToPassId);
                $subjectsToPass[] = $mainSub;
            }
        } else {
            $subjectsToPass = array();
        }
        return $this->render('AdminBundle:MainAdmin/Subjects:subjectsListTable.html.twig', array(
                    'subjectsToPass' => $subjectsToPass,
                    'class' => $classId,
                    'actionFlag' => 'leftTable',
                    'division' => $dividionId));
    }

    public function subjectsListLinkTableAction($classId, $dividionId) {
        $em = $this->getDoctrine()->getManager();
        $subjectsLinksArray = $em->getRepository("AdminBundle:SubjectClassLink")->findBy(array('classId' => $classId, 'divisionId' => $dividionId));
        if ($subjectsLinksArray) {
            foreach ($subjectsLinksArray as $subjectsLinkArray) {
                $subjectsLinks[] = $subjectsLinkArray->getSubjectId();
            }
        } else {
            $subjectsLinks = array();
        }
        return $this->render('AdminBundle:MainAdmin/Subjects:subjectsListTable.html.twig', array(
                    'subjectsToPass' => $subjectsLinks,
                    'class' => $classId,
                    'actionFlag' => 'rightTable',
                    'division' => $dividionId));
    }

    public function linkFromMasterToClassAction(Request $request, $classId, $dividionId) {
        $em = $this->getDoctrine()->getManager();
        $subjectsIds = $request->request->get('subjects');
        $class = $em->getRepository("AdminBundle:ClassRoom")->findOneById($classId);
        $division = $em->getRepository("AdminBundle:Division")->findOneById($dividionId);
        foreach ($subjectsIds as $subjectsId) {
            $subject = $em->getRepository("AdminBundle:Subjects")->findOneById($subjectsId);
            $subjectLink = new \AdminBundle\Entity\SubjectClassLink();
            $subjectLink->setClassId($class);
            $subjectLink->setDivisionId($division);
            $subjectLink->setSubjectId($subject);
            $em->persist($subjectLink);
        }
        $em->flush();
        return new JsonResponse(true);
    }

    public function removeSubjectClassLinkAction($classId, $dividionId, $subjectId) {
        $em = $this->getDoctrine()->getManager();
        $link = $em->getRepository("AdminBundle:SubjectClassLink")->findOneBy(array('classId' => $classId, 'divisionId' => $dividionId, 'subjectId' => $subjectId));
        $em->remove($link);
        $em->flush();
        return $this->redirect($this->generateUrl('add_subject_table', array('class' => $classId, 'division' => $dividionId)));
//        return new JsonResponse(true);
    }

}
