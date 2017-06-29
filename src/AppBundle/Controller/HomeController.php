<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/home",name="home")
     */
    public function home()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:WasteTypeCategory')->findAll();
        $kilogramUnit = $em->getRepository('AppBundle:Unit')->findOneBy(['name'=>'Kg']);
        $arrayToSendToTwig = [];
        foreach ($categories as $category)
        {
            $categoryWastes = $em->getRepository('AppBundle:Waste')->findBy(['waste_type_category' => $category->getId()]);

            $wasteCategoryTotal = 0;
            $i=0;
            $len = count($categoryWastes);
            foreach ($categoryWastes as $categoryWaste)
            {
                if($categoryWaste->getUnit() !== $kilogramUnit)
                {
                    $unit = $categoryWaste->getUnit();
                    $conversion = $em->getRepository('AppBundle:Conversion')->findOneBy(['unit'=>$unit->getId()]);
                    $wasteCategoryTotal+= ($conversion->getQuanInKg() * $categoryWaste->getQuantity());
                }
                else
                {
                    $wasteCategoryTotal+= ($categoryWaste->getQuantity());
                }
                if ($i == $len - 1)
                {
                    $arrayToSendToTwig[$categoryWaste->getWasteTypeCategory()->getName()] = $wasteCategoryTotal;
                }
                $i++;
            }
        }
//        return new Response(dump($arrayToSendToTwig));
        return $this->render(':agriApp:home.html.twig',['niceArray'=>$arrayToSendToTwig]);
    }
}
