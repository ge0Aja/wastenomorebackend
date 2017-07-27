<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubLicenseController extends Controller
{
    /*public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }*/


    /**
     * @Route("/cms/SubLicenseRecords",name="SubLicenseRecords")
     */
    public function getSubLicenseRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $SubLicenseRecords= $em->getRepository('AppBundle:SubLicense')->findAll();
        return $this->render("agriApp/License/SubLicenseRecordsInJson.html.twig", ['SubLicenseRecords' => $SubLicenseRecords]);
    }


    /**
     * @Route("/cms/SubLicenses",name="SubLicenses")
     */
    public function SubLicensesRecords()
    {

        return $this->render('agriApp/License/SubLicenseRecords.html.twig');
    }

    /**
     * @Route("/cms/deleteSubLicense", name="deleteSubLicense")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $SubLiceRecord = $em->getRepository('AppBundle:SubLicense')->find($request->request->get('delSubLicenseID'));
            $em->remove($SubLiceRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/changeSubLicenseStatus", name="changeSubLicenseStatus")
     */
    public function changeSubLicenseStatus(Request $request){
        try{
            $em = $this->getDoctrine()->getManager();
            $SubLiceRecord = $em->getRepository('AppBundle:SubLicense')->find($request->request->get('editSubLicenseID'));

            if($_POST["isActive"] == 1)
                $SubLiceRecord->setActive(1);
            else
                $SubLiceRecord->setActive(0);
            $em->persist($SubLiceRecord);
            $em->flush();
            return new JsonResponse(array('status' => 'success'));
        }catch (DBALException $e){
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit SubLicense'));
        }
    }


}
