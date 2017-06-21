<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/21/17
 * Time: 9:40 AM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sub_attribute_values")
 */
class SubAttributeValues
{

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubAttributeValues",mappedBy="subAttribute",cascade={"persist"})
     */
    private $subAttributeValues;

    public function __construct()
    {
        $this->subAttributeValues = new ArrayCollection();
    }

    /**
     * @return CompanyTypeAttributeSubAttribute
     */
    public function getSubAttribute()
    {
        return $this->subAttribute;
    }




    /**
     * @return ArrayCollection|SubAttributeValues[]
     */
    public function getSubAttributeValues()
    {
        return $this->subAttributeValues;
    }


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubAttributeValues",inversedBy="")
     */
    private $subAttribute;

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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $value;
}