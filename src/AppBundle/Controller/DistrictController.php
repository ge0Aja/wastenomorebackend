<?php

namespace AppBundle\Controller;

use AppBundle\Entity\District;
use AppBundle\Entity\Governorate;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $em = $this->getDoctrine()->getManager();
        $GovernorateRecords = $em->getRepository('AppBundle:Governorate')->findAll();
        return $this->render('agriApp/District/districtRecords.html.twig',['governorates' => $GovernorateRecords]);
    }


    /**
     * @Route("/deleteDistrict", name="deleteDistrict")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $DistrictsRecord = $em->getRepository('AppBundle:District')->find($request->request->get('h_distrcitID_del'));
            $em->remove($DistrictsRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/addDistrict", name="addDistrict")
     */
    public function addDistrict(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                    try {
                        $em = $this->getDoctrine()->getManager();
                        $districtrecord = new District();
                        $districtrecord->setName($request->request->get('txt_district_add'));
                        $gov = $em->getRepository('AppBundle:Governorate')->find($request->request->get('governorate_select'));
                        $districtrecord->setGovernorate($gov);
                        $em->persist($districtrecord);
                        $em->flush();

                        return new JsonResponse(array('status' => 'success'));
                    } catch (DBALException $e) {
                        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add District'));
                    }

            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add District'));
    }

    /**
     * @Route("/editDistrict", name="editDistrict")
     */
    public function editUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $districtrecord = $em->getRepository('AppBundle:District')->find($request->request->get('h_districtID_edit'));
                    $districtrecord->setName($request->request->get('txt_district_edit'));
                    $gov = $em->getRepository('AppBundle:Governorate')->find($request->request->get('governorate_select_edit'));
                    $districtrecord->setGovernorate($gov);

                    $em->persist($districtrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update District'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update District'));
    }

}
