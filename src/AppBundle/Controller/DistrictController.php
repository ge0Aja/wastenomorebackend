<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DistrictController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }




    /**
     * @Route("/DistrictsRecords",name="DistrictsRecords")
     */
    public function getDistrictsRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $DistrcitRecords = $em->getRepository('AppBundle:District')->findAll();
        return $this->render("agriApp/District/DistrictRecordsInJson.html.twig", ['DistrcitRecords' => $DistrcitRecords]);
    }


    /**
     * @Route("/Districts",name="DistrictsRecordsPage")
     */
    public function DistrictsRecords()
    {
        return $this->render('agriApp/District/districtRecords.html.twig');
    }


    /**
     * @Route("/deleteDistrict/{id}", name="deleteDistrict")
     */
    public function DeleteLogAction($id)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $DistrictRecord = $em->getRepository('AppBundle:District')->find($id);
            $em->remove($DistrictRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
