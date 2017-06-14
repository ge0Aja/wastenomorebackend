<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class GovernorateController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/GovernoratesRecords",name="GovernoratesRecords")
     */
    public function getGovernoratesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $GovernorateRecords = $em->getRepository('AppBundle:Governorate')->findAll();
        return $this->render("agriApp/Governorate/GovernorateRecordsInJson.html.twig", ['GovernorateRecords' => $GovernorateRecords]);
    }


    /**
     * @Route("/Governorates",name="GovernoratesRecordsPage")
     */
    public function CitiesRecords()
    {
        return $this->render('agriApp/Governorate/governorateRecords.html.twig');
    }


    /**
     * @Route("/deleteGovernorate/{id}", name="deleteGovernorate")
     */
    public function DeleteLogAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $GovernorateRecord = $em->getRepository('AppBundle:Governorate')->find($id);
            $em->remove($GovernorateRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
