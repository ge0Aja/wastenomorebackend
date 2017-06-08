<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/8/17
 * Time: 12:52 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Collator\Collator;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_type")
 */
class CompanyType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    public function __construct()
    {
        $this->Company = new ArrayCollection();
        $this->company_type_attribute = new ArrayCollection();
    }


    /**
     * @ORM\Column(type="string")
     */
    private $typeName;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company", mappedBy="type", cascade={"persist"})
     */
    private $Company;

    /**
     * @return ArrayCollection|Company[]
     */
    public function getCompany()
    {
        return $this->Company;
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
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param mixed $typeName
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyTypeAttribute", mappedBy="company_type", cascade={"persist"})
     */
    private $company_type_attribute;

    /**
     * @return ArrayCollection|CompanyTypeAttribute[]
     */
    public function getCompanyTypeAttribute()
    {
        return $this->company_type_attribute;
    }

    /**
     * @param mixed $company_type_attribute
     */
    public function setCompanyTypeAttribute($company_type_attribute)
    {
        $this->company_type_attribute = $company_type_attribute;
    }

}