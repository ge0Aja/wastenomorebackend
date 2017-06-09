<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WasteController extends Controller
{
    /**
     * @Route("/WasteLogs",name="wasteLogs")
     */
    public function WasteLogs()
    {
        $em = $this->getDoctrine()->getManager();
        $wasteLogs = $em->getRepository('AppBundle:Waste')->findAll();
        return $this->render('agriApp/wasteLogs.html.twig',['wasteLogs' => $wasteLogs]);
    }
}
