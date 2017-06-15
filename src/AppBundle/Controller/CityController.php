<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CityTown;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $em = $this->getDoctrine()->getManager();
        $DistictRecords = $em->getRepository('AppBundle:District')->findAll();
        return $this->render('agriApp/City/citiesRecords.html.twig',['districts' => $DistictRecords]);
    }



    /**
     * @Route("/addCity",name="addCity")
     */
    public function addCity(Request $request){
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = new CityTown();
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_select'));
                    $cityrecord->setName($request->request->get('txt_city_add'));
                    $cityrecord->setDistrict($distr);
                    $em->persist($cityrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add city'));
                }
                }
            }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add city'));
    }

    /**
     * @Route("/deleteCity", name="deleteCity")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CityRecord = $em->getRepository('AppBundle:CityTown')->find($request->request->get('h_cityID_del'));
            $em->remove($CityRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/editCity", name="editCity")
     */
    public function editUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = $em->getRepository('AppBundle:CityTown')->find($request->request->get('h_cityID_edit'));
                    $cityrecord->setName($request->request->get('txt_city_edit'));
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_select_edit'));
                    $cityrecord->setDistrict($distr);
                    $em->persist($cityrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update city'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update city'));
    }

}
