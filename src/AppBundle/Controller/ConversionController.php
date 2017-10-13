<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conversion;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ConversionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/cms/ConversionRecords",name="ConversionRecords")
     */
    public function getCoversionRecords()
    {
        $em =  $this->getDoctrine()->getManager();
        $conversionRecords = $em->getRepository('AppBundle:Conversion')->findAll();
        return $this->render(':agriApp/Conversion:ConversionRecordsInJson.html.twig',["conversionRecords" => $conversionRecords]);
    }

    /**
     * @Route("/cms/Conversions",name="Conversions")
     */
    public function getConversions(){

        $subcats = $this->getSubCatsRecordsNotInConversion();
        return $this->render(":agriApp/Conversion:ConversionRecords.html.twig",["Subcats" => $subcats]);
    }


    /**
     * @Route("/cms/getSubCatsForConversion",name="getSubCatsForConversion")
     */
    public function getSubCatsToUpdateDDl(){
        $subcats = $this->getSubCatsRecordsNotInConversion();
        if($subcats != null) {
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $subcats_json = $serializer->serialize($subcats, 'json');

            return new JsonResponse(array("status" => "success", "subcats" => $subcats_json));
        }

        return new JsonResponse(array("status" => "error"));
    }

    /**
     * @Route("/cms/editConversion",name="editConversion")
     */
    public function editConversion(Request $request)
{
    if ($request->getMethod() == "POST") {
        if ($request->request) {
            try {
                $em = $this->getDoctrine()->getManager();

                $conversionRecord = $em->getRepository("AppBundle:Conversion")->find(
                    $request->request->get("editconversionid")
                );

                $conversionRecord->setQuan($request->request->get("quanedit"));
                $conversionRecord->setQuanInKg($request->request->get("quankgedit"));
                $conversionRecord->setSubcategory($em->getRepository("AppBundle:WasteTypeCategorySubCategory")->find(
                    $request->request->get("subcatedit")
                ));
                $conversionRecord->setUnit($em->getRepository("AppBundle:Unit")->find(
                    $request->request->get("unitedit")
                ));
                $em->persist($conversionRecord);
                $em->flush();

                return new JsonResponse(array("status" => "success"));
            } catch (DBALException $e) {
                return new JsonResponse(array("status" => "error" , "message" => "Can't edit Conversion Record"));
            }
        }
    }
    return new JsonResponse(array("status" => "error" , "message" => "Can't edit Conversion Record"));
}

    /**
     * @Route("/cms/delConversion", name="delConversion")
     */
    public function delConversion(Request $request){
        if($request->getMethod() == "POST"){
            if($request->request){
                try{
                    $em = $this->getDoctrine()->getManager();
                    $conversionrecord = $em->getRepository("AppBundle:Conversion")->find($request->request->get("delConversionID"));

                    $em->remove($conversionrecord);
                    $em->flush();
                    return new JsonResponse(array("status" => "success"));
                }catch (DBALException $e){

                    return new JsonResponse(array("status" => "error", "message" => "Can't delete Conversion Record"));

                }
            }
        }
        return new JsonResponse(array("status" => "error", "message" => "Can't delete Conversion Record"));
    }


    /**
     * @Route("/cms/addConversion", name="addConversion")
     */
    public function addConversion(Request $request)
    {
        if ($request->getMethod() == "POST") {
            if ($request->request) {
                try {
                //    var_dump($request->request);
                    $em = $this->getDoctrine()->getManager();
                    $conversionRecord = new Conversion();
                    $conversionRecord->setQuan($request->request->get("quanadd"));
                    $conversionRecord->setQuanInKg($request->request->get("quankgadd"));
                    $conversionRecord->setSubcategory($em->getRepository("AppBundle:WasteTypeCategorySubCategory")->find(
                        $request->request->get("subcatadd")
                    ));
                    $conversionRecord->setUnit($em->getRepository("AppBundle:Unit")->find(
                        $request->request->get("unitadd")
                    ));
                    $em->persist($conversionRecord);
                    $em->flush();

                    return new JsonResponse(array("status" => "success"));
                } catch (DBALException $e) {
                    return new JsonResponse(array("status" => "error", "message" => "Can't add Conversion Record"));
                }
            }
        }
        return new JsonResponse(array("status" => "error", "message" => "Can't add Conversion Record"));
    }

    private function getUnitsForConversion($id)
    {
        $em = $this->getDoctrine()->getManager();
        $subcat = $em->getRepository("AppBundle:WasteTypeCategorySubCategory")->find($id);

        $units = $subcat->getSubcatunitNotKg()->map(function ($sbu){
            return $sbu->getUnit();
        });
        try {
            return $units;
        }catch(NoResultException $e){
            return null;
        }
    }

    /**
     * @Route("/cms/getUnitsUpdateDDL/{id}",name="getUnitsUpdateDDL")
     */
    public function getUnitsToUpdateDDl($id)
    {
        $units = $this->getUnitsForConversion($id);

        if($units != null) {
            $units_to_json = $units->map(function ($unit) {
                return array("id" => $unit->getId(), "name" => $unit->getName());
            });

///  var_dump($units_to_json);

            $encoder =  new JsonEncoder();
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceHandler(function ($object) {
                return $object->getId();
            });
            $serializer = new Serializer(array($normalizer), array($encoder));
            $units_json = $serializer->serialize($units_to_json, 'json');

          //  var_dump($units_json);
//
         //   var_dump(json_encode($units_json));

            return new JsonResponse(array("status" => "success" , "units" => $units_json));
        }
        return new JsonResponse(array("status" => "error"));

    }

    private function getSubCatsRecordsNotInConversion()
    {
        $em =  $this->getDoctrine()->getManager();

        $query=  $em->createQuery('SELECT sc.id, sc.name from AppBundle:WasteTypeCategorySubCategory sc 
                                  Left OUTER JOIN sc.conversionT c
                                  WHERE c.subcategory is null order by sc.name');
        try{
            return $query->getResult();
        }catch(NoResultException $e) {
            return null;
        }

    }

}
