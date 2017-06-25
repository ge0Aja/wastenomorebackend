<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/22/17
 * Time: 9:06 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="conversion")
 */
class Conversion
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategorySubCategory", inversedBy="conversionT")
     */
    private $subcategory;

    /**
     * @ORM\Column(type="string")
     */
    private $quan;

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
    public function getSubcategory()
    {
        return $this->subcategory;
    }

    /**
     * @param mixed $subcategory
     */
    public function setSubcategory($subcategory)
    {
        $this->subcategory = $subcategory;
    }

    /**
     * @return mixed
     */
    public function getQuan()
    {
        return $this->quan;
    }

    /**
     * @param mixed $quan
     */
    public function setQuan($quan)
    {
        $this->quan = $quan;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return mixed
     */
    public function getQuanInKg()
    {
        return $this->quanInKg;
    }

    /**
     * @param mixed $quanInKg
     */
    public function setQuanInKg($quanInKg)
    {
        $this->quanInKg = $quanInKg;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Unit", inversedBy="conversionT")
     */
    private $unit;

    /**
     * @ORM\Column(type="string")
     */
    private $quanInKg;

}