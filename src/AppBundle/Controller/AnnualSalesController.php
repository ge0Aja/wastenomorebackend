<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnnualSalesRanges;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnnualSalesController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/AnnualSalesRecords",name="AnnualSalesRecords")
     */
    public function getAnnualSalesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $AnnualSalesRecords = $em->getRepository('AppBundle:AnnualSalesRanges')->findAll();

        return $this->render("agriApp/AnnualSales/AnnualSalesRecordsInJson.html.twig", ['AnnualSalesRecords' => $AnnualSalesRecords]);
    }


    /**
     * @Route("/cms/annualSales",name="annualSales")
     */
    public function AnnualSalesRecords()
    {
        return $this->render('agriApp/AnnualSales/annualSalesRecords.html.twig');
    }



    /**
     * @Route("/cms/deleteAnnualSaleRange", name="deleteAnnualSaleRange")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $AnnualSalesRecord = $em->getRepository('AppBundle:AnnualSalesRanges')->find($request->request->get('delSalesID'));
            $em->remove($AnnualSalesRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/addAnnualSales",name="addAnnualSales")
     */
    public function addAnnualSales(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $annualSalesRangesRecord = new AnnualSalesRanges();
                    $annualSalesRangesRecord->setSalesRange($request->request->get('range'));
                    $em->persist($annualSalesRangesRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add range'));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add range'));
    }

    /**
     * @Route("/cms/editAnnualSales",name="editAnnualSales")
     */
    public function editAnnualSales(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $annualSalesRangesRecord = $em->getRepository('AppBundle:AnnualSalesRanges')->find($request->request->get('id'));
                    $annualSalesRangesRecord->setSalesRange($request->request->get('range'));
                    $em->persist($annualSalesRangesRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update range'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update range'));
    }
}
