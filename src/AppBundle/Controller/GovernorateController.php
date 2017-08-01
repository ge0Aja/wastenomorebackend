<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Governorate;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GovernorateController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/GovernoratesRecords",name="GovernoratesRecords")
     */
    public function getGovernoratesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $GovernorateRecords = $em->getRepository('AppBundle:Governorate')->findAll();
        return $this->render("agriApp/Governorate/GovernorateRecordsInJson.html.twig", ['GovernorateRecords' => $GovernorateRecords]);
    }


    /**
     * @Route("/cms/Governorates",name="GovernoratesRecordsPage")
     */
    public function CitiesRecords()
    {
        return $this->render('agriApp/Governorate/governorateRecords.html.twig');
    }


    /**
     * @Route("/cms/deleteGovernorate", name="deleteGovernorate")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $GovernoratesRecord = $em->getRepository('AppBundle:Governorate')->find($request->request->get('h_governorateID_del'));
            $em->remove($GovernoratesRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/editGovernorate", name="editGovernorate")
     */
    public function editGovernorates(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $governoratesRecord = $em->getRepository('AppBundle:Governorate')->find($request->request->get('h_governorateID_edit'));
                    $governoratesRecord->setName($request->request->get('txt_governorate_edit'));
                    $em->persist($governoratesRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Governorate'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Governorate'));
    }



    /**
     * @Route("/cms/addGovernorate",name="addGovernorate")
     */
    public function addUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $governoratesRecord = new Governorate();
                    $governoratesRecord->setName($request->request->get('txt_governorate_add'));
                    $em->persist($governoratesRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Governorate'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Governorate'));
    }



}
