<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    //TODO: Implement conversion for normalization relevant to quan and quan_in_kg, not only quan_in_kg, refer to the db table for more


    /**
     * @Route("/cms/home",name="home")
     */
    public function home()
    {

//        return new Response(dump($this->getGraph4()));

        $arrayToSendToTwigG1 = $this->getGraph1();
        $arrayToSendToTwigG2 = $this->getGraph2();
        $wasteCatDateValue = $this->getGraph3();
        $wasteCatDateValue2 = $this->getGraph4();


        return $this->render(':agriApp:home.html.twig',['niceArray'=>$arrayToSendToTwigG1,'niceArray2'=>$arrayToSendToTwigG2,'catTimeValues'=>$wasteCatDateValue,'catTimeValues2'=>$wasteCatDateValue2]);
    }




    public function getGraph1()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select("wtc.name as Name, case u.name when 'Kg' then sum(w.quantity) else sum(w.quantity*c.quanInKg)/c.quan END as Quantity")
            ->from("AppBundle:Waste", "w")
            ->join("w.unit", "u")
            ->join("w.waste_type_subcategory", "wts")
            ->leftJoin("wts.conversionT", "c")
            ->join("w.branch", "b")
            ->join("wts.category_type", "wtc")
            ->groupBy("Name,u.name,c.quan")
            ->orderBy('Name', 'ASC');

        $query = $qb->getQuery();
        $temparray = $query->getArrayResult();
        $total_waste = 0.0;
        $categories = array();
        $graph1 = array();
        foreach ($temparray as $tempas)
        {
            $total_waste += floatval($tempas["Quantity"]);
        }
        foreach ($temparray as $tempas)
        {
            $graph1[$tempas["Name"]] = (100*$tempas["Quantity"])/$total_waste;
        }
        return $graph1;
        ////
    }

    public function getGraph2()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb2 = $em->createQueryBuilder();

        $qb->select("wtc.name as Name, case u.name when 'Kg' then sum(w.quantity) else sum(w.quantity*c.quanInKg)/c.quan END as QUpper")
            ->from("AppBundle:Waste", "w")
            ->join("w.unit", "u")
            ->join("w.waste_type_subcategory", "wts")
            ->leftJoin("wts.conversionT", "c")
            ->join("w.branch", "b")
            ->join("wts.category_type", "wtc")
            ->groupBy("Name,u.name,c.quan")
            ->orderBy('Name', 'ASC');


        $qb2->select("wtc.name as Name, case u.name when 'Kg' then sum(p.quantity) else sum(p.quantity*c.quanInKg)/c.quan END as QLower")
            ->from("AppBundle:Purchases", "p")
            ->join("p.unit", "u")
            ->join("p.type", "wts")
            ->leftJoin("wts.conversionT", "c")
            ->join("p.branch", "b")
            ->join("wts.category_type", "wtc")
            ->groupBy("Name,u.name,c.quan")
            ->orderBy('Name', 'ASC');
        $query = $qb->getQuery();
        $query2 = $qb2->getQuery();
        $temparray = $query->getArrayResult();
        $temparray2 = $query2->getArrayResult();
        $categories = array();
        $graph2 = array();
        for ($i=0; $i < count($temparray);$i++)
        {
            $wasted = 100*($temparray[$i]["QUpper"])/$temparray2[$i]["QLower"];
            $purchased = 100-$wasted;
            $graph2[$temparray[$i]["Name"]] = ["waste"=>$wasted,"purchase"=>$purchased];
        }
        return $graph2;
    }

    public function getGraph3()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select("w.timestamp as Timestamp,wtc.name as Name,  case u.name when 'Kg' then (w.quantity) else (w.quantity*c.quanInKg)/c.quan END as Quantity")
            ->from("AppBundle:Waste", "w")
            ->join("w.unit", "u")
            ->join("w.waste_type_subcategory", "wts")
            ->leftJoin("wts.conversionT", "c")
            ->join("w.branch", "b")
            ->join("wts.category_type", "wtc")
           // ->groupBy("Name,u.name,c.quan,w.timestamp")
            ->orderBy('Timestamp', 'ASC');

        $query = $qb->getQuery();
        $temparray = $query->getArrayResult();

        $graph3 = array();

        foreach($temparray as $tempas2)
        {
            $quantity = $tempas2["Quantity"];
            $timeStamp = $tempas2["Timestamp"];
            $graph3[$tempas2["Name"]][]= ["timeStamp"=>$timeStamp,"quantity"=>$quantity];
        }
        return $graph3;
    }

    public function getGraph4()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select("p.timestamp as Timestamp, wtc.name as Name, case u.name when 'Kg' then (p.quantity) else (p.quantity*c.quanInKg)/c.quan END as QLower")
            ->from("AppBundle:Purchases", "p")
            ->join("p.unit", "u")
            ->join("p.type", "wts")
            ->leftJoin("wts.conversionT", "c")
            ->join("p.branch", "b")
            ->join("wts.category_type", "wtc")
           // ->groupBy("Name,u.name,c.quan,p.timestamp")
            ->orderBy('Name', 'ASC');

        $query = $qb->getQuery();
        $temparray = $query->getArrayResult();
//        return $temparray;

        $graph3 = array();

        foreach($temparray as $tempas2)
        {
            $quantity = $tempas2["QLower"];
            $timeStamp = $tempas2["Timestamp"];
            $graph3[$tempas2["Name"]][]= ["timeStamp"=>$timeStamp,"quantity"=>$quantity];
        }
        return $graph3;
    }
}
