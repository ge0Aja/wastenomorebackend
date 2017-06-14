<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{

    /**
     * @Route("/CompanyRecords",name="CompanyRecords")
     */
    public function getCompanyRecords()
    {
       /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $companyRecords = $em->getRepository('AppBundle:Company')->findAll();
        return $this->render("agriApp/Company/companyRecordsInJson.html.twig", ['companyRecords' => $companyRecords]);
    }


    /**
     * @Route("/Companies",name="CompanyRecordsPage")
     */
    public function CompanyRecords()
    {
        return $this->render('agriApp/Company/companyRecords.html.twig');
    }

    /**
     * @Route("/deleteCompany/{id}", name="deleteCompany")
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
}
