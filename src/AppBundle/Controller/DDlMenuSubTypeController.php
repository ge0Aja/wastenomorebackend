<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DDlMenuSubType;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DDlMenuSubTypeController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/cms/MenuSubTypes",name="MenuSubTypes")
     */
    public function getMenuSubTypes()
    {
        $em =  $this->getDoctrine()->getManager();
        $menuTypes = $em->getRepository('AppBundle:DDlMenuType')->findBy(array(),array("name" => "ASC"));

        return $this->render(':agriApp/DDlMenuST:DDlMenuSubTypes.html.twig',["menuTypes" => $menuTypes]);
    }


    /**
     * @Route("/cms/MenuSubTypeRecords", name="MenuSubTypeRecords")
     */
    public function getMenuSubTypeRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $menuSubTypeRecords = $em->getRepository('AppBundle:DDlMenuSubType')->findAll();
        return $this->render("agriApp/DDlMenuST/DDlMenuSubTypesInJson.html.twig", ['menuSubTypeRecords' => $menuSubTypeRecords]);
    }


    /**
     * @Route("/cms/deleteMenuSubType", name="deleteMenuSubType")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $MenuSubTypeRecord = $em->getRepository('AppBundle:DDlMenuSubType')->find($request->request->get('menusubtypedelID'));
            $em->remove($MenuSubTypeRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }


    /**
     * @Route("/cms/addMenuSubType",name="addMenuSubType")
     */
    public function addCompanyType(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $menuSubTypeRecord = new DDlMenuSubType();
                    $menuSubTypeRecord->setName($request->request->get('menusubtypeadd'));

                    $mt = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('menutypeselect'));
                    $menuSubTypeRecord->setType($mt);
                    $em->persist($menuSubTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Menu Sub Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Menu Sub Type'));

    }

    /**
     * @Route("/cms/editMenuSubType",name="editMenuSubType")
     */
    public function editCompanyType(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                try {
                    $em = $this->getDoctrine()->getManager();
                    $menuSubTypeRecord = $em->getRepository('AppBundle:DDlMenuSubType')->find($request->request->get('menusubtypeEditID'));
                    $menuSubTypeRecord->setName($request->request->get('menusubtypeedit'));

                    $mt = $em->getRepository('AppBundle:DDlMenuType')->find($request->request->get('menutypeselect_edit'));

                    $menuSubTypeRecord->setType($mt);

                    $em->persist($menuSubTypeRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Menu Sub Type'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t edit Menu Sub Type'));
    }
}
