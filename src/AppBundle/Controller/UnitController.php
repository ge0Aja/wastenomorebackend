<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UnitController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/UnitsRecords",name="UnitsRecords")
     */
    public function getUnitRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $UnitRecords = $em->getRepository('AppBundle:Unit')->findAll();
        return $this->render("agriApp/Unit/UnitRecordsInJson.html.twig", ['UnitRecords' => $UnitRecords]);
    }



    /**
     * @Route("/Units",name="UnitsRecordsPage")
     */
    public function UnitsRecords()
    {
        return $this->render('agriApp/Unit/unitsRecords.html.twig');
    }



    /**
     * @Route("/deleteUnit/{id}", name="deleteUnit")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $UnitRecord = $em->getRepository('AppBundle:Unit')->find($id);
            $em->remove($UnitRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
