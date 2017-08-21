<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/19/17
 * Time: 1:01 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_question_answer")
 */
class SurveyQuestionAnswer
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $details;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\DDlMenuSubType", inversedBy="QAnswer")
     */
    private $dropdownanswer;

    /**
     * @return mixed
     */
    public function getDropdownanswer()
    {
        return $this->dropdownanswer;
    }

    /**
     * @param mixed $dropdownanswer
     */
    public function setDropdownanswer($dropdownanswer)
    {
        $this->dropdownanswer = $dropdownanswer;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SurveyQuestion", inversedBy="questionAnswer")
     */
    private $question;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="surveyAnswerCompany")
     */
    private $company;

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
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
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
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
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }


    /**
     * @ORM\Column(type="string")
     */
    private $timestamp;


}