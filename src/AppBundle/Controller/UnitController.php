<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Unit;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/cms/UnitsRecords",name="UnitsRecords")
     */
    public function getUnitRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $UnitRecords = $em->getRepository('AppBundle:Unit')->findBy(array(),array("name" => "asc"));
        return $this->render("agriApp/Unit/UnitRecordsInJson.html.twig", ['UnitRecords' => $UnitRecords]);
    }



    /**
     * @Route("/cms/Units",name="UnitsRecordsPage")
     */
    public function UnitsRecords()
    {
        return $this->render('agriApp/Unit/unitsRecords.html.twig');
    }


    /**
     * @Route("/cms/addUnit",name="addUnit")
     */
    public function addUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $unitsRecord = new Unit();
                    $unitsRecord->setName($request->request->get('txt_unit_add'));
                    $em->persist($unitsRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Unit'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Unit'));
    }


    /**
     * @Route("/cms/deleteUnit", name="deleteUnit")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $UnitsRecord = $em->getRepository('AppBundle:Unit')->find($request->request->get('h_unitID_del'));
            $em->remove($UnitsRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/editUnit", name="editUnit")
     */
    public function editUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $unitsRecord = $em->getRepository('AppBundle:Unit')->find($request->request->get('h_unitID_edit'));
                    $unitsRecord->setName($request->request->get('txt_unit_edit'));
                    $em->persist($unitsRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Unit'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Unit'));
    }
}
