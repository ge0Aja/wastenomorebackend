<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/8/17
 * Time: 5:11 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="purchases")
 */
class Purchases
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategory",inversedBy="purchases")
     */
    private $category;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategorySubCategory",inversedBy="purchases")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $estimateOfCost;

    /**
     * @ORM\Column(type="string")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Branch",inversedBy="purchases")
     */
    private $branch;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getEstimateOfCost()
    {
        return $this->estimateOfCost;
    }

    /**
     * @param mixed $estimateOfCost
     */
    public function setEstimateOfCost($estimateOfCost)
    {
        $this->estimateOfCost = $estimateOfCost;
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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @ORM\Column(type="date")
     */
    private $date;
}