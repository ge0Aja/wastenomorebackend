<?php

namespace AppBundle\Controller;

use AppBundle\Entity\License;
use AppBundle\Entity\SubLicense;
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
     * @Route("/cms/LicenseRecords",name="LicenseRecords")
     */
    public function getLicenseRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $LicenseRecords = $em->getRepository('AppBundle:License')->findAll();
        return $this->render("agriApp/License/LicenseRecordsInJson.html.twig", ['LicenseRecords' => $LicenseRecords]);
    }


    /**
     * @Route("/cms/Licenses",name="Licenses")
     */
    public function LicensesRecords()
    {
        $length = 16;
        $random = bin2hex(random_bytes($length));
        return $this->render('agriApp/License/LicenseRecords.html.twig',['licenseKey' => $random]);
    }

    /**
     * @Route("/cms/deleteLicense", name="deleteLicense")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $LiceRecord = $em->getRepository('AppBundle:License')->find($request->request->get('delLicenseID'));
            $em->remove($LiceRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/addLicense",name="addLicense")
     */
    public function addLicense(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                $error = 0;
                $error_messages = array("usercount" => "",
                    "date" => "");
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {

                    $licenseRecord = new License();
                    $users_count = $request->request->get('usercountadd');

                    if($users_count == 0){
                        $error = 1;
                        $error_messages["usercount"] = "User Count Can't be 0";
                    }

                    $licenseString = $request->request->get('licenseadd');

                    $licenseRecord->setLicense($licenseString);
                    $licenseRecord->setUserCount($users_count);

                    if(isset($_POST['isActiveadd']))
                        $licenseRecord->setActive(1);
                    else
                        $licenseRecord->setActive(0);

                    $start_date = strtotime($request->request->get('startDateadd'));
                    $expiry_date = strtotime($request->request->get('expiryDateadd'));
                    $today = date("Y-m-d");
                    $today_time = strtotime($today);

                    if($expiry_date < $start_date){
                        $error = 1;
                        $error_messages["date"] = "Expiry Date Can't be less than Starting Date";
                    }

                    if( $expiry_date < $today_time){ //$start_date < $today_time ||
                        $error = 1;
                        if($error_messages["date"] != "")
                            $error_messages["date"] = $error_messages["date"]." And  Expiry Dates Can't be less than today's date";
                        else
                            $error_messages["date"] = "Expiry Dates Can't be less than today's date";
                    }

                    $licenseRecord->setStartDate(new \DateTime($request->request->get('startDateadd')));
                    $licenseRecord->setExpiryDate(new \DateTime($request->request->get('expiryDateadd')));

                    if(isset($_POST['isPremiumadd']))
                        $licenseRecord->setPremium(1);
                    else
                        $licenseRecord->setPremium(0);

                    if($error == 0) {
                        $em->persist($licenseRecord);
                        $em->flush();

                        for ($i = 1; $i <= $users_count; $i++) {
                            $sublicenseRecord = new SubLicense();
                            $sublicenseRecord->setUsed(0);
                            $sublicenseRecord->setLicense($licenseRecord);

                            if($i == 1) {
                                $sublicenseRecord->setIsCompanyManager(1);
                                $sublicenseRecord->setSubLicenseAppRole($em->getRepository("AppBundle:AppRole")->findOneBy(["role" => "COMPANY_MANAGER"]));
                            }
                            else {
                                $sublicenseRecord->setIsCompanyManager(0);
                                $sublicenseRecord->setSubLicenseAppRole($em->getRepository("AppBundle:AppRole")->findOneBy(["role" => "BRANCH_MANAGER"]));
                            }

                            if ($licenseRecord->getActive() == 1)
                                $sublicenseRecord->setActive(1);
                            else
                                $sublicenseRecord->setActive(0);
                            $sublicenseRecord->setSubLicenseString(hash('md5', $licenseString . $i));
                            $em->persist($sublicenseRecord);
                            $em->flush();
                        }
                        $em->getConnection()->commit();
                        return new JsonResponse(array('status' => 'success'));
                    }else{
                        $em->getConnection()->rollBack();
                        return new JsonResponse(array('status' => 'form_error', 'message' => $error_messages));
                    }
                } catch (DBALException $e) {
                    $em->getConnection()->rollBack();
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add License','info' =>$e->getMessage()));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add License'));
    }


    /**
     * @Route("/cms/editLicense",name="editLicense")
     */
    public function editLicense(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {
                    $error = 0;
                    $error_messages = array("usercount" => "",
                        "date" => "");

                    $licenseRecord = $em->getRepository('AppBundle:License')->find($request->request->get('editLicenseID'));

                    if(isset($_POST['isActiveedit']))
                        $licenseRecord->setActive(1);
                    else
                        $licenseRecord->setActive(0);

                    if(isset($_POST['isPremiumedit']))
                        $licenseRecord->setPremium(1);
                    else
                        $licenseRecord->setPremium(0);


                    $newUserCount = $request->request->get('usercountedit');
                    $oldUserCount = $licenseRecord->getUserCount();


                    if($newUserCount < $oldUserCount){
                        $error = 1;
                        $error_messages["usercount"] = "The New User Count Can't be less than the Old User Count";
                    }
                    else
                        $licenseRecord->setUserCount($request->request->get('usercountedit'));

                    $new_start_date = strtotime($request->request->get('startDateedit'));
                    $new_expiry_date = strtotime($request->request->get('expiryDateedit'));

                    $today = date("Y-m-d");
                    $today_time = strtotime($today);


                    if($new_expiry_date < $new_start_date){
                        $error = 1;
                        $error_messages["date"] = "Expiry Date Can't be less than Starting Date";
                    }

                    if( $new_expiry_date < $today_time){ //$new_start_date < $today_time ||
                        $error = 1;
                        if ($error_messages["date"] != "")
                            $error_messages["date"] = $error_messages["date"]." And New Expiry Date Can't be Less than Today's Date";
                        else
                            $error_messages["date"] = "New Expiry Dates Can't be Less than Today's Date";
                    }

                    $licenseRecord->setStartDate(new \DateTime($request->request->get('startDateedit')));
                    $licenseRecord->setExpiryDate(new \DateTime($request->request->get('expiryDateedit')));

                    if($error == 0) {
                        $em->persist($licenseRecord);
                        $em->flush();
                        if($newUserCount > $oldUserCount){
                            for ($i = $oldUserCount+1; $i <= $newUserCount; $i++) {
                                $sublicenseRecord = new SubLicense();
                                $sublicenseRecord->setUsed(0);
                                $sublicenseRecord->setLicense($licenseRecord);
                                if ($licenseRecord->getActive() == 1)
                                    $sublicenseRecord->setActive(1);
                                else
                                    $sublicenseRecord->setActive(0);
                                $sublicenseRecord->setSubLicenseString(hash('md5', $licenseRecord->getLicense() . $i));
                                $em->persist($sublicenseRecord);
                                $em->flush();
                            }
                        }
                        $em->getConnection()->commit();
                        return new JsonResponse(array('status' => 'success'));
                    }else{
                        $em->getConnection()->rollBack();
                        return new JsonResponse(array('status' => 'form_error', 'message' => $error_messages));
                    }
                } catch (DBALException $e) {
                    $em->getConnection()->rollBack();
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update License'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update License'));
    }

    /**
     * @Route("/cms/getCompanyLicense/{id}",name="getCompanyLicenses")
     */
    public function getCompanyLicenses($id){

        try {
            $em = $this->getDoctrine()->getManager();
            $branch = $em->getRepository("AppBundle:Branch")->find($id);

            $license = $branch->getBranchSubLicense()->getLicense();

            return new JsonResponse(array("status" => "success", "license" => array("id" => $license->getId(), "licecode" => $license->getLicense())));
        } catch (DBALException $e) {
            return new JsonResponse(array("status" => "error", "message" => "Can't get Company's License"));
        }
    }
}
