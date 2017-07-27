<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/21/17
 * Time: 3:06 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="license")
 */
class License
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
    private $license;


    /**
     * @ORM\Column(type="integer")
     */
    private $userCount;


    /**
     * @ORM\Column(type="integer")
     */
    private $premium;

    /**
     * @return mixed
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * @param mixed $premium
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;
    }

    /**
     * License constructor.
     */
    public function __construct()
    {
        $this->licenseSubLicense = new ArrayCollection();
        $this->Company = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUserCount()
    {
        return $this->userCount;
    }

    /**
     * @param mixed $userCount
     */
    public function setUserCount($userCount)
    {
        $this->userCount = $userCount;
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
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @param mixed $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubLicense", mappedBy="License", cascade={"persist"})
     */
    private $licenseSubLicense;


    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }


    /**
     * @return ArrayCollection|SubLicense[]
     */
    public function getLicenseSubLicense()
    {
        return $this->licenseSubLicense;
    }

    /**
     * @return ArrayCollection|SubLicense[]
     */
    public function getLicenseSubLicenseUsed()
    {
        return $this->licenseSubLicense->filter(function ($entry){
            return $entry->getUsed() == 1;
        });
    }

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param mixed $expiryDate
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
    }

    /**
     * @ORM\Column(type="date")
     */
    private $expiryDate;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company", mappedBy="companyLicense", cascade={"persist"});
     */
    private $Company;

    /**
     * @return ArrayCollection|Company[]
     */
    public function getCompany()
    {
        return $this->Company;
    }
}