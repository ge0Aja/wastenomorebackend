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
