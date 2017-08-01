<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{

    /**
     * @Route("/cms/CompanyRecords",name="CompanyRecords")
     */
    public function getCompanyRecords()
    {
       /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $companyRecords = $em->getRepository('AppBundle:Company')->findAll();
        return $this->render("agriApp/Company/companyRecordsInJson.html.twig", ['companyRecords' => $companyRecords]);
    }


    /**
     * @Route("/cms/Companies",name="CompanyRecordsPage")
     */
    public function CompanyRecords()
    {
        return $this->render('agriApp/Company/companyRecords.html.twig');
    }

    /**
     * @Route("/cms/deleteCompany/{id}", name="deleteCompany")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyRecord = $em->getRepository('AppBundle:Company')->find($id);
            $em->remove($companyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route(path="api/getCompanyTypesApi", name="getCompanyTypesApi")
     */
    public function getCompanyTypes() {
        $em = $this->getDoctrine()->getManager();
        $company_types = array();
        $CompanyTypes = $em->getRepository('AppBundle:CompanyType')->findAll();

        foreach ($CompanyTypes as $type){
            $company_types[$type->getId()] = $type->getTypeName();
        }
        return new JsonResponse($company_types);
    }


}
