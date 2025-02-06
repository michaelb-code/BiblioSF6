<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/accueil', name: 'app_accueil')]
    public function accueil(){
        $nombre = 45;
        $prenom = "john";

        return $this->render("base.html.twig",[
            "nombre" => $nombre,
            "prenom" => $prenom
        ]);
    }

    #[Route('/test/heritage', name: 'app_heritage')]
    public function heritage(){
        
        return $this->render("test/heritage.html.twig");
    }

    #[Route('/test/transitif', name: 'app_transitif')]
    public function transitif(){
        
        return $this->render("test/transitif.html.twig");
    }

    #[Route('/test/tableau', name: 'app_tableau')]
    public function tableau(){

        $tab =["jour" => "06", "mois" => "Février", "annee" => 2025];
        
        return $this->render("test/tableau.html.twig",
    [
        "tableau" => $tab,
        "tableau2" => [40, "test", true],
        "nombre" => 5,
        "chaine" => "",
    ]);
        
    }
}
