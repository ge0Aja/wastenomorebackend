<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CompanyController extends Controller
{

    /**
     * @Route("/CompanyRecords",name="CompanyRecords")
     */
    public function getCompanyRecords()
    {
        return $this->render('agriApp/companyRecords.html.twig');
    }
    /**
     * @Route("/CompanyTypes",name="CompanyTypes")
     */
    public function getCompanyTypes()
    {

    }
    /**
     * @Route("/CompanyAttributes",name="CompanyAttributes")
     */
    public function CompanyAttributes()
    {

    }
    /**
     * @Route("/CompanySubAttributes",name="CompanySubAttributes")
     */
    public function CompanySubAttributes()
    {

    }

}
