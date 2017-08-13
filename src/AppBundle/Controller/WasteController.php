<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WasteController extends Controller
{
    /**
     * @Route("/cms/WasteLogs",name="wasteLogs")
     */
    public function WasteLogs()
    {
        $em = $this->getDoctrine()->getManager();
        $wasteLogs = $em->getRepository('AppBundle:Waste')->findAll();
//        return $this->render('agriApp/wasteLogs.html.twig',['wasteLogs' => $wasteLogs]);
        return $this->render('agriApp/Waste/wasteLogs.html.twig',['wasteLogs' => $wasteLogs]);
    }

    /**
     * @Route("/cms/renderWasteLogs",name="renderWasteLogs")
     */
    public function renderWasteLogs()
    {
        $em = $this->getDoctrine()->getManager();
        $wasteLogs = $em->getRepository('AppBundle:Waste')->findAll();
        return $this->render("agriApp/Waste/wasteLogsInJson.html.twig", ['wasteLogs' => $wasteLogs]);
    }


    /**
     * @Route("/cms/deleteWasteLog/{id}", name="delete")
     */
    public function deleteWasteLog($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $wasteLog = $em->getRepository('AppBundle:Waste')->find($id);
            $em->remove($wasteLog);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route(path="api/getWasteGraph1", name="getWasteGraph1")
     */
    public function getWasteBar(Request $request){

        try{

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);


            $content = $request->getContent();

            $params = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();

            try{

                $branch_checked_id = null;
                $company_id = null;
                $branches_array = array();
                $isPremiumLicense = $user->getSubLicense()->getLicense()->getPremium();

                if($isPremiumLicense == 1) {
                    if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER) {

                        $branches_records = $em->getRepository("AppBundle:Branch")->findBy(["Company" => $user->getManagedCompany()->getId()]);

                        foreach($branches_records as $branch) {

                            array_push($branches_array,array("id"=> $branch->getId(),"city" => $branch->getLocation()->getName(),"address" => $branch->getAddress()));
                        }

                        $company_id = $user->getManagedCompany()->getId();

                        if (!empty($content) && array_key_exists("branch", $params)){

                            $branch_id = (int) $params["branch"];

                            $checkbranch = $em->getRepository("AppBundle:Branch")->findOneBy(["Company" => $company_id, "id" => $branch_id]);


                            if($checkbranch != null)
                                $branch_checked_id = $checkbranch->getId();
                        }

                    }

                    if(!empty($content) && array_key_exists("toDate",$params) && !empty($params["toDate"]) && !empty($params["fromDate"]) )
                        $toDate = date("Y-m-d",strtotime($params["toDate"]));
                    else
                        $toDate = date("Y-m-d");

                    if(!empty($content) && array_key_exists("fromDate",$params) && !empty($params["fromDate"]) && !empty($params["toDate"]))
                        $fromDate = date("Y-m-d",strtotime($params["fromDate"]));
                    else
                        $fromDate = date("Y-m-d",strtotime($toDate.' -1 months'));
                }else{

                    if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER) {
                        $branch_checked_id = $user->getCompanyBranch()->getId();
                    }else{
                        $company_id = $user->getManagedCompany()->getId();
                    }


                    $toDate = date("Y-m-d");
                    $fromDate = date("Y-m-d",strtotime($toDate.' -1 months'));
                }


                if($fromDate > $toDate)
                    throw new Exception("Date Error", 777);

                $qb = $em->createQueryBuilder();

                if($branch_checked_id != null){
                    $qb->select(" wtc.name as Name, case u.name when 'Kg' then sum(w.quantity) else sum(w.quantity*c.quanInKg)/c.quan END as Quantity")
                        ->from("AppBundle:Waste","w")
                        ->join("w.unit","u")
                        ->join("w.waste_type_subcategory","wts")
                        ->leftJoin("wts.conversionT","c")
                        ->join("w.branch","b")
                        ->join("w.waste_type_category","wtc")
                        ->where("b.id = :branchId and w.waste_date >= :fromDate and w.waste_date <= :toDate")
                        ->groupBy("wtc.id")
                        ->setParameter("fromDate",$fromDate)
                        ->setParameter("toDate", $toDate)
                        ->setParameter("branchId", $branch_checked_id);

                }else{
                    $qb->select("wtc.name as Name, case u.name when 'Kg' then sum(w.quantity) else sum(w.quantity*c.quanInKg)/c.quan END as Quantity")
                        ->from("AppBundle:Waste","w")
                        ->join("w.unit","u")
                        ->join("w.waste_type_subcategory","wts")
                        ->leftJoin("wts.conversionT","c")
                        ->join("w.branch","b")
                        ->join("w.waste_type_category","wtc")
                        ->where("b.Company = :companyId and w.waste_date >= :fromDate and w.waste_date <= :toDate")
                        ->groupBy("wtc.id")
                        ->setParameter("fromDate",$fromDate)
                        ->setParameter("toDate", $toDate)
                        ->setParameter("companyId", $company_id);

                }

                $query = $qb->getQuery();


                $temparray = $query->getArrayResult();


                $total_waste = 0.0;
                $categories = array();
                $data_array = array();
                foreach($temparray as $tempas){
                    $total_waste+= floatval($tempas["Quantity"]);
                }

                foreach ($temparray as $item){
                    array_push($categories,$item["Name"]);
                    array_push($data_array,round((floatval($item["Quantity"])/$total_waste)*100,2));
                }

                if($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER)
                    return new JsonResponse(array("status"=>"success","categories" => $categories,"data" => $data_array, "premium" => ($isPremiumLicense == 1) ? true : false, "branches" => $branches_array));
                else
                    return new JsonResponse(array("status"=>"success","categories" => $categories,"data" => $data_array, "premium" => ($isPremiumLicense == 1) ? true : false));

            }catch (DBALException $e){
                throw new Exception("DB Error", 777);
            }
            catch (Exception $e){
                throw  new Exception("Params Error", 666);
            }
//            catch (\Throwable $t){
//                throw  new Exception("Null Error", 666);
//            }


        }catch (Exception $e){
            return new JsonResponse(array("status" => "error","reason" => $e->getMessage()));
        }

    }


    private function getLoggedUser(Request $request)
    {
        try {
            $token = $this->get('app.jwt_token_authenticator')->getCredentials($request);

            if (null === $token)
                throw new Exception("Invalid token", 401);

            $usr = $this->get('lexik_jwt_authentication.jwt_manager')->decode(new PreAuthenticationJWTUserToken($token));


            //var_dump($usr);

            //var_dump($usr["username"]);

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
