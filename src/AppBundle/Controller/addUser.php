<?php
/**
 * Created by PhpStorm.
 * User: saugo
 * Date: 9/2/16
 * Time: 1:04 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\addUsers;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class addUser extends Controller
{
    /**
     * @Route("/addUser", name="addUser")
     */
    public function  addUser(Request $request)
    {
        // 1) build the form
        $user = new Users();
        $form = $this->createForm(addUsers::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('login');
        }
        return $this->render(
            'agriApp/addUser.html.twig',
            array('form' => $form->createView())
        );
    }
}