<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/10/17
 * Time: 2:21 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="annual_sales_ranges")
 */
class AnnualSalesRanges
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
    private $salesRange;

    /**
     * AnnualSalesRanges constructor.
     */
    public function __construct()
    {
        $this->company = new ArrayCollection();
    }

    /**
     * AnnualSalesRanges constructor.
     */

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
    public function getSalesRange()
    {
        return $this->salesRange;
    }

    /**
     * @param mixed $salesRange
     */
    public function setSalesRange($salesRange)
    {
        $this->salesRange = $salesRange;
    }

    /**
     * @return ArrayCollection|Company[]
     */
    public function getCompany()
    {
        return $this->company;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company", mappedBy="totalAnnualSales", cascade={"persist"})
     */
    private $company;

}