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
 * Description of StudentsController
 *
 * @author akhil
 */
class StudentsController extends Controller {

    public function mastersManageAddStudentsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('masters_add_students'))
                ->setMethod("POST")
                ->add('numberOfStudents', 'text', array(
                    'label' => 'Number of students:',
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
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
                ->add('save', 'submit', array('attr' => array('class' => 'btn btn-success')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $numbStudents = $form['numberOfStudents']->getData();
            $class = $form['class']->getData();
            $division = $form['division']->getData();

            for ($i = 1; $i <= $numbStudents; $i++) {
                $studentsName = $_POST['student_name_' . $i];
                $admissionNumber = $_POST['admission_number_' . $i];
                $fathersName = $_POST['fathers_name_' . $i];
                $mothersName = $_POST['mothers_name_' . $i];
                $mobileNumber = $_POST['mobile_number_' . $i];
                $gender = $_POST['gender_' . $i];
                $dob = $_POST['dob_' . $i];

                $student = new \AdminBundle\Entity\MasterStudents();
                $student->setStudentsName($studentsName);
                $student->setDob(new \DateTime($dob));
                $student->setAdmissionNumber($admissionNumber);
                $student->setFathersName($fathersName);
                $student->setMothersName($mothersName);
                $student->setParentsMobNumber($mobileNumber);
                $student->setClass($class);
                $student->setDivision($division);
                $student->setGender($gender);
                $em->persist($student);
                $em->flush();
            }
            $this->get('session')->getFlashBag()->add('success', 'Record(s) of ' . $numbStudents . ' students added in ' . $class->getClassNum() . '.' . $division->getDivision() . ' class.');
            return $this->render('AdminBundle:MainAdmin:index.html.twig');
        }
        return $this->render('AdminBundle:MainAdmin/Students:manageAddStudents.html.twig', array('form' => $form->createView()));
    }

    public function studentsGenerateMultipleFormAction(Request $request) {
        $numbStudents = $request->request->get('numStu');
        $view = $this->renderView('AdminBundle:MainAdmin/Students:multipleStudentForm.html.twig', array('numStu' => $numbStudents));
        $result = array('view' => $view, 'numb' => $numbStudents);
        return new JsonResponse($result);
    }

    public function studentsDetailsListAction() {
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
                ->getForm();
        return $this->render('AdminBundle:MainAdmin/Students:studentsListDetailsForm.html.twig', array('form' => $form->createView()));
    }

    public function studentsDetailsTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $class = $request->request->get('classId');
        $division = $request->request->get('division');
        $students = $em->getRepository("AdminBundle:MasterStudents")->findBy(array('class' => $class, 'division' => $division), array('studentsName' => 'ASC'));
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Students:studentsListDetailsTable.html.twig', array('students' => $students)));
    }

    public function studentsDetailsEditAction(Request $request, $guId) {
        $em = $this->getDoctrine()->getManager();
        $studentsEntity = $em->getRepository("AdminBundle:MasterStudents")->findOneByGuId($guId);
        $studentsType = new \AdminBundle\Form\MasterStudentsType();
        $form = $this->createForm($studentsType, $studentsEntity, array("action" => $this->generateUrl("edit_student_details", array('guId' => $guId))));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($studentsEntity);
            $em->flush();

            $form2 = $this->createFormBuilder()
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
                    ->getForm();
            return $this->render('AdminBundle:MainAdmin/Students:studentsListDetailsForm.html.twig', array('form' => $form2->createView()));
        }
        return new JsonResponse($this->renderView('AdminBundle:MainAdmin/Students:studentsListDetailsEdit.html.twig', array('form' => $form->createView())));
    }

    public function ulpoadCsvFormAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cmd = $em->getClassMetadata("AdminBundle:MasterStudents");
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('add_students_import_from_csv'))
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
                ->add('csvFile', 'file', array(
                    'label' => "Upload your CSV File : "))
                ->add('submit', 'submit', array("label" => "Submit", "attr" => array('class' => 'btn btn-success')))
                ->getForm();
        $count = 0;
        $strStatus = '';
        $err = array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $class = $form['class']->getData();
            $division = $form['division']->getData();
            $errFlag = FALSE;
            $fileUploaded = $form['csvFile']->getData();
            if (!$fileUploaded->isValid()) {
                $errFlag = TRUE;
                $err[] = 'Somethong went wrong!!';
            }
            $arrSupportFormat = array("text/plain", "text/comma-separated-values",);
            if (!in_array($fileUploaded->getMimeType(), $arrSupportFormat)) {
                $errFlag = TRUE;
                $err[] = "unsupported file formate";
            }
            $dir = sys_get_temp_dir();
            $form['csvFile']->getData()->move($dir, "release.csv");

            $file = fopen("$dir/release.csv", "r");
            $keys = fgetcsv($file);
            if (!$errFlag && !in_array("admissionNumber", $keys) ||
                    !in_array("studentsName", $keys) ||
                    !in_array("address", $keys) ||
                    !in_array("fathersName", $keys) ||
                    !in_array("mothersName", $keys) ||
                    !in_array("parentsMobNumber", $keys) ||
                    !in_array("landPhoneNumber", $keys) ||
                    !in_array("parentsEMail", $keys) ||
                    !in_array("gender", $keys) ||
                    !in_array("dob", $keys)) {
                $err[] = "heading row in your csv file is wrong..";
                $errFlag = TRUE;
            }
            $arrPics = '';
            if (!$errFlag) {
                while (!feof($file)) {
                    $row = fgetcsv($file);
                    if (is_array($row) && count($row) == count($keys)) {
                        $fileRow[$count] = array_combine($keys, $row);
                        $arrPics[] = $fileRow[$count]['admissionNumber'];
                        $objStudents = new \AdminBundle\Entity\MasterStudents();

                        $objStudents->setClass($class);
                        $objStudents->setDivision($division);
                        $objStudents->setAdmissionNumber($fileRow[$count]['admissionNumber']);
                        $objStudents->setStudentsName($fileRow[$count]['studentsName']);
                        $objStudents->setAddress($fileRow[$count]['address']);
                        $objStudents->setFathersName($fileRow[$count]['fathersName']);
                        $objStudents->setMothersName($fileRow[$count]['mothersName']);
                        $objStudents->setParentsMobNumber($fileRow[$count]['parentsMobNumber']);
                        $objStudents->setLandPhoneNumber($fileRow[$count]['landPhoneNumber']);
                        $objStudents->setParentsEMail($fileRow[$count]['parentsEMail']);
                    if ($fileRow[$count]['gender'] == 'Male' || $fileRow[$count]['gender'] == 'male'|| $fileRow[$count]['gender'] == 'M' || $fileRow[$count]['gender'] == "m") {
                            $objStudents->setGender("M");
                        } elseif ($fileRow[$count]['gender'] == 'Female' || $fileRow[$count]['gender'] == 'female'|| $fileRow[$count]['gender'] == 'F' || $fileRow[$count]['gender'] == "f") {
                            $objStudents->setGender("F");
                        }
                        $objStudents->setDob(new \DateTime($fileRow[$count]['dob']));
                        $em->persist($objStudents);
                        $count++;
                    }
                }
                $em->flush();
                $strStatus = "ERROR";
            } else {
                $strStatus = "SUCCESS";
            }
            fclose($file);
            unlink($dir . '/release.csv');
            return $this->redirect($this->generateUrl('list_students_details'));
        }
//        $rstNabard = $this->getReleaseForConfirmation();

        return new JsonResponse($this->renderView("AdminBundle:MainAdmin/Students:uploadCsv.html.twig", array(
                    'form' => $form->createView(),
//                    'nabardRelease' => $rstNabard,
                    'status' => $strStatus,
                    'errorMessage' => $err
        )));
    }

}
