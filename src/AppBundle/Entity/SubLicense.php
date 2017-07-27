<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 7/27/17
 * Time: 11:21 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sub_license")
 */
class SubLicense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $subLicenseString;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\License", inversedBy="licenseSubLicense")
     */
    private $License;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", mappedBy="SubLicense", cascade={"persist"})
     */
    private $SubLicenseUser;

    /**
     * @return mixed
     */
    public function getSubLicenseString()
    {
        return $this->subLicenseString;
    }

    /**
     * @param mixed $subLicenseString
     */
    public function setSubLicenseString($subLicenseString)
    {
        $this->subLicenseString = $subLicenseString;
    }

    /**
     * @return mixed
     */
    public function getLicense()
    {
        return $this->License;
    }

    /**
     * @param mixed $License
     */
    public function setLicense($License)
    {
        $this->License = $License;
    }

    /**
     * @return mixed
     */
    public function getSubLicenseUser()
    {
        return $this->SubLicenseUser;
    }

    /**
     * @param mixed $SubLicenseUser
     */
    public function setSubLicenseUser($SubLicenseUser)
    {
        $this->SubLicenseUser = $SubLicenseUser;
    }

    /**
     * @return mixed
     */
    public function getSubLicenseBranch()
    {
        return $this->SubLicenseBranch;
    }

    /**
     * @param mixed $SubLicenseBranch
     */
    public function setSubLicenseBranch($SubLicenseBranch)
    {
        $this->SubLicenseBranch = $SubLicenseBranch;
    }


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Branch", mappedBy="BranchSubLicense", cascade={"persist"})
     */
    private $SubLicenseBranch;

}