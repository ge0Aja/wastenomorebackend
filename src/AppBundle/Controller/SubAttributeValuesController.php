<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SubAttributeValues;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubAttributeValuesController extends Controller
{
    /**
     * @Route("/subAttributeValues",name="subAttributeValues")
     */
    public function subAttributeValues()
    {
        $em = $this->getDoctrine()->getManager();
        $subAttributes = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->findAll();

        return $this->render(':agriApp/CompanySubAttributeValues:companySubAttributeValuesRecords.html.twig',['subAttributes'=>$subAttributes]);
    }

    /**
     * @Route("/subAttributeValuesRecords",name="subAttributeValuesRecords")
     */
    public function subAttributeValuesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $values = $em->getRepository('AppBundle:SubAttributeValues')->findAll();
        return $this->render(':agriApp/CompanySubAttributeValues:companySubAttributeValuesRecordsInJson.html.twig',['subAttributeValueRecords'=>$values]);
    }

    /**
     * @Route("/addCompanySubAttributeValue",name="addCompanySubAttributeValue")
     */
    public function addCompanySubAttributeValue(Request $request)
    {
        if ($request->getMethod() === 'POST')
        {
            if ($request->request)
            {
                $em = $this->getDoctrine()->getManager();
                $subAttributeValue = new SubAttributeValues();
                $subAttribute = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($request->request->get('subAttribute'));
                $subAttributeValue->setValue($request->request->get('subAttributeValue'));
                $subAttributeValue->setSubAttribute($subAttribute);

                $em->persist($subAttributeValue);
                $em->flush();
                return new Response('Add Sub Attribute Value Succeeded');
            }
        }
        return new Response('Add Sub Attribute Value Failed');
    }

    /**
     * @Route("/editCompanySubAttributeValue",name="editCompanySubAttributeValue")
     */
    public function editCompanySubAttributeValue(Request $request)
    {
        if ($request->getMethod() === 'POST')
        {
            if ($request->request)
            {
                $em = $this->getDoctrine()->getManager();
                $subAttributeValue =$em->getRepository('AppBundle:SubAttributeValues')->find($request->request->get('subAttributeValueID'));
                $subAttribute = $em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->find($request->request->get('subAttribute'));
                $subAttributeValue->setValue($request->request->get('subAttributeValueEDIT'));
                $subAttributeValue->setSubAttribute($subAttribute);

                $em->persist($subAttributeValue);
                $em->flush();
                return new Response('Edit Sub Attribute Value Succeeded');
            }
        }
        return new Response('Edit Sub Attribute Value Failed');
    }



    /**
     * @Route("/deleteSubAttributeValue",name="deleteSubAttributeValue")
     */
    public function deleteSubAttributeValue(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                $subAttributeValue = $em->getRepository('AppBundle:SubAttributeValues')->find($request->request->get('delValueID'));
                $em->remove($subAttributeValue);
                $em->flush();
                return new Response('Delete Sub Attribute Value Succeeded');
            }
        }
        return new Response('Delete Sub Attribute Value Failed');
    }
}
