<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompanyTypeAttribute;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyTypeAttributeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/cms/CompanyTypeAttributes",name="CompanyTypeAttributes")
     */
    public function CompanyTypeAttributes()
    {
        $em = $this->getDoctrine()->getManager();
        $companyTypes = $em->getRepository('AppBundle:CompanyType')->findAll();
        return $this->render('agriApp/CompanyTypeAttribute/companyTypeAttributesRecords.html.twig',['companyTypes'=>$companyTypes]);
    }

    /**
     * @Route("/cms/CompanyTypeAttributesRecords", name="CompanyTypeAttributesRecords")
     */
    public function getCompanyTypeAttributeRecords(){
        $em = $this->getDoctrine()->getManager();
        $companyTypeAttributeRecords = $em->getRepository('AppBundle:CompanyTypeAttribute')->findAll();
        return $this->render("agriApp/CompanyTypeAttribute/companyTypeAttributeRecordsInJson.html.twig", ['companyTypeAttributeRecords' => $companyTypeAttributeRecords]);
    }


    /**
     * @Route("/cms/deleteCompanyTypeAttribute/", name="deleteCompanyTypeAttribute")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeAttributeRecord = $em->getRepository('AppBundle:CompanyTypeAttribute')->find($request->request->get('delAttributeID'));
            $em->remove($companyTypeAttributeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/addCompanyTypeAttribute",name="addCompanyTypeAttribute")
     */
    public function addCompanyTypeAttribute(Request $request)
    {
        if ($request->getMethod() === 'POST')
        {
            if ($request->request)
            {
                $em = $this->getDoctrine()->getManager();
                $companyType = $em->getRepository('AppBundle:CompanyType')->find($request->request->get('companyType'));
                $companyTypeAttribute = new CompanyTypeAttribute();
                $companyTypeAttribute->setCompanyType($companyType);
                $companyTypeAttribute->setName($request->request->get('attribute'));
                $em->persist($companyTypeAttribute);
                $em->flush();
                return new Response('Added Attribute Successfully');
            }
        }
        return new Response('Add Attribute Fail');
    }

    /**
     * @Route("/cms/editCompanyTypeAttribute",name="editCompanyTypeAttribute")
     */
    public function editCompanyTypeAttribute(Request $request)
    {
        if ($request->getMethod() === 'POST')
        {
            if ($request->request) {
                    $em=$this->getDoctrine()->getManager();
                    $attribute = $em->getRepository('AppBundle:CompanyTypeAttribute')->find($request->request->get('attributeID'));
                    $companyType= $em->getRepository('AppBundle:CompanyType')->find($request->request->get('companyType'));
                    $attribute->setName($request->request->get('attribute'));
                    $attribute->setCompanyType($companyType);
                    $em->persist($attribute);
                    $em->flush();
                    return new Response ('Attribute Edit Successful');
            }
        }
        return new Response ('Attribute Edit Failed');

    }


}
