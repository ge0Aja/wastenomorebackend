<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BranchController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/cms/BranchRecords",name="BranchRecords")
     */
    public function getBranchRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $BranchRecords = $em->getRepository('AppBundle:Branch')->findAll();
        return $this->render("agriApp/Branch/BranchRecordsInJson.html.twig", ['BranchRecords' => $BranchRecords]);
    }


    /**
     * @Route("/cms/Branches",name="Branches")
     */
    public function BranchRecords()
    {
        return $this->render('agriApp/Branch/branchRecords.html.twig');
    }



    /**
     * @Route("/cms/deleteBranch", name="deleteBranch")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $branchRecord = $em->getRepository('AppBundle:Branch')->find($request->request->get('barnchdelID'));
            $em->remove($branchRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route(path="api/getCompanyBranches", name="getCompanyBranches")
     */
    public function getBranchesApi(Request $request){
        $branches = array();
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


            $branchesRecords = $em->getRepository('AppBundle:Branch')->findBy(["Company" => $company->getId()]);

            foreach ($branchesRecords as $branchesRecord){
                $branch = array("BranchId" => $branchesRecord->getId(),"location" => $branchesRecord->getLocation()->getName(),
                    "location_district" => $branchesRecord->getLocation()->getDistrict()->getName(),
                    "location_governorate" => $branchesRecord->getLocation()->getDistrict()->getGovernorate()->getName(),
                    "staff_count" => $branchesRecord->getStaffCount(),
                    "opening_date" => $branchesRecord->getOpeningDate(),
                    "address" => $branchesRecord->getAddress(),
                    "mainBranch" => ($company->getMainBranch()->getId() == $branchesRecord->getId())? true : false);

                array_push($branches,$branch);
            }

            return new JsonResponse(array("status" => "success" , "branches" => $branches));

        }catch (\Exception $e) {
            return new JsonResponse(array("status" => "error", "reason" => "Params error"));
        } catch (DBALException $e){
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
                throw new \Exception("Invalid token", 401);

            $usr = $this->get('lexik_jwt_authentication.jwt_manager')->decode(new PreAuthenticationJWTUserToken($token));


            if (null === $usr)
                throw new \Exception("Invalid User", 401);

            if (null === $usr)
                throw new \Exception("Invalid User", 401);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:User')->findOneBy(["username" => $usr["username"]]);

            return $user;

        } catch (\Exception $e) {
            return null;
        }

    }
}
