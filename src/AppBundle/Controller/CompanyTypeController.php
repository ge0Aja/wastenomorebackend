<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompanyType;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyTypeController extends Controller
{
    /**
     * @Route("/cms/CompanyTypes",name="CompanyTypes")
     */
    public function getCompanyTypes()
    {
        return $this->render('agriApp/CompanyType/companyTypeRecords.html.twig');
    }

    /**
     * @Route("/cms/CompanyTypeRecords", name="CompanyTypeRecords")
     */
    public function getCompanyTypeRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $companyTypeRecords = $em->getRepository('AppBundle:CompanyType')->findAll();
        return $this->render("agriApp/CompanyType/companyTypeRecordsInJson.html.twig", ['companyTypeRecords' => $companyTypeRecords]);
    }

    /**
     * @Route("/cms/deleteCompanyType", name="deleteCompanyType")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $CompanyTypeRecord = $em->getRepository('AppBundle:CompanyType')->find($request->request->get('companytypedelID'));
            $em->remove($CompanyTypeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/addCompanyType",name="addCompanyType")
     */
    public function addCompanyType(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $companyTypeRecord = new CompanyType();
                    $companyTypeRecord->setTypeName($request->request->get('companytypeadd'));
                    $em->persist($companyTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Company Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Company Type'));

    }

    /**
     * @Route("/cms/editCompanyType",name="editCompanyType")
     */
    public function editCompanyType(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $companyTypeRecord = $em->getRepository('AppBundle:CompanyType')->find($request->request->get('companytypeEditID'));
                    $companyTypeRecord->setTypeName($request->request->get('editcompanytypetxt'));
                    $em->persist($companyTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Company Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Company Type'));
    }

    /**
     * @Route(path="api/getCompanyTypesApi", name="getCompanyTypesApi")
     */
    public function getCompanyTypesApi() {
        try {
            $em = $this->getDoctrine()->getManager();
            $company_types = array();
            $CompanyTypes = $em->getRepository('AppBundle:CompanyType')->findAll();

            foreach ($CompanyTypes as $type) {
                // $company_types[$type->getId()] = $type->getTypeName();
               // array_push($company_types, $type->getTypeName());

                array_push($company_types, array("key" => $type->getId(),"label" => $type->getTypeName()));
            }
            return new JsonResponse(array("status" =>"success", "types" => $company_types));
        }catch(Exception $e){
            return new JsonResponse(array("status" => "error"));
        }
    }



}
