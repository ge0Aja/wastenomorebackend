<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/8/17
 * Time: 12:43 PM
 */

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company")
 */
class Company
{


    public function __construct()
    {
        $this->Branch = new ArrayCollection();
        $this->company_attributes_and_subattributes = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyType", inversedBy="Company")
     */
    private $type;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDateOfEstablishment()
    {
        return $this->dateOfEstablishment;
    }

    /**
     * @param mixed $dateOfEstablishment
     */
    public function setDateOfEstablishment($dateOfEstablishment)
    {
        $this->dateOfEstablishment = $dateOfEstablishment;
    }

    /**
     * @return mixed
     */
    public function getMainBranchLocation()
    {
        return $this->mainBranchLocation;
    }

    /**
     * @param mixed $mainBranchLocation
     */
    public function setMainBranchLocation($mainBranchLocation)
    {
        $this->mainBranchLocation = $mainBranchLocation;
    }

    /**
     * @return mixed
     */
    public function getTotalAnnualSales()
    {
        return $this->totalAnnualSales;
    }

    /**
     * @param mixed $totalAnnualSales
     */
    public function setTotalAnnualSales($totalAnnualSales)
    {
        $this->totalAnnualSales = $totalAnnualSales;
    }

    /**
     * @ORM\Column(type="date")
     */
    private $dateOfEstablishment;

    /**
     * @ORM\Column(type="string")
     */
    private $mainBranchLocation;

    /**
     * @ORM\Column(type="string")
     */
    private $totalAnnualSales;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Branch", mappedBy="Company", cascade={"persist"})
     */
    private $Branch;

    /**
     * @return ArrayCollection|Branch[]
     */
    public function getBranch()
    {
        return $this->Branch;
    }

    /**
     * @param mixed $Branch
     */
    public function setBranch($Branch)
    {
        $this->Branch = $Branch;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyAttributesAndSubAttributes", mappedBy="company", cascade={"persist"})
     */
    private $company_attributes_and_subattributes;

    /**
     * @return ArrayCollection|CompanyAttributesAndSubAttributes[]
     */
    public function getCompanyAttributesAndSubattributes()
    {
        return $this->company_attributes_and_subattributes;
    }
}