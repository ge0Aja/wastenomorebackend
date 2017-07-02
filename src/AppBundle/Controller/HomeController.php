<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    //TODO: Implement conversion for normalization relevant to quan and quan_in_kg, not only quan_in_kg, refer to the db table for more


    /**
     * @Route("/home",name="home")
     */
    public function home()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:WasteTypeCategory')->findAll();
        $kilogramUnit = $em->getRepository('AppBundle:Unit')->findOneBy(['name'=>'Kg']);
        $arrayToSendToTwigG1 = [];
        $arrayToSendToTwigG2 = [];
        $wasteCatDateValue = [];

        $unitsArr=[];

        foreach ($categories as $category)
        {

            $categoryWastes = $em->getRepository('AppBundle:Waste')->findBy(['waste_type_category' => $category->getId()]);
            $wasteCategoryTotal = 0;
            $wasteCategoryTotalQuantity=0;
            $i=0;
            $len = count($categoryWastes);

            $categoryWastesByDate = $em->getRepository('AppBundle:Waste')->findBy(['waste_type_category'=>$category->getId()],['waste_date'=>'ASC']);
//            return new Response(dump($categoryWastesByDate));
            foreach ($categoryWastesByDate as $categoryWasteByDate)
            {
                $wasteDate = $categoryWasteByDate->getWasteDate()->format('Y-m-d');
//                $unitsArr[$categoryWasteByDate->getUnit()->getName()]=$categoryWasteByDate->getUnit()->getName();
                if(array_key_exists($category->getName(),$wasteCatDateValue))
                {
                    if(array_key_exists($wasteDate,$wasteCatDateValue[$category->getName()]))
                    {
                        $previousSum = (int)$wasteCatDateValue[$category->getName()][$wasteDate];

                        if($categoryWasteByDate->getUnit()->getName() == 'Kg')
                        {
//                            return new Response(dump($previousSum));
                            $wasteCatDateValue[$category->getName()][$wasteDate] = (int)$previousSum + $categoryWasteByDate->getQuantity();
//                            return new Response(dump($wasteCatDateValue));
                        }
                        else
                        {
                            $unit = $categoryWasteByDate->getUnit();
                            $conversion = $em->getRepository('AppBundle:Conversion')->findOneBy(['unit'=>$unit->getId()]);
                            $wasteCatDateValue[$category->getName()][$wasteDate]= (int)$previousSum+$categoryWasteByDate->getQuantity()*$conversion->getQuanInKg();
                        }

                    }
                    else
                    {
                        if($categoryWasteByDate->getUnit()->getName() === 'Kg')
                        {
                            $wasteCatDateValue[$category->getName()][$wasteDate] = $categoryWasteByDate->getQuantity();
//                            return new Response(dump($wasteCatDateValue));
                        }
                        else
                        {
                            $unit = $categoryWasteByDate->getUnit();
                            $conversion = $em->getRepository('AppBundle:Conversion')->findOneBy(['unit'=>$unit->getId()]);
                            $wasteCatDateValue[$category->getName()][$wasteDate]= $categoryWasteByDate->getQuantity()*$conversion->getQuanInKg();
//                            return new Response(dump($conversion));
                        }

                    }
                }

                else
                {
                    $wasteDate = $categoryWasteByDate->getWasteDate()->format('Y-m-d');
                    $wasteCatDateValue[$category->getName()] = [$wasteDate => (int)$categoryWasteByDate->getQuantity()];
                }
            }


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
                //for graph2 normalization
                $wasteCategoryTotalQuantity+=$categoryWaste->getQuantity();

                if ($i == $len - 1)
                {
                    $arrayToSendToTwigG1[$category->getName()] = $wasteCategoryTotal;
                    $catTimeValues[$category->getName()] = ['date'=>$categoryWaste->getWasteDate(),'quantityInKg'=>$wasteCategoryTotal];
                }
                $i++;
            }

            $purchcaseCategoryTotalQuantity = 0;

            $purchases = $em->getRepository('AppBundle:Purchases')->findBy(['type'=>$category->getId()]);
            foreach ($purchases as $purchase)
            {
                $purchcaseCategoryTotalQuantity += $purchase->getQuantity();
            }

//            return new Response(dump(($wasteCategoryTotalQuantity/$purchcaseCategoryTotalQuantity)*100));
//            return new Response(dump($purchcaseCategoryTotalQuantity));

            if($purchcaseCategoryTotalQuantity!=0) {
                $wastePercentage = 100 * ($wasteCategoryTotalQuantity / $purchcaseCategoryTotalQuantity);
                $purchasePercentage = 100 - $wastePercentage;
            }
            else {
                $wastePercentage = 0;
                $purchasePercentage = 0;
            }

            $arrayToSendToTwigG2[$category->getName()]=['waste'=>$wastePercentage, 'purchase'=>$purchasePercentage];

        }

//        return new Response(dump($wasteCatDateValue));
//        return new Response(dump($arrayToSendToTwigG1));
//        return new Response(dump($arrayToSendToTwigG2));
//        return new Response(dump($unitsArr));
        return $this->render(':agriApp:home.html.twig',['niceArray'=>$arrayToSendToTwigG1,'niceArray2'=>$arrayToSendToTwigG2,'catTimeValues'=>$wasteCatDateValue]);
    }
}
