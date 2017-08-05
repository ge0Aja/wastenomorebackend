<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/19/17
 * Time: 12:57 PM
 */

namespace AppBundle\Entity;



use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_question")
 */
class SurveyQuestion
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
    private $question;

    /**
     * SurveyQuestion constructor.
     */
    public function __construct()
    {
        $this->questionAnswer = new ArrayCollection();
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
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SurveyQuestionAnswer", mappedBy="question", cascade={"persist"})
     */
    private $questionAnswer;

    /**
     * @return ArrayCollection|SurveyQuestionAnswer
     */
    public function getQuestionAnswer()
    {
        return $this->questionAnswer;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $questionwithdropdown;

    /**
     * @return mixed
     */
    public function getQuestionwithdropdown()
    {
        return $this->questionwithdropdown;
    }

    /**
     * @param mixed $questionwithdropdown
     */
    public function setQuestionwithdropdown($questionwithdropdown)
    {
        $this->questionwithdropdown = $questionwithdropdown;
    }

    /**
     * @return mixed
     */
    public function getQuestionwithdetails()
    {
        return $this->questionwithdetails;
    }

    /**
     * @param mixed $questionwithdetails
     */
    public function setQuestionwithdetails($questionwithdetails)
    {
        $this->questionwithdetails = $questionwithdetails;
    }

    /**
     * @return mixed
     */
    public function getDetailshint()
    {
        return $this->detailshint;
    }

    /**
     * @param mixed $detailshint
     */
    public function setDetailshint($detailshint)
    {
        $this->detailshint = $detailshint;
    }

    /**
     * @return mixed
     */
    public function getDropdowntable()
    {
        return $this->dropdowntable;
    }

    /**
     * @param mixed $dropdowntable
     */
    public function setDropdowntable($dropdowntable)
    {
        $this->dropdowntable = $dropdowntable;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $questionwithdetails;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $detailshint;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DDlMenuType", inversedBy="surveyQuestion")
     */
    private $dropdowntable;


}