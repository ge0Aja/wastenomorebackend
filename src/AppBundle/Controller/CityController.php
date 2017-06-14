<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CityController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/CitiesRecords",name="CitiesRecords")
     */
    public function getCitiesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $CityRecords = $em->getRepository('AppBundle:CityTown')->findAll();
        return $this->render("agriApp/City/CityRecordsInJson.html.twig", ['CityRecords' => $CityRecords]);
    }


    /**
     * @Route("/Cities",name="CitiesRecordsPage")
     */
    public function CitiesRecords()
    {
        return $this->render('agriApp/City/citiesRecords.html.twig');
    }


    /**
     * @Route("/deleteCity/{id}", name="deleteCity")
     */
    public function DeleteLogAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CityRecord = $em->getRepository('AppBundle:CityTown')->find($id);
            $em->remove($CityRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
