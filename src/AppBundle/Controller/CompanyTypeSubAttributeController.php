<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyTypeSubAttributeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/CompanyTypeSubAttributes",name="CompanyTypeSubAttributes")
     */
    public function CompanyTypeSubAttributes()
    {
        return $this->render('agriApp/TypeSubAttribute/companyTypeSubAttributeRecords.html.twig');
    }

    /**
     * @Route("/CompanyTypeSubAttributesRecords", name="CompanyTypeSubAttributesRecords")
     */
    public function getCompanyTypeSubAttributeRecords(){
        $em = $this->getDoctrine()->getManager();
        $companyTypeSubAttributeRecords = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->findAll();
        return $this->render("agriApp/TypeSubAttribute/companyTypeSubAttributeRecordsInJson.html.twig", ['companyTypeSubAttributeRecords' => $companyTypeSubAttributeRecords]);
    }


    /**
     * @Route("/deleteCompanyTypeSubAttribute/{id}", name="deleteCompanyTypeSubAttribute")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeSubAttributeRecord = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($id);
            $em->remove($companyTypeSubAttributeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }



}
