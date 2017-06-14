<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WasteController extends Controller
{
    /**
     * @Route("/WasteLogs",name="wasteLogs")
     */
    public function WasteLogs()
    {
        $em = $this->getDoctrine()->getManager();
        $wasteLogs = $em->getRepository('AppBundle:Waste')->findAll();
//        return $this->render('agriApp/wasteLogs.html.twig',['wasteLogs' => $wasteLogs]);
        return $this->render('agriApp/Waste/wasteLogs.html.twig',['wasteLogs' => $wasteLogs]);
    }

    /**
     * @Route("/renderWasteLogs",name="renderWasteLogs")
     */
    public function renderWasteLogs()
    {
        $em = $this->getDoctrine()->getManager();
        $wasteLogs = $em->getRepository('AppBundle:Waste')->findAll();
        return $this->render("agriApp/Waste/wasteLogsInJson.html.twig", ['wasteLogs' => $wasteLogs]);
    }


    /**
     * @Route("/deleteWasteLog/{id}", name="delete")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $wasteLog = $em->getRepository('AppBundle:Waste')->find($id);
            $em->remove($wasteLog);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

}
