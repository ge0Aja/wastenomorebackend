<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 3:14 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="waste")
 */
class Waste
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CollectingCompany",inversedBy="wastes")
     */
    private $collectingCompany;

    /**
     * @return mixed
     */
    public function getCollectingCompany()
    {
        return $this->collectingCompany;
    }

    /**
     * @param mixed $collectingCompany
     */
    public function setCollectingCompany($collectingCompany)
    {
        $this->collectingCompany = $collectingCompany;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Reason", inversedBy="waste")
     */
    private $reason;

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteType", inversedBy="waste")
     */
    private $waste_type;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategory", inversedBy="waste")
     */
    private $waste_type_category;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategorySubCategory", inversedBy="waste")
     */
    private $waste_type_subcategory;

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
     * @return WasteType
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
     * @return WasteTypeCategory
     */
    public function getWasteTypeCategory()
    {
        return $this->waste_type_category;
    }

    /**
     * @param mixed $waste_type_category
     */
    public function setWasteTypeCategory($waste_type_category)
    {
        $this->waste_type_category = $waste_type_category;
    }

    /**
     * @return WasteTypeCategorySubCategory
     */
    public function getWasteTypeSubcategory()
    {
        return $this->waste_type_subcategory;
    }

    /**
     * @param mixed $waste_type_subcategory
     */
    public function setWasteTypeSubcategory($waste_type_subcategory)
    {
        $this->waste_type_subcategory = $waste_type_subcategory;
    }

    /**
     * @return DateTime
     */
    public function getWasteDate()
    {
        return $this->waste_date;
    }

    /**
     * @param mixed $waste_date
     */
    public function setWasteDate($waste_date)
    {
        $this->waste_date = $waste_date;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Unit $unit
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
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @ORM\Column(type="date")
     */
    private $waste_date;

    /**
     * @ORM\Column(type="string")
     */
    private $quantity;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Unit", inversedBy="waste")
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Branch", inversedBy="waste")
     */
    private $branch;


    /**
     * @ORM\Column(type="string")
     */
    private $timestamp;

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

}