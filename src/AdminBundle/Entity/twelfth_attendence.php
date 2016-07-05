<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * twelfth_attendence
 *
 * @ORM\Table(name="twelfth_attendence")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\twelfth_attendenceRepository")
 */
class twelfth_attendence
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
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\MasterStudents")
     * @ORM\JoinColumn(name="student", referencedColumnName="id")
     */
    private $student;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="attendence", type="boolean")
     */
    private $attendence;



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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return twelfth_attendence
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set attendence
     *
     * @param boolean $attendence
     *
     * @return twelfth_attendence
     */
    public function setAttendence($attendence)
    {
        $this->attendence = $attendence;

        return $this;
    }

    /**
     * Get attendence
     *
     * @return boolean
     */
    public function getAttendence()
    {
        return $this->attendence;
    }

    /**
     * Set student
     *
     * @param \AdminBundle\Entity\MasterStudents $student
     *
     * @return twelfth_attendence
     */
    public function setStudent(\AdminBundle\Entity\MasterStudents $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \AdminBundle\Entity\MasterStudents
     */
    public function getStudent()
    {
        return $this->student;
    }
}
