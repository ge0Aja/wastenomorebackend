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

class loginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if($error)
        {
            $text='<div class="alert alert-danger alert-dismissible">';
            $text.= '<button class="close" data-dismiss="alert">&times;</button>';
            $text.= '<strong><i class="icon fa fa-ban"></i>Invalid Credentials</strong>';
            $text.= '</div>';
            return $this->render('agriApp/login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'text' => $text,
                )
            );
        }
        $text = '';// $this->render_flashed_alert();
        return $this->render('agriApp/login.html.twig', array(
                'last_username' => $lastUsername,
                'error' => $error,
                'text' => $text,
            )
        );
    }

    /**
     * @Route("/")
     */
    public function lgg()
    {
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/logout",name="logout")
     */
    public function lgout()
    {
        return $this->redirectToRoute('login');
    }


}