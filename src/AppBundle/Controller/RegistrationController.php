<?php

namespace AppBundle\Controller;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;

use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\Model\ChangePassword;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends Controller
{

    CONST ROLE_ADMIN = 'ROLE_ADMIN';
    CONST ROLE_USER = 'ROLE_USER';
    /**
     * @Route("/register", name="registerCMSUser")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setActiveUser(true);

            // 4) save the User!
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('home');
        }

        return $this->render(
            ':agriApp:addUser.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/changePasswd", name="changePasswdCMSUser")
     */
    public function changePasswdAction(Request $request,UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em)
    {
        $changePasswordModel = new ChangePassword();
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $changePassword = $form->getData();

          //  var_dump($changePassword);

            $password = $passwordEncoder->encodePassword($user, $changePassword->getPlainPassword());

            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render(
            ':agriApp:changePassword.html.twig',
            array('form' => $form->createView())
        );
    }
}