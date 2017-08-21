<?php
/**
 * Created by PhpStorm.
 * User: saugo
 * Date: 8/31/16
 * Time: 2:43 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class loginController extends Controller
{
    /**
     * @Route("cms/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('agriApp/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/cms/")
     */
    public function lgg()
    {
        return $this->redirectToRoute('login');
    }


    /**
     * @Route("/")
     */
    public function lgg2()
    {
        return $this->render('agriApp/Welcome.html.twig');
    }

    /**
     * @Route("/logout",name="logout")
     */
    public function lgout()
    {
        $this->get('session')->clear();
        return $this->redirectToRoute('login');
    }


}