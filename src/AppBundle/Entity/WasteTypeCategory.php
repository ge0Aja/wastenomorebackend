<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 3:18 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Monolog\Handler\NewRelicHandlerTest;

/**
 * @ORM\Entity
 * @ORM\Table(name="waste_type_category")
 */
class WasteTypeCategory
{
    public function __construct()
    {
        $this->sub_category = new ArrayCollection();
    }

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\WasteTypeCategorySubCategory", mappedBy="category_type", cascade={"persist"})
     */
    private $sub_category;

    /**
     * @return ArrayCollection|WasteTypeCategorySubCategory[]
     */
    public function getSubCategory()
    {
        return $this->sub_category;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteType", inversedBy="waste_type_category", cascade={"persist"})
     */
    private $waste_type;

    /**
     * @return mixed
     */
    public function getWasteType()
    {
        return $this->waste_type;
    }

    /**
     * @param mixed $waste_type
     */
    public function setWasteType($waste_type)
    {
        $this->waste_type = $waste_type;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste", mappedBy="waste_type_category", cascade={"persist"})
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
}