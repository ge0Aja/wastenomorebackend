<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/9/17
 * Time: 2:41 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reason")
 */
class Reason
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
    private $reason;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste", mappedBy="reason", cascade={"persist"})
     */
    private $waste;

    /**
     * Reason constructor.
     */
    public function __construct()
    {
        $this->waste = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Waste[]
     */
    public function getWaste()
    {
        return $this->waste;
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
}