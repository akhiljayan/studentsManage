<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MasterStudents
 *
 * @ORM\Table(name="master_students")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\MasterStudentsRepository")
 */
class MasterStudents
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="guId", type="string", length=32)
     */
    private $guId;

    /**
     * @var string
     *
     * @ORM\Column(name="admissionNumber", type="string", length=50)
     */
    private $admissionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="studentsName", type="string", length=50)
     */
    private $studentsName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1)
     */
    private $gender;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ClassRoom")
     * @ORM\JoinColumn(name="class", referencedColumnName="id")
     */
    private $class;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Division")
     * @ORM\JoinColumn(name="division", referencedColumnName="id")
     */
    private $division;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="fathersName", type="string", length=50)
     */
    private $fathersName;

    /**
     * @var string
     *
     * @ORM\Column(name="mothersName", type="string", length=50)
     */
    private $mothersName;

    /**
     * @var int
     *
     * @ORM\Column(name="parentsMobNumber", type="integer")
     */
    private $parentsMobNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="landPhoneNumber", type="integer", nullable=true)
     */
    private $landPhoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="parentsEMail", type="string", length=50, nullable=true)
     */
    private $parentsEMail;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="datetime")
     */
    private $dob;
    
    public function __construct() {
        $this->guId = md5(uniqid(php_uname('n')));
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set guId
     *
     * @param string $guId
     *
     * @return MasterStudents
     */
    public function setGuId($guId)
    {
        $this->guId = $guId;

        return $this;
    }

    /**
     * Get guId
     *
     * @return string
     */
    public function getGuId()
    {
        return $this->guId;
    }

    /**
     * Set admissionNumber
     *
     * @param string $admissionNumber
     *
     * @return MasterStudents
     */
    public function setAdmissionNumber($admissionNumber)
    {
        $this->admissionNumber = $admissionNumber;

        return $this;
    }

    /**
     * Get admissionNumber
     *
     * @return string
     */
    public function getAdmissionNumber()
    {
        return $this->admissionNumber;
    }

    /**
     * Set studentsName
     *
     * @param string $studentsName
     *
     * @return MasterStudents
     */
    public function setStudentsName($studentsName)
    {
        $this->studentsName = $studentsName;

        return $this;
    }

    /**
     * Get studentsName
     *
     * @return string
     */
    public function getStudentsName()
    {
        return $this->studentsName;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return MasterStudents
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set fathersName
     *
     * @param string $fathersName
     *
     * @return MasterStudents
     */
    public function setFathersName($fathersName)
    {
        $this->fathersName = $fathersName;

        return $this;
    }

    /**
     * Get fathersName
     *
     * @return string
     */
    public function getFathersName()
    {
        return $this->fathersName;
    }

    /**
     * Set mothersName
     *
     * @param string $mothersName
     *
     * @return MasterStudents
     */
    public function setMothersName($mothersName)
    {
        $this->mothersName = $mothersName;

        return $this;
    }

    /**
     * Get mothersName
     *
     * @return string
     */
    public function getMothersName()
    {
        return $this->mothersName;
    }

    /**
     * Set parentsMobNumber
     *
     * @param integer $parentsMobNumber
     *
     * @return MasterStudents
     */
    public function setParentsMobNumber($parentsMobNumber)
    {
        $this->parentsMobNumber = $parentsMobNumber;

        return $this;
    }

    /**
     * Get parentsMobNumber
     *
     * @return integer
     */
    public function getParentsMobNumber()
    {
        return $this->parentsMobNumber;
    }

    /**
     * Set landPhoneNumber
     *
     * @param integer $landPhoneNumber
     *
     * @return MasterStudents
     */
    public function setLandPhoneNumber($landPhoneNumber)
    {
        $this->landPhoneNumber = $landPhoneNumber;

        return $this;
    }

    /**
     * Get landPhoneNumber
     *
     * @return integer
     */
    public function getLandPhoneNumber()
    {
        return $this->landPhoneNumber;
    }

    /**
     * Set parentsEMail
     *
     * @param string $parentsEMail
     *
     * @return MasterStudents
     */
    public function setParentsEMail($parentsEMail)
    {
        $this->parentsEMail = $parentsEMail;

        return $this;
    }

    /**
     * Get parentsEMail
     *
     * @return string
     */
    public function getParentsEMail()
    {
        return $this->parentsEMail;
    }

    /**
     * Set class
     *
     * @param \AdminBundle\Entity\ClassRoom $class
     *
     * @return MasterStudents
     */
    public function setClass(\AdminBundle\Entity\ClassRoom $class = null)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return \AdminBundle\Entity\ClassRoom
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set division
     *
     * @param \AdminBundle\Entity\Division $division
     *
     * @return MasterStudents
     */
    public function setDivision(\AdminBundle\Entity\Division $division = null)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return \AdminBundle\Entity\Division
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return MasterStudents
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return MasterStudents
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }
}
