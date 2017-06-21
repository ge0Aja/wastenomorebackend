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
     * @return CompanyTypeAttributeSubAttribute
     */
    public function getSubAttribute()
    {
        return $this->subAttribute;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CompanyTypeAttributeSubAttribute",inversedBy="subAttributeValues")
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