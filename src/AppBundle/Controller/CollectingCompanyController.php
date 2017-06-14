<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CollectingCompanyController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/CollectingCompanyRecords",name="CollectingCompanyRecords")
     */
    public function getCollectingCompaniesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $CollectingCompanyRecords = $em->getRepository('AppBundle:CollectingCompany')->findAll();
        return $this->render("agriApp/CollectingCompany/CollectingCompanyRecordsInJson.html.twig", ['CollectingCompanyRecords' => $CollectingCompanyRecords]);
    }


    /**
     * @Route("/CollectingCompanies",name="CollectingCompaniesPage")
     */
    public function CollectingCompaniesRecords()
    {
        return $this->render('agriApp/CollectingCompany/collectingCompanies.html.twig');
    }


    /**
     * @Route("/deleteCollectingCompany/{id}", name="deleteCollectingCompany")
     */
    public function DeleteLogAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CollectingCompanyRecord = $em->getRepository('AppBundle:CollectingCompany')->find($id);
            $em->remove($CollectingCompanyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
