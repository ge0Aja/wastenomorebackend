<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\SurveyQuestionAnswer;
use Doctrine\DBAL\DBALException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SurveyAnswerController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route(path="/cms/SurveyAnswers", name="SurveyAnswers")
     */
    public function getSurveyAnswers(){

        return $this->render('agriApp/SurveyAnswers/SurveyAnswer.html.twig');

    }


    /**
     * @Route(path="/cms/SurveyAnswersRecords", name="SurveyAnswersRecords")
     */
    public function getSurveyAnswersRecords(){

        $em = $this->getDoctrine()->getManager();
        $surveyAsRecords = $em->getRepository('AppBundle:SurveyQuestionAnswer')->findAll();
        return $this->render("agriApp/SurveyAnswers/SurveyAnswerInJson.html.twig", ['surveyAsRecords' => $surveyAsRecords]);

    }


    /**
     * @Route(path="api/answerSurvey",name="answerSurvey")
     */
    public function submitSurveyAnswers(Request $request) {

        try{

            $user = $this->getLoggedUser($request);

            if (null === $user)
                throw new Exception("User Error", 401);

            if ($user->getAppRole()->getRole() != AppRole::COMPANY_MANAGER)
                throw new Exception("User Error", 401);

            $content = $request->getContent();

            if (empty($content))
                throw new Exception("Content Error", 666);

            $params = json_decode($content, true);

            $em = $this->getDoctrine()->getManager();


            $em->getConnection()->beginTransaction();
            try{

                $company = $user->getManagedCompany();

                $answer_timestamp = '';
                $surveyVersion =null;

                $answers = array();
                foreach($params as $param){

                    $attrs = explode(":", $param);


                    if($attrs[0] == "surveyVersion"){
                        $surveyVersion = (int)$attrs[1];
                    }
                    if($attrs[0] == "timestamp"){
                        $answer_timestamp = $attrs[1];

                    }else {

                        $answer_type = substr($attrs[0], 0, 1);
                        $question_id = substr($attrs[0], 1, 1);

                        if(!array_key_exists($question_id,$answers)) {
                            $answer = new SurveyQuestionAnswer();

                            if ($answer_type == "D") {
                                $answer->setDetails($attrs[1]);
                            }

                            if ($answer_type == "P") {
                                $answer->setDropdownanswer($em->getRepository("AppBundle:DDlMenuSubType")->findOneBy(["id" => (int)$attrs[1]]));
                            }

                            $answer->setCompany($company);
                            $answer->setQuestion($em->getRepository("AppBundle:SurveyQuestion")->findOneBy(["id" => $question_id]));
                            $answer->setTimestamp($answer_timestamp);
                            $answer->setSurveyVersion($surveyVersion);
                            $answers[$question_id] = $answer;
                        }else{

                            $current_answer = $answers[$question_id];

                            if ($answer_type == "D") {
                                $current_answer->setDetails($attrs[1]);
                            }

                            if ($answer_type == "P") {
                                $current_answer->setDropdownanswer($em->getRepository("AppBundle:DDlMenuSubType")->findOneBy(["id" => (int)$attrs[1]]));
                            }
                        }

                    }

                }

                foreach ($answers as $answerItem){
                    $em->persist($answerItem);
                }
                $em->flush();
                $em->getConnection()->commit();
                return new JsonResponse(array("status" => "success"));
            }catch (Exception $e){
                $em->getConnection()->rollBack();
                Throw new Exception("Params Error",666);
            }
            catch (DBALException $e){
                $em->getConnection()->rollBack();
                Throw new Exception("DB Error",666);
            }
            catch (\Throwable $t){
                $em->getConnection()->rollBack();
                Throw  new Exception("Null Error",777);
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
