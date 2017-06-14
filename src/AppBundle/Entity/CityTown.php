<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/12/17
 * Time: 1:12 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="city_town")
 */
class CityTown
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
     * CityTown constructor.
     */
    public function __construct()
    {
        $this->branch_location = new ArrayCollection();
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
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\District", inversedBy="city_twon")
     */
    private $district;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Branch", mappedBy="location", cascade={"persist"})
     */
    private $branch_location;

    /**
     * @return ArrayCollection|Branch[]
     */
    public function getBranchLocation()
    {
        return $this->branch_location;
    }

}