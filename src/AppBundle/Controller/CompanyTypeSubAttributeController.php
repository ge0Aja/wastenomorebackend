<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompanyTypeAttributeSubAttribute;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyTypeSubAttributeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/CompanyTypeSubAttributes",name="CompanyTypeSubAttributes")
     */
    public function CompanyTypeSubAttributes()
    {
        $em = $this->getDoctrine()->getManager();
        $companyTypes = $em->getRepository('AppBundle:CompanyType')->findAll();
        return $this->render('agriApp/CompanyTypeSubAttribute/companyTypeSubAttributeRecords.html.twig',['companyTypes'=>$companyTypes]);
    }

    /**
     * @Route("/CompanyTypeSubAttributesRecords", name="CompanyTypeSubAttributesRecords")
     */
    public function getCompanyTypeSubAttributeRecords(){
        $em = $this->getDoctrine()->getManager();
        $companyTypeSubAttributeRecords = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->findAll();
        return $this->render("agriApp/CompanyTypeSubAttribute/companyTypeSubAttributeRecordsInJson.html.twig", ['companyTypeSubAttributeRecords' => $companyTypeSubAttributeRecords]);
    }


    /**
     * @Route("/deleteCompanyTypeSubAttribute/{id}", name="deleteCompanyTypeSubAttribute")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeSubAttributeRecord = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($id);
            $em->remove($companyTypeSubAttributeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/addSubAttribute",name="addSubAttribute")
     */
    public function addSubAttribute(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request)
            {
                $em = $this->getDoctrine()->getManager();
                $subAttribute = new CompanyTypeAttributeSubAttribute();
               // $companyType = $em->getRepository('AppBundle:CompanyType')->find($request->request->get('companyType'));
                $companyTypeAttribute = $em->getRepository('AppBundle:CompanyTypeAttribute')->find($request->request->get('companyTypeAttribute'));
                $subAttribute->setCompanyTypeAttribute($companyTypeAttribute);
                $subAttribute->setName($request->request->get('subAttribute'));
                $em->persist($subAttribute);
                $em->flush();
                return new Response('Added subattribute successfully');
            }
        }
        return new Response('Add subattribute failed');

    }

    /**
     * @Route("/deleteCompanyTypeAttributeSubAttribute/", name="deleteCompanyTypeAttributeSubAttribute")
     */
    public function deleteCompanyTypeAttributeSubAttribute(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyTypeAttributeSubAttributeRecord = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($request->request->get('delAttributeID'));
            $em->remove($companyTypeAttributeSubAttributeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/editCompanyTypeAttributeSubAttribute",name="editCompanyTypeAttributeSubAttribute")
     */
    public function editCompanyTypeAttributeSubAttribute(Request $request)
    {
        if ($request->getMethod() === 'POST')
        {
            if ($request->request) {
                $em=$this->getDoctrine()->getManager();

                $subAttribute = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($request->request->get('subAttributeID'));
                $attribute = $em->getRepository('AppBundle:CompanyTypeAttribute')->find($request->request->get('companyTypeAttribute'));

                $subAttribute->setCompanyTypeAttribute($attribute);

                $em->persist($subAttribute);
                $em->flush();
                return new Response ('SUBAttribute Edit Successful');
            }
        }
        return new Response ('SUBAttribute Edit Failed');

    }



}
