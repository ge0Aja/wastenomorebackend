<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RecyclingCompany;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RecyclingCompanyController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/RecyclingCompanyRecords",name="RecyclingCompanyRecords")
     */
    public function getRecyclingCompaniesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $RecyclingCompanyRecords = $em->getRepository('AppBundle:RecyclingCompany')->findAll();
        return $this->render("agriApp/RecyclingCompany/RecyclingCompanyRecordsInJson.html.twig", ['RecyclingCompanyRecords' => $RecyclingCompanyRecords]);
    }


    /**
     * @Route("/cms/RecyclingCompanies",name="RecyclingCompanies")
     */
    public function RecyclingCompaniesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $DistictRecords = $em->getRepository('AppBundle:District')->findAll();
        return $this->render('agriApp/RecyclingCompany/recyclingCompanies.html.twig',['districts' => $DistictRecords]);
    }



    /**
     * @Route("/cms/addRecyclingCompany",name="addRecyclingCompany")
     */
    public function addRecyclingCompany(Request $request){
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $recyclingCompanyrecord = new RecyclingCompany();
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_rc_select'));
                    $recyclingCompanyrecord->setName($request->request->get('txt_rccompany_add'));
                    $recyclingCompanyrecord->setMaterial($request->request->get('txt_rcmaterial_add'));
                    $recyclingCompanyrecord->setPickupService($request->request->get('txt_rcpickup_add'));
                    $recyclingCompanyrecord->setLocation($distr);
                    $em->persist($recyclingCompanyrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Recycling Company'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Recycling Company'));
    }



    /**
     * @Route("/cms/editRecyclingCompany", name="editRecyclingCompany")
     */
    public function editRecyclingCompanies(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $recyclingcompanyrecord = $em->getRepository('AppBundle:RecyclingCompany')->find($request->request->get('h_rccompanyID_edit'));
                    $recyclingcompanyrecord->setName($request->request->get('txt_rccompany_edit'));
                    $recyclingcompanyrecord->setMaterial($request->request->get('txt_rcmaterial_edit'));
                    $recyclingcompanyrecord->setPickupService($request->request->get('txt_rcpickup_edit'));
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_rc_select_edit'));
                    $recyclingcompanyrecord->setLocation($distr);
                    $em->persist($recyclingcompanyrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Recycling Company'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Recycling Company'));
    }



    /**
     * @Route("/cms/deleteRecyclingCompany", name="deleteRecyclingCompany")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CollectingCompanyRecord = $em->getRepository('AppBundle:RecyclingCompany')->find($request->request->get('h_rccompanyID_del'));
            $em->remove($CollectingCompanyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
