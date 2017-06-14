<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class BranchController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }



    /**
     * @Route("/BranchRecords",name="BranchRecords")
     */
    public function getBranchRecords()
    {
        /* return $this->render('agriApp/companyRecords.html.twig');*/

        $em = $this->getDoctrine()->getManager();
        $BranchRecords = $em->getRepository('AppBundle:Branch')->findAll();
        return $this->render("agriApp/Branch/BranchRecordsInJson.html.twig", ['BranchRecords' => $BranchRecords]);
    }


    /**
     * @Route("/Branches",name="BranchRecordsPage")
     */
    public function BranchRecords()
    {
        return $this->render('agriApp/Branch/branchRecords.html.twig');
    }



    /**
     * @Route("/deleteBranch/{id}", name="deleteBranch")
     */
    public function DeleteLogAction($id){
        try {
            $em = $this->getDoctrine()->getManager();
            $branchRecord = $em->getRepository('AppBundle:Branch')->find($id);
            $em->remove($branchRecord);
            $em->flush();

            return new JsonResponse(array('status' => 'success'));
        } catch (DBALException $e) {
            return new JsonResponse(array('status' => 'error', 'message' => 'Can\'t delete record'));
        }
    }
}
