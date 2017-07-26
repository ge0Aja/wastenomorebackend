<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class DefaultController extends Controller
{
    /**
     * @Route(path="api/token_authentication", name="token_authentication")
     */
    public function tokenAuthentication(Request $request)
    {

        $logger = $this->get('logger');

        if ($request->getMethod() === 'POST') {
            if ($request->request) {

                $username = $request->request->get('username');
                $password = $request->request->get('password');

                if(isset($username) && isset($password)) {
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

                    if (!$user) {
                        throw $this->createNotFoundException();
                    }

                    // password check
                    if (!$this->get('security.password_encoder')->isPasswordValid($user, $password)) {
                        throw $this->createAccessDeniedException();
                    }

                    // Use LexikJWTAuthenticationBundle to create JWT token that hold only information about user name
                    $token = $this->get('lexik_jwt_authentication.encoder.default')
                        ->encode(['username' => $user->getUsername()]);

                    // Return genereted tocken
                    return new JsonResponse(['token' => $token]);
                }
            }
        }
    }

    /**
     * @Route(path="api/secure_resource", name="secure_resource")
     */
    public function secureResource(){
        $data = [
            "test" => 'test',
            "test2" => 'test2'
        ];

        return new JsonResponse($data);
    }

}
