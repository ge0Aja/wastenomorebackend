<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AppRoleController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/AppRolesRecords",name="AppRolesRecords")
     */
    public function getAppRolesRecords()
    {
        $em = $this->getDoctrine()->getManager();
        $AppRolesRecords = $em->getRepository('AppBundle:AppRole')->findAll();
        return $this->render("agriApp/AppRole/appRolesRecordsInJson.html.twig", ['AppRolesRecords' => $AppRolesRecords]);
    }


    /**
     * @Route("/appRoles",name="appRoles")
     */
    public function AppRolesRecords()
    {
        return $this->render('agriApp/AppRole/appRolesRecords.html.twig');
    }

    /**
     * @Route("/deleteAppRole", name="deleteAppRole")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $AppRoleRecord = $em->getRepository('AppBundle:AppRole')->find($request->request->get('delRoleID'));
            $em->remove($AppRoleRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }

    /**
     * @Route("/addAppRole",name="addAppRole")
     */
    public function addAppRole(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $appRolesRecord = new AppRole();
                    $appRolesRecord->setRole($request->request->get('roleadd'));
                    $em->persist($appRolesRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Role'));
                }
            }
        }
        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t add Role'));
    }

    /**
     * @Route("/editAppRole",name="editAppRole")
     */
    public function editAnnualSales(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            if ($request->request) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $appRoleRecord = $em->getRepository('AppBundle:AppRole')->find($request->request->get('editRoleID'));
                    $appRoleRecord->setRole($request->request->get('roleedit'));
                    $em->persist($appRoleRecord);
                    $em->flush();
                    return new JsonResponse(array('status' => 'success'));
                } catch (DBALException $e) {
                    return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Role'));
                }
            }
        }

        return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t update Role'));
    }


}
