<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\CityTown;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }


    /**
     * @Route("/cms/CitiesRecords",name="CitiesRecords")
     */
    public function getCitiesRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $CityRecords = $em->getRepository('AppBundle:CityTown')->findAll();
        return $this->render("agriApp/City/CityRecordsInJson.html.twig", ['CityRecords' => $CityRecords]);
    }


    /**
     * @Route("/cms/Cities",name="CitiesRecordsPage")
     */
    public function CitiesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $DistictRecords = $em->getRepository('AppBundle:District')->findAll();
        return $this->render('agriApp/City/citiesRecords.html.twig',['districts' => $DistictRecords]);
    }



    /**
     * @Route("/cms/addCity",name="addCity")
     */
    public function addCity(Request $request){
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = new CityTown();
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_select'));
                    $cityrecord->setName($request->request->get('txt_city_add'));
                    $cityrecord->setDistrict($distr);
                    $em->persist($cityrecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add city'));
                }
                }
            }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add city'));
    }

    /**
     * @Route("/cms/deleteCity", name="deleteCity")
     */
    public function DeleteLogAction(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $CityRecord = $em->getRepository('AppBundle:CityTown')->find($request->request->get('h_cityID_del'));
            $em->remove($CityRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/cms/editCity", name="editCity")
     */
    public function editCities(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $cityrecord = $em->getRepository('AppBundle:CityTown')->find($request->request->get('h_cityID_edit'));
                    $cityrecord->setName($request->request->get('txt_city_edit'));
                    $distr = $em->getRepository('AppBundle:District')->find($request->request->get('district_select_edit'));
                    $cityrecord->setDistrict($distr);
                    $em->persist($cityrecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update city'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update city'));
    }


    /**
     * @Route(path="api/getLocations", name="getLocations")
     */
    public function getLocationsApi(Request $request){
        $locations = array();
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


            $locations_records = $em->getRepository('AppBundle:CityTown')->findBy(array(),array('name' => 'ASC'));

            foreach ($locations_records as $locations_record) {
                $loc = array("city" => $locations_record->getName(),
                    "district" => $locations_record->getDistrict()->getName(),
                    "governorate" => $locations_record->getDistrict()->getGovernorate()->getName());

                array_push($locations,$loc);
            }

            return new JsonResponse(array("status" => "success", "location" => $locations));
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


            //var_dump($usr);

            //var_dump($usr["username"]);

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
