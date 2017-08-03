<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\Branch;
use AppBundle\Entity\Company;
use AppBundle\Entity\CompanyAttributesAndSubAttributes;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{

    /**
     * @Route("/cms/CompanyRecords",name="CompanyRecords")
     */
    public function getCompanyRecords()
    {
       /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $companyRecords = $em->getRepository('AppBundle:Company')->findAll();
        return $this->render("agriApp/Company/companyRecordsInJson.html.twig", ['companyRecords' => $companyRecords]);
    }


    /**
     * @Route("/cms/Companies",name="CompanyRecordsPage")
     */
    public function CompanyRecords()
    {
        return $this->render('agriApp/Company/companyRecords.html.twig');
    }

    /**
     * @Route("/cms/deleteCompany/{id}", name="deleteCompany")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $companyRecord = $em->getRepository('AppBundle:Company')->find($id);
            $em->remove($companyRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }






    /**
     * @Route(path="api/newCompanyRecord", name="newCompanyRecord")
     */
    public function newCompanyRecord(Request $request)
    {
        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error",401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if(empty($content))
                throw new \Exception("Content Error",666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $company_name = $params["company_name"];
                $est_date = $params["est_date"];
                $company_type = $params["company_type"];
                $annual_sales = $params["annual_sales"];

                $Company = new Company();

                $Company->setName($company_name);
                $Company->setCompanyManager($user);
                $Company->setCompanyLicense($user->getSubLicense()->getLicense());
                $Company->setDateOfEstablishment(new \DateTime($est_date));
                $Company->setTotalAnnualSales($em->getRepository('AppBundle:AnnualSalesRanges')->findOneBy(['salesRange' => $annual_sales]));
                $Company->setType($em->getRepository('AppBundle:CompanyType')->findOneBy(["typeName" => $company_type]));

                $user->setManagedCompany($Company);

                $em->persist($Company);
                $em->persist($user);

                $em->flush();
                $em->getConnection()->commit();
                return new JsonResponse(array("status" => "success"));

            }catch (DBALException $e2){
                $em->getConnection()->rollBack();
                throw new \Exception("DB Error",777);
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw  new \Exception("Params Error",666);
            }

        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }

    }

    /**
     * @Route(path="api/newCompanyAttrSubAttr", name="newCompanyAttrSubAttr")
     */
    public function addCompanyAttrsSubAttrs(Request $request){
        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            try {
                $company = $user->getManagedCompany();

                $attribute = $params["attribute"];
                $sub_attribute = $params["sub_attribute"];

                $em = $this->getDoctrine()->getManager();

                $attr_sub_attr = new CompanyAttributesAndSubAttributes();

                $attr_sub_attr->setAttribute($em->getRepository('AppBundle:CompanyTypeAttribute')->findOneBy(["id" => $attribute]));
                $attr_sub_attr->setSubAttribute($em->getRepository('AppBundle:CompanyTypeAttributeSubAttribute')->findOneBy(["id" => $sub_attribute]));
                $attr_sub_attr->setCompany($company);
                $em->persist($attr_sub_attr);
                $em->flush();

                return new JsonResponse(array("status" => "success"));
            }catch (DBALException $e2){
                throw new \Exception("DB Error",777);
            }catch (\Exception $e) {
                throw  new \Exception("Params Error",666);
            }
        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }
    }


    /**
     * @Route(path="api/deleteAttrSubAttr", name="deleteAttrSubAttr")
     */
    public function deleteCompanyAttrsSubAttrs(Request $request){

        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em= $this->getDoctrine()->getManager();
            try {
                $record_id = $params["attr_sub_attr"];

                $company = $user->getManagedCompany();

                $attr_sub_attr = $em->getRepository('AppBundle:CompanyAttributesAndSubAttributes')->findOneBy(["id" => $record_id, "company" => $company->getId()]);

                $em->remove($attr_sub_attr);

                $em->flush();

                return new JsonResponse(array("status" => "success"));
            }catch (DBALException $e){
                throw new \Exception("DB Error",777);

            }catch (\Exception $e2){
                throw  new \Exception("Params Error",666);
            }
        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error" , "reason" => $e->getMessage()));
        }

    }

    /**
     * @Route(path="api/editCompanyBasicInfo", name="editCompanyBasicInfo")
     */
    public function editCompanyBasicInfo(Request $request){

        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();

            try{

                $name = $params["company_name"];
                $est_date = $params["est_date"];
                $annual_sales = $params["annual_sales"];

                $company = $user->getManagedCompany();

                $company->setName($name);
                $company->setDateOfEstablishment(new \DateTime($est_date));
                $company->setTotalAnnualSales($em->getRepository('AppBundle:AnnualSalesRanges')->findOneBy(["id" => $annual_sales]));

                $em->persist($company);
                $em->flush();

                return new JsonResponse(array("status" => "success"));
            }catch (DBALException $e){
                throw new \Exception("DB Error",777);
            }catch (\Exception $e2){
                throw  new \Exception("Params Error",666);
            }

        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error" , "reason" => $e->getMessage()));
        }

    }


    /**
     * @Route(path="api/newCompanyBranch", name="newCompanyBranch")
     */
    public function addCompanyBranch(Request $request){
        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $staff_count = $params["staff_count"];
                $opening_date = $params["opening_date"];
                $main_branch = $params["main_branch"];
                $location = $params["location"];
                //$sub_license = $params["license"];

                $company = $user->getManagedCompany();

                if(null === $company)
                    throw  new \Exception("No Managed Company",666);

                $branch = new Branch();

                $branch->setCompany($company);
                $branch->setStaffCount($staff_count);
                $branch->setOpeningDate(new \DateTime($opening_date));
                $branch->setLocation($em->getRepository('AppBundle:CityTown')->findOneBy(["id" => $location]));

                if($main_branch === true)
                    $company->setMainBranch($branch);

                //  $subLicense = $em->getRepository('AppBundle:SubLicense')->findOneBy(["id" => $sub_license]);

//                if(null === $subLicense)
//                    throw  new Exception("Sub License Error",666);
//
//                $subLicense->setSubLicenseBranch($branch);

                $em->persist($branch);
                $em->persist($company);
                // $em->persist($subLicense);

                $em->flush();

                $em->getConnection()->commit();
                return new JsonResponse(array("status" => "success"));

            }catch (DBALException $e){
                $em->getConnection()->rollBack();
                throw new \Exception("DB Error",777);
            }catch (\Exception $e2){
                $em->getConnection()->rollBack();
                throw  new \Exception("Params Error",666);
            }

        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }

    }

    /**
     * @Route(path="api/editBranchBasicInfo", name="editBranchBasicInfo")
     */
    public function editBranchBasicInfo(Request $request){

        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();
            try{
                $company = $user->getManagedCompany();

                $branch_id = $params["branch"];
                $staff_count = $params["staff_count"];
                $opening_date = $params["opening_date"];
                $location = $params["location"];
                $main_branch = $params["main_branch"];
                // $sub_license_id = $params["sub_license"];

                $branch = $em->getRepository("AppBundle:Branch")->findOneBy(["id" => $branch_id, "Company" => $company->getId()]);
                //  $sublicense = $em->getRepository('AppBundle:SubLicense')->findOneBy(["id" => $sub_license_id,"License" => $company->getCompanyLicense(), "Used" => 0, "active" => 1]);

                $branch->setStaffCount($staff_count);
                $branch->setOpeningDate(new \DateTime($opening_date));
                $branch->setLocation($em->getRepository('AppBundle:CityTown')->findOneBy(["id" => $location]));

                //$branch->get

                if($main_branch === true)
                    $company->setMainBranch($branch);

                $em->persist($branch);
                $em->persist($company);

                $em->flush();

                return new JsonResponse(array("status" => "success"));
            }catch (DBALException $e){
                throw new \Exception("DB Error",777);
            }catch (\Exception $e){
                throw  new \Exception("Params Error",666);
            }
        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error" , "reason" => $e->getMessage()));
        }

    }


    /**
     * @Route(path="api/getCompanyBranchLicensesFree", name="getCompanyBranchLicensesFree")
     */
    public function getCompanyBranchSubLicensesFree(Request $request){
        $sub_licenses = array();
        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $em = $this->getDoctrine()->getManager();

            $company = $user->getManagedCompany();

            if(null === $company)
                throw new \Exception("Company Error",666);

            $subLicenses = $em->getRepository("AppBundle:SubLicense")->findBy(["License" => $company->getCompanyLicense()->getId(),"Used" => 0, "active" => 1, "isCompanyManager" => 0, "SubLicenseBranch" => null]);

            foreach ($subLicenses as $sublicense){
                $sub_licenses[$sublicense->getId()] = $sublicense->getSubLicenseString();
            }

            return new JsonResponse(array("status" => "success", "sublicenses" => $sub_licenses));

        }catch (\Exception $e){

            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }
    }


    /**
     * @Route(path="api/getCompanyBranchLicensesUsed", name="getCompanyBranchLicensesUsed")
     */
    public function getCompanyBranchSubLicensesUsed(Request $request){
        $sub_licenses = array();
        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $em = $this->getDoctrine()->getManager();

            $company = $user->getManagedCompany();

            if(null === $company)
                throw new \Exception("Company Error",666);

            $subLicenses = $em->getRepository("AppBundle:SubLicense")->findBy(["License" => $company->getCompanyLicense()->getId(),"Used" => 1, "active" => 1, "isCompanyManager" => 0, "SubLicenseBranch" != null]);

            foreach ($subLicenses as $sublicense){
                $sub_licenses[$sublicense->getId()] = $sublicense->getSubLicenseString();
            }

            return new JsonResponse(array("status" => "success", "sublicenses" => $sub_licenses));

        }catch (\Exception $e){

            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }
    }


    /**
     * @Route(path="api/setCompanyBranchLicense", name="setCompanyBranchLicense")
     */
    public function setSubLicenseBranch(Request $request){

        try {
            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new \Exception("User Error", 401);

            if($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new \Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new \Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();

            try{
                $company = $user->getManagedCompany();

                $sub_license_id = $params["sub_license"];
                $branch_id = $params["branch"];

                $sub_license = $em->getRepository("AppBundle:SubLicense")->findOneBy(["isCompanyManager" => 0 , "Used" => 0, "active" => 1 , "id" => $sub_license_id, "License" => $company->getCompanyLicense()->getId()]);

                $branch = $em->getRepository("AppBundle:Branch")->findOneBy(["id" => $branch_id, "Company" => $company->getId()]);

                $sub_license->setSubLicenseBranch($branch);

                return new JsonResponse(array("status" => "success"));

            }catch (DBALException $e){
                throw new \Exception("DB Error",777);
            }catch (\Exception $e2){
                throw  new \Exception("Params Error",666);
            }

        }catch (\Exception $e){
            return new JsonResponse(array("status" => "error" , "reason" => $e->getMessage()));
        }

    }


    private function getLoggedUser(Request $request){
        try{
            $token =  $this->get('app.jwt_token_authenticator')->getCredentials($request);

            if(null === $token)
                throw new \Exception("Invalid token",401);

            $usr = $this->get('lexik_jwt_authentication.jwt_manager')->decode(new PreAuthenticationJWTUserToken($token));


            //var_dump($usr);

            //var_dump($usr["username"]);
            if(null === $usr)
                throw new \Exception("Invalid User",401);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')->findOneBy(["username" => $usr["username"]]);

            //var_dump($user);
           // exit();

            return $user;

        }catch (\Exception $e){
           // var_dump($e->getMessage());
            return null;
        }

    }


}
