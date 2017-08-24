<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\WasteTypeCategorySubCategory;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WasteTypeSubCategoryController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/WasteTypeSubCategoryRecords",name="WasteTypeSubCategoryRecords")
     */
    public function getCitiesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $SubCatRecords = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->findAll();
        return $this->render("agriApp/WasteTypeSubCategory/WasteTypeSubCategoriesInJson.html.twig", ['WasteSubCatRecords' => $SubCatRecords]);
    }


    /**
     * @Route("/cms/WasteTypeSubCategories",name="WasteTypeSubCategories")
     */
    public function CitiesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $CatRecords = $em->getRepository('AppBundle:WasteTypeCategory')->findAll();
        return $this->render('agriApp/WasteTypeSubCategory/WasteTypeSubCategories.html.twig', ['wasteTypeCats' => $CatRecords]);
    }


    /**
     * @Route("/cms/addSubCat",name="addSubCat")
     */
    public function addCity(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = new WasteTypeCategorySubCategory();
                    $WasteTypeCat = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('wastetypecat_select'));
                    $cityrecord->setName($request->request->get('txt_subcat_add'));
                    $cityrecord->setCategoryType($WasteTypeCat);
                    $em->persist($cityrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add  Waste Type Sub-Category'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Waste Type Sub-Category'));
    }

    /**
     * @Route("/cms/deleteSubCat", name="deleteSubCat")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $SubCatRecord = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->find($request->request->get('h_subcat_del'));
            $em->remove($SubCatRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/editSubCat", name="editSubCat")
     */
    public function editCities(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $subcatrecord = $em->getRepository('AppBundle:WasteTypeCategorySubCategory')->find($request->request->get('h_subcat_edit'));
                    $subcatrecord->setName($request->request->get('txt_subcat_edit'));
                    $WasteTypeCat = $em->getRepository('AppBundle:WasteTypeCategory')->find($request->request->get('wastetypecat_select_edit'));
                    $subcatrecord->setCategoryType($WasteTypeCat);
                    $em->persist($subcatrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Sub-Category'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update  Waste Type Sub-Category'));
    }


    /**
     * @Route(path="api/getBranchWasteSubCats",name="getBranchWasteSubCats")
     */
    public function getSubCategoriesFromPurchases(Request $request)
    {

        try {

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);


            $em = $this->getDoctrine()->getManager();

            try {

                $branch = $user->getCompanyBranch();

                if(null == $branch)
                    throw  new Exception("Params Error",666);

                $qb = $em->createQueryBuilder();

                $qb->select("DISTINCT wts.id, wts.name")
                    ->from("AppBundle:WasteTypeCategorySubCategory", "wts")
                    ->join("wts.purchases","p")
                    ->where("p.branch = :branch")
                    ->orderBy("wts.name")
                    ->setParameter('branch', $branch);

                $query = $qb->getQuery();

                $SubCatsRecords = $query->getResult();

                $wasteReasonsRecords = $em->getRepository("AppBundle:Reason")->findAll();

                $collectCompnyRecord = $em->getRepository("AppBundle:CollectingCompany")->findAll();


                $subCategories = array();
                $wasteReasons = array();
                $collectingCompanies = array();

                foreach ($SubCatsRecords as $record){

                    $units = array();

                    $units_records = $em->getRepository("AppBundle:SubCategoryUnit")->findBy(["subcategory" => $record["id"]]);

                    foreach ($units_records as $units_record){

                        array_push($units,array("key" => $units_record->getUnit()->getId(),"label" => $units_record->getUnit()->getName()));

                    }

                    array_push($subCategories,array("id" => $record["id"], "name" => $record["name"], "units" => $units));
                }

                foreach ($wasteReasonsRecords as $reason){

                   array_push($wasteReasons,array("key" => $reason->getId(), "label" =>  $reason->getReason())) ;

                }

                foreach ($collectCompnyRecord as $comp){
                    array_push($collectingCompanies, array("key" => $comp->getId(), "label" => $comp->getName()));
                }

                return new JsonResponse(array("status" => "success" , "items" => $subCategories, "reasons" => $wasteReasons, "companies" => $collectingCompanies)); //,

            } catch (Exception $e) {
                throw new Exception("Param Error",666);

            } catch (DBALException $e){
                throw new Exception("DB Error",666);

            }
            catch (\Throwable $t){
                throw new Exception("Null Error",777);
            }

        } catch (Exception $e) {
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }


    }


    /**
     * @Route(path="api/getPurchSubCats", name="getPurchSubCats")
     */
    public function getSubCategoriesForPuchases(Request $request){

        try {

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() == AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);


            $em = $this->getDoctrine()->getManager();

            $branch = $user->getCompanyBranch();

            if(null == $branch)
                throw  new Exception("Params Error",666);

            try{

                $SubCatsRecords = $em->getRepository("AppBundle:WasteTypeCategorySubCategory")->findAll();

                $subCategories = array();

                foreach ($SubCatsRecords as $record){

                    $units = array();

                    $units_records = $em->getRepository("AppBundle:SubCategoryUnit")->findBy(["subcategory" => $record->getId()]);

                    foreach ($units_records as $units_record){

                        array_push($units,array("key" => $units_record->getUnit()->getId(),"label" => $units_record->getUnit()->getName()));

                    }

                    array_push($subCategories,array("id" => $record->getId(), "name" => $record->getName(), "units" => $units));
                }

                return new JsonResponse(array("status" => "success", "items" => $subCategories));

            }catch (Exception $e){
                throw new Exception("Params Error",666);
            }catch (DBALException $e){
                throw new Exception("DB Error",666);
            }catch (\Throwable $t){
                throw new Exception("Null Error",777);
            }
        }catch (Exception $e){
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
