<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/19/17
 * Time: 5:43 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ddl_menu_type")
 */
class DDlMenuType
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
     * DDlMenuType constructor.
     */
    public function __construct()
    {
        $this->subType = new ArrayCollection();
        $this->surveyQuestion = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\DDlMenuSubType", mappedBy="type", cascade={"persist"})
     */
    private $subType;

    /**
     * @return ArrayCollection|DDlMenuSubType[]
     */
    public function getSubType()
    {
        return $this->subType;
    }


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SurveyQuestion", mappedBy="dropdowntable", cascade={"persist"})
     */
    private $surveyQuestion;

    /**
     * @return ArrayCollection|SurveyQuestion[]
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
    }


}