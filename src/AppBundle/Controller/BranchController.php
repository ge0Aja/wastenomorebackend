<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BranchController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/cms/BranchRecords",name="BranchRecords")
     */
    public function getBranchRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $BranchRecords = $em->getRepository('AppBundle:Branch')->findAll();
        return $this->render("agriApp/Branch/BranchRecordsInJson.html.twig", ['BranchRecords' => $BranchRecords]);
    }


    /**
     * @Route("/cms/Branches",name="Branches")
     */
    public function BranchRecords()
    {
        return $this->render('agriApp/Branch/branchRecords.html.twig');
    }



    /**
     * @Route("/cms/deleteBranch", name="deleteBranch")
     */
    public function DeleteLogAction(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            $branchRecord = $em->getRepository('AppBundle:Branch')->find($request->request->get('barnchdelID'));
            $em->remove($branchRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
