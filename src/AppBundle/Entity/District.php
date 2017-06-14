<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/12/17
 * Time: 1:07 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="district")
 */
class District
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * District constructor.
     */
    public function __construct()
    {
        $this->city_twon = new ArrayCollection();
        $this->collecting_company = new ArrayCollection();
        $this->recycling_company = new ArrayCollection();
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
     * @return mixed
     */
    public function getGovernorate()
    {
        return $this->governorate;
    }

    /**
     * @param mixed $governorate
     */
    public function setGovernorate($governorate)
    {
        $this->governorate = $governorate;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Governorate" , inversedBy="district")
     */
    private $governorate;

    /**
     * @return ArrayCollection|CityTown[]
     */
    public function getCityTwon()
    {
        return $this->city_twon;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CityTown", mappedBy="district", cascade={"persist"})
     */
    private $city_twon;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CollectingCompany", mappedBy="location", cascade={"persist"})
     */
    private $collecting_company;

    /**
     * @return ArrayCollection|CollectingCompany[]
     */
    public function getCollectingCompany()
    {
        return $this->collecting_company;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RecyclingCompany", mappedBy="location", cascade={"persist"})
     */
    private $recycling_company;

    /**
     * @return ArrayCollection|RecyclingCompany[]
     */
    public function getRecyclingCompany()
    {
        return $this->recycling_company;
    }
}