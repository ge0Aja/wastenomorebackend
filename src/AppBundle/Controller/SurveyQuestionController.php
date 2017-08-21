<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\SurveyQuestion;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SurveyQuestionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/cms/SurveyQuestions",name="SurveyQuestions")
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
     * @Route("/cms/SurveyQsRecords", name="SurveyQsRecords")
     */
    public function getSurveyQuestionRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $surveyQsRecords = $em->getRepository('AppBundle:SurveyQuestion')->findAll();
        return $this->render("agriApp/SurveyQuestion/SurveyQsInJson.html.twig", ['surveyQsRecords' => $surveyQsRecords]);
    }


    /**
     * @Route("/cms/deleteSurveyQ", name="deleteSurveyQ")
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
     * @Route("/cms/addSurveyQ",name="addSurveyQ")
     */
    public function addSurveyQ(Request $request)
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
                        $surveyQRecord->setQuestionwithdropdown(1);
                    }


                    if($request->request->get('surveywithdetailsadd') == 'Yes'){
                        $surveyQRecord->setDetailshint($request->request->get('surveydetailshintadd'));
                        $surveyQRecord->setQuestionwithdetails(1);
                    }


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
     * @Route("/cms/editSurveyQ",name="editSurveyQ")
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
                        $surveyQRecord->setQuestionwithdropdown(1);
                    }else{
                        $surveyQRecord->setQuestionwithdropdown(0);
                    }


                    if($request->request->get('surveywithdetailsedit') == 'Yes'){
                        $surveyQRecord->setDetailshint($request->request->get('surveydetailshintedit'));
                        $surveyQRecord->setQuestionwithdetails(1);
                    }else{
                        $surveyQRecord->setQuestionwithdetails(0);
                    }


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


    /**
     * @Route(path="api/generateQs",name="generateQs")
     */
    public function getSurveyQuestion(Request $request) {

        try{

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);

            $em = $this->getDoctrine()->getManager();

            try{

             $questions_records = $em->getRepository("AppBundle:SurveyQuestion")->findAll();

             $questions = array();

             foreach ($questions_records as $record){

                 $ddl_items = array();

                 $with_detail = $record->getQuestionwithdetails() == 1;
                 $with_ddl = $record->getQuestionwithdropdown() == 1;


                 if($with_ddl && null != $record->getDropdowntable()){

                     $ddl_items_records = $em->getRepository("AppBundle:DDlMenuSubType")->findBy(["type" => $record->getDropdowntable()]);

                     foreach ($ddl_items_records as $ddl_item){
                         array_push($ddl_items,array("key" => $ddl_item->getId(),"label" => $ddl_item->getName()));
                     }

                 }

                 array_push($questions,array("qid" => $record->getId(),"q" => $record->getQuestion(), "detail" => $with_detail, "hint" => $record->getDetailshint() ,"ddl" => $with_ddl, "ddl_items" => $ddl_items));
             }

             return new JsonResponse(array("status" => "success", "questions" => $questions));

            }catch (Exception $e){
                Throw new Exception("Params Exception",666);

            }catch (DBALException $e){

                Throw new Exception("DB Error",777);

            }catch (\Throwable $t){

                Throw new Exception("Null Error",666);
            }

        }catch (Exception $e) {
            return new JsonResponse(array("status" => "error", "reason" => $e->getMessage()));
        }

    }


    public function submitSurveyAnswers(Request $request) {

        try{

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);

            if (empty($content))
                throw new Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();

            try{

                $company = $user->getManagedCompany();



            }catch (Exception $e){

            }


        }catch (Exception $e){

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
