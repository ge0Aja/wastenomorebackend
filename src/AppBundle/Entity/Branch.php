<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 1:11 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="branch")
 */
class Branch
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
    private  $location;

    /**
     * @ORM\Column(type="string")
     */
    private $staff_count;

    /**
     * @ORM\Column(type="string")
     */
    private $opening_date;

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

    /**
     * @return mixed
     */
    public function getStaffCount()
    {
        return $this->staff_count;
    }

    /**
     * @param mixed $staff_count
     */
    public function setStaffCount($staff_count)
    {
        $this->staff_count = $staff_count;
    }

    /**
     * @return mixed
     */
    public function getOpeningDate()
    {
        return $this->opening_date;
    }

    /**
     * @param mixed $opening_date
     */
    public function setOpeningDate($opening_date)
    {
        $this->opening_date = $opening_date;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="Branch", cascade={"persist"})
     */

    private $Company;

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->Company;
    }

    /**
     * @param mixed $Company
     */
    public function setCompany($Company)
    {
        $this->Company = $Company;
    }

}