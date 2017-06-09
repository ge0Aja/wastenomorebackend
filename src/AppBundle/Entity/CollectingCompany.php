<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/9/17
 * Time: 2:41 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="collecting_company")
 */
class CollectingCompany
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste",mappedBy="collectingCompany",cascade={"persist"})
     */
    private $wastes;


    public function __construct()
    {
        $this->wastes= new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Waste[]
     */
    public function getWastes()
    {
        return $this->wastes;
    }
}