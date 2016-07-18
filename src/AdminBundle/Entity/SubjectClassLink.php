<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubjectClassLink
 *
 * @ORM\Table(name="subject_class_link")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\SubjectClassLinkRepository")
 */
class SubjectClassLink
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
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\ClassRoom")
     * @ORM\JoinColumn(name="classId", referencedColumnName="id")
     */
    private $classId;
    
    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Division")
     * @ORM\JoinColumn(name="divisionId", referencedColumnName="id")
     */
    private $divisionId;

    /**
     * @ORM\ManyToOne(targetEntity="AdminBundle\Entity\Subjects")
     * @ORM\JoinColumn(name="subjectId", referencedColumnName="id")
     */
    private $subjectId;



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
     * Set classId
     *
     * @param \AdminBundle\Entity\ClassRoom $classId
     *
     * @return SubjectClassLink
     */
    public function setClassId(\AdminBundle\Entity\ClassRoom $classId = null)
    {
        $this->classId = $classId;

        return $this;
    }

    /**
     * Get classId
     *
     * @return \AdminBundle\Entity\ClassRoom
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * Set divisionId
     *
     * @param \AdminBundle\Entity\Division $divisionId
     *
     * @return SubjectClassLink
     */
    public function setDivisionId(\AdminBundle\Entity\Division $divisionId = null)
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * Get divisionId
     *
     * @return \AdminBundle\Entity\Division
     */
    public function getDivisionId()
    {
        return $this->divisionId;
    }

    /**
     * Set subjectId
     *
     * @param \AdminBundle\Entity\Subjects $subjectId
     *
     * @return SubjectClassLink
     */
    public function setSubjectId(\AdminBundle\Entity\Subjects $subjectId = null)
    {
        $this->subjectId = $subjectId;

        return $this;
    }

    /**
     * Get subjectId
     *
     * @return \AdminBundle\Entity\Subjects
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }
}
