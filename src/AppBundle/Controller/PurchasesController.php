<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\Purchases;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PurchasesController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/PurchaseRecords",name="PurchaseRecords")
     */
    public function getBranchRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $PurchaseRecords = $em->getRepository('AppBundle:Purchases')->findAll();
        return $this->render("agriApp/Purchase/purchasesRecordsInJson.html.twig", ['PurchaseRecords' => $PurchaseRecords]);
    }


    /**
     * @Route("/cms/Purchases",name="Purchase")
     */
    public function BranchRecords()
    {
        /* $em = $this->getDoctrine()->getManager();
         $FoodsubTypes = $em->getRepository('AppBundle:WasteTypeCategorySubCategory');*/
        return $this->render('agriApp/Purchase/purchasesRecords.html.twig');
    }


    /**
     * @Route("/cms/deletePurchase", name="deletePurchase")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $purchaseRecord = $em->getRepository('AppBundle:Branch')->find($request->request->get('purchasedelID'));
            $em->remove($purchaseRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route(path="api/addBranchPurchase", name="addBranchPurchase")
     */
    public function addBranchPurchase(Request $request)
    {
        try {

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);

            $branch = $user->getCompanyBranch();

            if (null == $branch)
                throw  new Exception("Params Error", 666);

            $content = $request->getContent();

            if (null == $content)
                throw  new Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();

            try {

                if (!array_key_exists("item", $params) ||
                    !array_key_exists("unit", $params) ||
                    !array_key_exists("quantity", $params) ||
                    !array_key_exists("cost", $params) ||
                    !array_key_exists("date", $params)) {
                    throw new Exception("Params Error", 666);
                }

                $purchaseItem = (int)$params["item"];
                $purchaseUnit = (int)$params["unit"];
                $purchaseQuantity = floatval($params["quantity"]);
                $purchaseCost = floatval($params["cost"]);
                $purchaseDate = $params["date"];

                $purchaseRecord = new Purchases();

                $purchaseRecord->setBranch($user->getCompanyBranch());
                $purchaseRecord->setEstimateOfCost($purchaseCost);
                $purchaseRecord->setQuantity($purchaseQuantity);
                $purchaseRecord->setDate(new \DateTime($purchaseDate));
                $purchaseRecord->setTimestamp(strtotime($purchaseDate)."000");

                $purchaseRecord->setUnit($em->getRepository("AppBundle:Unit")->findOneBy(["id" => $purchaseUnit]));
                $purchaseRecord->setType($em->getRepository("AppBundle:WasteTypeCategorySubCategory")->findOneBy(["id" => $purchaseItem]));


                $em->persist($purchaseRecord);
                $em->flush();

                return new JsonResponse(array("status" => "success"));

            } catch (Exception $e) {
                throw new Exception($e->getMessage(), 666);

            } catch (DBALException $e) {
                throw new Exception("DB Error", 666);
            } catch (\Throwable $t) {
                throw new Exception("Null Error", 777);

            }

        } catch (Exception $e) {
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }

    }


    /**
     * @Route(path="api/getPurchaseTimeSeries", name="getPurchaseTimeSeries")
     */
    public function getPurchaseTimeSeries(Request $request)
    {
        try {

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            $content = $request->getContent();

            $params = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();

            try {

                $branch_checked_id = null;
                $company_id = null;
                $branches_array = array();
                $isPremiumLicense = $user->getSubLicense()->getLicense()->getPremium();

                if ($isPremiumLicense == 1) {
                    if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER) {

                        $branches_records = $em->getRepository("AppBundle:Branch")->findBy(["Company" => $user->getManagedCompany()->getId()]);

                        array_push($branches_array, array("key" => 0, "label" => "Choose Branch"));

                        foreach ($branches_records as $branch) {

                            array_push($branches_array, array("key" => $branch->getId(), "label" => $branch->getLocation()->getName() . '-' . $branch->getAddress()));
                        }

                        $company_id = $user->getManagedCompany()->getId();

                        if (!empty($content) && array_key_exists("branch", $params)) {

                            $branch_id = (int)$params["branch"];

                            $checkbranch = $em->getRepository("AppBundle:Branch")->findOneBy(["Company" => $company_id, "id" => $branch_id]);


                            if ($checkbranch != null)
                                $branch_checked_id = $checkbranch->getId();
                        }

                    }else{


                        $checkbranch = $user->getCompanyBranch();

                        if ($checkbranch != null)
                            $branch_checked_id = $checkbranch->getId();

                    }
                } else {

                    if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER) {
                        $branch_checked_id = $user->getCompanyBranch()->getId();
                    } else {
                        $company_id = $user->getManagedCompany()->getId();
                    }

                    $toDate = date("Y-m-d");
                    $fromDate = date("Y-m-d", strtotime($toDate . ' -1 months'));
                }

                $qb = $em->createQueryBuilder();

                if ($branch_checked_id != null) {

                    if($isPremiumLicense){

                        $qb->select(" p.timestamp as Timestamp,wtc.name as Name,  case u.name when 'Kg' then (p.quantity) else (p.quantity*c.quanInKg)/c.quan END as Quantity")
                            ->from("AppBundle:Purchases", "p")
                            ->join("p.unit", "u")
                            ->join("p.type", "wts")
                            ->leftJoin("wts.conversionT", "c")
                            ->join("p.branch", "b")
                            ->join("wts.category_type", "wtc")
                            ->where("b.id = :branchId ")

                            ->setParameter("branchId", $branch_checked_id);

                    }else{

                        $qb->select(" p.timestamp as Timestamp,wtc.name as Name,  case u.name when 'Kg' then (p.quantity) else (p.quantity*c.quanInKg)/c.quan END as Quantity")
                            ->from("AppBundle:Purchases", "p")
                            ->join("p.unit", "u")
                            ->join("p.type", "wts")
                            ->leftJoin("wts.conversionT", "c")
                            ->join("p.branch", "b")
                            ->join("wts.category_type", "wtc")
                            ->where("b.id = :branchId and p.date >= :fromDate and p.date <= :toDate")
                            ->setParameter("fromDate", $fromDate)
                            ->setParameter("toDate", $toDate)
                            ->setParameter("branchId", $branch_checked_id);

                    }



                } else {

                    if($isPremiumLicense){
                        $qb->select("p.timestamp as Timestamp,wtc.name as Name,  case u.name when 'Kg' then (p.quantity) else (p.quantity*c.quanInKg)/c.quan END as Quantity")
                            ->from("AppBundle:Purchases", "p")
                            ->join("p.unit", "u")
                            ->join("p.type", "wts")
                            ->leftJoin("wts.conversionT", "c")
                            ->join("p.branch", "b")
                            ->join("wts.category_type", "wtc")
                            ->where("b.Company = :companyId")
                            ->orderBy('Name', 'ASC')
                            ->setParameter("companyId", $company_id);

                    }else{
                        $qb->select("p.timestamp as Timestamp,wtc.name as Name,  case u.name when 'Kg' then (p.quantity) else (p.quantity*c.quanInKg)/c.quan END as Quantity")
                            ->from("AppBundle:Purchases", "p")
                            ->join("p.unit", "u")
                            ->join("p.type", "wts")
                            ->leftJoin("wts.conversionT", "c")
                            ->join("p.branch", "b")
                            ->join("wts.category_type", "wtc")
                            ->where("b.Company = :companyId  and p.date >= :fromDate and p.date <= :toDate")
                            ->orderBy('Name', 'ASC')
                            ->setParameter("fromDate", $fromDate)
                            ->setParameter("toDate", $toDate)
                            ->setParameter("companyId", $company_id);
                    }

                }
                $query = $qb->getQuery();
                $temparray = $query->getArrayResult();

                $graph = array();
                $data_array = array();
                foreach ($temparray as $tempas2) {
                    $quantity = floatval($tempas2["Quantity"]);
                    $timeStamp = $tempas2["Timestamp"] + 0;
                    $graph[$tempas2["Name"]][] = [ $timeStamp, $quantity];
                }

                foreach ($graph as $key => $value){
                    array_push($data_array,array("name" => $key, "data" => $value));
                }

                if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER)
                    return new JsonResponse(array("status" => "success", "data" => $data_array, "premium" => ($isPremiumLicense == 1) ? true : false, "branches" => $branches_array));
                else
                    return new JsonResponse(array("status" => "success", "data" => $data_array, "premium" => ($isPremiumLicense == 1) ? true : false, "branches" => []));

            } catch (DBALException $e) {
                throw new Exception("DB Error", 777);
            } catch (Exception $e) {
                throw  new Exception("Params Error", 666);
            }
            catch (\Throwable $t) {
                throw  new Exception("Null Error", 666);
            }
        } catch (Exception $e) {
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
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
