<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/12/17
 * Time: 1:42 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="recycling_company")
 */
class RecyclingCompany
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
     * @return mixed
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param mixed $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @return mixed
     */
    public function getPickupService()
    {
        return $this->pickup_service;
    }

    /**
     * @param mixed $pickup_service
     */
    public function setPickupService($pickup_service)
    {
        $this->pickup_service = $pickup_service;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $material;

    /**
     * @ORM\Column(type="string")
     */
    private $pickup_service;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District", inversedBy="recycling_company")
     */
    private $location;

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}