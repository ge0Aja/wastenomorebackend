<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WasteTypeCategory;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WasteTypeCategoryController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/WasteTypeCategoryRecords",name="WasteTypeCategoryRecords")
     */
    public function getWasteTypeCatsRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $WasteTypeCatRecords = $em->getRepository('AppBundle:WasteTypeCategory')->findAll();
        return $this->render("agriApp/WasteTypeCategory/WasteTypeCategoriesInJson.html.twig", ['WasteTypeCatRecords' => $WasteTypeCatRecords]);
    }


    /**
     * @Route("/WasteTypeCategories",name="WasteTypeCategories")
     */
    public function WasteTypeCatsRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $WasteTypeRecords = $em->getRepository('AppBundle:WasteType')->findAll();
        return $this->render('agriApp/WasteTypeCategory/WasteTypeCategories.html.twig',['wastetypes' => $WasteTypeRecords]);
    }


    /**
     * @Route("/deleteWasteTypeCat", name="deleteWasteTypeCat")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $WasteTypeCatRecord = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('h_wastetypecatID_del'));
            $em->remove($WasteTypeCatRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/addWasteTypeCat", name="addWasteTypeCat")
     */
    public function addDistrict(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $wastetypecatrecord = new WasteTypeCategory();
                    $wastetypecatrecord->setName($request->request->get('txt_wastetypecat_add'));
                    $wt = $em->getRepository('AppBundle:WasteType')->find($request->request->get('wastetype_select'));
                    $wastetypecatrecord->setWasteType($wt);
                    $em->persist($wastetypecatrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type Category'));
                }

            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type Category'));
    }

    /**
     * @Route("/editWasteTypeCat", name="editWasteTypeCat")
     */
    public function editUnits(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $wastetypecatrecord = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('h_wastetypecatID_edit'));
                    $wastetypecatrecord->setName($request->request->get('txt_wastetypecat_edit'));
                    $wt = $em->getRepository('AppBundle:WasteType')->find($request->request->get('wastetype_select_edit'));
                    $wastetypecatrecord->setWasteType($wt);

                    $em->persist($wastetypecatrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Category'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Category'));
    }
}
