<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/9/17
 * Time: 3:15 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_attributes_and_sub_attributes")
 */
class CompanyAttributesAndSubAttributes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="company_attributes_and_subattributes")
     */
    private $company;

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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return mixed
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyTypeAttribute", inversedBy="company_attributes_and_subattributes")
     */
    private $attribute;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyTypeAttributeSubAttribute", inversedBy="company_attributes_and_subattributes")
     */
    private $sub_attribute;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubAttributeValues", inversedBy="attrSubAttrRecord")
     */
    private $subAttrVal;

    /**
     * @return mixed
     */
    public function getSubAttrVal()
    {
        return $this->subAttrVal;
    }

    /**
     * @param mixed $subAttrVal
     */
    public function setSubAttrVal($subAttrVal)
    {
        $this->subAttrVal = $subAttrVal;
    }
}