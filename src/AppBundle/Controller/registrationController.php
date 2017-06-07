<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class registrationController extends Controller
{
    /**
     * @Route("/register",name="register")
     */
    public function registrationPage()
    {
        return $this->render("agriApp/registration.html.twig");
    }
}
