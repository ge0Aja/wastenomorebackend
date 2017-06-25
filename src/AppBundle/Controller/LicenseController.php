<?php

namespace AppBundle\Controller;

use AppBundle\Entity\License;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LicenseController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/LicenseRecords",name="LicenseRecords")
     */
    public function getLicenseRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $LicenseRecords = $em->getRepository('AppBundle:License')->findAll();
        return $this->render("agriApp/License/LicenseRecordsInJson.html.twig", ['LicenseRecords' => $LicenseRecords]);
    }


    /**
     * @Route("/Licenses",name="Licenses")
     */
    public function LicensesRecords()
    {
        $length = 16;
        $random = bin2hex(random_bytes($length));
        return $this->render('agriApp/License/LicenseRecords.html.twig',['licenseKey' => $random]);
    }

    /**
     * @Route("/deleteLicense", name="deleteLicense")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $AppRoleRecord = $em->getRepository('AppBundle:License')->find($request->request->get('delLicenseID'));
            $em->remove($AppRoleRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/addLicense",name="addLicense")
     */
    public function addLicense(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $licenseRecord = new License();
                    $licenseRecord->setLicense($request->request->get('licenseadd'));
                    $licenseRecord->setUserCount($request->request->get('usercountadd'));

                    /*var_dump(date("Y-m-d",strtotime($request->request->get('startDateadd'))));
                    var_dump(date("Y-m-d",strtotime($request->request->get('expiryDateadd'))));

                    exit();*/

                    $licenseRecord->setStartDate(new \DateTime($request->request->get('startDateadd')));
                    $licenseRecord->setExpiryDate(new \DateTime($request->request->get('expiryDateadd')));
                    $em->persist($licenseRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add License','info' =>$e->getMessage()));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add License'));
    }


    /**
     * @Route("/editLicense",name="editLicense")
     */
    public function editLicense(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $licenseRecord = $em->getRepository('AppBundle:License')->find($request->request->get('editLicenseID'));
                    $licenseRecord->setUserCount($request->request->get('usercountedit'));
                    $em->persist($licenseRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update License'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update License'));
    }

    /**
     * @Route("/getCompanyLicense/{id}",name="getCompanyLicenses")
     */
    public function getCompanyLicenses($id){

        try {
            $em = $this->getDoctrine()->getManager();
            $branch = $em->getRepository("AppBundle:Branch")->find($id);
            $license = $branch->getCompany()->getCompanyLicense();

            return new JsonResponse(array("status" => "success", "license" => array("id" => $license->getId(), "licecode" => $license->getLicense())));
        } catch (DBALException $e) {
            return new JsonResponse(array("status" => "error", "message" => "Can't get Company's License"));
        }
    }
}
