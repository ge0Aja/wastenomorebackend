<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WasteTypeCategorySubCategory;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WasteTypeSubCategoryController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/WasteTypeSubCategoryRecords",name="WasteTypeSubCategoryRecords")
     */
    public function getCitiesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $SubCatRecords = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->findAll();
        return $this->render("agriApp/WasteTypeSubCategory/WasteTypeSubCategoriesInJson.html.twig", ['WasteSubCatRecords' => $SubCatRecords]);
    }


    /**
     * @Route("/WasteTypeSubCategories",name="WasteTypeSubCategories")
     */
    public function CitiesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $CatRecords = $em->getRepository('AppBundle:WasteTypeCategory')->findAll();
        return $this->render('agriApp/WasteTypeSubCategory/WasteTypeSubCategories.html.twig',['wasteTypeCats' => $CatRecords]);
    }



    /**
     * @Route("/addSubCat",name="addSubCat")
     */
    public function addCity(Request $request){
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = new WasteTypeCategorySubCategory();
                    $WasteTypeCat = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('wastetypecat_select'));
                    $cityrecord->setName($request->request->get('txt_subcat_add'));
                    $cityrecord->setCategoryType($WasteTypeCat);
                    $em->persist($cityrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add  Waste Type Sub-Category'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type Sub-Category'));
    }

    /**
     * @Route("/deleteSubCat", name="deleteSubCat")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $SubCatRecord = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->find($request->request->get('h_subcat_del'));
            $em->remove($SubCatRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/editSubCat", name="editSubCat")
     */
    public function editCities(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $subcatrecord = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->find($request->request->get('h_subcat_edit'));
                    $subcatrecord->setName($request->request->get('txt_subcat_edit'));
                    $WasteTypeCat = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('wastetypecat_select_edit'));
                    $subcatrecord->setCategoryType($WasteTypeCat);
                    $em->persist($subcatrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Sub-Category'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Sub-Category'));
    }
}
