<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SurveyQuestion;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SurveyQuestionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/SurveyQuestions",name="SurveyQuestions")
     */
    public function getSurveyQuestions()
    {
        /*$entities = array();
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = str_replace("AppBundle\Entity\\"," ",$m->getName());
        }*/
        $em = $this->getDoctrine()->getManager();
        $MenuTypes =  $em->getRepository('AppBundle:DDlMenuType')->findAll();

        return $this->render('agriApp/SurveyQuestion/SurveyQs.html.twig',['possibleTables' => $MenuTypes]);
    }

    /**
     * @Route("/SurveyQsRecords", name="SurveyQsRecords")
     */
    public function getSurveyQuestionRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $surveyQsRecords = $em->getRepository('AppBundle:SurveyQuestion')->findAll();
        return $this->render("agriApp/SurveyQuestion/SurveyQsInJson.html.twig", ['surveyQsRecords' => $surveyQsRecords]);
    }


    /**
     * @Route("/deleteSurveyQ", name="deleteSurveyQ")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $SurveyQRecord = $em->getRepository('AppBundle:SurveyQuestion')->find($request->request->get('surveyqdelID'));
            $em->remove($SurveyQRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/addSurveyQ",name="addSurveyQ")
     */
    public function adSurveyQ(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $surveyQRecord = new SurveyQuestion();
                    $surveyQRecord->setQuestion($request->request->get('surveyqadd'));

                    if ($request->request->get('surveywithdropdownadd') == 'Yes'){
                        $ddlt = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('surveydropdownadd'));
                        $surveyQRecord->setDropdowntable($ddlt);
                    }
                    $surveyQRecord->setQuestionwithdropdown($request->request->get('surveywithdropdownadd'));

                    if($request->request->get('surveywithdetailsadd') == 'Yes'){
                        $surveyQRecord->setDetailshint($request->request->get('surveydetailshintadd'));
                    }
                    $surveyQRecord->setQuestionwithdetails($request->request->get('surveywithdetailsadd'));

                    $em->persist($surveyQRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Survey Question','info' => $e->getMessage()));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Survey Question'));

    }

    /**
     * @Route("/editSurveyQ",name="editSurveyQ")
     */
    public function editSurveyQ(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $surveyQRecord = $em->getRepository('AppBundle:SurveyQuestion')->find($request->request->get('surveyqEditID'));
                    $surveyQRecord->setQuestion($request->request->get('surveyqedit'));

                    if ($request->request->get('surveywithdropdownedit') == 'Yes'){

                        $ddlt = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('surveydropdownedit'));
                        $surveyQRecord->setDropdowntable($ddlt);

                        //$surveyQRecord->setDropdowntable($request->request->get('surveydropdownedit'));
                    }else{
                        $surveyQRecord->setDropdowntable(null);
                    }
                    $surveyQRecord->setQuestionwithdropdown($request->request->get('surveywithdropdownedit'));

                    if($request->request->get('surveywithdetailsedit') == 'Yes'){
                        $surveyQRecord->setDetailshint($request->request->get('surveydetailshintedit'));
                    }else{
                        $surveyQRecord->setDetailshint(null);
                    }
                    $surveyQRecord->setQuestionwithdetails($request->request->get('surveywithdetailsedit'));

                    $em->persist($surveyQRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Survey Question'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Survey Question'));
    }

}
