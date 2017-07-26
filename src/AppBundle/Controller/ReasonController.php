<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reason;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReasonController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/ReasonsRecords",name="ReasonsRecords")
     */
    public function getReasonsRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $ReasonsRecords = $em->getRepository('AppBundle:Reason')->findAll();
        return $this->render("agriApp/Reason/ReasonRecordsInJson.html.twig", ['ReasonRecords' => $ReasonsRecords]);
    }


    /**
     * @Route("/cms/Reasons",name="ReasonsRecordsPage")
     */
    public function ReasonsRecords()
    {
        return $this->render('agriApp/Reason/reasonsRecords.html.twig');
    }


    /**
     * @Route("/cms/addReason",name="addReason")
     */
    public function addReasons(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $reasonsRecord = new Reason();
                    $reasonsRecord->setReason($request->request->get('txt_reason_add'));
                    $em->persist($reasonsRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add reason'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add reason'));
    }


    /**
     * @Route("/cms/deleteReason", name="deleteReason")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $ReasonsRecord = $em->getRepository('AppBundle:Reason')->find($request->request->get('h_reasonID_del'));
            $em->remove($ReasonsRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/editReason", name="editReason")
     */
    public function editReasons(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $reasonsRecord = $em->getRepository('AppBundle:Reason')->find($request->request->get('h_reasonID_edit'));
                    $reasonsRecord->setReason($request->request->get('txt_reason_edit'));
                    $em->persist($reasonsRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit reason'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit reason'));
    }
}
