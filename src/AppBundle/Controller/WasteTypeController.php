<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WasteType;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WasteTypeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/cms/WasteTypes",name="WasteTypes")
     */
    public function getCompanyTypes()
    {
        return $this->render('agriApp/WasteType/wasteTypes.html.twig');
    }

    /**
     * @Route("/cms/WasteTypeRecords", name="WasteTypeRecords")
     */
    public function getCompanyTypeRecords(){
        $em = $this->getDoctrine()->getManager();
        $wasteTypeRecords = $em->getRepository('AppBundle:WasteType')->findAll();
        return $this->render(":agriApp/WasteType:wasteTypesInJson.html.twig", ['wasteTypeRecords' => $wasteTypeRecords]);
    }


    /**
     * @Route("/cms/deleteWasteType", name="deleteWasteType")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $WasteTypeRecord = $em->getRepository('AppBundle:WasteType')->find($request->request->get('wastetypedelID'));
            $em->remove($WasteTypeRecord);
            $em->flush();
            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/addWasteType",name="addWasteType")
     */
    public function addCompanyType(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $wasteTypeRecord = new WasteType();
                    $wasteTypeRecord->setName($request->request->get('wastetypeadd'));
                    $em->persist($wasteTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type'));

    }

    /**
     * @Route("/cms/editWasteType",name="editWasteType")
     */
    public function editCompanyType(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $wasteTypeRecord = $em->getRepository('AppBundle:WasteType')->find($request->request->get('wastetypeEditID'));
                    $wasteTypeRecord->setName($request->request->get('editwastetypetxt'));
                    $em->persist($wasteTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Waste Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Waste Type'));
    }




}
