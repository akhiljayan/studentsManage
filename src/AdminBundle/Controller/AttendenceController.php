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
        $today = new \DateTime("now");

        $students = $em->getRepository("AdminBundle:MasterStudents")->findBy(array("class" => $class, 'division' => $division));
        $classObj = $em->getRepository("AdminBundle:ClassRoom")->findOneById($class);
        $attendenceTableInClass = $classObj->getClassAttendenceTable();

        if ($attendenceTableInClass) {
            if ($dateTime <= $today || $dateTime->format('dd-mm-yyy') == $today->format('dd-mm-yyy')) {
                if ($dateTime->format('dd-mm-yyy') == $today->format('dd-mm-yyy')) {
                    $dateFlag = "equal";
                } else {
                    $dateFlag = "less";
                }

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
                    return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Attendence:attendenceDetailsList.html.twig', array('attendenceDetails' => $attendenceDetails, 'attendenceTable' => $attendenceTableInClass, 'dateFlag' => $dateFlag, 'datetime' => $dateTime)));
                } else {
                    $entityName = '\AdminBundle\Entity\\' . $attendenceTableInClass;
                    foreach ($students as $student) {
                        $attendenceEntity = $em->getClassMetadata($entityName)->newInstance();
                        $attendenceEntity->setStudent($student);
                        $attendenceEntity->setDate($dateTime);
                        $attendenceEntity->setAttendence(true);
                        $attendenceEntity->setConfirmationFlag(false);
                        $em->persist($attendenceEntity);
                    }
                    $em->flush();
//                $this->selectAttendenceDataAfterPersist($attendenceTableInClass, $dateTime, $class, $division);
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
                    return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Attendence:attendenceDetailsList.html.twig', array('attendenceDetails' => $attendenceDetails, 'attendenceTable' => $attendenceTableInClass, 'dateFlag' => $dateFlag, 'datetime' => $dateTime)));
                }
            } else {
                return new JsonResponse('<h4 class="alert alert-danger"> Cannot select future date for adding attendence !!! </h4>');
            }
        } else {
            return new JsonResponse('<h4 class="alert alert-info"> Attendence Facility not activated for this class. Please contact administrator to activate !!! </h4>');
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
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Attendence:attendenceDetailsList.html.twig', array('attendenceDetails' => $attendenceDetails, 'attendenceTable' => $attendenceTableInClass, 'datetime' => $dateTime)));
    }

    public function absentPresentStudentAction(Request $request, $id, $attendence) {
        $em = $this->getDoctrine()->getManager();
        $flag = $request->request->get('flag');
        $atendence = $em->getRepository("AdminBundle:" . $attendence . "")->findOneById($id);
        if ($flag == 'on') {
            $atendence->setAttendence(true);
        } else {
            $atendence->setAttendence(false);
        }
        $em->persist($atendence);
        $em->flush();
        return new JsonResponse(true);
    }

    public function confirmSendSmsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $date = $request->request->get('date');
        $attable = $request->request->get('attable');
        $dateTime = new \DateTime($date);
        $atendence = $em->getRepository("AdminBundle:" . $attable . "")->findBy(array('date' => $dateTime));
        foreach ($atendence as $att) {
            $att->setConfirmationFlag(true);
            $em->persist($att);
        }
        $em->flush();

        $absentees = $em->getRepository("AdminBundle:" . $attable . "")->findBy(array('date' => $dateTime, 'attendence' => false));
        foreach ($absentees as $absent) {
            $username = "kapmsg";
            $pass = "kap@user!123";

            $dest_mobileno = $absent->getStudent()->getParentsMobNumber();
            if ($absent->getStudent()->getGender() == 'M') {
                $sms = "For your kind information.. Your son" . $absent->getStudent()->getStudentsName() . " was absent in his class on the " . $date . "";
            } else {
                $sms = "For your kind information.. Your dauter" . $absent->getStudent()->getStudentsName() . " was absent in her class on the " . $date . "";
            }
            $senderid = "257147";

            $postvars = "username=" . $username . "&password=" . $pass . "&senderid=" . $senderid . "&dest_mobileno=" . $dest_mobileno . "&message=" . $sms . "";

            $url = sprintf("http://123.63.33.43/blank/sms/user/urlsmstemp.php?username=%s&pass=%s&senderid=%s&dest_mobileno=%s&message=%s&mtype=UNI&response=Y", $username, $pass, $senderid, $dest_mobileno, urlencode($sms));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, '3');
            $content = trim(curl_exec($ch));
            curl_close($ch);
        }

        return new JsonResponse(true);
    }

}
