<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyTypeController extends Controller
{
    /**
     * @Route("/CompanyTypes",name="CompanyTypes")
     */
    public function getCompanyTypes()
    {
        return $this->render('agriApp/CompanyType/companyTypeRecords.html.twig');
    }

    /**
     * @Route("/CompanyTypeRecords", name="CompanyTypeRecords")
     */
    public function getCompanyTypeRecords(){
        $em = $this->getDoctrine()->getManager();
        $companyTypeRecords = $em->getRepository('AppBundle:CompanyType')->findAll();
        return $this->render("agriApp/CompanyType/companyTypeRecordsInJson.html.twig", ['companyTypeRecords' => $companyTypeRecords]);
    }


    /**
     * @Route("/deleteCompanyType/{id}", name="deleteCompanyType")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeRecord = $em->getRepository('AppBundle:CompanyType')->find($id);
            $em->remove($companyTypeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
