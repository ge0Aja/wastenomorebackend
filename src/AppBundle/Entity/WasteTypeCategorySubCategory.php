<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/8/17
 * Time: 3:24 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="waste_type_category_sub_category")
 */
class WasteTypeCategorySubCategory
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
    public function getCategoryType()
    {
        return $this->category_type;
    }

    /**
     * @param mixed $category_type
     */
    public function setCategoryType($category_type)
    {
        $this->category_type = $category_type;
    }


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WasteTypeCategory", inversedBy="sub_category", cascade={"persist"})
     */
    private $category_type;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Waste", mappedBy="waste_type_subcategory", cascade={"persist"})
     */
    private $waste;

    /**
     * @return mixed
     */
    public function getWaste()
    {
        return $this->waste;
    }

    /**
     * @param mixed $waste
     */
    public function setWaste($waste)
    {
        $this->waste = $waste;
    }
}