<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SubCategoryUnit;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SubCategoryUnitController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/SubCatsUnitsRecords", name="SubCatsUnitsRecords")
     */
    public function getSubCatUnitRecords()
    {

        $em = $this->getDoctrine()->getManager();
        $records = $em->getRepository('AppBundle:SubCategoryUnit')->findAll();

        return $this->render("agriApp/SubCategoryUnit/SubCategoryUnitRecordsInJson.html.twig", ["SubCatUnitRecords" => $records]);
    }


    /**
     * @Route("/cms/SubCatsUnits", name="SubCatsUnits")
     */
    public function getSubCatUnits()
    {

        $em = $this->getDoctrine()->getManager();
        $subcats = $em->getRepository("AppBundle:WasteTypeCategorySubCategory")->findBy(array(),array("name" => "ASC"));
        $units = $em->getRepository("AppBundle:Unit")->findBy(array(),array("name" => "ASC"));

        return $this->render("agriApp/SubCategoryUnit/SubCategoryUnitRecords.html.twig", ["SubCats" => $subcats, "Units" => $units]);
    }


    /**
     * @Route("/cms/addSubCatUnit", name="addSubCatUnit")
     */
    public function addSubCatUnit(Request $request)
    {

        if ($request->getMethod() == "POST") {

            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();

                    $subcatunitrecord = new SubCategoryUnit();
                    $subcatunitrecord->setSubcategory($em->getRepository("AppBundle:WasteTypeCategorySubCategory")->find($request->request->get("subcatadd")));
                    $subcatunitrecord->setUnit($em->getRepository("AppBundle:Unit")->find($request->request->get("unitadd")));
                    $em->persist($subcatunitrecord);
                    $em->flush();

                    return new JsonResponse(array("status" => "success"));
                } catch (DBALException $e) {

                    return new JsonResponse(array("status" => "error", "message" => "Can't add Sub-Category Unit"));
                }

            }
        }
        return new JsonResponse(array("status" => "error", "message" => "Can't add Sub-Category Unit"));
    }

    /**
     * @Route("/cms/editSubCatUnit", name="editSubCatUnit")
     */
    public function editSubCatUnit(Request $request)
    {

        if ($request->getMethod() == "POST") {

            if ($request->request) {

                try {

                    $em = $this->getDoctrine()->getManager();

                    $subcatunitrecord = $em->getRepository("AppBundle:SubCategoryUnit")->find($request->request->get("subcatuniteditID"));
                    $subcatunitrecord->setSubcategory($em->getRepository("AppBundle:WasteTypeCategorySubCategory")->find($request->request->get("subcatedit")));
                    $subcatunitrecord->setUnit($em->getRepository("AppBundle:Unit")->find($request->request->get("unitedit")));

                    $em->persist($subcatunitrecord);
                    $em->flush();

                    return new JsonResponse(array("status" => "success"));
                } catch (DBALException $e) {
                    return new JsonResponse(array("status" => "error", "message" => "Can't edit Sub-Category Unit"));
                }
            }
        }
        return new JsonResponse(array("status" => "error", "message" => "Can't edit Sub-Category Unit"));
    }


    /**
     * @Route("/cms/delSubCatUnit", name="delSubCatUnit")
     */
    public function deleteSubCatUnit(Request $request)
    {
        if ($request->getMethod() == "POST") {

            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $subcatunitrecord = $em->getRepository("AppBundle:SubCategoryUnit")->find($request->request->get("subcatunitdelID"));
                    $em->remove($subcatunitrecord);

                    $em->flush();
                    return new JsonResponse(array("status" => "success"));

                } catch (DBALException $e) {

                    return new JsonResponse(array("status" => "error", "message" => "Can't delete Sub-Category Unit"));

                }
            }
        }
        return new JsonResponse(array("status" => "error", "message" => "Can't delete Sub-Category Unit"));
    }
}
