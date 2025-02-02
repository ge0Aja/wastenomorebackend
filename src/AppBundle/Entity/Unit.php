<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 3:58 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="unit")
 */
class Unit
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
     * Unit constructor.
     */
    public function __construct()
    {
        $this->conversionT = new ArrayCollection();
        $this->subcatunit = new ArrayCollection();
        $this->purchaseUnit = new ArrayCollection();
        $this->waste = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste", mappedBy="unit", cascade={"persist"})
     */
    private $waste;

    /**
     * @return ArrayCollection|Waste[]
     */
    public function getWaste()
    {
        return $this->waste;
    }


    /**
     * @return ArrayCollection|Conversion[]
     */
    public function getConversionT()
    {
        return $this->conversionT;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Conversion", mappedBy="unit", cascade={"persist"})
     */
    private $conversionT;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SubCategoryUnit", mappedBy="unit", cascade={"persist"})
     */
    private $subcatunit;

    /**
     * @return ArrayCollection|SubCategoryUnit[]
     */
    public function getSubcatunit()
    {
        return $this->subcatunit;
    }



    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Purchases", mappedBy="unit", cascade={"persist"})
     */
    private $purchaseUnit;

    /**
     * @return ArrayCollection|Purchases[]
     */
    public function getPurchaseUnit()
    {
        return $this->purchaseUnit;
    }

}