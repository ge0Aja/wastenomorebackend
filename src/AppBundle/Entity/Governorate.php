<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/12/17
 * Time: 1:05 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="governorates")
 */
class Governorate
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
     * Governorate constructor.
     */
    public function __construct()
    {
        $this->district = new ArrayCollection();
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
     * @return ArrayCollection|District[]
     */
    public function getDistrict()
    {
        return $this->district;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\District", mappedBy="governorate", cascade={"persist"})
     */
    private $district;


}