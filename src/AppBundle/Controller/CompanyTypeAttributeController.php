<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyTypeAttributeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/CompanyTypeAttributes",name="CompanyTypeAttributes")
     */
    public function CompanyTypeAttributes()
    {
        return $this->render('agriApp/CompanyTypeAttribute/companyTypeAttributesRecords.html.twig');
    }

    /**
     * @Route("/CompanyTypeAttributesRecords", name="CompanyTypeAttributesRecords")
     */
    public function getCompanyTypeAttributeRecords(){
        $em = $this->getDoctrine()->getManager();
        $companyTypeAttributeRecords = $em->getRepository('AppBundle:CompanyTypeAttribute')->findAll();
        return $this->render("agriApp/CompanyTypeAttribute/companyTypeAttributeRecordsInJson.html.twig", ['companyTypeAttributeRecords' => $companyTypeAttributeRecords]);
    }


    /**
     * @Route("/deleteCompanyTypeAttribute/{id}", name="deleteCompanyTypeAttribute")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeAttributeRecord = $em->getRepository('AppBundle:CompanyTypeAttribute')->find($id);
            $em->remove($companyTypeAttributeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
