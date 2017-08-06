<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/21/17
 * Time: 3:24 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_role")
 */
class AppRole
{
    CONST COMPANY_MANAGER = "COMPANY_MANAGER";
    CONST BRANCH_MANAGER = "BRANCH_MANAGER";
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * AppRole constructor.
     */
    public function __construct()
    {
        $this->roleUser = new ArrayCollection();
        $this->RoleSubLicense = new ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="appRole", cascade={"persist"})
     */
    private $roleUser;

    /**
     * @return ArrayCollection|User[]
     */
    public function getRoleUser()
    {
        return $this->roleUser;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubLicense", mappedBy="SubLicenseAppRole", cascade={"persist"})
     */
    private $RoleSubLicense;

    /**
     * @return ArrayCollection|SubLicense[]
     */
    public function getRoleSubLicense()
    {
        return $this->RoleSubLicense;
    }

}