<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 5:17 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_type_attribute")
 */
class CompanyTypeAttribute
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
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyType", inversedBy="company_type_attribute")
     */
    private $company_type;

    /**
     * CompanyTypeAttribute constructor.
     */
    public function __construct()
    {
        $this->sub_attribute = new ArrayCollection();
        $this->company_attributes_and_subattributes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCompanyType()
    {
        return $this->company_type;
    }

    /**
     * @param mixed $company_type
     */
    public function setCompanyType($company_type)
    {
        $this->company_type = $company_type;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyTypeAttributeSubAttribute", mappedBy="company_type_attribute")
     */
    private $sub_attribute;

    /**
     * @return ArrayCollection|CompanyTypeAttributeSubAttribute[]
     */
    public function getSubAttribute()
    {
        return $this->sub_attribute;
    }

    /**
     * @param mixed $sub_attribute
     */
    public function setSubAttribute($sub_attribute)
    {
        $this->sub_attribute = $sub_attribute;
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyAttributesAndSubAttributes", mappedBy="attribute", cascade={"persist"})
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