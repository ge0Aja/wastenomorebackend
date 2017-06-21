<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DDlMenuType;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DDlMenuTypeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/MenuTypes",name="MenuTypes")
     */
    public function getMenuTypes()
    {
        return $this->render('agriApp/DDlMenuT/DDlMenuTypes.html.twig');
    }


    /**
     * @Route("/MenuTypeRecords", name="MenuTypeRecords")
     */
    public function getMenuTypeRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $menuTypeRecords = $em->getRepository('AppBundle:DDlMenuType')->findAll();
        return $this->render("agriApp/DDlMenuT/DDlMenuTypesInJson.html.twig", ['menuTypeRecords' => $menuTypeRecords]);
    }


    /**
     * @Route("/deleteMenuType", name="deleteMenuType")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $MenuTypeRecord = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('menutypedelID'));
            $em->remove($MenuTypeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/addMenuType",name="addMenuType")
     */
    public function addMenuType(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $menuTypeRecord = new DDlMenuType();
                    $menuTypeRecord->setName($request->request->get('menutypeadd'));
                    $em->persist($menuTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Menu Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Menu Type'));

    }

    /**
     * @Route("/editMenuType",name="editMenuType")
     */
    public function editMenuType(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $menuTypeRecord = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('menutypeEditID'));
                    $menuTypeRecord->setName($request->request->get('editmenutypetxt'));
                    $em->persist($menuTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Menu Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Menu Type'));
    }
}
