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
     * @Route("/AnnualSalesRecords",name="AnnualSalesRecords")
     */
    public function getAnnualSalesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $AnnualSalesRecords = $em->getRepository('AppBundle:AnnualSalesRanges')->findAll();
        return $this->render("agriApp/AnnualSales/AnnualSalesRecordsInJson.html.twig", ['AnnualSalesRecords' => $AnnualSalesRecords]);
    }


    /**
     * @Route("/renderAddEditAnnualSale/{id}", name="renderAddEditAnnualSale")
     */
    public function renderAddEditRange($id = null){

        if($id == null){
            $em = $this->getDoctrine()->getManager();
            $AnnualSalesRecord = $em->getRepository('AppBundle:AnnualSalesRanges')->find($id);
            return $this->render('agriApp/AnnualSales/addEditAnnualSale.html.twig',['range' => $AnnualSalesRecord]);

        }else{
            return $this->render('agriApp/AnnualSales/addEditAnnualSale.html.twig');
        }
    }


    /**
     * @Route("/AddEditAnnualSale",name="AddEditAnnualSale")
     */
    public function AddEditRange(Request $request){
        $em = $this->getDoctrine()->getManager();

        if($request->request->has('record_id')){
            $id = $request->request->get('record_id');
            $annualsalerecord = $em->getRepository('AppBundle:AnnualSalesRanges')->find($id);

        }else{
            $annualsalerecord = new AnnualSalesRanges;
        }
        $range_val = $request->request->get('range');

        $annualsalerecord->setSalesRange($range_val);

        $em->persist($annualsalerecord);
        $em->flush();

    }

    /**
     * @Route("/annualSales",name="annualSales")
     */
    public function AnnualSalesRecords()
    {
        return $this->render('agriApp/AnnualSales/annualSalesRecords.html.twig');
    }



    /**
     * @Route("/deleteAnnualSaleRange", name="deleteAnnualSaleRange")
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
     * @Route("/addAnnualSales",name="addAnnualSales")
     */
    public function addAnnualSales(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                $annualSalesRangesRecord = new AnnualSalesRanges();
                $annualSalesRangesRecord->setSalesRange($request->request->get('range'));
                $em->persist($annualSalesRangesRecord);
                $em->flush();
                return new Response('Range Added Successfully');
            }
        }

        return new Response(dump($request));

    }

    /**
     * @Route("/editAnnualSales",name="editAnnualSales")
     */
    public function editAnnualSales(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                $annualSalesRangesRecord = $em->getRepository('AppBundle:AnnualSalesRanges')->find($request->request->get('id'));
                $annualSalesRangesRecord->setSalesRange($request->request->get('range'));
                $em->persist($annualSalesRangesRecord);
                $em->flush();
                return new Response('Range Updated Successfully');
            }
        }

        return new Response(dump($request));
    }
}
