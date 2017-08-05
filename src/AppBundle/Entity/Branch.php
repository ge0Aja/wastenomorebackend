<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 1:11 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="branch")
 */
class Branch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CityTown", inversedBy="branch_location")
     */
    private  $location;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Purchases",mappedBy="branch",cascade={"persist"})
     */
    private $purchases;

    /**
     * @ORM\Column(type="integer")
     */
    private $staff_count;

    /**
     * @ORM\Column(type="date")
     */
    private $opening_date;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubLicense", mappedBy="SubLicenseBranch", cascade={"persist"})
     */
    private $BranchSubLicense;

    /**
     * @return ArrayCollection|SubLicense[]
     */
    public function getBranchSubLicense()
    {
        return $this->BranchSubLicense;
    }


    /**
     * Branch constructor.
     */
    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->surveyAnswerBranch = new ArrayCollection();
        $this->branchUser = new ArrayCollection();
        $this->BranchSubLicense = new ArrayCollection();
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getStaffCount()
    {
        return $this->staff_count;
    }

    /**
     * @param mixed $staff_count
     */
    public function setStaffCount($staff_count)
    {
        $this->staff_count = $staff_count;
    }

    /**
     * @return mixed
     */
    public function getOpeningDate()
    {
        return $this->opening_date;
    }

    /**
     * @param mixed $opening_date
     */
    public function setOpeningDate($opening_date)
    {
        $this->opening_date = $opening_date;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="Branch", cascade={"persist"})
     */

    private $Company;

    /**
     * @return ArrayCollection|Purchases[]
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @param mixed $purchases
     */
    public function setPurchases($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->Company;
    }

    /**
     * @param mixed $Company
     */
    public function setCompany($Company)
    {
        $this->Company = $Company;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste", mappedBy="branch", cascade={"persist"})
     */
    private $waste;

    /**
     * @return mixed
     */
    public function getWaste()
    {
        return $this->waste;
    }

    /**
     * @param mixed $waste
     */
    public function setWaste($waste)
    {
        $this->waste = $waste;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SurveyQuestionAnswer", mappedBy="branch", cascade={"persist"})
     */
    private $surveyAnswerBranch;

    /**
     * @return ArrayCollection|SurveyQuestionAnswer[]
     */
    public function getSurveyAnswerBranch()
    {
        return $this->surveyAnswerBranch;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="companyBranch", cascade={"persist"})
     */
    private $branchUser;

    /**
     * @return ArrayCollection|User[]
     */
    public function getBranchUser()
    {
        return $this->branchUser;
    }

    /**
     * @param mixed $branchUser
     */
    public function setBranchUser($branchUser)
    {
        $this->branchUser = $branchUser;
    }


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Company", mappedBy="mainBranch")
     */
    private $CompMainBranch;
}