<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecyclingCompanyController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/RecyclingCompanyRecords",name="RecyclingCompanyRecords")
     */
    public function getRecyclingCompaniesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $RecyclingCompanyRecords = $em->getRepository('AppBundle:RecyclingCompany')->findAll();
        return $this->render("agriApp/RecyclingCompany/RecyclingCompanyRecordsInJson.html.twig", ['RecyclingCompanyRecords' => $RecyclingCompanyRecords]);
    }


    /**
     * @Route("/RecyclingCompanies",name="RecyclingCompanies")
     */
    public function RecyclingCompaniesRecords()
    {
        return $this->render('agriApp/RecyclingCompany/recyclingCompanies.html.twig');
    }


    /**
     * @Route("/deleteRecyclingCompany/{id}", name="deleteRecyclingCompany")
     */
    public function DeleteLogAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $RecyclingCompanyRecord = $em->getRepository('AppBundle:RecyclingCompany')->find($id);
            $em->remove($RecyclingCompanyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
