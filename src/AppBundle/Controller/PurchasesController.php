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
                $purchaseRecord->setTimestamp(strtotime($purchaseDate));

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
