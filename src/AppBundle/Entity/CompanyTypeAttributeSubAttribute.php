<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 5:19 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_type_attribute_sub_attribute")
 */
class CompanyTypeAttributeSubAttribute
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyTypeAttribute", inversedBy="sub_attribute")
     */

    private $company_type_attribute;

    /**
     * CompanyTypeAttributeSubAttribute constructor.
     */
    public function __construct()
    {
        $this->company_attributes_and_subattributes = new ArrayCollection();
        $this->subAttributeValues = new ArrayCollection();
    }

    /**
     * @return mixed
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyAttributesAndSubAttributes", mappedBy="sub_attribute", cascade={"persist"})
     */
    private $company_attributes_and_subattributes;

    /**
     * @return ArrayCollection|CompanyAttributesAndSubAttributes[]
     */
    public function getCompanyAttributesAndSubattributes()
    {
        return $this->company_attributes_and_subattributes;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubAttributeValues",mappedBy="subAttribute",cascade={"persist"})
     */
    private $subAttributeValues;

    /**
     * @return ArrayCollection|SubAttributeValues[]
     */
    public function getSubAttributeValues()
    {
        return $this->subAttributeValues;
    }

}