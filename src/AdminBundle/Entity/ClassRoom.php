<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class
 *
 * @ORM\Table(name="classRoom")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ClassRoomRepository")
 */
class ClassRoom
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
     * @var int
     *
     * @ORM\Column(name="classNum", type="integer")
     */
    private $classNum;

    /**
     * @var string
     *
     * @ORM\Column(name="classString", type="string", length=25)
     */
    private $classString;

    /**
     * @var string
     *
     * @ORM\Column(name="classAttendenceTable", type="string", length=50)
     */
    private $classAttendenceTable;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set classNum
     *
     * @param integer $classNum
     *
     * @return Class
     */
    public function setClassNum($classNum)
    {
        $this->classNum = $classNum;

        return $this;
    }

    /**
     * Get classNum
     *
     * @return int
     */
    public function getClassNum()
    {
        return $this->classNum;
    }

    /**
     * Set classString
     *
     * @param string $classString
     *
     * @return Class
     */
    public function setClassString($classString)
    {
        $this->classString = $classString;

        return $this;
    }

    /**
     * Get classString
     *
     * @return string
     */
    public function getClassString()
    {
        return $this->classString;
    }

    /**
     * Set classAttendenceTable
     *
     * @param string $classAttendenceTable
     *
     * @return Class
     */
    public function setClassAttendenceTable($classAttendenceTable)
    {
        $this->classAttendenceTable = $classAttendenceTable;

        return $this;
    }

    /**
     * Get classAttendenceTable
     *
     * @return string
     */
    public function getClassAttendenceTable()
    {
        return $this->classAttendenceTable;
    }
}

