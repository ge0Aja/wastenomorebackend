<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CollectingCompany;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CollectingCompanyController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/cms/CollectingCompanyRecords",name="CollectingCompanyRecords")
     */
    public function getCollectingCompaniesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $CollectingCompanyRecords = $em->getRepository('AppBundle:CollectingCompany')->findAll();
        return $this->render("agriApp/CollectingCompany/CollectingCompanyRecordsInJson.html.twig", ['CollectingCompanyRecords' => $CollectingCompanyRecords]);
    }


    /**
     * @Route("/cms/CollectingCompanies",name="CollectingCompaniesPage")
     */
    public function CollectingCompaniesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $DistictRecords = $em->getRepository('AppBundle:District')->findAll();
        return $this->render('agriApp/CollectingCompany/collectingCompanies.html.twig',['districts' => $DistictRecords]);
    }



    /**
     * @Route("/cms/addCollectingCompany",name="addCollectingCompany")
     */
    public function addCollectingCompany(Request $request){
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $collectingCompanyrecord = new CollectingCompany();
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_gc_select'));
                    $collectingCompanyrecord->setName($request->request->get('txt_gccompany_add'));
                    $collectingCompanyrecord->setLocation($distr);
                    $em->persist($collectingCompanyrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Collection Company'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Collection Company'));
    }




    /**
     * @Route("/cms/editCollectingCompany", name="editCollectingCompany")
     */
    public function editCollectingCompanies(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $collectingcompanyrecord = $em->getRepository('AppBundle:CollectingCompany')->find($request->request->get('h_gccompanyID_edit'));
                    $collectingcompanyrecord->setName($request->request->get('txt_gccompany_edit'));
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_gc_select_edit'));
                    $collectingcompanyrecord->setLocation($distr);
                    $em->persist($collectingcompanyrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Collection Company'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Collection Company'));
    }


    /**
     * @Route("/cms/deleteCollectingCompany", name="deleteCollectingCompany")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CollectingCompanyRecord = $em->getRepository('AppBundle:CollectingCompany')->find($request->request->get('h_gccompanyID_del'));
            $em->remove($CollectingCompanyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


}
