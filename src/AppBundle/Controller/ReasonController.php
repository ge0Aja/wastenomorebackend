<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReasonController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }




    /**
     * @Route("/ReasonsRecords",name="ReasonsRecords")
     */
    public function getReasonsRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $ReasonsRecords = $em->getRepository('AppBundle:Reason')->findAll();
        return $this->render("agriApp/Reason/ReasonRecordsInJson.html.twig", ['ReasonRecords' => $ReasonsRecords]);
    }


    /**
     * @Route("/Reasons",name="ReasonsRecordsPage")
     */
    public function ReasonsRecords()
    {
        return $this->render('agriApp/Reason/reasonsRecords.html.twig');
    }



    /**
     * @Route("/deleteReason/{id}", name="deleteReason")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $ReasonRecord = $em->getRepository('AppBundle:Reason')->find($id);
            $em->remove($ReasonRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
