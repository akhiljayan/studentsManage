<?php

namespace AkjnBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;

/**
 * @ORM\Entity
 * @ORM\Table(name="admin_users")
 *
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="integer",length=1, options={ "default"=0 })
     */
    protected $attempted = 0;

    /**
     * @ORM\Column(type="smallint",name="is_logged", options={ "default"=0 })
     */
    protected $isLogged = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     *
     * @ORM\Column(type="datetime",name="attempted_at", nullable=true)
     */
    protected $attemptedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="mobile", type="integer")
     */
    private $mobile;

    public function __construct() {
        parent::__construct();
        // your own logic
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function setCredentialsExpireAt(\DateTime $date = null) {
        $date = new \DateTime('now');
        $date->add(new \DateInterval('P60D'));
        $this->credentialsExpireAt = $date;

        return $this;
    }

    /**
     * Set attempted
     *
     * @param integer $attempted
     * @return User
     */
    public function setAttempted($attempted) {
        if (is_null($attempted)) {
            $attempted = 0;
        }
        $this->attempted = $attempted;

        return $this;
    }

    /**
     * Get attempted
     *
     * @return integer
     */
    public function getAttempted() {
        return $this->attempted;
    }

    /**
     * Set attemptedAt
     *
     * @param \DateTime $attemptedAt
     * @return User
     */
    public function setAttemptedAt($attemptedAt = null) {
        $dt = new \DateTime('now');
        $this->attemptedAt = $dt;

        return $this;
    }

    /**
     * Get attemptedAt
     *
     * @return \DateTime
     */
    public function getAttemptedAt() {
        return $this->attemptedAt;
    }

    /**
     * Set is_logged
     *
     * @param smallint $isLogged
     * @return User
     */
    public function setIsLogged($islogged = 1) {
        $this->isLogged = $islogged;

        return $this;
    }

    /**
     * Get login_flag
     *
     * @return integer
     */
    public function getIsLogged() {
        return $this->isLogged;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Name
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set mobile
     *
     * @param integer $mobile
     *
     * @return User
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return integer
     */
    public function getMobile() {
        return $this->mobile;
    }

//    public function getRoles() {
//        parent::getRoles();
//    }
//
//    public function hasRole($role) {
//        parent::hasRole($role);
//    }

}
