<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PurchasesController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/PurchaseRecords",name="PurchaseRecords")
     */
    public function getBranchRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $PurchaseRecords = $em->getRepository('AppBundle:Purchases')->findAll();
        return $this->render("agriApp/Purchase/purchasesRecordsInJson.html.twig", ['PurchaseRecords' => $PurchaseRecords]);
    }


    /**
     * @Route("/cms/Purchases",name="Purchase")
     */
    public function BranchRecords()
    {
       /* $em = $this->getDoctrine()->getManager();
        $FoodsubTypes = $em->getRepository('AppBundle:WasteTypeCategorySubCategory');*/
        return $this->render('agriApp/Purchase/purchasesRecords.html.twig');
    }


    /**
     * @Route("/cms/deletePurchase", name="deletePurchase")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $purchaseRecord = $em->getRepository('AppBundle:Branch')->find($request->request->get('purchasedelID'));
            $em->remove($purchaseRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
