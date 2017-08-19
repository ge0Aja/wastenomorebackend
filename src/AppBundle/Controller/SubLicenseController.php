<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubLicenseController extends Controller
{

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
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        try {

            $SubLiceRecord = $em->getRepository('AppBundle:SubLicense')->find($request->request->get('delSubLicenseID'));
            $LicenseRecord = $SubLiceRecord->getLicense();
            $LicenseRecord->setUserCount($LicenseRecord->getUserCount() -1);
            $em->remove($SubLiceRecord);
            $em->persist($LicenseRecord);
            $em->flush();
            $em->getConnection()->commit();
            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            $em->getConnection()->rollBack();
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/changeSubLicenseIsManager", name="changeSubLicenseIsManager")
     */
    public function changeSubLicenseIsManager(Request $request){
        try{
            $em = $this->getDoctrine()->getManager();
            $SubLiceRecord = $em->getRepository('AppBundle:SubLicense')->find($request->request->get('editSubLicenseID'));

            if($_POST["isManager"] == 1)
                $SubLiceRecord->setIsCompanyManager(1);
            else
                $SubLiceRecord->setIsCompanyManager(0);
            $em->persist($SubLiceRecord);
            $em->flush();
            return new JsonResponse(array('status' => 'success'));
        }catch (DBALException $e){
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit SubLicense'));
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


    /**
     * @Route(path="api/getCompanyLicenses", name="getCompanyLicenses")
     */
    public function getCompanySubLicenses(Request $request){
        $subLicenses = array();
        try{
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $em = $this->getDoctrine()->getManager();

            $company = $user->getManagedCompany();


            if (null === $company)
                throw new \Exception("Company Error", 666);

            $subLicensesRecords = $em->getRepository('AppBundle:SubLicense')->findBy(["active" => 1,"License" => $company->getCompanyLicense(), "isCompanyManager" => 0]);


            foreach ($subLicensesRecords as $subLicenseRecord){
                $sublic = array("subLicId" => $subLicenseRecord->getId(),
                    "sublicString" => $subLicenseRecord->getSubLicenseString(),
                    "used" => $subLicenseRecord->getUsed(),
                    "userEmail" => ($subLicenseRecord->getUsed() == 1)? $subLicenseRecord->getSubLicenseUser()->getEmail() : "NA",
                    "branchId" => ($subLicenseRecord->getSubLicenseBranch() != null)? $subLicenseRecord->getSubLicenseBranch()->getId() : "NA",
                    "branchLocation" => ($subLicenseRecord->getSubLicenseBranch() != null)? $subLicenseRecord->getSubLicenseBranch()->getLocation()->getName()."-".$subLicenseRecord->getSubLicenseBranch()->getAddress() : "NA");

                array_push($subLicenses,$sublic);
            }

            return new JsonResponse(array("status" => "success", "licenses" => $subLicenses));

        }catch (Exception $e){
            return new JsonResponse(array("status" => "error", "reason" => "Params error"));
        }catch (DBALException $e){
            return new JsonResponse(array("status" => "error", "reason" => "DB error"));
        }
        catch (\Throwable $t) {
            return new JsonResponse(array("status" => "error", "reason" => "Null Error"));
        }

    }


    private function getLoggedUser(Request $request)
    {
        try {
            $token = $this->get('app.jwt_token_authenticator')->getCredentials($request);

            if (null === $token)
                throw new Exception("Invalid token", 401);

            $usr = $this->get('lexik_jwt_authentication.jwt_manager')->decode(new PreAuthenticationJWTUserToken($token));

            if (null === $usr)
                throw new Exception("Invalid User", 401);

            if (null === $usr)
                throw new Exception("Invalid User", 401);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')->findOneBy(["username" => $usr["username"]]);

            return $user;

        } catch (Exception $e) {
            return null;
        }

    }



}
