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
class AttendenceController extends Controller {

    public function studentsAttendenceManageAction() {
        $form = $this->createFormBuilder()
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
                ->add('date', 'text', array(
                    'label' => 'Date',
                    'attr' => array('class' => 'form-control')
                ))
                ->getForm();
        return $this->render('AdminBundle:MainAdmin/Attendence:studentsAttendenceManage.html.twig', array('form' => $form->createView()));
    }

    public function listStudentAttendenceAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $class = $request->request->get('classId');
        $division = $request->request->get('division');
        $date = $request->request->get('date');
        $dateTime = new \DateTime($date);

        $students = $em->getRepository("AdminBundle:MasterStudents")->findBy(array("class" => $class, 'division' => $division));
        $classObj = $em->getRepository("AdminBundle:ClassRoom")->findOneById($class);
        $attendenceTableInClass = $classObj->getClassAttendenceTable();

        if ($attendenceTableInClass) {
            $atendenceTableName = $em->getRepository("AdminBundle:" . $attendenceTableInClass . "")->findBy(array('date' => $dateTime));
            if ($atendenceTableName) {
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();
                $attendenceDetails = $qb->select('a')
                        ->from('AdminBundle:' . $attendenceTableInClass . '', 'a')
                        ->innerJoin('AdminBundle:MasterStudents', 's', 'WITH', 'a.student = s.id')
                        ->where('a.date = :date')
                        ->andWhere('s.class = :class')
                        ->andWhere('s.division = :division')
                        ->orderBy('s.studentsName', 'ASC')
                        ->setParameters(array('date' => $dateTime, 'class' => $class, 'division' => $division))
                        ->getQuery()
                        ->getResult();
                return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Attendence:attendenceDetailsList.html.twig', array('attendenceDetails' => $attendenceDetails,'attendenceTable'=>$attendenceTableInClass)));
            } else {
                $entityName = '\AdminBundle\Entity\\' . $attendenceTableInClass;
                foreach ($students as $student) {
                    $attendenceEntity = $em->getClassMetadata($entityName)->newInstance();
                    $attendenceEntity->setStudent($student);
                    $attendenceEntity->setDate($dateTime);
                    $attendenceEntity->setAttendence(true);
                    $em->persist($attendenceEntity);
                }
                $em->flush();
                $this->selectAttendenceDataAfterPersist($attendenceTableInClass, $dateTime, $class, $division);
            }
        } else {
            return new JsonResponse('<h4 class="alert alert-info"> DB Error please contact administrator !!! </h4>');
        }
    }

    private function selectAttendenceDataAfterPersist($attendenceTableInClass, $dateTime, $class, $division) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $attendenceDetails = $qb->select('a')
                ->from('AdminBundle:' . $attendenceTableInClass . '', 'a')
                ->innerJoin('AdminBundle:MasterStudents', 's', 'WITH', 'a.student = s.id')
                ->where('a.date = :date')
                ->andWhere('s.class = :class')
                ->andWhere('s.division = :division')
                ->orderBy('s.studentsName', 'ASC')
                ->setParameters(array('date' => $dateTime, 'class' => $class, 'division' => $division))
                ->getQuery()
                ->getResult();
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Attendence:attendenceDetailsList.html.twig', array('attendenceDetails' => $attendenceDetails, 'attendenceTable'=>$attendenceTableInClass)));
    }
    
    public function absentPresentStudentAction(Request $request,$id,$attendence){
        $em = $this->getDoctrine()->getManager();
        $flag = $request->request->get('flag');
        $atendence = $em->getRepository("AdminBundle:" . $attendence . "")->findOneById($id);
        if($flag == 'on'){
            $atendence->setAttendence(true);
        }else{
            $atendence->setAttendence(false);
        }
        $em->persist($atendence);
        $em->flush();
        return new JsonResponse(true);
    }

}
