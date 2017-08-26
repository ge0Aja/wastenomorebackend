<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SurveyVersion;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SurveyVersionController extends Controller
{
//    public function indexAction($name)
//    {
//        return $this->render('', array('name' => $name));
//    }


    /**
     * @Route("/cms/SurveyVersions",name="SurveyVersions")
     */
    public function getSurveyVersions()
    {
        return $this->render('agriApp/SurveyVersions/SurveyVersion.html.twig');
    }


    /**
     * @Route("/cms/SurveyVersionsRecords", name="SurveyVersionsRecords")
     */
    public function getSurveyVersionsRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $surveyVersionsRecords = $em->getRepository('AppBundle:SurveyVersion')->findAll();
        return $this->render("agriApp/SurveyVersions/SurveyVersionInJson.html.twig", ['surveyVersionsRecords' => $surveyVersionsRecords]);
    }


    /**
     * @Route(path="/cms/addSurveyVersion", name="addSurveyVersion")
     */
    public function addSurveyVersion(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {

                    $startDate = new \DateTime($request->request->get('startDateadd'));
                    $expiryDate = new \DateTime($request->request->get('expiryDateadd'));

                    if ($startDate >= $expiryDate)
                        return new JsonResponse(array("status" => "form_error", "message" => "Date Error"));


                    $em = $this->getDoctrine()->getManager();

                    $surveyVRecord = new SurveyVersion();


                    $surveyVRecord->setActive(0);
                    $surveyVRecord->setTitle($request->request->get('surveytitleadd'));
                    $surveyVRecord->setNote($request->request->get('surveynoteadd'));
                    $surveyVRecord->setBeginDate($startDate);
                    $surveyVRecord->setEndDate($expiryDate);

                    $em->persist($surveyVRecord);
                    $em->flush();

                    return new JsonResponse(array('status' => 'success'));

                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Survey Version', 'info' => $e->getMessage()));
                }
            }
        }
    }


    /**
     * @Route(path="/cms/editSurveyVersion", name="editSurveyVersion")
     */
    public function editSurveyVersion(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {

                    $startDate = new \DateTime($request->request->get('startDateedit'));
                    $expiryDate = new \DateTime($request->request->get('expiryDateedit'));

                    if ($startDate >= $expiryDate)
                        return new JsonResponse(array("status" => "form_error", "message" => "Date Error"));

                    $em = $this->getDoctrine()->getManager();
                    $surveyVRecord = $em->getRepository('AppBundle:SurveyVersion')->find($request->request->get('surveyvEditID'));

                    $surveyVRecord->setTitle($request->request->get('surveytitleedit'));
                    $surveyVRecord->setNote($request->request->get('surveynoteedit'));
                    $surveyVRecord->setBeginDate($startDate);
                    $surveyVRecord->setEndDate($expiryDate);

                    $em->persist($surveyVRecord);
                    $em->flush();


                    return new JsonResponse(array('status' => 'success'));

                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t Edit Survey Version'));
                }
            }
        }
    }


    /**
     * @Route("/cms/changeSurveyVersionStatus", name="changeSurveyVersionStatus")
     */
    public function changeSurveyVStatus(Request $request)
    {
        if ($request->request) {
            try {

                $em = $this->getDoctrine()->getManager();

                $surveyVRecords = $em->getRepository('AppBundle:SurveyVersion')->findAll();

                foreach ($surveyVRecords as $record) {
                    $record->setActive(0);
                    $em->persist($record);
                }

                $surveyVRecord = $em->getRepository('AppBundle:SurveyVersion')->find($request->request->get('editSurveyVID'));

                if ($_POST["isActive"] == 1)
                    $surveyVRecord->setActive(1);
                else
                    $surveyVRecord->setActive(0);
                $em->persist($surveyVRecord);
                $em->flush();
                return new JsonResponse(array('status' => 'success'));

            } catch (DBALException $e) {
                return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Survey Version Status'));
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Survey Version Status'));
    }


    /**
     * @Route("/cms/deActivateSurveyVersions", name="deActivateSurveyVersions")
     */
    public function deActivateAll(Request $request)
    {
        if ($request->request) {

            try {
                $em = $this->getDoctrine()->getManager();

                $surveyVRecords = $em->getRepository('AppBundle:SurveyVersion')->findAll();

                foreach ($surveyVRecords as $record) {
                    $record->setActive(0);
                    $em->persist($record);
                }
                $em->flush();
                return new JsonResponse(array('status' => 'success'));
            } catch (DBALException $e) {
                return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t DeActivate Survey Version'));
            }

        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t DeActivate Survey Version'));
    }

    /**
     * @Route("/cms/deleteSurveyV", name="deleteSurveyV")
     */
    public function DeleteLogAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        try {

            $surveyVRecord = $em->getRepository('AppBundle:SurveyVersion')->find($request->request->get('surveyvdelID'));
            $em->remove($surveyVRecord);
            $em->flush();
            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


}
